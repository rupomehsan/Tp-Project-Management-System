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

                // Adjust server time by subtracting 6 hours to get local time
                $adjustedTimestamp = time() - (6 * 60 * 60); // Subtract 6 hours
                $currentTime = date('H:i:s', $adjustedTimestamp);
                $currentHour = (int)date('H', $adjustedTimestamp);
                $currentMinute = (int)date('i', $adjustedTimestamp);
                
                // Calculate total minutes from midnight
                $currentTotalMinutes = ($currentHour * 60) + $currentMinute;
                $deadlineMinutes = (9 * 60) + 15; // 09:15 = 555 minutes from midnight

                // Debug: Log the times for troubleshooting
                \Illuminate\Support\Facades\Log::info('Attendance Check-in Times:', [
                    'server_time' => date('H:i:s'),
                    'adjusted_time' => $currentTime,
                    'adjusted_time_12hr' => date('h:i:s A', $adjustedTimestamp),
                    'deadline_time' => '09:15:00',
                    'current_total_minutes' => $currentTotalMinutes,
                    'deadline_minutes' => $deadlineMinutes,
                    'user_id' => auth()->id()
                ]);

                if ($currentTotalMinutes > $deadlineMinutes) {
                    $requestData['attendance_status'] = 'Present';
                    $requestData['is_late'] = true;
                    
                    // Calculate late minutes: current minutes - deadline minutes
                    $lateMinutes = $currentTotalMinutes - $deadlineMinutes;
                    $requestData['late_minutes'] = $lateMinutes;
                    
                    // Debug: Log the calculation
                    \Illuminate\Support\Facades\Log::info('Late Minutes Calculation:', [
                        'server_time' => date('H:i:s'),
                        'adjusted_time' => $currentTime,
                        'adjusted_time_12hr' => date('h:i:s A', $adjustedTimestamp),
                        'deadline_time' => '09:15:00',
                        'current_total_minutes' => $currentTotalMinutes,
                        'deadline_minutes' => $deadlineMinutes,
                        'late_minutes_calculated' => $lateMinutes,
                        'final_late_minutes' => $requestData['late_minutes']
                    ]);
                } else {
                    $requestData['attendance_status'] = 'Present';
                    $requestData['is_late'] = false;
                    $requestData['late_minutes'] = 0;
                    
                    \Illuminate\Support\Facades\Log::info('On Time Check-in:', [
                        'server_time' => date('H:i:s'),
                        'adjusted_time' => $currentTime,
                        'adjusted_time_12hr' => date('h:i:s A', $adjustedTimestamp),
                        'user_id' => auth()->id()
                    ]);
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
