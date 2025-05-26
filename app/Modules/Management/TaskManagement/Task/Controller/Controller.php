<?php

namespace App\Modules\Management\TaskManagement\Task\Controller;

use App\Http\Controllers\Controller as ControllersController;
use App\Modules\Management\TaskManagement\Task\Actions\StoreData;
use App\Modules\Management\TaskManagement\Task\Actions\GetAllData;
use App\Modules\Management\TaskManagement\Task\Actions\ImportData;
use App\Modules\Management\TaskManagement\Task\Actions\SoftDelete;
use App\Modules\Management\TaskManagement\Task\Actions\UpdateData;
use App\Modules\Management\TaskManagement\Task\Actions\BulkActions;
use App\Modules\Management\TaskManagement\Task\Actions\DestroyData;

use App\Modules\Management\TaskManagement\Task\Actions\RestoreData;
use App\Modules\Management\TaskManagement\Task\Actions\UpdateStatus;
use App\Modules\Management\TaskManagement\Task\Actions\GetSingleData;

use App\Modules\Management\TaskManagement\Task\Validations\DataStoreValidation;
use App\Modules\Management\TaskManagement\Task\Validations\BulkActionsValidation;


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