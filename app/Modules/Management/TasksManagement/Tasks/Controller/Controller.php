<?php

namespace App\Modules\Management\TasksManagement\Tasks\Controller;
use App\Modules\Management\TasksManagement\Tasks\Actions\GetAllData;
use App\Modules\Management\TasksManagement\Tasks\Actions\DestroyData;
use App\Modules\Management\TasksManagement\Tasks\Actions\GetSingleData;
use App\Modules\Management\TasksManagement\Tasks\Actions\StoreData;
use App\Modules\Management\TasksManagement\Tasks\Actions\UpdateData;
use App\Modules\Management\TasksManagement\Tasks\Actions\UpdateStatus;
use App\Modules\Management\TasksManagement\Tasks\Actions\SoftDelete;
use App\Modules\Management\TasksManagement\Tasks\Actions\RestoreData;
use App\Modules\Management\TasksManagement\Tasks\Actions\ImportData;
use App\Modules\Management\TasksManagement\Tasks\Validations\BulkActionsValidation;
use App\Modules\Management\TasksManagement\Tasks\Validations\DataStoreValidation;
use App\Modules\Management\TasksManagement\Tasks\Actions\BulkActions;
use App\Http\Controllers\Controller as ControllersController;


class Controller extends ControllersController
{

    public function index( ){

        $data = GetAllData::execute();
        return $data;
    }

    public function store(DataStoreValidation $request)
    {
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