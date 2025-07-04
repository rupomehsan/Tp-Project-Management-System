<?php

namespace App\Modules\Management\ProjectManagement\Project\Actions;

use Illuminate\Support\Facades\Storage;

class FileLInkDelete
{
    static $model = \App\Modules\Management\ProjectManagement\Project\Models\Model::class;

    // Delete a document (file or link) from the array by id
    public static function execute($request, $id)
    {
        try {
            $data = self::$model::where('id', $id)->first();
            if (!$data) {
                return messageResponse('Data not found...', null, 404, 'error');
            }

            $index = $request->input('index');
            $files = $data->project_document_files;
            $file = $files[$index] ?? null;
            if ($file && isset($file['file'])) {
                // Delete the image file from storage if it exists
                if (Storage::disk('public')->exists($file['file'])) {
                    Storage::disk('public')->delete($file['file']);
                }
                unset($files[$index]);
                $files = array_values($files); // reindex array
                $data->project_document_files = $files;
            }

            $data->save();

            return messageResponse('Item deleted successfully', $data, 200, 'success');
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
