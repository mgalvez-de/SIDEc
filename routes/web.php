<?php

use App\Http\Controllers\ArbaciaFertilizationController;
use App\Http\Controllers\ArbaciaLarvalStageController;
use App\Http\Controllers\BioassayController;
use App\Http\Controllers\DaphniaMagnaTemplateController;
use App\Http\Controllers\DaphniaMagnaChronicController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IsochrysisGalbanaController;
use App\Http\Controllers\PasswordController;
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

// Sin registro público ni "¿Olvidaste tu contraseña?": las cuentas y contraseñas las maneja
// el Supervisor o el Jefe de Área en la pantalla Usuarios
Auth::routes(['register' => false, 'reset' => false, 'confirm' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rutas protegidas por autenticación, cuenta activa y roles
Route::middleware(['auth', 'active'])->group(function () {

    // Cambiar mi contraseña -> cualquier persona con cuenta
    Route::get('/mi-cuenta/contrasena', [PasswordController::class, 'edit'])->name('account.password');
    Route::put('/mi-cuenta/contrasena', [PasswordController::class, 'update'])->name('account.password.update');

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

        // Bioassays: un solo listado para los 8 organismos, con filtros (?tipo=daphnia-magna&estado=en-proceso)
        Route::get('/bioensayos', [BioassayController::class, 'index'])->name('bioassays.index');
        // Reabrir un bioensayo validado (la policy solo deja al Supervisor y al Jefe de Área)
        Route::patch('/bioensayos/{tipo}/{id}/reabrir', [BioassayController::class, 'reopen'])->name('bioassays.reopen');

        // Los listados antiguos de cada organismo siguen existiendo, pero llevan al listado único ya filtrado
        foreach (array_keys(BioassayController::MODELS) as $section) {
            Route::get($section, [BioassayController::class, 'byType'])
                ->name("$section.index")
                ->defaults('tipo', $section);
        }

        Route::resource('daphnia-magna', DaphniaMagnaTemplateController::class)->except(['index', 'create', 'store']);
        Route::patch('daphnia-magna/{daphnia_magna}/validate', [DaphniaMagnaTemplateController::class, 'validateBioassay'])
            ->name('daphnia-magna.validate');
        Route::resource('daphnia-magna-chronic', DaphniaMagnaChronicController::class)
            ->parameters(['daphnia-magna-chronic' => 'daphnia_magna_chronic'])->except(['index', 'create', 'store']);
        Route::patch('daphnia-magna-chronic/{daphnia_magna_chronic}/validate', [DaphniaMagnaChronicController::class, 'validateBioassay'])
            ->name('daphnia-magna-chronic.validate');

        Route::resource('isochrysis-galbana', IsochrysisGalbanaController::class)->except(['index', 'create', 'store']);
        Route::patch('isochrysis-galbana/{isochrysis_galbana}/validate', [IsochrysisGalbanaController::class, 'validateBioassay'])
            ->name('isochrysis-galbana.validate');

        Route::resource('selenastrum-capricornutum', SelenastrumCapricornutumController::class)
            ->parameters(['selenastrum-capricornutum' => 'selenastrum_capricornutum'])->except(['index', 'create', 'store']);
        Route::patch('selenastrum-capricornutum/{selenastrum_capricornutum}/validate', [SelenastrumCapricornutumController::class, 'validateBioassay'])
            ->name('selenastrum-capricornutum.validate');

        Route::resource('tisbe-longicornis-water', TisbeLongicornisWaterController::class)->except(['index', 'create', 'store']);
        Route::patch('tisbe-longicornis-water/{tisbe_longicornis_water}/validate', [TisbeLongicornisWaterController::class, 'validateBioassay'])
            ->name('tisbe-longicornis-water.validate');

        Route::resource('tisbe-longicornis-riles', TisbeLongicornisRilesController::class)
            ->parameters(['tisbe-longicornis-riles' => 'tisbe_longicornis_riles'])->except(['index', 'create', 'store']);
        Route::patch('tisbe-longicornis-riles/{tisbe_longicornis_riles}/validate', [TisbeLongicornisRilesController::class, 'validateBioassay'])
            ->name('tisbe-longicornis-riles.validate');

        Route::resource('arbacia-fertilization', ArbaciaFertilizationController::class)
            ->parameters(['arbacia-fertilization' => 'arbacia_fertilization'])->except(['index', 'create', 'store']);
        Route::patch('arbacia-fertilization/{arbacia_fertilization}/validate', [ArbaciaFertilizationController::class, 'validateBioassay'])
            ->name('arbacia-fertilization.validate');

        Route::resource('arbacia-larval-stage', ArbaciaLarvalStageController::class)
            ->parameters(['arbacia-larval-stage' => 'arbacia_larval_stage'])->except(['index', 'create', 'store']);
        Route::patch('arbacia-larval-stage/{arbacia_larval_stage}/validate', [ArbaciaLarvalStageController::class, 'validateBioassay'])
            ->name('arbacia-larval-stage.validate');
    });
});