<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\PICController;
use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    // Check for staff guard
    if (Auth::guard('staff')->check()) {
        return redirect()->route('staff.dashboard');
    }

    // Check for student guard
    if (Auth::guard('student')->check()) {
        return redirect()->route('student.dashboard');
    }

    // Check for general user authentication and redirect based on user type
    if (Auth::check()) {
        switch (Auth::user()->type) {
            case 'technician':
                return redirect('/technician/dashboard');
            case 'admin':
                return redirect('/admin/dashboard');
            case 'pic':
                return redirect('/pic/dashboard');
        }
    }

    // If none of the checks match, show the welcome page
    return view('welcome');
});

// Redirect /student to /student/login if not authenticated
Route::get('/student/login', function () {
    if (Auth::guard('student')->check()) {
        // If student is authenticated, redirect to the dashboard
        return redirect('/student/dashboard');
    }

    // If not authenticated, show the login form
    return view('auth.student.login');
});


// Redirect /staff to /staff/login if not authenticated
Route::get('/staff/login', function () {
    if (Auth::guard('staff')->check()) {
        // If student is authenticated, redirect to the dashboard
        return redirect('/staff/dashboard');
    }

    // If not authenticated, show the login form
    return view('auth.staff.login');
});

Auth::routes(['register' => false]);

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// student route
Route::prefix('student')->group(function () {
    // Student login routes (no authentication required)
    Route::get('/login', [App\Http\Controllers\Auth\StudentLoginController::class, 'showloginform'])->name('student.login');
    Route::post('/login', [App\Http\Controllers\Auth\StudentLoginController::class, 'login'])->name('student.login.submit');
    
    // Student logout (requires student authentication)
    Route::post('/logout', [App\Http\Controllers\Auth\StudentLoginController::class, 'logout'])->name('student.logout')->middleware('auth:student');

    // Protected routes (require student authentication)
    Route::middleware(['auth.student'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\StudentController::class, 'dashboard'])->name('student.dashboard');
        Route::get('/complaint/damage', [App\Http\Controllers\StudentController::class, 'showDamageForm'])->name('student.damageform');
        Route::get('/get-damagetypedetails/{damagetypeId}', [App\Http\Controllers\StudentController::class, 'getDamageTypeDetails']);
        Route::post('/complaint/damage', [App\Http\Controllers\StudentController::class, 'submitDamageForm'])->name('student.damageform.submit');
        Route::get('/complaint/general', [App\Http\Controllers\StudentController::class, 'showGeneralForm'])->name('student.generalform');
        Route::post('/complaint/general', [App\Http\Controllers\StudentController::class, 'submitGeneralForm'])->name('student.generalform.submit');
        Route::get('/report/damage', [App\Http\Controllers\StudentController::class, 'damageComplaintList'])->name('student.damagereport');
        Route::post('/report/damage/detail', [App\Http\Controllers\StudentController::class, 'damageComplaintDetails'])->name('student.damagereport.detail');
        Route::get('/report/general', [App\Http\Controllers\StudentController::class, 'generalComplaintList'])->name('student.generalreport');
    });
});

// staff route
Route::prefix('staff')->group(function () {
    // Staff login routes (no authentication required)
    Route::get('/login', [App\Http\Controllers\Auth\StaffLoginController::class, 'showloginform'])->name('staff.login');
    Route::post('/login', [App\Http\Controllers\Auth\StaffLoginController::class, 'login'])->name('staff.login.submit');
    
    // Staff logout (requires staff authentication)
    Route::post('/logout', [App\Http\Controllers\Auth\StaffLoginController::class, 'logout'])->name('staff.logout')->middleware('auth:staff');

    // Protected routes (require staff authentication)
    Route::middleware(['auth.staff'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\StaffController::class, 'dashboard'])->name('staff.dashboard');
        Route::get('/complaint/damage', [App\Http\Controllers\StaffController::class, 'showDamageForm'])->name('staff.damageform');
        Route::get('/get-damagetypedetails/{damagetypeId}', [App\Http\Controllers\StaffController::class, 'getDamageTypeDetails']);
        Route::post('/complaint/damage', [App\Http\Controllers\StaffController::class, 'submitDamageForm'])->name('staff.damageform.submit');
        Route::get('/complaint/general', [App\Http\Controllers\StaffController::class, 'showGeneralForm'])->name('staff.generalform');
        Route::post('/complaint/general', [App\Http\Controllers\StaffController::class, 'submitGeneralForm'])->name('staff.generalform.submit');
        Route::get('/report/damage', [App\Http\Controllers\StaffController::class, 'damageComplaintList'])->name('staff.damagereport');
        Route::post('/report/damage/detail', [App\Http\Controllers\StaffController::class, 'damageComplaintDetails'])->name('staff.damagereport.detail');
        Route::get('/report/general', [App\Http\Controllers\StaffController::class, 'generalComplaintList'])->name('staff.generalreport');
    });
});

