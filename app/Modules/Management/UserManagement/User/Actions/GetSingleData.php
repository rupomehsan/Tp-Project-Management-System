<?php

namespace App\Modules\Management\UserManagement\User\Actions;



class GetSingleData
{
    static $model = \App\Modules\Management\UserManagement\User\Models\Model::class;

    public static function execute($slug)
    {
        try {
            $with = ['role', 'tasks', 'project', 'attendance'];
           
            $fields = request()->input('fields') ?? ['*'];
            if (!$data = self::$model::query()->with($with)->select($fields)->where('slug', $slug)->first()) {
                return messageResponse('Data not found...',$data, 404, 'error');
            }
            // dd($data->toArray());
            return entityResponse($data);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(),[], 500, 'server_error');
        }
    }
}