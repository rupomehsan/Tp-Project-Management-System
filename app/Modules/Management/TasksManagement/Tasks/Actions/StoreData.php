<?php

namespace App\Modules\Management\TasksManagement\Tasks\Actions;

use App\Modules\Management\Notification\Actions\StoreData as SendNotification;

class StoreData
{
    static $model = \App\Modules\Management\TasksManagement\Tasks\Models\Model::class;

    public static function execute($request)
    {
        try {
            $requestData = $request->validated();
            
            if (auth()->user()->role_id != 1) {
                $requestData['assigned_to'] = auth()->id();
            }
            
            $data = self::$model::create($requestData);
            if (!$data) {
                return messageResponse('Failed to add item', [], 400, 'error');
            }

            if (auth()->user()->role_id == 1) {
                $project = \App\Modules\Management\ProjectManagement\Project\Models\Model::find($data->project_id);
                $notification = [
                    'type' => 'task',
                    'title' => auth()->user()->name . ' assigned a new task: ' . $data->title . ' in project: ' . ($project->name ?? ''),
                    'link' => 'tasks/details/' . $data->slug,
                    'creator' => auth()->id(),
                    'slug' => $data->slug,
                ];
                $notificationUsers = [$data->assigned_to];
                SendNotification::execute($notification, $notificationUsers);
            }



            return messageResponse('Item added successfully', $data, 201);
        } catch (\Throwable $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
