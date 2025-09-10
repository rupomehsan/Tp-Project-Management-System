<?php

namespace App\Modules\Management\AttendanceManagement\Attendance\Actions;

class UpdateData
{
    static $model = \App\Modules\Management\AttendanceManagement\Attendance\Models\Model::class;

    public static function execute($request, $slug)
    {
        try {

            if (!$data = self::$model::query()->where('slug', $slug)->first()) {
                return messageResponse('Data not found...', $data, 404, 'error');
            }

            $requestData = $request->validated();
            $date = auth()->user()->role_id != 1 ? date('Y-m-d') : ($requestData['check_in'] ?? date('Y-m-d'));

            // Calculate late minutes if check_in time is provided
            if (isset($requestData['check_in']) && $requestData['check_in']) {
                // Parse the check_in time
                $checkInTime = $requestData['check_in'];

                // Extract time part (handle both datetime and time formats)
                if (strpos($checkInTime, 'T') !== false) {
                    // Handle datetime format like "2025-08-03T09:30"
                    $timePart = explode('T', $checkInTime)[1];
                } elseif (strpos($checkInTime, ' ') !== false) {
                    // Handle datetime format like "2025-08-03 09:30:00"
                    $timePart = explode(' ', $checkInTime)[1];
                } else {
                    // Already just time like "09:30:00"
                    $timePart = $checkInTime;
                }

                // Extract hours and minutes
                $timeParts = explode(':', $timePart);
                $checkInHour = (int)$timeParts[0];
                $checkInMinute = (int)$timeParts[1];

                // Calculate total minutes from midnight
                $checkInTotalMinutes = ($checkInHour * 60) + $checkInMinute;
                $deadlineMinutes = (9 * 60) + 15; // 09:15 = 555 minutes from midnight

                // Calculate late minutes and update status
                if ($checkInTotalMinutes > $deadlineMinutes) {
                    $lateMinutes = $checkInTotalMinutes - $deadlineMinutes;
                    $requestData['late_minutes'] = $lateMinutes;
                    $requestData['is_late'] = true;
                } else {
                    $requestData['late_minutes'] = 0;
                    $requestData['is_late'] = false;
                }
            }

            // Set 'date' to the date part of 'check_in' if available, else fallback to today
            if (isset($requestData['check_in']) && $requestData['check_in']) {
                $checkInValue = $requestData['check_in'];
                if (strpos($checkInValue, 'T') !== false) {
                    $datePart = explode('T', $checkInValue)[0];
                } elseif (strpos($checkInValue, ' ') !== false) {
                    $datePart = explode(' ', $checkInValue)[0];
                } else {
                    // If only time is provided, fallback to today
                    $datePart = date('Y-m-d');
                }
                $requestData['date'] = $datePart;
            } else {
                $requestData['date'] = date('Y-m-d');
            }
            if (auth()->user()->role_id != 1) {
                $requestData['user_id'] = auth()->id();
            }

            // Handle absent status - if user is absent, no checkout time is needed
            if (isset($requestData['attendance_status']) && $requestData['attendance_status'] === 'Absent') {
                $requestData['check_out'] = null; // Clear checkout time for absent users
                $requestData['is_late'] = false;
                $requestData['late_minutes'] = 0;
            }



            $data->update($requestData);
            return messageResponse('Item updated successfully', $data, 201);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
