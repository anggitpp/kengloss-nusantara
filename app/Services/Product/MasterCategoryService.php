<?php

namespace App\Services\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\MasterCategoryRequest;
use App\Models\Product\MasterCategory;
use App\Repositories\Product\MasterCategoryRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterCategoryService extends Controller
{
    private MasterCategoryRepository $masterCategoryRepository;

    public function __construct()
    {
        $this->masterCategoryRepository = new MasterCategoryRepository(
            new MasterCategory()
        );
    }

    public function getMasterCategories(): Builder
    {
        return $this->masterCategoryRepository->query();
    }

    public function getMasterCategoryById(int $id): MasterCategory
    {
        return $this->masterCategoryRepository->getById($id);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getMasterCategories();
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) $query->where('name', 'like', "%{$filter}%")
                    ->orWhere('code', 'like', "%{$filter}%");
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'status', 'type' => 'status'],
            ], true);
        }
    }

    public function submit(MasterCategoryRequest $request, int $id = 0): void
    {
        $fields = [
            'code' => strtoupper($request->input('code')),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ];

        if ($id == 0)
            $this->masterCategoryRepository->create($fields);
        else
            $this->masterCategoryRepository->update($fields, $id);
    }

    public function delete(int $id): void
    {
        $this->masterCategoryRepository->destroy($id);
    }
}