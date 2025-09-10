<?php

namespace App\Modules\Management\ProjectManagement\ProjectPlan\Actions;

class DestroyData
{
    static $model = \App\Modules\Management\ProjectManagement\ProjectPlan\Models\PlanManagementModel::class;

    public static function execute($id)
    {
        try {


            if (!$data = self::$model::where('id', $id)->first()) {
                return messageResponse('Data not found...', $data, 404, 'error');
            }
            $data->forceDelete();
            return messageResponse('Item Successfully deleted', [], 200, 'success');
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
