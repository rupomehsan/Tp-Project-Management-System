<?php

namespace App\Modules\Management\TasksManagement\Tasks\Actions;



class ReplyToTaskReview
{
    static $model = \App\Modules\Management\TasksManagement\Tasks\Models\Model::class;

    public static function execute()
    {
        try {
            $request = request()->all();

            $data = [
                'task_id' =>  $request['task_id'],
                'user_id' => auth()->id(),
                'comment' => $request['comment'],
            ];
            $taskReviewModel = \App\Modules\Management\TasksManagement\Tasks\Models\TaskReviewModel::create($data);
            if (!$taskReviewModel) {
                return messageResponse('Failed to add comment ', [], 500, 'error');
            }



            return messageResponse('Comment added successfully', $data, 201);
        } catch (\Throwable $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