// admin route
Route::prefix('admin')->middleware(['auth', 'user-access:admin'])->group(function() {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/user', [AdminController::class, 'user'])->name('admin.user');
    Route::post('/user/add', [AdminController::class, 'addUser'])->name('admin.user.register');
    Route::post('/user/update/{id}', [AdminController::class, 'updateUser'])->name('admin.user.update');
    Route::match(['get', 'post'], '/complaint/damage', [AdminController::class, 'damageComplaintLists'])->name('admin.damagecomplaint');    
    Route::post('/complaint/damage/detail', [AdminController::class, 'damageComplaintDetails'])->name('admin.damagecomplaint.detail');
    Route::match(['get', 'post'], '/complaint/general', [AdminController::class, 'generalComplaintLists'])->name('admin.generalcomplaint');
    Route::post('/complaint/general/detail', [AdminController::class, 'generalComplaintDetails'])->name('admin.generalcomplaint.detail');
    Route::put('/complaint/general/{id}/update', [AdminController::class, 'complaintUpdate'])->name('admin.generalcomplaint.update');
    Route::put('/complaint/general/{id}/cancel', [AdminController::class, 'complaintCancel'])->name('admin.generalcomplaint.cancel');
    Route::match(['get', 'post'], '/report/damage', [AdminController::class, 'damageReport'])->name('admin.damageReport');  
    Route::match(['get', 'post'], '/report/general', [AdminController::class, 'generalReport'])->name('admin.generalReport');  

    // Logout route
    Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');
});

// technician route
Route::prefix('technician')->middleware(['auth', 'user-access:technician'])->group(function() { 
    Route::get('/dashboard', [TechnicianController::class, 'dashboard'])->name('technician.dashboard');  
    Route::match(['get', 'post'], '/complaint/damage', [TechnicianController::class, 'complaintLists'])->name('technician.damagecomplaint');
    Route::post('/complaint/damage/detail', [TechnicianController::class, 'complaintListDetails'])->name('technician.damagecomplaint.detail');
    Route::put('/complaint/damage/{id}/update', [TechnicianController::class, 'complaintUpdate'])->name('technician.damagecomplaint.update');
    Route::put('/complaint/damage/{id}/cancel', [TechnicianController::class, 'complaintCancel'])
    ->name('technician.damagecomplaint.cancel');
    Route::match(['get', 'post'], '/report/damage', [TechnicianController::class, 'damageReport'])->name('technician.damageReportList');  
    Route::match(['get', 'post'], '/statistic/damage', [TechnicianController::class, 'damageStatistic'])->name('technician.damageReport');  

    // Logout route
    Route::post('/logout', [LoginController::class, 'logout'])->name('technician.logout');
});

// pic route
Route::prefix('pic')->middleware(['auth', 'user-access:pic'])->group(function() { 
    Route::get('/dashboard', [PICController::class, 'dashboard'])->name('pic.dashboard');
    Route::get('/complaint/general', [PICController::class, 'generalComplaintLists'])->name('pic.generalcomplaint');
    Route::post('/complaint/general/detail', [PICController::class, 'generalComplaintDetails'])->name('pic.generalcomplaint.detail');
    Route::put('/complaint/general/{id}/update', [PICController::class, 'complaintUpdate'])->name('pic.generalcomplaint.update');
    Route::put('/complaint/general/{id}/cancel', [PICController::class, 'complaintCancel'])->name('pic.generalcomplaint.cancel');  

    // Logout route
    Route::post('/logout', [LoginController::class, 'logout'])->name('pic.logout');
});



