<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * "Inicio": lleva a la página principal de cada rol (antes mostraba una página vacía de Laravel)
     */
    public function index(Request $request)
    {
        return $request->user()->hasAnyRole(\App\Models\User::ADMIN_ROLES)
            ? redirect()->route('dashboard')
            : redirect()->route('sample_entries.index');
    }
}
