<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FormEntry;
use App\Models\Form;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Solo usuarios autenticados pueden acceder
        $this->middleware('auth');
    }

    /**
     * Muestra el dashboard
     */
    public function index()
    {
        $user = auth()->user();

        // Opcional: verificar roles dentro del controlador
        if (!$user->hasAnyRole(['Manager', 'Supervisor', 'Area Manager'])) {
            abort(403, 'No tienes permiso para acceder al dashboard');
        }

        
        return view('dashboard');
    }
}
