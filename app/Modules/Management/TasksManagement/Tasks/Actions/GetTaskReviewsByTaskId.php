<?php

namespace App\Modules\Management\TasksManagement\Tasks\Actions;



class GetTaskReviewsByTaskId
{
    static $model = \App\Modules\Management\TasksManagement\Tasks\Models\Model::class;
    static $taskReviewModel = \App\Modules\Management\TasksManagement\Tasks\Models\TaskReviewModel::class;

    public static function execute($id)
    {
        try {



            if (!$data = self::$model::where('id', $id)->first()) {
                return messageResponse('Data not found...', $data, 404, 'error');
            }

            $taskReviews = self::$taskReviewModel::query()

                ->where('task_id', $data->id)
                ->get();

            return entityResponse($taskReviews);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
