<?php

namespace App\Modules\Management\TodoManagement\AddTodo\Actions;

class StoreData
{
    static $model = \App\Modules\Management\TodoManagement\AddTodo\Models\Model::class;

    public static function execute($request)
    {
        try {
      
            $requestData = $request->validated();
            $requestData['user_id'] = auth()->id();
            if ($data = self::$model::query()->create($requestData)) {
                
                return messageResponse('Item added successfully', $data, 201);
            }

        } catch (\Exception $e) {
            return messageResponse($e->getMessage(),[], 500, 'server_error');
        }
    }
}
