<?php

namespace App\Modules\Management\ProjectManagement\ProjectModule\Actions;

class StoreData
{
    static $model = \App\Modules\Management\ProjectManagement\ProjectModule\Models\Model::class;

    public static function execute($request)
    {
        try {
            

                $requestData = $request->validated();
        
                // Ensure "order" exists
                if (isset($requestData['order'])) {
                    $order = (int)$requestData['order'];
        
                    // Shift existing orders >= new order
                    self::$model::where('order', '>=', $order)
                        ->increment('order');
                }
        
                if ($data = self::$model::create($requestData)) {
                    return messageResponse('Item added successfully', $data, 201);
                }
            
        } catch (\Exception $e) {
            
            return messageResponse($e->getMessage(),[], 500, 'server_error');
            
        }
        
    }
}