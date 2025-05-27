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

/// TaskManagement for Task
include_once base_path("app/Modules/Management/TaskManagement/Task/Routes/Route.php");
include_once base_path("app/Modules/Management/TaskManagement/TaskComment/Routes/Route.php");