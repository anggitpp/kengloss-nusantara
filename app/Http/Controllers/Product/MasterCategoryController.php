<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\MasterCategoryRequest;
use App\Services\Product\MasterCategoryService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Str;

class MasterCategoryController extends Controller
{
    private array $statusOption;
    private MasterCategoryService $masterCategoryService;

    public function __construct()
    {
        $this->statusOption = defaultStatus();
        $this->middleware('auth');
        $this->masterCategoryService = new MasterCategoryService();

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
        return view(Str::replace('/', '.', $this->menu_path()) . '.index');
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->masterCategoryService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MasterCategoryRequest $request
     * @return JsonResponse
     */
    public function store(MasterCategoryRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->masterCategoryService->submit($request);
        }, true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $master = $this->masterCategoryService->getMasterCategoryById($id);

        return view(Str::replace('/', '.', $this->menu_path()) . '.form', compact('master'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MasterCategoryRequest $request
     * @param int $id
     * @return RedirectResponse|JsonResponse
     */
    public function update(MasterCategoryRequest $request, int $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->masterCategoryService->submit($request, $id);
        }, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        return deleteDataHelper(function () use ($id) {
            $this->masterCategoryService->delete($id);
        });
    }
}
