<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Default dashboard - will redirect based on user role
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->isAdmin() || $user->isFieldLeader()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isEmployee()) {
        return redirect()->route('employee.dashboard');
    }
    
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin routes
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    Route::resource('admin/employees', \App\Http\Controllers\EmployeeController::class)->names([
        'index' => 'admin.employees.index',
        'create' => 'admin.employees.create',
        'store' => 'admin.employees.store',
        'show' => 'admin.employees.show',
        'edit' => 'admin.employees.edit',
        'update' => 'admin.employees.update',
        'destroy' => 'admin.employees.destroy',
    ]);
    
    Route::resource('admin/work-locations', \App\Http\Controllers\WorkLocationController::class)->names([
        'index' => 'admin.work-locations.index',
        'create' => 'admin.work-locations.create',
        'store' => 'admin.work-locations.store',
        'show' => 'admin.work-locations.show',
        'edit' => 'admin.work-locations.edit',
        'update' => 'admin.work-locations.update',
        'destroy' => 'admin.work-locations.destroy',
    ]);
    
    Route::get('/admin/attendances', [App\Http\Controllers\AttendanceController::class, 'index'])->name('admin.attendances.index');
    Route::get('/admin/attendances/{id}', [App\Http\Controllers\AttendanceController::class, 'show'])->name('admin.attendances.show');
    Route::post('/admin/attendances/report', [App\Http\Controllers\AttendanceController::class, 'report'])->name('admin.attendances.report');
    Route::post('/admin/attendances/export-excel', [App\Http\Controllers\AttendanceController::class, 'exportExcel'])->name('admin.attendances.export-excel');
    Route::post('/admin/attendances/export-pdf', [App\Http\Controllers\AttendanceController::class, 'exportPdf'])->name('admin.attendances.export-pdf');
});

// Field Leader routes
Route::middleware(['auth', 'verified', 'role:field_leader'])->group(function () {
    Route::get('/field-leader/dashboard', function () {
        return view('field-leader.dashboard');
    })->name('field-leader.dashboard');
    
    Route::resource('field-leader/work-locations', \App\Http\Controllers\WorkLocationController::class)->names([
        'index' => 'field-leader.work-locations.index',
        'create' => 'field-leader.work-locations.create',
        'store' => 'field-leader.work-locations.store',
        'show' => 'field-leader.work-locations.show',
        'edit' => 'field-leader.work-locations.edit',
        'update' => 'field-leader.work-locations.update',
        'destroy' => 'field-leader.work-locations.destroy',
    ]);
    
    Route::get('/field-leader/attendances', [App\Http\Controllers\AttendanceController::class, 'index'])->name('field-leader.attendances.index');
});

// Employee routes
Route::middleware(['auth', 'verified', 'role:employee'])->group(function () {
    Route::get('/employee/dashboard', function () {
        return view('employee.dashboard');
    })->name('employee.dashboard');
    
    Route::get('/employee/attendance', function () {
        return view('employee.attendance.index');
    })->name('employee.attendance.index');
    
    Route::get('/employee/profile', function () {
        return view('employee.profile.show');
    })->name('employee.profile.show');
    
    // Attendance routes
    Route::post('/attendance/check-in', [App\Http\Controllers\AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [App\Http\Controllers\AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::get('/attendance/history', [App\Http\Controllers\AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/attendance/today-status', [App\Http\Controllers\AttendanceController::class, 'todayStatus'])->name('attendance.today-status');
});

// Common profile routes for all authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Face recognition routes
    Route::post('/face-verification', [App\Http\Controllers\FaceRecognitionController::class, 'verifyFace'])->name('face.verify');
    Route::post('/face-registration', [App\Http\Controllers\FaceRecognitionController::class, 'registerFace'])->name('face.register');
    Route::post('/face-detection', [App\Http\Controllers\FaceRecognitionController::class, 'detectFace'])->name('face.detect');
});

require __DIR__.'/auth.php';
