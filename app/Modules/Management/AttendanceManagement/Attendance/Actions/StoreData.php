<?php

namespace App\Modules\Management\AttendanceManagement\Attendance\Actions;

class StoreData
{
    static $model = \App\Modules\Management\AttendanceManagement\Attendance\Models\Model::class;

    public static function execute($request)
    {
        try {
            $requestData = $request->validated();
            $date = auth()->user()->role_id != 1 ? date('Y-m-d') : $requestData['date'];
            // Check if date is Friday or Saturday (weekend/holiday)
            if (isset($requestData['date'])) {

                $dayOfWeek = date('N', strtotime($date)); // 5=Friday, 6=Saturday
                if ($dayOfWeek == 5 || $dayOfWeek == 6) {

                    return messageResponse('Attendance cannot be added on holidays (Friday or Saturday).', [], 404, 'error');
                }
            }


            $alreadyExists = self::$model::where('user_id', auth()->id())
                ->where('date', $date)
                ->exists();

            if ($alreadyExists) {
                return messageResponse('Attendance already submitted for today.', [], 409, 'duplicate_entry');
            }



            if (auth()->user()->role_id != 1) {
                $requestData['user_id'] = auth()->id();
                $requestData['date'] = date('Y-m-d');

                $currentTime = strtotime(date('H:i'));
                $checkInDeadline = strtotime('09:15');

                if ($currentTime > $checkInDeadline) {
                    $requestData['attendance_status'] = 'Present';
                    $requestData['is_late'] = true;
                    $requestData['late_minutes'] = ceil(($currentTime - $checkInDeadline) / 60); // Calculate minutes late
                } else {
                    $requestData['attendance_status'] = 'Present';
                    $requestData['is_late'] = false;
                    $requestData['late_minutes'] = 0;
                }

                // Optional: if recording check_in time automatically
                $requestData['check_in'] = now();
            }


            if ($data = self::$model::query()->create($requestData)) {
                return messageResponse('Item added successfully', $data, 201);
            }
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
