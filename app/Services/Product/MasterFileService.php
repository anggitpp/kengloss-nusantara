<?php
namespace App\Services\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\MasterFileRequest;
use App\Repositories\Product\MasterFileRepository;
use App\Models\Product\MasterFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Str;

class MasterFileService extends Controller
{
    private MasterFileRepository $masterFileRepository;
    private string $filePath;
    public function __construct()
    {
        $this->masterFileRepository = new MasterFileRepository(
            new MasterFile()
        );
        $this->filePath = 'uploads/master-files/';
    }

    public function getMasterFiles(): Builder
    {
        return $this->masterFileRepository->query();
    }

    public function getMasterFileById(int $id): MasterFile
    {
        return $this->masterFileRepository->getById($id);
    }

    /**
     * @throws \Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getMasterFiles();
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) $query->where('name', 'like', "%{$filter}%")
                    ->orWhere('code', 'like', "%{$filter}%");
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'file', 'type' => 'filename'],
                ['name' => 'status', 'type' => 'status'],
            ], true);
        }
    }

    public function submit(MasterFileRequest $request, int $id = 0): void
    {
        $fields = [
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ];

        if ($id == 0)
            $masterFile = $this->masterFileRepository->create($fields);
        else
            $masterFile = $this->masterFileRepository->update($fields, $id);

        defaultUploadFile($masterFile, $request, $this->filePath, 'master-file_' . Str::slug($request->input('name')) . '_' . time(), 'file');
    }

    public function delete(int $id): void
    {
        $master = $this->getMasterFileById($id);
        if($master->file != null) {
            deleteFile($master->file);
        }
        $master->delete();
    }
}