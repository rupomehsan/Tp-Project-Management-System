<?php

namespace App\Modules\Management\TasksManagement\Tasks\Actions;

class UpdateData
{
    static $model = \App\Modules\Management\TasksManagement\Tasks\Models\Model::class;
    static $taskReviewModel = \App\Modules\Management\TasksManagement\Tasks\Models\TaskReviewModel::class;

    public static function execute($request, $slug)
    {
        try {
            if (!$data = self::$model::query()->where('slug', $slug)->first()) {
                return messageResponse('Data not found...', $data, 404, 'error');
            }

            $requestData = $request->validated();
            $data->update($requestData);

            $reviewData = [
                'task_id' => $data->id,
                'comment' => $request->input('review_comments', ''),
                'user_id' => auth()->id(),
            ];

            self::$taskReviewModel::query()->create($reviewData);

            return messageResponse('Item updated successfully', $data, 201);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
