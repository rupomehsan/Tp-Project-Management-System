<?php

namespace App\Modules\Management\AttendanceManagement\Attendance\Actions;

class StoreData
{
    static $model = \App\Modules\Management\AttendanceManagement\Attendance\Models\Model::class;

    public static function execute($request)
    {
        try {
            $requestData = $request->validated();
            // Check if date is Friday or Saturday (weekend/holiday)
            if (isset($requestData['date'])) {
                $date = $requestData['date'];
                $dayOfWeek = date('N', strtotime($date)); // 5=Friday, 6=Saturday
                if ($dayOfWeek == 5 || $dayOfWeek == 6) {

                    return messageResponse('Attendance cannot be added on holidays (Friday or Saturday).', [], 404, 'error');
                }
            }
            if ($data = self::$model::query()->create($requestData)) {
                return messageResponse('Item added successfully', $data, 201);
            }
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
