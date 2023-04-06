<?php

use App\Http\Controllers\Attendance\AttendanceCorrectionController;
use App\Http\Controllers\Attendance\AttendanceDailyController;
use App\Http\Controllers\Attendance\AttendanceDurationRecapController;
use App\Http\Controllers\Attendance\AttendanceHolidayController;
use App\Http\Controllers\Attendance\AttendanceLeaveController;
use App\Http\Controllers\Attendance\AttendanceLeaveMasterController;
use App\Http\Controllers\Attendance\AttendanceLocationSettingController;
use App\Http\Controllers\Attendance\AttendanceMachineController;
use App\Http\Controllers\Attendance\AttendanceMonthlyController;
use App\Http\Controllers\Attendance\AttendanceOvertimeController;
use App\Http\Controllers\Attendance\AttendancePermissionController;
use App\Http\Controllers\Attendance\AttendanceRecapController;
use App\Http\Controllers\Attendance\AttendanceShiftController;
use App\Http\Controllers\Attendance\AttendanceTimesheetController;
use App\Http\Controllers\Attendance\AttendanceTimesheetRecapController;
use App\Http\Controllers\Attendance\AttendanceWorkScheduleController;
use App\Http\Controllers\Product\ProductController;
use Illuminate\Support\Facades\Route;

Route::name('products.')->group(function () {
    Route::get('/products/products/data', [ProductController::class, 'data'])->name('products.data');
    Route::resource('/products/products', ProductController::class);
});


