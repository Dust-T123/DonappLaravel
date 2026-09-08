@extends('layouts.app')
@section('title', 'DONAPP — Mi Panel')
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/usuario_style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
@endsection

@section('content')
<div class="sidebar">
    <div class="sidebar-logo">
        <a href="{{ route('home') }}"><img src="{{ asset('assets/uploads/Red-Logo.png') }}" alt="Donapp" onerror="this.style.display='none'"></a>
        <span class="sidebar-role">Donante / Solicitante</span>
        <div class="sidebar-username">{{ $usuario->nombre }}</div>
    </div>
    <ul class="nav-menu">
        <li><a href="?tab=inicio"      class="nav-link {{ $tabActivo==='inicio'      ? 'active' : '' }}"><i class="fa-solid fa-house"></i><span> Inicio</span></a></li>
        <li><a href="?tab=donaciones"  class="nav-link {{ $tabActivo==='donaciones'  ? 'active' : '' }}"><i class="fa-solid fa-box-open"></i><span> Mis Donaciones</span></a></li>
        <li><a href="?tab=solicitudes" class="nav-link {{ $tabActivo==='solicitudes' ? 'active' : '' }}"><i class="fa-solid fa-clipboard-list"></i><span> Mis Solicitudes</span></a></li>
        <li><a href="?tab=visitas"     class="nav-link {{ $tabActivo==='visitas'     ? 'active' : '' }}"><i class="fa-solid fa-house-chimney-user"></i><span> Visitas Domiciliarias</span></a></li>
        <li><a href="?tab=eventos"     class="nav-link {{ $tabActivo==='eventos'     ? 'active' : '' }}"><i class="fa-solid fa-calendar-days"></i><span> Eventos</span></a></li>
        <li><a href="?tab=perfil"      class="nav-link {{ $tabActivo==='perfil'      ? 'active' : '' }}"><i class="fa-solid fa-user-gear"></i><span> Mi Perfil</span></a></li>
        <li><hr></li>
        <li>
            <form action="{{ route('logout') }}" method="POST" style="margin:0">
                @csrf
                <button type="submit" class="nav-link logout">
                    <i class="fa-solid fa-power-off"></i><span> Cerrar Sesión</span>
                </button>
            </form>
        </li>
    </ul>
</div>

