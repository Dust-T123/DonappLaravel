<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Donacion;
use App\Models\Evento;
use App\Models\Publicacion;

class PublicController extends Controller
{
    public function index()
    {
        $statsUsuarios   = Usuario::where('rol', 'donante')->count();
        $statsDonaciones = Donacion::count();
        $statsEventos    = Evento::where('estado', 'activo')->count();

        $publicaciones = Publicacion::with(['autor', 'evento.programacion'])
            ->latest('fechaPublicacion')
            ->get();

        return view('public.index', compact(
            'statsUsuarios', 'statsDonaciones', 'statsEventos', 'publicaciones'
        ));
    }
}
