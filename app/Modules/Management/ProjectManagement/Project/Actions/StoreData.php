<?php

namespace App\Modules\Management\ProjectManagement\Project\Actions;

use Illuminate\Support\Facades\Storage;

class StoreData
{
    static $model = \App\Modules\Management\ProjectManagement\Project\Models\Model::class;

    public static function execute($request)
    {
        try {
            $requestData = $request->validated(); // Get validated form data

            $currentDate = now()->format('Y/m'); // e.g. 2025/05

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

            // Create the database record
            if ($data = self::$model::query()->create($requestData)) {
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
