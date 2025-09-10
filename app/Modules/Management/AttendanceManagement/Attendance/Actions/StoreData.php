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

     
            if (auth()->user()->role_id != 1) {
                
                $requestData['user_id'] = auth()->id();
                $requestData['date'] = \Carbon\Carbon::parse($requestData['check_in'])->toDateString();

                // Adjust server time by subtracting 6 hours to get local time
                $adjustedTimestamp = time() - (6 * 60 * 60); // Subtract 6 hours
                $currentTime = date('H:i:s', $adjustedTimestamp);
                $currentHour = (int)date('H', $adjustedTimestamp);
                $currentMinute = (int)date('i', $adjustedTimestamp);
                
                // Calculate total minutes from midnight
                $currentTotalMinutes = ($currentHour * 60) + $currentMinute;
                $deadlineMinutes = (9 * 60) + 15; // 09:15 = 555 minutes from midnight

          

                if ($currentTotalMinutes > $deadlineMinutes) {
                    $requestData['attendance_status'] = 'Present';
                    $requestData['is_late'] = true;
                    
                    // Calculate late minutes: current minutes - deadline minutes
                    $lateMinutes = $currentTotalMinutes - $deadlineMinutes;
                    $requestData['late_minutes'] = $lateMinutes;
                    
                  
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
