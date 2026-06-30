<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Supervisor;
use App\Http\Controllers\Operator;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('supervisor')) return redirect()->route('admin.dashboard');
    if ($user->hasRole('karyawan')) return redirect()->route('operator.dashboard');
    return abort(403);
})->middleware(['auth', 'verified'])->name('dashboard');

// ==================== SUPERVISOR (admin routes now for supervisor role) ====================
Route::middleware(['auth', 'role:supervisor'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('lot-numbers', Admin\LotNumberController::class);
        Route::resource('activity-codes', Admin\ActivityCodeController::class);
        Route::resource('part-codes', Admin\PartCodeController::class);
        Route::resource('shifts', Admin\ShiftController::class);
        Route::resource('users', Admin\UserController::class);
    });

    Route::resource('parts', Admin\PartController::class);
    Route::resource('lines', Admin\LineController::class);
    Route::resource('defects', Admin\DefectController::class);
    Route::resource('downtimes', Admin\DowntimeController::class);

    Route::get('/operational-records', [Admin\OperationalRecordController::class, 'index'])->name('op-record.index');
    Route::get('/operational-records/{id}', [Admin\OperationalRecordController::class, 'show'])->name('op-record.show');

    Route::get('/horenzo', [Admin\HorenzoController::class, 'index'])->name('horenzo.index');
    Route::post('/horenzo/generate', [Admin\HorenzoController::class, 'generate'])->name('horenzo.generate');
    Route::get('/horenzo/{id}/export', [Admin\HorenzoController::class, 'export'])->name('horenzo.export');

    Route::get('/audit-logs', [Admin\AuditLogController::class, 'index'])->name('audit.index');
});

// ==================== SUPERVISOR (existing supervisor routes kept as-is) ====================
Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [Supervisor\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('planning', Supervisor\PlanningController::class);
    Route::get('/operational-records', [Supervisor\OperationalRecordController::class, 'index'])->name('op-record.index');
    Route::get('/operational-records/{id}', [Supervisor\OperationalRecordController::class, 'show'])->name('op-record.show');
    Route::get('/horenzo', [Supervisor\HorenzoController::class, 'index'])->name('horenzo.index');
    Route::post('/horenzo/generate', [Supervisor\HorenzoController::class, 'generate'])->name('horenzo.generate');
});

// ==================== KARYAWAN (operator routes now for karyawan role) ====================
Route::middleware(['auth', 'role:karyawan'])->prefix('operator')->name('operator.')->group(function () {
    Route::get('/dashboard', [Operator\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/operational-records', [Operator\OperationalRecordController::class, 'index'])->name('op-record.index');
    Route::get('/operational-records/create', [Operator\OperationalRecordController::class, 'create'])->name('op-record.create');
    Route::post('/operational-records', [Operator\OperationalRecordController::class, 'store'])->name('op-record.store');
    Route::get('/operational-records/{id}', [Operator\OperationalRecordController::class, 'show'])->name('op-record.show');
    Route::get('/operational-records/{id}/edit', [Operator\OperationalRecordController::class, 'edit'])->name('op-record.edit');
    Route::put('/operational-records/{id}', [Operator\OperationalRecordController::class, 'update'])->name('op-record.update');
    Route::post('/operational-records/{id}/submit', [Operator\OperationalRecordController::class, 'submit'])->name('op-record.submit');

    Route::post('/operational-records/{id}/bodies', [Operator\BodyController::class, 'store'])->name('body.store');
    Route::put('/operational-records/{id}/bodies/{body}', [Operator\BodyController::class, 'update'])->name('body.update');
    Route::delete('/operational-records/{id}/bodies/{body}', [Operator\BodyController::class, 'destroy'])->name('body.destroy');

    // Horenzo untuk Karyawan
    Route::get('/horenzo', [Operator\HorenzoController::class, 'index'])->name('horenzo.index');
    Route::post('/horenzo/generate', [Operator\HorenzoController::class, 'generate'])->name('horenzo.generate');

    // Report per Record (1 header + body)
    Route::get('/report-records', [Operator\RecordReportController::class, 'index'])->name('report-record.index');
    Route::get('/report-record/{id}', [Operator\RecordReportController::class, 'preview'])->name('report-record.preview');

    // Legacy
    Route::post('/record', [Operator\RecordController::class, 'store'])->name('record.store');
    Route::get('/record/{or_id}/lots', [Operator\LotController::class, 'index'])->name('lot.index');
    Route::post('/record/{or_id}/lots', [Operator\LotController::class, 'store'])->name('lot.store');
    Route::get('/record/{or_id}/lots/{id}', [Operator\LotController::class, 'execute'])->name('lot.execute');
    Route::post('/api/lot/{id}/start', [Operator\LotController::class, 'start'])->name('api.lot.start');
    Route::post('/api/lot/{id}/finish', [Operator\LotController::class, 'finish'])->name('api.lot.finish');
    Route::post('/api/lot/{id}/downtime/start', [Operator\LotDowntimeController::class, 'start'])->name('api.downtime.start');
    Route::post('/api/lot/{id}/downtime/end', [Operator\LotDowntimeController::class, 'end'])->name('api.downtime.end');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
