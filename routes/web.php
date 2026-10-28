<?php

use App\Http\Controllers\ArbaciaFertilizationController;
use App\Http\Controllers\ArbaciaLarvalStageController;
use App\Http\Controllers\DaphniaMagnaTemplateController;
use App\Http\Controllers\DaphniaMagnaChronicController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IsochrysisGalbanaController;
use App\Http\Controllers\ReceptionTemplateController;
use App\Http\Controllers\RejectionTemplateController;
use App\Http\Controllers\SampleEntryController;
use App\Http\Controllers\SelenastrumCapricornutumController;
use App\Http\Controllers\TisbeLongicornisRilesController;
use App\Http\Controllers\TisbeLongicornisWaterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rutas protegidas por autenticación, cuenta activa y roles
Route::middleware(['auth', 'active'])->group(function () {

    // Receptions, Rejections y Usuarios -> Supervisor y Area Manager
    Route::middleware(['role:Supervisor|Area Manager'])->group(function () {
        Route::resource('receptions', ReceptionTemplateController::class);
        Route::resource('rejections', RejectionTemplateController::class);

        Route::resource('users', UserController::class)->except(['show', 'destroy']);
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');
    });

    // Dashboard -> Manager, Supervisor y Area Manager (el Manager lo ve sin datos de clientes)
    Route::middleware(['role:Manager|Supervisor|Area Manager'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::redirect('/seguimiento', '/dashboard'); // Seguimiento ahora es parte del Dashboard
    });

    // Sample Entry y Bioassays -> Analist, Manager, Supervisor y Area Manager
    Route::middleware(['role:Analist|Manager|Supervisor|Area Manager'])->group(function () {
        Route::resource('sample_entries', SampleEntryController::class);

        // Bioassays
        Route::resource('daphnia-magna', DaphniaMagnaTemplateController::class);
        Route::patch('daphnia-magna/{daphnia_magna}/validate', [DaphniaMagnaTemplateController::class, 'validateBioassay'])
            ->name('daphnia-magna.validate');
        Route::resource('daphnia-magna-chronic', DaphniaMagnaChronicController::class)
            ->parameters(['daphnia-magna-chronic' => 'daphnia_magna_chronic']);
        Route::patch('daphnia-magna-chronic/{daphnia_magna_chronic}/validate', [DaphniaMagnaChronicController::class, 'validateBioassay'])
            ->name('daphnia-magna-chronic.validate');

        Route::resource('isochrysis-galbana', IsochrysisGalbanaController::class);
        Route::patch('isochrysis-galbana/{isochrysis_galbana}/validate', [IsochrysisGalbanaController::class, 'validateBioassay'])
            ->name('isochrysis-galbana.validate');

        Route::resource('selenastrum-capricornutum', SelenastrumCapricornutumController::class)
            ->parameters(['selenastrum-capricornutum' => 'selenastrum_capricornutum']);
        Route::patch('selenastrum-capricornutum/{selenastrum_capricornutum}/validate', [SelenastrumCapricornutumController::class, 'validateBioassay'])
            ->name('selenastrum-capricornutum.validate');

        Route::resource('tisbe-longicornis-water', TisbeLongicornisWaterController::class);
        Route::patch('tisbe-longicornis-water/{tisbe_longicornis_water}/validate', [TisbeLongicornisWaterController::class, 'validateBioassay'])
            ->name('tisbe-longicornis-water.validate');

        Route::resource('tisbe-longicornis-riles', TisbeLongicornisRilesController::class)
            ->parameters(['tisbe-longicornis-riles' => 'tisbe_longicornis_riles']);
        Route::patch('tisbe-longicornis-riles/{tisbe_longicornis_riles}/validate', [TisbeLongicornisRilesController::class, 'validateBioassay'])
            ->name('tisbe-longicornis-riles.validate');

        Route::resource('arbacia-fertilization', ArbaciaFertilizationController::class)
            ->parameters(['arbacia-fertilization' => 'arbacia_fertilization']);
        Route::patch('arbacia-fertilization/{arbacia_fertilization}/validate', [ArbaciaFertilizationController::class, 'validateBioassay'])
            ->name('arbacia-fertilization.validate');

        Route::resource('arbacia-larval-stage', ArbaciaLarvalStageController::class)
            ->parameters(['arbacia-larval-stage' => 'arbacia_larval_stage']);
        Route::patch('arbacia-larval-stage/{arbacia_larval_stage}/validate', [ArbaciaLarvalStageController::class, 'validateBioassay'])
            ->name('arbacia-larval-stage.validate');
    });
});