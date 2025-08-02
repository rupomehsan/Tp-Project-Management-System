<?php

namespace App\Modules\Management\AttendanceManagement\Attendance\Actions;

class StoreData
{
    static $model = \App\Modules\Management\AttendanceManagement\Attendance\Models\Model::class;

    public static function execute($request)
    {
        try {

            $requestData = $request->validated();
            $date = auth()->user()->role_id != 1 ? date('Y-m-d') : ($requestData['date'] ?? date('Y-m-d'));

            // Only apply restrictions to non-admin users (role_id != 1)
            if (auth()->user()->role_id != 1) {
                // Check if date is Friday or Saturday (weekend/holiday)
                $dayOfWeek = date('N', strtotime($date)); // 5=Friday, 6=Saturday
                if ($dayOfWeek == 5 || $dayOfWeek == 6) {
                    return messageResponse('Attendance cannot be added on holidays (Friday or Saturday).', [], 400, 'error');
                }

                // Check if attendance already exists for today
                $alreadyExists = self::$model::where('user_id', auth()->id())
                    ->where('date', $date)
                    ->exists();

                if ($alreadyExists) {
                    return messageResponse('Attendance already submitted for today.', [], 400, 'error');
                }
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
            } else {
                // For admin users, ensure date is set
                $requestData['date'] = $date;
            }

            // Handle absent status - if user is absent, no checkout time is needed
            if (isset($requestData['attendance_status']) && $requestData['attendance_status'] === 'Absent') {
                $requestData['check_out'] = null; // Clear checkout time for absent users
                $requestData['is_late'] = false;
                $requestData['late_minutes'] = 0;
            }

    




            if ($data = self::$model::query()->create($requestData)) {
                return messageResponse('Item added successfully', $data, 201);
            }
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
