<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Supervisor;
use App\Http\Controllers\Operator;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('admin')) return redirect()->route('admin.dashboard');
    if ($user->hasRole('supervisor')) return redirect()->route('supervisor.dashboard');
    if ($user->hasRole('operator')) return redirect()->route('operator.dashboard');
    return abort(403);
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('parts', Admin\PartController::class);
    Route::resource('lines', Admin\LineController::class);
    Route::resource('shifts', Admin\ShiftController::class);
    Route::resource('defects', Admin\DefectController::class);
    Route::resource('downtimes', Admin\DowntimeController::class);
    Route::get('audit-logs', [Admin\AuditLogController::class, 'index'])->name('audit.index');
});

// Supervisor Routes
Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [Supervisor\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('planning', Supervisor\PlanningController::class);
    Route::get('/horenzo', [Supervisor\HorenzoController::class, 'index'])->name('horenzo.index');
});

// Operator Routes
Route::middleware(['auth', 'role:operator'])->prefix('operator')->name('operator.')->group(function () {
    Route::get('/dashboard', [Operator\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/record', [Operator\RecordController::class, 'store'])->name('record.store');
    
    // Lot UI Routes
    Route::get('/record/{or_id}/lots', [Operator\LotController::class, 'index'])->name('lot.index');
    Route::post('/record/{or_id}/lots', [Operator\LotController::class, 'store'])->name('lot.store');
    Route::get('/record/{or_id}/lots/{id}', [Operator\LotController::class, 'execute'])->name('lot.execute');
    
    // Lot API Routes
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
