<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    HomeController, 
    UserController, 
    RoleController, 
    PermissionController, 
    ProfileController, 
    StudentController,
    BatchController,
    CourseController,
    StudentEnrollmentController
};
use App\Http\Controllers\Admin\CertificateController as AdminCert;
use App\Http\Controllers\Faculty\CertificateController as FacultyCert;
use App\Http\Controllers\Student\CertificateController as StudentCert;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

// --- ALL AUTHENTICATED ROUTES ---
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'show')->name('show');
        Route::get('/edit', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::put('/password', 'updatePassword')->name('password.update');
    });

    // --- Student Section ---
    // NOTE: Custom routes MUST come BEFORE resource routes to avoid being intercepted by 'students/{student}'
    Route::get('students/quick-create', [StudentController::class, 'quickCreate'])->name('students.quick_create');
    Route::post('students/quick-store', [StudentController::class, 'quickStore'])->name('students.quick_store');
    Route::get('students/ajax/course-batches/{courseId}', [StudentController::class, 'getCourseBatches']);
    Route::post('students/{student}/notify-status', [StudentController::class, 'notifyStatus'])->name('students.notify.status');
    
    Route::resource('students', StudentController::class);
    Route::resource('courses', CourseController::class);
    Route::resource('enrollments', StudentEnrollmentController::class);

    // --- Batch Management ---
    // Custom batch routes
    Route::post('batches/transfer', [BatchController::class, 'transfer'])->name('batches.transfer.store');
    Route::post('attendance', [BatchController::class, 'storeAttendance'])->name('attendance.store');

    // Batch Specific Operations
    Route::controller(BatchController::class)->prefix('batches/{batch}')->name('batches.')->group(function () {
        Route::get('attendance', 'attendance')->name('attendance');
        Route::get('enroll', 'enrollForm')->name('enroll');
        Route::post('enroll', 'enroll')->name('enroll.store');
        Route::post('schedule', 'addSchedule')->name('schedule.store');
        Route::post('faculty', 'storeFaculty')->name('faculty.store');
        Route::get('students/{student}/transfer', 'showTransfer')->name('transfer');
        Route::delete('students/{student}/unenroll', 'unenroll')->name('unenroll');
        Route::post('notify/{student}', 'notifyStudent')->name('notify-student');
        Route::post('notify', 'sendNotification')->name('notify');
    });

    Route::resource('batches', BatchController::class);

    // Access Control (RBAC)
    Route::resources([
        'permissions' => PermissionController::class,
        'roles'       => RoleController::class,
        'users'       => UserController::class,
    ]);

    /* |--------------------------------------------------------------------------
       | Certification Workflow
       |-------------------------------------------------------------------------- */

    // Admin Routes
    Route::middleware(['role:Admin|Super Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/certificates', [AdminCert::class, 'index'])->name('certificates.index');
        Route::post('/certificates/{certificate}/process', [AdminCert::class, 'process'])->name('certificates.process');
        Route::post('/certificates/{certificate}/regenerate', [AdminCert::class, 'regenerate'])->name('certificates.regenerate');
    });

    // Faculty Routes
    Route::middleware(['role:Faculty|Super Admin'])->prefix('faculty')->name('faculty.')->group(function () {
        Route::get('/certificates', [FacultyCert::class, 'index'])->name('certificates.index');
        Route::post('/certificates/{certificate}/process', [FacultyCert::class, 'process'])->name('certificates.process');
    });

    // Student Download Route
    Route::get('/certificates/{certificate}/download', [StudentCert::class, 'download'])->name('student.certificates.download');

});