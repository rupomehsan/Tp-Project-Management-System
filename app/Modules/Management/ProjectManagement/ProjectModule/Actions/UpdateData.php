<?php

namespace App\Modules\Management\ProjectManagement\ProjectModule\Actions;

class UpdateData
{
    static $model = \App\Modules\Management\ProjectManagement\ProjectModule\Models\Model::class;

    public static function execute($request,$slug)
    {
        try {
           $requestData = $request->validated();
           $item = self::$model::where('slug',$slug)->first();
    
    
            if (isset($requestData['order'])) {
                $oldOrder = $item->order;
                $newOrder = (int)$requestData['order'];
    
                if ($newOrder < $oldOrder) {
                    // Moving UP
                    self::$model::where('project_id',$item->project_id)->whereBetween('order', [$newOrder, $oldOrder - 1])
                        ->increment('order');
                } elseif ($newOrder > $oldOrder) {
                    // Moving DOWN
                    self::$model::where('project_id',$item->project_id)->whereBetween('order', [$oldOrder + 1, $newOrder])
                        ->decrement('order');
                }
            }
    
            $item->update($requestData);
    
            return messageResponse('Item updated successfully', $item, 200);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(),[], 500, 'server_error');
        }
    }
}