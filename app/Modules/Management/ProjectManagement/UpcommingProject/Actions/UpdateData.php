<?php

namespace App\Modules\Management\ProjectManagement\UpcommingProject\Actions;

use Illuminate\Support\Facades\Storage;

class UpdateData
{
    static $model = \App\Modules\Management\ProjectManagement\UpcommingProject\Models\Model::class;

    public static function execute($request, $slug)
    {
        try {
            // Find by slug
            $data = self::$model::query()->where('slug', $slug)->first();
            if (!$data) {
                return messageResponse('Data not found...', null, 404, 'error');
            }

            $requestData = $request->validated();
            $currentDate = now()->format('Y/m');

            // Handle project_agrement_file update
            if ($request->hasFile('project_agrement_file')) {
                // Delete old file if it exists
                if ($data->project_agrement_file && Storage::disk('public')->exists($data->project_agrement_file)) {
                    Storage::disk('public')->delete($data->project_agrement_file);
                }

                $file = $request->file('project_agrement_file');
                $requestData['project_agrement_file'] = self::uploader($file, "uploads/project/agrement/{$currentDate}");
            }

            // Handle project_document update
            if ($request->hasFile('project_document')) {
                // Delete old file if it exists
                if ($data->project_document && Storage::disk('public')->exists($data->project_document)) {
                    Storage::disk('public')->delete($data->project_document);
                }

                $file = $request->file('project_document');
                $requestData['project_document'] = self::uploader($file, "uploads/project/document/{$currentDate}");
            }

            // Update the model
            $data->update($requestData);

            return messageResponse('Item updated successfully', $data, 200);

        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }

    private static function uploader($file, $path)
    {
        return $file->store($path, 'public');
    }
}
