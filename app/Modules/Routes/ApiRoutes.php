<?php
/*
|--------------------------------------------------------------------------
| Auth Management Module
|--------------------------------------------------------------------------
*/
include_once base_path("app/Modules/Management/Auth/Routes/Route.php");
/*
|--------------------------------------------------------------------------
| Setting Management Module
|--------------------------------------------------------------------------
*/
include_once base_path("app/Modules/Management/SettingManagement/WebsiteSettings/Routes/Route.php");
/*
|--------------------------------------------------------------------------
| User Management Module
|--------------------------------------------------------------------------


*/
include_once base_path("app/Modules/Management/UserManagement/User/Routes/Route.php");
include_once base_path("app/Modules/Management/UserManagement/Role/Routes/Route.php");


/*
|--------------------------------------------------------------------------
| Project Management Module
|--------------------------------------------------------------------------
*/
include_once base_path("app/Modules/Management/ProjectManagement/ProjectGroup/Routes/Route.php");
include_once base_path("app/Modules/Management/ProjectManagement/Project/Routes/Route.php");
include_once base_path("app/Modules/Management/ProjectManagement/UpcommingProject/Routes/Route.php");

/*
|--------------------------------------------------------------------------
| Todo Management Module
|--------------------------------------------------------------------------
*/
include_once base_path("app/Modules/Management/TodoManagement/Category/Routes/Route.php");
include_once base_path("app/Modules/Management/TodoManagement/AddTodo/Routes/Route.php");
include_once base_path("app/Modules/Management/TodoManagement/Credential/Routes/Route.php");
/*
|--------------------------------------------------------------------------
| Task Management Module
|--------------------------------------------------------------------------
*/
include_once base_path("app/Modules/Management/TasksManagement/Tasks/Routes/Route.php");

/*
|--------------------------------------------------------------------------
| Team Management Module
|--------------------------------------------------------------------------
*/
include_once base_path("app/Modules/Management/TeamManagement/Team/Routes/Route.php");

/*
|--------------------------------------------------------------------------
| Attendance Management Module
|--------------------------------------------------------------------------
*/
include_once base_path("app/Modules/Management/AttendanceManagement/Attendance/Routes/Route.php");
/*
|--------------------------------------------------------------------------
| Meeting Management Module
|--------------------------------------------------------------------------
*/
include_once base_path("app/Modules/Management/MeetingManagement/MeetingGroup/Routes/Route.php");
include_once base_path("app/Modules/Management/MeetingManagement/Meeting/Routes/Route.php");
include_once base_path("app/Modules/Management/MeetingManagement/MeetingAgenda/Routes/Route.php");

/*
|--------------------------------------------------------------------------
| System Loss Management Module
|--------------------------------------------------------------------------
*/
include_once base_path("app/Modules/Management/SystemLossManagement/SystemLossCategory/Routes/Route.php");
include_once base_path("app/Modules/Management/SystemLossManagement/SystemLoss/Routes/Route.php");


