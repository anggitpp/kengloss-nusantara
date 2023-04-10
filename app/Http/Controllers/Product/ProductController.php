<?php

namespace App\Http\Controllers\Product;

use App\Exports\GlobalExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Models\Product\MasterCategory;
use App\Models\Product\MasterFile;
use App\Models\Product\Product;
use App\Services\Product\MasterCategoryService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Storage;
use Str;
use URL;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    public string $productPath;
    public string $qrPath;
    private MasterCategoryService $masterCategoryService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->masterCategoryService = new MasterCategoryService();
        $this->productPath = '/uploads/product/product/';
        $this->qrPath = '/uploads/product/qr/';
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $categories = $this->masterCategoryService->getMasterCategories()->pluck('name', 'id')->toArray();

        return view('products.product.index', [
            'categories' => $categories,
        ]);
    }

    public function data(Request $request){
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            $filterCategory = $request->get('combo_1');
            $sql = Product::orderBy('name');

            return DataTables::of($sql)
                ->filter(function ($query) use ($filter, $filterCategory) {
                    if (isset($filter)) $query->where('name', 'like', "%{$filter}%")->orWhere('number', 'like', "%{$filter}%");
                    if(isset($filterCategory)) $query->where('category_id', $filterCategory);
                })
                ->editColumn('category_id', function ($model) {
                    return $model->category->name ?? '';
                })
                ->editColumn('file_id', function ($model) {
                    return $model->file?->file ? view('components.datatables.download', [
                        'url' => $model->file->file
                    ]) : '';
                })
                ->editColumn('production_date', function ($model) {
                    return $model->production_date && $model->production_date != '0000-00-00' ? setDate($model->production_date) : '';
                })
                ->editColumn('expired_date', function ($model) {
                    return $model->expired_date && $model->expired_date != '0000-00-00' ? setDate($model->expired_date) : '';
                })
                ->editColumn('photo', function ($model) {
                    return view('components.datatables.photo', [
                        'photo' => $model->photo,
                    ]);
                })
                ->editColumn('qr', function ($model) {
                    return $model->qr ? view('components.datatables.download', [
                        'url' => $model->qr
                    ]) : '';
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(str_replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'url_destroy' => route(str_replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                        'url_slot' => route(str_replace('/', '.', $this->menu_path()).'.show', $model->id),
                        'isModal' => false,
                    ]);
                })
                ->addIndexColumn()
                ->make();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $categories = MasterCategory::orderBy('name')
            ->selectRaw("id, CONCAT(code, ' - ', name) as combined_name")
            ->get()
            ->pluck('combined_name', 'id')
            ->toArray();

        $files = MasterFile::orderBy('name')
            ->selectRaw("id, CONCAT(code, ' - ', name) as combined_name")
            ->get()
            ->pluck('combined_name', 'id')
            ->toArray();


        return view('products.product.form', [
            'categories' => $categories,
            'files' => $files,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductRequest $request
     * @return RedirectResponse
     */
    public function store(ProductRequest $request)
    {
        try {
            $request->merge(['production_date' => resetDate($request->input('production_date'))]);
            $request->merge(['expired_date' => resetDate($request->input('expired_date'))]);

            $product = Product::create($request->except(['photo', 'qr']));

            QrCode::format('png')->size(500)->errorCorrection('H')->generate(URL::to('/detail/'.$product->id), public_path('storage'.$this->qrPath . $product->id.'.png'));

            $qr = $this->qrPath . $product->id.'.png';

            $product->update([
                'qr' => $qr
            ]);

            defaultUploadFile($product, $request, $this->productPath, 'product_' . Str::slug($request->input('name')) . '_' . time(), 'photo');

            Alert::success('Success', 'Data berhasil disimpan');

            return redirect()->route(Str::replace('/', '.', $this->menu_path()).'.index');
        } catch (Exception $e) {

            DB::rollBack();

            Alert::error('Error', $e->getMessage());

            return redirect()->route(Str::replace('/', '.', $this->menu_path()).'.index');
        }
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        $categories = MasterCategory::orderBy('name')->pluck('name', 'id')->toArray();
        $files = MasterFile::orderBy('name')->pluck('name', 'id')->toArray();

        return view('products.product.show', [
            'categories' => $categories,
            'files' => $files,
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $product = Product::findOrFail($id);
        $categories = MasterCategory::orderBy('name')
            ->selectRaw("id, CONCAT(code, ' - ', name) as combined_name")
            ->get()
            ->pluck('combined_name', 'id')
            ->toArray();

        $files = MasterFile::orderBy('name')
            ->selectRaw("id, CONCAT(code, ' - ', name) as combined_name")
            ->get()
            ->pluck('combined_name', 'id')
            ->toArray();

        return view('products.product.form', [
            'categories' => $categories,
            'files' => $files,
            'product' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(ProductRequest $request, int $id)
    {
        try {
            $product = Product::findOrFail($id);

            defaultUploadFile($product, $request, $this->productPath, 'product_' . Str::slug($request->input('name')) . '_' . time(), 'photo');

            $request->merge(['production_date' => resetDate($request->input('production_date'))]);
            $request->merge(['expired_date' => resetDate($request->input('expired_date'))]);

            $product->update($request->except('photo'));

            if(Storage::exists($this->qrPath.$product->qr)) Storage::delete($this->qrPath.$product->qr);
            QrCode::format('png')->size(500)->errorCorrection('H')->generate(URL::to('/detail/'.$product->id), public_path('storage'.$this->qrPath . $product->id.'.png'));

            $qr = $this->qrPath . $product->id.'.png';

            $product->update([
                'qr' => $qr
            ]);

            Alert::success('Success', 'Data berhasil disimpan');

            return redirect()->route(Str::replace('/', '.', $this->menu_path()).'.index');
        } catch (Exception $e) {

            DB::rollBack();

            Alert::error('Error', $e->getMessage());

            return redirect()->route(Str::replace('/', '.', $this->menu_path()).'.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        try {
            $product = Product::findOrFail($id);
            if(Storage::exists($this->productPath.$product->photo)) Storage::delete($this->productPath.$product->photo);
            $product->delete();

            DB::commit();

            Alert::success('Success', 'Data berhasil dihapus');

            return redirect()->back();
        } catch (Exception $e) {

            DB::rollBack();

            Alert::error('Error', $e->getMessage());

            return redirect()->back();
        }
    }
}
