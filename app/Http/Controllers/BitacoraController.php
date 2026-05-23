<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;

class BitacoraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver bitacora');
    }

    public function index()
    {
        $bitacoras = Bitacora::orderByDesc('fecha_hora')->limit(2000)->get();
        return view('bitacora.index', compact('bitacoras'));
    }
}
