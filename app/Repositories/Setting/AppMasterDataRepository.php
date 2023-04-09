<?php

namespace App\Repositories\Setting;

use App\Models\Setting\AppMasterData;
use Illuminate\Database\Eloquent\Builder;

class AppMasterDataRepository
{
    public function getMasters(): Builder
    {
        return AppMasterData::query();
    }

    public function getMasterById(int $id): AppMasterData
    {
        return AppMasterData::findOrFail($id);
    }
}
