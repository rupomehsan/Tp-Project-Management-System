<?php

namespace App\Modules\Management\Dashboard\Actions;

use App\Modules\Management\ProjectManagement\Project\Models\Model as Project;
use App\Modules\Management\TodoManagement\AddTodo\Models\Model as Todo;
use App\Modules\Management\TodoManagement\Credential\Models\Model as Credential;
use App\Modules\Management\AttendanceManagement\Attendance\Models\Model as Attendance;
use App\Modules\Management\TasksManagement\Tasks\Models\Model as Task;

class GetEmployeeDashboardData
{
    public static function execute()
    {
        try {
            $user = auth()->user();

            // Separate variables for each count
            $totalProjects        = Project::whereHas('project_users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();
            $totalTodos           = Todo::where('user_id', $user->id)->count();
            $totalCredentials     = Credential::where('user_id', $user->id)->count();
            $totalPresentAttendance      = Attendance::where('user_id', $user->id)->where('attendance_status', 'Present')->count();
            $totalAbsentAttendance      = Attendance::where('user_id', $user->id)->where('attendance_status', 'Absent')->count();
            $totalTasks = Task::where('assigned_to', $user->id)->count();
            $totalComleteTasks = Task::where('assigned_to', $user->id)->where('status', 'Completed')->count();
            $totalNotCompletedTasks = Task::where('assigned_to', $user->id)->where('status', 'Not Completed')->count();

            // Prepare final data array
            $data = [
                'total_projects'        => $totalProjects,
                'total_todos'           => $totalTodos,
                'total_credentials'     => $totalCredentials,
                'total_present_attendance' => $totalPresentAttendance,
                'total_absent_attendance' => $totalAbsentAttendance,
                'total_tasks'           => $totalTasks,
                'total_complete_tasks'  => $totalComleteTasks,
                'total_not_completed_tasks' => $totalNotCompletedTasks
            ];

            return entityResponse($data);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
