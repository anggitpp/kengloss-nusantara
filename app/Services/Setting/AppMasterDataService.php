<?php

namespace App\Services\Setting;

use App\Repositories\Setting\AppMasterDataRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AppMasterDataService
{

    private AppMasterDataRepository $appMasterDataRepository;
    public function __construct()
    {
        $this->appMasterDataRepository = new AppMasterDataRepository();
    }

    public function getMasters(string $code) : Builder
    {
        return $this->appMasterDataRepository->getMasters()->where('app_master_category_code', $code);
    }

    public function getMasterWithSpecificColumns(string $code, array $columns) : Collection
    {
        return $this->appMasterDataRepository->getMasters()->select($columns)->where('app_master_category_code', $code)->get();
    }

    public function getMasterForArray(string $code, int $parentId = 0, string $orderField = 'order', $isForImport = false) : array
    {
        $query = $this->getMasters($code);
        if($parentId != 0) $query->where('parent_id', $parentId);

        if($isForImport) return $query->orderBy($orderField)->pluck('id', DB::raw('lower(name)'))->toArray();
        return $query->orderBy($orderField)->pluck('name', 'id')->toArray();
    }

    public function getMasterByParentId(int $parentId, string $orderField = 'order', array $columns = ['id', 'name']) : Collection
    {
        return $this->appMasterDataRepository->getMasters()
            ->where('parent_id', $parentId)
            ->select($columns)
            ->orderBy($orderField)
            ->get();
    }
}
