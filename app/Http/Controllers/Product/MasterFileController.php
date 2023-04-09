<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\MasterFileRequest;
use App\Models\Product\MasterFile;
use App\Services\Product\MasterFileService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use RealRashid\SweetAlert\Facades\Alert;
use Str;
use Yajra\DataTables\DataTables;

class MasterFileController extends Controller
{
    private array $statusOption;
    private MasterFileService $masterFileService;
    public function __construct()
    {
        $this->statusOption = defaultStatus();
        $this->middleware('auth');
        $this->masterFileService = new MasterFileService();

        \View::share('statusOption', $this->statusOption);
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('products.master-files.index');
    }

    /**
     * @throws Exception
     */
    public function data(Request $request){
        return $this->masterFileService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('products.master-files.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MasterFileRequest $request
     * @return JsonResponse
     */
    public function store(MasterFileRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->masterFileService->submit($request);
        }, true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $master = MasterFile::findOrFail($id);

        return view('products.master-files.form', compact('master'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MasterFileRequest $request
     * @param int $id
     * @return RedirectResponse|JsonResponse
     */
    public function update(MasterFileRequest $request, int $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->masterFileService->submit($request, $id);
        }, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        return deleteDataHelper(function () use ($id) {
            $this->masterFileService->delete($id);
        });
    }
}
