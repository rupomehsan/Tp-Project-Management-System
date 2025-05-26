<?php

namespace App\Modules\Management\ProjectManagement\Project\Controller;
use App\Modules\Management\ProjectManagement\Project\Actions\GetAllData;
use App\Modules\Management\ProjectManagement\Project\Actions\DestroyData;
use App\Modules\Management\ProjectManagement\Project\Actions\GetSingleData;
use App\Modules\Management\ProjectManagement\Project\Actions\StoreData;
use App\Modules\Management\ProjectManagement\Project\Actions\UpdateData;
use App\Modules\Management\ProjectManagement\Project\Actions\UpdateStatus;
use App\Modules\Management\ProjectManagement\Project\Actions\SoftDelete;
use App\Modules\Management\ProjectManagement\Project\Actions\RestoreData;
use App\Modules\Management\ProjectManagement\Project\Actions\ImportData;
use App\Modules\Management\ProjectManagement\Project\Validations\BulkActionsValidation;
use App\Modules\Management\ProjectManagement\Project\Validations\DataStoreValidation;
use App\Modules\Management\ProjectManagement\Project\Actions\BulkActions;
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