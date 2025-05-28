<?php
namespace App\Modules\Management\TaskManagement\TaskComment\Controller;

use App\Http\Controllers\Controller as ControllersController;
use App\Modules\Management\TaskManagement\TaskComment\Actions\BulkActions;
use App\Modules\Management\TaskManagement\TaskComment\Actions\DestroyData;
use App\Modules\Management\TaskManagement\TaskComment\Actions\GetAllData;
use App\Modules\Management\TaskManagement\TaskComment\Actions\GetSingleData;
use App\Modules\Management\TaskManagement\TaskComment\Actions\ImportData;
use App\Modules\Management\TaskManagement\TaskComment\Actions\RestoreData;
use App\Modules\Management\TaskManagement\TaskComment\Actions\SoftDelete;
use App\Modules\Management\TaskManagement\TaskComment\Actions\StoreData;
use App\Modules\Management\TaskManagement\TaskComment\Actions\UpdateData;
use App\Modules\Management\TaskManagement\TaskComment\Actions\UpdateStatus;
use App\Modules\Management\TaskManagement\TaskComment\Validations\BulkActionsValidation;
use App\Modules\Management\TaskManagement\TaskComment\Validations\DataStoreValidation;

class Controller extends ControllersController
{

    public function index()
    {

        $data = GetAllData::execute();
        // dd($data);
        return $data;
    }

    public function store(DataStoreValidation $request)
    {

        // dd($request->all());
        $data = StoreData::execute($request);
        return $data;
    }

    public function show($slug)
    {
        $data = GetSingleData::execute($slug);
        return $data;
    }

    public function update(DataStoreValidation $request, $slug)
    {
        $data = UpdateData::execute($request, $slug);
        return $data;
    }

    public function updateStatus()
    {
        $data = UpdateStatus::execute();
        return $data;
    }

    public function softDelete()
    {
        $data = SoftDelete::execute();
        return $data;
    }
    public function destroy($slug)
    {
        $data = DestroyData::execute($slug);
        return $data;
    }
    public function restore()
    {
        $data = RestoreData::execute();
        return $data;
    }
    public function import()
    {
        $data = ImportData::execute();
        return $data;
    }
    public function bulkAction(BulkActionsValidation $request)
    {
        $data = BulkActions::execute($request);
        return $data;
    }
}