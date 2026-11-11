<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CharacterRankingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\PointCategoryController;
use App\Http\Controllers\PointTransactionController;
use App\Http\Controllers\RemedialPointController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SchoolSettingController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ViolationFollowUpController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Protected Operational Routes (Single Admin Role)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

   
    // 1. Data Master: Siswa

    // Import siswa — harus sebelum resource students
    Route::get('/students/import', [StudentController::class, 'importIndex'])
        ->name('students.import.index');

    Route::get('/students/import/template', [StudentController::class, 'downloadImportTemplate'])
        ->name('students.import.template');

    Route::post('/students/import', [StudentController::class, 'importExcel'])
        ->name('students.import.store');

    // Resource siswa
    Route::resource('students', StudentController::class);

    Route::post('/students/{student}/regenerate-qr', [StudentController::class, 'regenerateQr'])
        ->name('students.regenerate-qr');

    Route::get('/students/{student}/print-card', [StudentController::class, 'printCard'])
        ->name('students.print-card');

    Route::get('/classes/{schoolClass}/print-cards', [StudentController::class, 'printCardsByClass'])
        ->name('students.print-class-cards');


    // 2. Data Master: Kelas & Jurusan
    Route::resource('classes', SchoolClassController::class)->except(['create', 'show', 'edit']);
    Route::resource('majors', MajorController::class)->except(['create', 'show', 'edit']);

    // 3. Data Master: Tahun Ajaran & Semester
    Route::resource('academic-years', AcademicYearController::class)->except(['create', 'show', 'edit']);
    Route::resource('semesters', SemesterController::class)->only(['index', 'store']);
    Route::post('/semesters/{semester}/set-active', [SemesterController::class, 'setActive'])->name('semesters.set-active');
    Route::post('/semesters/{semester}/execute-reset', [SemesterController::class, 'executeReset'])->name('semesters.execute-reset');

    // 4. Sistem Absensi QR Code & Manual
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/scan', [AttendanceController::class, 'scan'])->name('scan');
        Route::post('/process-scan', [AttendanceController::class, 'processScan'])->middleware('throttle:60,1')->name('process-scan');
        Route::get('/monitoring', [AttendanceController::class, 'monitoring'])->name('monitoring');
        Route::get('/history', [AttendanceController::class, 'history'])->name('history');
        Route::post('/manual', [AttendanceController::class, 'storeManual'])->name('manual');
        Route::post('/{attendance}/correct', [AttendanceController::class, 'correct'])->name('correct');
        Route::get('/settings', [AttendanceController::class, 'settings'])->name('settings');
        Route::post('/settings', [AttendanceController::class, 'updateSettings'])->name('settings.update');
    });

    // 5. Sistem Poin Karakter Siswa
    Route::prefix('points')->name('points.')->group(function () {
        Route::get('/create', [PointTransactionController::class, 'create'])->name('create');
        Route::post('/', [PointTransactionController::class, 'store'])->name('store');
        Route::get('/prestasi', [PointTransactionController::class, 'prestasi'])->name('prestasi');
        Route::get('/pelanggaran', [PointTransactionController::class, 'pelanggaran'])->name('pelanggaran');
        Route::get('/history', [PointTransactionController::class, 'history'])->name('history');
        Route::post('/{pointTransaction}/cancel', [PointTransactionController::class, 'cancel'])->name('cancel');
    });

    // 6. Pembinaan, Tindak Lanjut & Remedial
    Route::prefix('follow-ups')->name('follow-ups.')->group(function () {
        Route::get('/', [ViolationFollowUpController::class, 'index'])->name('index');
        Route::put('/{violationFollowUp}', [ViolationFollowUpController::class, 'update'])->name('update');
    });

    Route::prefix('remedials')->name('remedials.')->group(function () {
        Route::get('/', [RemedialPointController::class, 'index'])->name('index');
        Route::post('/', [RemedialPointController::class, 'store'])->name('store');
    });

    // 7. Juara Karakter & Buku Induk
    Route::prefix('juara-karakter')->name('juara-karakter.')->group(function () {
        Route::get('/', [CharacterRankingController::class, 'index'])->name('index');
        Route::get('/{student}/certificate', [CharacterRankingController::class, 'printCertificate'])->name('certificate');
    });
    Route::get('/disciplinary-book', [CharacterRankingController::class, 'disciplinaryBook'])->name('disciplinary-book.index');

    // 8. Kategori Poin
    Route::prefix('point-categories')->name('point-categories.')->group(function () {
        Route::get('/', [PointCategoryController::class, 'index'])->name('index');
        Route::post('/', [PointCategoryController::class, 'store'])->name('store');
        Route::put('/{pointCategory}', [PointCategoryController::class, 'update'])->name('update');
        Route::post('/{pointCategory}/toggle', [PointCategoryController::class, 'toggleActive'])->name('toggle');
    });

    // 9. Laporan & Export
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/attendance/pdf', [ReportController::class, 'attendancePdf'])->name('attendance.pdf');
        Route::get('/attendance/excel', [ReportController::class, 'attendanceExcel'])->name('attendance.excel');
        Route::get('/points', [ReportController::class, 'points'])->name('points');
        Route::get('/points/pdf', [ReportController::class, 'pointsPdf'])->name('points.pdf');
        Route::get('/points/excel', [ReportController::class, 'pointsExcel'])->name('points.excel');
    });

    // 10. Pengaturan Aplikasi
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/school', [SchoolSettingController::class, 'school'])->name('school');
        Route::post('/school', [SchoolSettingController::class, 'updateSchool'])->name('school.update');
        Route::get('/account', [SchoolSettingController::class, 'account'])->name('account');
        Route::post('/account', [SchoolSettingController::class, 'updateAccount'])->name('account.update');
        Route::post('/password', [AuthController::class, 'updatePassword'])->name('password.update');
    });
});

/*
|--------------------------------------------------------------------------
| Storage Asset Stream Route (Universal compatibility)
|--------------------------------------------------------------------------
*/
Route::get('/storage/{path}', function ($path) {
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }
    return response()->file(Storage::disk('public')->path($path));
})->where('path', '.*')->name('storage.local');
