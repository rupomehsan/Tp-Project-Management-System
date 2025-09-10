<?php

namespace App\Modules\Management\ProjectManagement\ProjectPlan\Controller;

use App\Modules\Management\ProjectManagement\ProjectPlan\Actions\GetAllData;
use App\Modules\Management\ProjectManagement\ProjectPlan\Actions\DestroyData;
use App\Modules\Management\ProjectManagement\ProjectPlan\Actions\GetSingleData;
use App\Modules\Management\ProjectManagement\ProjectPlan\Actions\StoreData;
use App\Modules\Management\ProjectManagement\ProjectPlan\Actions\UpdateData;
use App\Modules\Management\ProjectManagement\ProjectPlan\Actions\UpdateStatus;
use App\Modules\Management\ProjectManagement\ProjectPlan\Actions\SoftDelete;
use App\Modules\Management\ProjectManagement\ProjectPlan\Actions\RestoreData;
use App\Modules\Management\ProjectManagement\ProjectPlan\Actions\ImportData;
use App\Modules\Management\ProjectManagement\ProjectPlan\Validations\BulkActionsValidation;
use App\Modules\Management\ProjectManagement\ProjectPlan\Validations\DataStoreValidation;
use App\Modules\Management\ProjectManagement\ProjectPlan\Actions\BulkActions;
use App\Http\Controllers\Controller as ControllersController;
use Illuminate\Http\Request;

class Controller extends ControllersController
{

    public function index()
    {

        $data = GetAllData::execute();
        return $data;
    }

    public function store(Request $request)
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
