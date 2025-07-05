<?php

namespace App\Modules\Management\ProjectManagement\Project\Actions;

use App\Modules\Management\Notification\Actions\StoreData as SendNotification;

class StoreData
{
    static $model = \App\Modules\Management\ProjectManagement\Project\Models\Model::class;

    public static function execute($request)
    {
        try {
            $requestData = $request->validated(); // Get validated form data

            $currentDate = now()->format('Y/m'); // e.g. 2025/05


            // dd($request->all());

            // Handle project agreement file upload
            if ($request->hasFile('project_agrement_file')) {
                $project_agrement_file = $request->file('project_agrement_file');
                $requestData['project_agrement_file'] = self::uploader($project_agrement_file, "uploads/project/agrement/{$currentDate}");
            }


            // Handle project document file upload
            if ($request->hasFile('project_document')) {
                $project_document = $request->file('project_document');
                $requestData['project_document'] = self::uploader($project_document, "uploads/project/document/{$currentDate}");
            }

            // Handle new separated project document links and files
            $links = [];
            $files = [];
            if ($request->has('project_document_links') && is_array($request->project_document_links)) {
                foreach ($request->project_document_links as $doc) {
                    if (isset($doc['link'])) {
                        $links[] = [
                            'name' => $doc['name'] ?? '',
                            'link' => $doc['link']
                        ];
                    }
                }
            }
            if ($request->has('project_document_files') && is_array($request->project_document_files)) {
                foreach ($request->project_document_files as $doc) {
                    if (isset($doc['file']) && $doc['file'] instanceof \Illuminate\Http\UploadedFile) {
                        $files[] = [
                            'name' => $doc['name'] ?? '',
                            'file' => self::uploader($doc['file'], "uploads/project/documents/{$currentDate}")
                        ];
                    }
                }
            }
            if (!empty($links)) {
                $requestData['project_document_links'] = $links;
            }
            if (!empty($files)) {
                $requestData['project_document_files'] = $files;
            }

            // Create the database record

            if ($data = self::$model::query()->create($requestData)) {
                $project_users = json_decode($request->input('project_users', []));
                $data->project_users()->sync($project_users);
                $notification = [
                    'type' => 'project',
                    'title' => auth()->user()->name . ' assigned a new project: ' . $data->name,
                    'link' => 'project/details/' . $data->slug,
                    'creator' => auth()->user()->id,
                    'slug' => $data->slug,
                ];
                $notificationUsers = $project_users;

                SendNotification::execute($notification, $notificationUsers);
                return messageResponse('Item added successfully', $data, 201);
            }
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }

    // ✅ Custom uploader function
    private static function uploader($file, $path)
    {
        return $file->store($path, 'public'); // stores in storage/app/public/uploads/project/...
    }
}
