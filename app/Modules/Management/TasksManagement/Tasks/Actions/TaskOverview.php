<?php

namespace App\Modules\Management\TasksManagement\Tasks\Actions;

class TaskOverview
{
    static $model = \App\Modules\Management\TasksManagement\Tasks\Models\Model::class;

    public static function execute($slug)
    {
        try {
            $model = self::$model;
            $data = $model::where('slug', $slug)->first();
            if (!$data) {
                return messageResponse('Data not found...', null, 404, 'error');
            }

            $requestData = request()->all();
            $status = $requestData['task_status'] ?? null;
            $rating = $requestData['rating'] ?? 0;

            if ($status === 'Not Completed') {
                $newData = $model::create([
                    'project_id' => $data->project_id,
                    'title' => $data->title,
                    'description' => $data->description,
                    'system_loss' => $data->system_loss,
                    'assigned_to' => $data->assigned_to,
                    'start_date' => $data->start_date,
                    'end_date' => $data->end_date,
                    'task_status' => 'Pending', // Set to 'pending' for new item
                    'priority' => $data->priority,
                    'rating' => 0,
                ]);
                $data->task_status = $status ?? $data->task_status;
                $data->rating = $rating;
                $data->update($requestData);
                return messageResponse('New item added as Not Completed', $newData, 201);
            }

            // Update existing data for other statuses
            $data->task_status = $status ?? $data->task_status;
            $data->rating = $rating;
            $data->update($requestData);

            return messageResponse('Item updated successfully', $data, 201);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
