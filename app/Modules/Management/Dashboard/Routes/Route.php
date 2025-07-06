<?php

use  App\Modules\Management\Dashboard\Controller\Controller;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth:api')->group(function () {
    Route::get('get-all-dashboard-data', [Controller::class, 'GetAllDashboardData']);
    Route::get('get-employee-dashboard-data', [Controller::class, 'GetEmployeeDashboardData']);
});
