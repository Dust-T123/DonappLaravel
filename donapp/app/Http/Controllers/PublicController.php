<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Donacion;
use App\Models\Evento;

class PublicController extends Controller
{
    public function index()
    {
        $statsUsuarios   = Usuario::where('rol', 'donante')->count();
        $statsDonaciones = Donacion::count();
        $statsEventos    = Evento::whereIn('estado', ['publicado', 'en_curso'])->count();

        // Antes vivía en la tabla publicacion (ya eliminada): el contenido
        // publicado sobre cada evento ahora es parte del propio evento.
        $publicaciones = Evento::whereIn('estado', ['publicado', 'en_curso'])
            ->latest('fechaPublicacion')
            ->get();

        return view('public.index', compact(
            'statsUsuarios', 'statsDonaciones', 'statsEventos', 'publicaciones'
        ));
    }
    public function terminos()
    {
        return view('public.terminos');
    }
}