<main class="main-content">

    {{-- ══ INICIO ══ --}}
    <div id="inicio" class="tab-pane {{ $tabActivo==='inicio' ? 'active' : '' }}">
        <div class="welcome-hero">
            <h1>Hola, {{ explode(' ', $usuario->nombre)[0] }} 👋</h1>
            <p>Bienvenido a tu panel personal de DONAPP — aquí puedes gestionar tus donaciones y solicitudes.</p>
            <div class="hero-actions">
                <button class="btn-hero btn-hero-primary" onclick="abrirModal('modalNuevaDonacion')">
                    <i class="fa-solid fa-box-open"></i> Nueva Donación
                </button>
                <button class="btn-hero btn-hero-outline" onclick="abrirModal('modalNuevaSolicitud')">
                    <i class="fa-solid fa-clipboard-list"></i> Nueva Solicitud
                </button>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fa-solid fa-box-open"></i></div>
                <div><h3>{{ $stats['total_don'] }}</h3><p>Total donaciones</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
                <div><h3>{{ $stats['don_pendientes'] }}</h3><p>Donaciones pendientes</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
                <div><h3>{{ $stats['don_aprobadas'] }}</h3><p>Donaciones aprobadas</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa-solid fa-clipboard-list"></i></div>
                <div><h3>{{ $stats['total_sol'] }}</h3><p>Total solicitudes</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                <div><h3>{{ $stats['sol_pendientes'] }}</h3><p>Solicitudes pendientes</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa-solid fa-handshake-angle"></i></div>
                <div><h3>{{ $stats['sol_aprobadas'] }}</h3><p>Solicitudes aprobadas</p></div>
            </div>
        </div>

        @if($eventos->isNotEmpty())
        <div class="card">
            <div class="card-title"><i class="fa-solid fa-calendar-star"></i> Próximos Eventos Activos</div>
            <div class="eventos-grid">
                @foreach($eventos->take(3) as $ev)
                <div class="event-card">
                    @if($ev->imagen)
                        <img src="{{ $ev->imagenBase64() }}" alt="Evento" class="event-card-img">
                    @else
                        <div class="event-card-noimg"><i class="fa-solid fa-calendar-days"></i></div>
                    @endif
                    <div class="event-card-body">
                        <h3>{{ $ev->Nombre }}</h3>
                        @if($ev->contenido)
                        <p>{{ mb_substr($ev->contenido, 0, 100) }}...</p>
                        @endif
                        <div class="event-meta">
                            @if($ev->fechaInicio)
                            <span><i class="fa-solid fa-calendar"></i> {{ \Carbon\Carbon::parse($ev->fechaInicio)->format('d/m/Y') }}@if($ev->esMultidia()) – {{ \Carbon\Carbon::parse($ev->fechaFin)->format('d/m/Y') }}@endif</span>
                            @endif
                            @if($ev->lugar)
                            <span><i class="fa-solid fa-location-dot"></i> {{ mb_substr($ev->lugar, 0, 40) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($eventos->count() > 3)
            <div class="eventos-more">
                <a href="?tab=eventos" class="btn btn-secondary btn-sm">Ver todos los eventos <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            @endif
        </div>
        @endif
    </div>

    {{-- ══ DONACIONES ══ --}}
    <div id="donaciones" class="tab-pane {{ $tabActivo==='donaciones' ? 'active' : '' }}">
        <div class="section-header">
            <div>
                <h2 class="page-title">Mis Donaciones</h2>
                <p class="page-subtitle">Consulta y gestiona las donaciones que has registrado.</p>
            </div>
            <button class="btn btn-primary" onclick="abrirModal('modalNuevaDonacion')">
                <i class="fa-solid fa-plus"></i> Nueva Donación
            </button>
        </div>
        <div class="card">
            <form method="GET" action="{{ route('usuario.dashboard') }}" class="filter-bar" id="formFiltrosDon">
                <input type="hidden" name="tab" value="donaciones">
                <input type="text" name="don_buscar" class="form-input search-input"
                       placeholder="🔍 Buscar por descripción..." value="{{ request('don_buscar') }}" maxlength="200"
                       onchange="this.form.submit()">
                <select name="don_estado" class="form-input" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('don_estado')=='pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="aprobada"  {{ request('don_estado')=='aprobada'  ? 'selected' : '' }}>Aprobada</option>
                    <option value="rechazada" {{ request('don_estado')=='rechazada' ? 'selected' : '' }}>Rechazada</option>
                    <option value="completada" {{ request('don_estado')=='completada' ? 'selected' : '' }}>Completada</option>
                    <option value="cancelada" {{ request('don_estado')=='cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
                <select name="don_cat" class="form-input" onchange="this.form.submit()">
                    <option value="0">Todas las categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->idCategoria }}" {{ request('don_cat')==$cat->idCategoria ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                    @endforeach
                </select>
                <select name="don_sort" class="form-input" onchange="this.form.submit()">
                    <option value="">Más recientes</option>
                    <option value="az" {{ request('don_sort')=='az' ? 'selected' : '' }}>Descripción A-Z</option>
                    <option value="za" {{ request('don_sort')=='za' ? 'selected' : '' }}>Descripción Z-A</option>
                </select>
                <a href="{{ route('usuario.dashboard', ['tab'=>'donaciones']) }}" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-xmark"></i> Limpiar filtros
                </a>
            </form>

            @if($misDonaciones->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fa-solid fa-box-open"></i></div>
                <h3>Sin donaciones registradas</h3>
                <p>Aún no has realizado ninguna donación{{ request('don_estado') || request('don_cat') ? ' con estos filtros' : '' }}.</p>
                @if(!request('don_estado') && !request('don_cat'))
                <button class="btn btn-primary" onclick="abrirModal('modalNuevaDonacion')">
                    <i class="fa-solid fa-plus"></i> Hacer mi primera donación
                </button>
                @endif
            </div>
            @else
            <div class="table-wrap">
                <table>
                    <thead><tr>
                        <th>#</th><th>Descripción</th><th>Categoría</th><th>Stock</th>
                        <th>Estado</th><th>Fecha</th><th>Observación</th><th>Acciones</th>
                    </tr></thead>
                    <tbody>
                        @foreach($misDonaciones as $d)
                        @php $dJson = json_encode(['idDonacion'=>$d->idDonacion,'descripcion'=>$d->descripcion,'categoria'=>$d->categoria?->nombre??'—','stock'=>$d->stock,'estado'=>$d->estado,'fechaCreacion'=>$d->donantes->first()?->pivot->FechaCreacion??'','observacion'=>$d->observacion??'','imagen'=>$d->imagenBase64()??'','idCategoria'=>$d->idCategoria], JSON_HEX_APOS | JSON_UNESCAPED_UNICODE); @endphp
                        <tr>
                            <td>{{ $d->idDonacion }}</td>
                            <td class="td-desc" title="{{ $d->descripcion }}">{{ $d->descripcion }}</td>
                            <td class="td-cat">{{ $d->categoria?->nombre ?? '—' }}</td>
                            <td>{{ $d->stock }}</td>
                            <td><span class="badge estado-{{ $d->estado }}">{{ $d->estado }}</span></td>
                            <td>{{ $d->donantes->first()?->pivot->FechaCreacion ? \Carbon\Carbon::parse($d->donantes->first()->pivot->FechaCreacion)->format('d/m/Y') : '—' }}</td>
                            <td class="td-obs">{{ $d->observacion ?? '—' }}</td>
                            <td class="td-actions">
                                <button onclick='verDetalleDonacion({{ $dJson }})' class="btn btn-sm btn-secondary" title="Ver detalle">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                @if($d->estado === 'pendiente')
                                <button onclick='abrirModalEditarDonacion({{ $dJson }})' class="btn btn-sm btn-primary" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form action="{{ route('usuario.donaciones.cancelar', $d->idDonacion) }}" method="POST" style="display:inline"
                                      onsubmit="return confirm('¿Cancelar esta donación? Esta acción no se puede deshacer.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Cancelar">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- ══ SOLICITUDES ══ --}}
    <div id="solicitudes" class="tab-pane {{ $tabActivo==='solicitudes' ? 'active' : '' }}">
        <div class="section-header">
            <div>
                <h2 class="page-title">Mis Solicitudes</h2>
                <p class="page-subtitle">Revisa el estado de tus solicitudes de ayuda.</p>
            </div>
            <button class="btn btn-primary" onclick="abrirModal('modalNuevaSolicitud')">
                <i class="fa-solid fa-plus"></i> Nueva Solicitud
            </button>
        </div>
        <div class="card">
            <form method="GET" action="{{ route('usuario.dashboard') }}" class="filter-bar" id="formFiltrosSol">
                <input type="hidden" name="tab" value="solicitudes">
                <input type="text" name="sol_buscar" class="form-input search-input"
                       placeholder="🔍 Buscar por descripción..." value="{{ request('sol_buscar') }}" maxlength="200"
                       onchange="this.form.submit()">
                <select name="sol_estado" class="form-input" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('sol_estado')=='pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="aprobada"  {{ request('sol_estado')=='aprobada'  ? 'selected' : '' }}>Aprobada</option>
                    <option value="rechazada" {{ request('sol_estado')=='rechazada' ? 'selected' : '' }}>Rechazada</option>
                    <option value="completada" {{ request('sol_estado')=='completada' ? 'selected' : '' }}>Completada</option>
                    <option value="cancelada" {{ request('sol_estado')=='cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
                <select name="sol_cat" class="form-input" onchange="this.form.submit()">
                    <option value="0">Todas las categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->idCategoria }}" {{ request('sol_cat')==$cat->idCategoria ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                    @endforeach
                </select>
                <select name="sol_sort" class="form-input" onchange="this.form.submit()">
                    <option value="">Más recientes</option>
                    <option value="az" {{ request('sol_sort')=='az' ? 'selected' : '' }}>Descripción A-Z</option>
                    <option value="za" {{ request('sol_sort')=='za' ? 'selected' : '' }}>Descripción Z-A</option>
                </select>
                <a href="{{ route('usuario.dashboard', ['tab'=>'solicitudes']) }}" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-xmark"></i> Limpiar filtros
                </a>
            </form>

            @if($misSolicitudes->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fa-solid fa-clipboard-list"></i></div>
                <h3>Sin solicitudes registradas</h3>
                <p>No tienes solicitudes{{ request('sol_estado') || request('sol_cat') ? ' con estos filtros' : ' registradas aún' }}.</p>
                @if(!request('sol_estado') && !request('sol_cat'))
                <button class="btn btn-primary" onclick="abrirModal('modalNuevaSolicitud')">
                    <i class="fa-solid fa-plus"></i> Hacer mi primera solicitud
                </button>
                @endif
            </div>
            @else
            <div class="table-wrap">
                <table>
                    <thead><tr>
                        <th>#</th><th>Descripción</th><th>Categoría</th>
                        <th>Estado</th><th>Fecha</th><th>Observación</th><th>Acciones</th>
                    </tr></thead>
                    <tbody>
                        @foreach($misSolicitudes as $s)
                        @php $sJson = json_encode(['idSolicitud'=>$s->idSolicitud,'descripcion'=>$s->descripcion,'categoria'=>$s->categoria?->nombre??'—','estado'=>$s->estado,'fechaCreacion'=>$s->fechaCreacion??'','observacion'=>$s->observacion??'','idCategoria'=>$s->idCategoria], JSON_HEX_APOS | JSON_UNESCAPED_UNICODE); @endphp
                        <tr>
                            <td>{{ $s->idSolicitud }}</td>
                            <td class="td-desc">{{ $s->descripcion }}</td>
                            <td class="td-cat">{{ $s->categoria?->nombre ?? '—' }}</td>
                            <td><span class="badge estado-{{ $s->estado }}">{{ $s->estado }}</span></td>
                            <td>—</td>
                            <td class="td-obs">{{ $s->observacion ?? '—' }}</td>
                            <td class="td-actions">
                                <button onclick='verDetalleSolicitud({{ $sJson }})' class="btn btn-sm btn-secondary" title="Ver detalle">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                @if($s->estado === 'pendiente')
                                <button onclick='abrirModalEditarSolicitud({{ $sJson }})' class="btn btn-sm btn-primary" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form action="{{ route('usuario.solicitudes.cancelar', $s->idSolicitud) }}" method="POST" style="display:inline"
                                      onsubmit="return confirm('¿Cancelar esta solicitud? Esta acción no se puede deshacer.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Cancelar">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- ══ VISITAS DOMICILIARIAS ══ --}}
    <div id="visitas" class="tab-pane {{ $tabActivo==='visitas' ? 'active' : '' }}">
        <div class="section-header">
            <div>
                <h2 class="page-title">Visitas Domiciliarias</h2>
                <p class="page-subtitle">Solicita que un asistente o administrador visite tu domicilio para validar tu necesidad.</p>
            </div>
            <button class="btn btn-primary" onclick="abrirModal('modalNuevaVisita')">
                <i class="fa-solid fa-plus"></i> Solicitar Visita
            </button>
        </div>
        <div class="card">
            @if($misVisitas->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fa-solid fa-house-chimney-user"></i></div>
                <h3>Sin visitas solicitadas</h3>
                <p>Aún no has solicitado ninguna visita domiciliaria.</p>
                <button class="btn btn-primary" onclick="abrirModal('modalNuevaVisita')">
                    <i class="fa-solid fa-plus"></i> Solicitar mi primera visita
                </button>
            </div>
            @else
            <div class="table-wrap">
                <table>
                    <thead><tr>
                        <th>#</th><th>Dirección</th><th>Motivo</th><th>Fecha preferida</th>
                        <th>Estado</th><th>Gestor</th><th>Observación</th><th>Acciones</th>
                    </tr></thead>
                    <tbody>
                        @foreach($misVisitas as $v)
                        <tr>
                            <td>{{ $v->idVisita }}</td>
                            <td class="td-desc">{{ $v->direccion }}</td>
                            <td class="td-desc">{{ $v->motivo }}</td>
                            <td>{{ $v->fechaPreferida ? \Carbon\Carbon::parse($v->fechaPreferida)->format('d/m/Y') : '—' }}</td>
                            <td><span class="badge estado-{{ $v->estado }}">{{ $v->estado }}</span></td>
                            <td>{{ $v->gestor?->nombre ?? '—' }}</td>
                            <td class="td-obs">{{ $v->observacion ?? '—' }}</td>
                            <td class="td-actions">
                                @if($v->estado === 'pendiente')
                                <form action="{{ route('usuario.visitas.cancelar', $v->idVisita) }}" method="POST" style="display:inline"
                                      onsubmit="return confirm('¿Cancelar esta solicitud de visita?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Cancelar">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                                @else
                                —
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- ══ EVENTOS ══ --}}
    <div id="eventos" class="tab-pane {{ $tabActivo==='eventos' ? 'active' : '' }}">
        <div>
            <h2 class="page-title">Eventos de la Fundación</h2>
            <p class="page-subtitle">Conoce las jornadas de entrega y actividades publicadas.</p>
        </div>
        @if($eventos->isEmpty())
        <div class="card">
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fa-solid fa-calendar-xmark"></i></div>
                <h3>No hay eventos activos</h3>
                <p>Por el momento no hay eventos publicados. ¡Vuelve pronto!</p>
            </div>
        </div>
        @else
        <div class="eventos-grid">
            @foreach($eventos as $ev)
                @php
                    $evJson = json_encode([
                        'nombre'        => $ev->Nombre,
                        'contenido'     => $ev->contenido,
                        'imagen'        => $ev->imagenBase64(),
                        'fechaInicio'   => $ev->fechaInicio,
                        'fechaFin'      => $ev->fechaFin,
                        'lugar'         => $ev->lugar,
                        'fechaPublicacion' => $ev->fechaPublicacion,
                    ], JSON_HEX_APOS | JSON_UNESCAPED_UNICODE);
                @endphp
            <div class="event-card" onclick='abrirDetalleEvento({{ $evJson }})' style="cursor:pointer" title="Ver evento completo">
                @if($ev->imagen)
                    <img src="{{ $ev->imagenBase64() }}" alt="Evento" class="event-card-img">
                @else
                    <div class="event-card-noimg"><i class="fa-solid fa-calendar-days"></i></div>
                @endif
                <div class="event-card-body">
                    <h3>{{ $ev->Nombre }}</h3>
                    @if($ev->contenido)
                    <p>{{ \Illuminate\Support\Str::limit($ev->contenido, 110) }}</p>
                    @endif
                    <div class="event-meta">
                        @if($ev->fechaInicio)
                        <span><i class="fa-solid fa-calendar-check"></i> {{ \Carbon\Carbon::parse($ev->fechaInicio)->format('d \d\e F \d\e Y') }}@if($ev->esMultidia()) – {{ \Carbon\Carbon::parse($ev->fechaFin)->format('d \d\e F \d\e Y') }}@endif</span>
                        @endif
                        @if($ev->lugar)
                        <span><i class="fa-solid fa-location-dot"></i> {{ $ev->lugar }}</span>
                        @endif
                    </div>
                    <p class="event-ver-mas"><i class="fa-solid fa-eye"></i> Ver evento completo</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- MODAL: Detalle completo del evento --}}
    <div id="modalDetalleEvento" class="modal"><div class="modal-content">
        <div class="modal-header">
            <h3 id="det_ev_titulo"><i class="fa-solid fa-calendar-days"></i></h3>
            <button class="modal-close" onclick="cerrarModal('modalDetalleEvento')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="det_ev_body" class="detalle-box"></div>
    </div></div>

    {{-- ══ PERFIL ══ --}}
    <div id="perfil" class="tab-pane {{ $tabActivo==='perfil' ? 'active' : '' }}">
        <h2 class="page-title">Mi Perfil</h2>
        <p class="page-subtitle">Actualiza tu información personal y contraseña.</p>

        @if(session('correccion_ok'))
        <div class="alert-box alert-success">
            <i class="fa-solid fa-circle-check"></i> Tu solicitud de corrección fue enviada y quedará pendiente de aprobación por un administrador.
        </div>
        @endif

        <div class="card card-perfil">
            <div class="perfil-header">
                <div class="perfil-avatar">{{ mb_strtoupper(mb_substr($usuario->nombre, 0, 1)) }}</div>
                <div class="perfil-header-info">
                    <h2>{{ $usuario->nombre }} {{ $usuario->apellido }}</h2>
                    <p><i class="fa-solid fa-envelope"></i> {{ $usuario->email }}</p>
                    <p class="perfil-estado"><span class="badge estado-{{ $usuario->estado }}">{{ $usuario->estado }}</span></p>
                </div>
            </div>

            <div class="perfil-lock-notice">
                <i class="fa-solid fa-lock"></i>
                <div>
                    <strong>Nombre, tipo/número de documento y fecha de nacimiento no se pueden editar directamente.</strong>
                    <p class="text-muted" style="margin:4px 0 0;">
                        Estos campos son datos de identidad. Si detectas un error o necesitas actualizarlos,
                        usa el botón <b>"Solicitar corrección de datos"</b>; un administrador revisará y aprobará el cambio.
                    </p>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="abrirModal('modalSolicitarCorreccionPerfil')">
                    <i class="fa-solid fa-pen-to-square"></i> Solicitar corrección de datos
                </button>
            </div>

            <form action="{{ route('usuario.perfil.update') }}" method="POST" id="formPerfil">
                @csrf @method('PUT')
                <div class="form-grid-2">
                    <div class="form-group"><label>Nombre completo <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                        <input type="text" class="form-input" value="{{ $usuario->nombre }}" disabled readonly></div>
                    <div class="form-group"><label>Apellido <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                        <input type="text" class="form-input" value="{{ $usuario->apellido }}" disabled readonly></div>
                    <div class="form-group"><label>Tipo de documento <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                        <input type="text" class="form-input" value="{{ $usuario->tipoDocumento }}" disabled readonly></div>
                    <div class="form-group"><label>Número de documento <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                        <input type="text" class="form-input" value="{{ $usuario->numDocumento }}" disabled readonly></div>
                    <div class="form-group"><label>Fecha de nacimiento <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                        <input type="date" class="form-input" value="{{ $usuario->fechaNacimiento }}" disabled readonly></div>
                    <div class="form-group"><label>Teléfono *</label>
                        <input type="tel" name="telefono" class="form-input" value="{{ $usuario->telefono }}" required pattern="[0-9]{10}" maxlength="10" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)"></div>
                    <div class="form-group"><label>Dirección *</label>
                        <input type="text" name="direccion" class="form-input" value="{{ $usuario->direccion }}" required minlength="5" maxlength="255"></div>
                    <div class="form-group form-group-full"><label>Email *</label>
                        <input type="email" name="email" class="form-input" value="{{ $usuario->email }}" required maxlength="150"></div>
                    <div class="form-group form-group-full"><label>Necesidad <small class="text-muted">(describe tu situación)</small></label>
                        <textarea name="necesidad" class="form-input" maxlength="255" rows="3" placeholder="Describe brevemente tu necesidad...">{{ $usuario->necesidad }}</textarea></div>
                </div>
                <hr class="section-divider">
                <p class="hint-text"><i class="fa-solid fa-lock"></i> Cambiar contraseña — deja vacío para no cambiar</p>
                <div class="form-group"><label>Contraseña actual <small class="text-muted">(requerida para cambiar)</small></label>
                    <div class="pass-wrap">
                        <input type="password" name="password_actual" id="perfil_pass_actual" class="form-input" maxlength="30" placeholder="Ingresa tu contraseña actual para confirmar el cambio">
                        <button type="button" class="eye-btn" onclick="togglePass('perfil_pass_actual','eye_actual')"><i class="fa-solid fa-eye" id="eye_actual"></i></button>
                    </div>
                    <p class="hint-text">¿No recuerdas tu contraseña? <a href="{{ route('recuperar') }}" class="link-primary">Recupérala aquí</a></p>
                </div>
                <div class="form-grid-2">
                    <div class="form-group"><label>Nueva contraseña</label>
                        <div class="pass-wrap">
                            <input type="password" name="password" id="perfil_pass" class="form-input" minlength="6" maxlength="30" placeholder="Crea una nueva clave de seguridad">
                            <button type="button" class="eye-btn" onclick="togglePass('perfil_pass','perfil_eye')"><i class="fa-solid fa-eye" id="perfil_eye"></i></button>
                        </div></div>
                    <div class="form-group"><label>Confirmar contraseña</label>
                        <div class="pass-wrap">
                            <input type="password" name="password_confirmation" id="perfil_pass2" class="form-input" minlength="6" maxlength="30" placeholder="Repite la nueva clave para confirmar">
                            <button type="button" class="eye-btn" onclick="togglePass('perfil_pass2','perfil_eye2')"><i class="fa-solid fa-eye" id="perfil_eye2"></i></button>
                        </div></div>
                </div>
                <p id="perfil_pass_err" class="field-error" style="display:none;">Las contraseñas no coinciden.</p>
                <button type="submit" class="btn btn-primary" onclick="return validarPassPerfil()">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                </button>
            </form>
        </div>

        @if(isset($misCorrecciones) && $misCorrecciones->isNotEmpty())
        <div class="card" style="border-left:4px solid #b8860b;">
            <h3 style="margin-top:0;"><i class="fa-solid fa-user-shield"></i> Mis solicitudes de corrección de datos</h3>
            <p class="text-muted" style="margin-top:-6px;">Solo un administrador puede aprobarlas o rechazarlas. Aquí solo consultas su estado.</p>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Campo</th><th>Valor propuesto</th><th>Estado</th><th>Fecha</th></tr></thead>
                    <tbody>
                        @foreach($misCorrecciones as $c)
                        <tr>
                            <td>{{ $c->campo }}</td>
                            <td><strong>{{ $c->valorNuevo }}</strong></td>
                            <td><span class="badge estado-{{ $c->estado }}">{{ $c->estado }}</span></td>
                            <td>{{ $c->fechaSolicitud?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

</main>


{{-- MODALES --}}
<div id="modalNuevaDonacion" class="modal"><div class="modal-content">
    <div class="modal-header"><h3><i class="fa-solid fa-box-open"></i> Registrar Donación</h3>
        <button class="modal-close" onclick="cerrarModal('modalNuevaDonacion')"><i class="fa-solid fa-xmark"></i></button></div>
    <form action="{{ route('usuario.donaciones.crear') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group"><label>Descripción del artículo *</label>
            <textarea name="descripcion" class="form-input" required maxlength="200" rows="3" placeholder="Describe el artículo que deseas donar..."></textarea></div>
        <div class="form-grid-2">
            <div class="form-group"><label>Categoría *</label>
                <select name="idCategoria" class="form-input" required>
                    <option value="">Selecciona una categoría</option>
                    @foreach($categorias as $cat)<option value="{{ $cat->idCategoria }}">{{ $cat->nombre }}</option>@endforeach
                </select></div>
            <div class="form-group"><label>Cantidad / Stock *</label>
                <input type="number" name="stock" class="form-input" required min="1" max="9999" value="1"></div>
        </div>
        <div class="form-group"><label>Imagen del artículo <small class="text-muted">(opcional)</small></label>
            <input type="file" name="imagen" class="form-input" accept="image/*" onchange="previewImg(this,'prev_don')">
            <img id="prev_don" src="" alt="" class="img-file-preview" style="display:none;">
            <p class="form-hint"><i class="fa-solid fa-circle-info"></i> La imagen ayuda al equipo a validar tu donación.</p></div>
        <div class="modal-footer">
            <button type="submit" name="crear_donacion" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Enviar Donación</button>
            <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalNuevaDonacion')">Cancelar</button>
        </div>
    </form>
</div></div>

<div id="modalEditarDonacion" class="modal"><div class="modal-content">
    <div class="modal-header"><h3><i class="fa-solid fa-pen"></i> Editar Donación</h3>
        <button class="modal-close" onclick="cerrarModal('modalEditarDonacion')"><i class="fa-solid fa-xmark"></i></button></div>
    <form id="formEditarDonacion" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <input type="hidden" name="idDonacion" id="ed_id">
        <div class="form-group"><label>Descripción *</label>
            <textarea name="descripcion" id="ed_desc" class="form-input" required maxlength="200" rows="3"></textarea></div>
        <div class="form-grid-2">
            <div class="form-group"><label>Categoría *</label>
                <select name="idCategoria" id="ed_cat" class="form-input" required>
                    <option value="">Selecciona una categoría</option>
                    @foreach($categorias as $cat)<option value="{{ $cat->idCategoria }}">{{ $cat->nombre }}</option>@endforeach
                </select></div>
            <div class="form-group"><label>Stock *</label>
                <input type="number" name="stock" id="ed_stock" class="form-input" required min="1" max="9999"></div>
        </div>
        <div class="form-group"><label>Nueva imagen <small class="text-muted">(deja vacío para mantener la actual)</small></label>
            <input type="file" name="imagen" class="form-input" accept="image/*" onchange="previewImg(this,'prev_ed_don')">
            <img id="prev_ed_don" src="" alt="" class="img-file-preview" style="display:none;"></div>
        <div class="modal-footer">
            <button type="submit" name="editar_donacion" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</button>
            <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalEditarDonacion')">Cancelar</button>
        </div>
    </form>
</div></div>

<div id="modalDetalleDonacion" class="modal"><div class="modal-content">
    <div class="modal-header"><h3><i class="fa-solid fa-box-open"></i> Detalle de Donación</h3>
        <button class="modal-close" onclick="cerrarModal('modalDetalleDonacion')"><i class="fa-solid fa-xmark"></i></button></div>
    <div id="detalle_don_body" class="modal-body-pad"></div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="cerrarModal('modalDetalleDonacion')">Cerrar</button></div>
</div></div>

<div id="modalNuevaVisita" class="modal"><div class="modal-content">
    <div class="modal-header"><h3><i class="fa-solid fa-house-chimney-user"></i> Solicitar Visita Domiciliaria</h3>
        <button class="modal-close" onclick="cerrarModal('modalNuevaVisita')"><i class="fa-solid fa-xmark"></i></button></div>
    <form action="{{ route('usuario.visitas.crear') }}" method="POST">
        @csrf
        <div class="form-group"><label>Dirección de la visita *</label>
            <input type="text" name="direccion" class="form-input" required maxlength="255"
                   value="{{ $usuario->direccion }}" placeholder="Dirección donde se realizará la visita"></div>
        <div class="form-group"><label>Motivo de la visita *</label>
            <textarea name="motivo" class="form-input" required maxlength="300" rows="3"
                      placeholder="Cuéntanos por qué necesitas la visita..."></textarea></div>
        <div class="form-group"><label>Fecha preferida <small class="text-muted">(opcional)</small></label>
            <input type="date" name="fechaPreferida" class="form-input" min="{{ date('Y-m-d') }}"></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Enviar Solicitud</button>
            <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalNuevaVisita')">Cancelar</button>
        </div>
    </form>
</div></div>

<div id="modalNuevaSolicitud" class="modal"><div class="modal-content">
    <div class="modal-header"><h3><i class="fa-solid fa-clipboard-list"></i> Registrar Solicitud</h3>
        <button class="modal-close" onclick="cerrarModal('modalNuevaSolicitud')"><i class="fa-solid fa-xmark"></i></button></div>
    <form action="{{ route('usuario.solicitudes.crear') }}" method="POST">
        @csrf
        <div class="form-group"><label>Descripción de la solicitud *</label>
            <textarea name="descripcion" class="form-input" required maxlength="300" rows="3" placeholder="Describe qué necesitas..."></textarea></div>
        <div class="form-group"><label>Categoría *</label>
            <select name="idCategoria" class="form-input" required>
                <option value="">Selecciona la categoría de tu necesidad</option>
                @foreach($categorias as $cat)<option value="{{ $cat->idCategoria }}">{{ $cat->nombre }}</option>@endforeach
            </select></div>
        <div class="modal-footer">
            <button type="submit" name="crear_solicitud" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Enviar Solicitud</button>
            <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalNuevaSolicitud')">Cancelar</button>
        </div>
    </form>
</div></div>

<div id="modalEditarSolicitud" class="modal"><div class="modal-content">
    <div class="modal-header"><h3><i class="fa-solid fa-pen"></i> Editar Solicitud</h3>
        <button class="modal-close" onclick="cerrarModal('modalEditarSolicitud')"><i class="fa-solid fa-xmark"></i></button></div>
    <form id="formEditarSolicitud" method="POST">
        @csrf @method('PUT')
        <input type="hidden" name="idSolicitud" id="es_id">
        <div class="form-group"><label>Descripción *</label>
            <textarea name="descripcion" id="es_desc" class="form-input" required maxlength="300" rows="3"></textarea></div>
        <div class="form-group"><label>Categoría *</label>
            <select name="idCategoria" id="es_cat" class="form-input" required>
                <option value="">Selecciona una categoría</option>
                @foreach($categorias as $cat)<option value="{{ $cat->idCategoria }}">{{ $cat->nombre }}</option>@endforeach
            </select></div>
        <div class="modal-footer">
            <button type="submit" name="editar_solicitud" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</button>
            <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalEditarSolicitud')">Cancelar</button>
        </div>
    </form>
</div></div>

<div id="modalDetalleSolicitud" class="modal"><div class="modal-content">
    <div class="modal-header"><h3><i class="fa-solid fa-clipboard-list"></i> Detalle de Solicitud</h3>
        <button class="modal-close" onclick="cerrarModal('modalDetalleSolicitud')"><i class="fa-solid fa-xmark"></i></button></div>
    <div id="detalle_sol_body" class="modal-body-pad"></div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="cerrarModal('modalDetalleSolicitud')">Cerrar</button></div>
</div></div>

{{-- Solicitar corrección de datos sensibles del propio donante --}}
<div id="modalSolicitarCorreccionPerfil" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-shield"></i> Solicitar corrección de datos</h3>
            <button class="modal-close" onclick="cerrarModal('modalSolicitarCorreccionPerfil')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('usuario.perfil.solicitarCorreccion') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <p class="text-muted" style="margin-top:0;">
                Esta solicitud quedará <b>pendiente</b> hasta que un administrador la revise y apruebe. No modifica tus datos de inmediato.
            </p>
            <div class="form-group">
                <label>Campo a corregir *</label>
                <select name="campo" class="form-input" required>
                    <option value="">Selecciona el campo</option>
                    <option value="nombre">Nombre</option>
                    <option value="apellido">Apellido</option>
                    <option value="tipoDocumento">Tipo de documento</option>
                    <option value="numDocumento">Número de documento</option>
                    <option value="fechaNacimiento">Fecha de nacimiento</option>
                </select>
            </div>
            <div class="form-group">
                <label>Valor correcto propuesto *</label>
                <input type="text" name="valorNuevo" class="form-input" required minlength="1" maxlength="150" placeholder="Escribe el valor correcto">
            </div>
            <div class="form-group">
                <label>Justificación *</label>
                <textarea name="justificacion" class="form-input" rows="3" required minlength="10" maxlength="300"
                          placeholder="Explica por qué necesitas este cambio (ej. error de digitación, actualización de documento, etc.)"></textarea>
            </div>
            <div class="form-group">
                <label>Documento de identidad (foto o PDF) *</label>
                <input type="file" name="soporte" class="form-input" accept=".jpg,.jpeg,.png,.pdf" required>
                <small class="text-muted">Máx. 4MB. Solo lo verá un administrador, y se elimina automáticamente al resolverse la solicitud.</small>
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:flex-start;gap:8px;font-weight:normal;">
                    <input type="checkbox" name="consentimiento" required style="margin-top:3px;">
                    <span>Autorizo el tratamiento de este documento únicamente para verificar mi identidad en esta solicitud, conforme a la Política de Tratamiento de Datos Personales de Donapp.</span>
                </label>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Enviar solicitud</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalSolicitarCorreccionPerfil')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

@endsection
@section('scripts')
<script src="{{ asset('assets/js/usuario.js') }}"></script>
<script>
// Conectar formularios de edición con rutas Laravel
const _origEditDon = window.abrirModalEditarDonacion;
window.abrirModalEditarDonacion = function(d) {
    if(_origEditDon) _origEditDon(d);
    document.getElementById('formEditarDonacion').action = `{{ url('/usuario/donaciones') }}/${d.idDonacion}`;
    document.getElementById('ed_id').value = d.idDonacion;
};
const _origEditSol = window.abrirModalEditarSolicitud;
window.abrirModalEditarSolicitud = function(s) {
    if(_origEditSol) _origEditSol(s);
    document.getElementById('formEditarSolicitud').action = `{{ url('/usuario/solicitudes') }}/${s.idSolicitud}`;
    document.getElementById('es_id').value = s.idSolicitud;
};
</script>
@endsection