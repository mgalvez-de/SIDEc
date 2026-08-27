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

// Rutas protegidas por autenticación y roles
Route::middleware(['auth'])->group(function () {

    // Dashboard, Receptions, Rejections -> Supervisor y Area Manager
    Route::middleware(['role:Supervisor|Area Manager'])->group(function () {
        Route::get('/dashboard', [SampleEntryController::class, 'dashboard'])->name('dashboard');
        Route::resource('receptions', ReceptionTemplateController::class);
        Route::resource('rejections', RejectionTemplateController::class);
    });

    // Sample Entry y Bioassays -> Analist, Manager y Area Manager
    Route::middleware(['role:Analist|Manager|Area Manager'])->group(function () {
        Route::resource('sample_entries', SampleEntryController::class);
        
        // Bioassays
        Route::resource('daphnia-magna', DaphniaMagnaTemplateController::class);
        Route::resource('daphnia-magna-chronic', DaphniaMagnaChronicController::class)
            ->parameters(['daphnia-magna-chronic' => 'daphnia_magna_chronic']);
        Route::resource('isochrysis-galbana', IsochrysisGalbanaController::class);
        Route::resource('selenastrum-capricornutum', SelenastrumCapricornutumController::class)
            ->parameters(['selenastrum-capricornutum' => 'selenastrum_capricornutum']);
        Route::resource('tisbe-longicornis-water', TisbeLongicornisWaterController::class);
        Route::resource('tisbe-longicornis-riles', TisbeLongicornisRilesController::class)
            ->parameters(['tisbe-longicornis-riles' => 'tisbe_longicornis_riles']);
        Route::resource('arbacia-fertilization', ArbaciaFertilizationController::class)
            ->parameters(['arbacia-fertilization' => 'arbacia_fertilization']);
        Route::resource('arbacia-larval-stage', ArbaciaLarvalStageController::class)
            ->parameters(['arbacia-larval-stage' => 'arbacia_larval_stage']);
    });
});