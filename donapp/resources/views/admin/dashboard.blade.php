@extends('layouts.app')
@section('title', 'Donapp — Panel Administrativo')
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin_style.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
@endsection

@section('content')

<div class="admin-wrapper">

    {{-- ═══════ SIDEBAR ═══════ --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <a href="{{ route('home') }}">
                <img src="{{ asset('assets/uploads/Red-Logo.png') }}" alt="Donapp">
            </a>
            <p class="sidebar-title">Panel Administrativo</p>
        </div>
        <ul class="nav-menu">
            <li><a href="#dashboard"  class="nav-link active"><i class="fa-solid fa-house"></i><span> Dashboard</span></a></li>
            <li><a href="#usuarios"   class="nav-link"><i class="fa-solid fa-users"></i><span> Usuarios</span></a></li>
            <li><a href="#categorias" class="nav-link"><i class="fa-solid fa-tags"></i><span> Categorías</span></a></li>
            <li><a href="#donapp"     class="nav-link"><i class="fa-solid fa-hand-holding-heart"></i><span> Donaciones/Sol.</span></a></li>
            <li><a href="#eventos"    class="nav-link"><i class="fa-solid fa-calendar-days"></i><span> Eventos</span></a></li>
            <li><a href="#reportes"   class="nav-link"><i class="fa-solid fa-file-pdf"></i><span> Reportes</span></a></li>
            <li><a href="#perfil"     class="nav-link"><i class="fa-solid fa-user-gear"></i><span> Mi Perfil</span></a></li>
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
    </aside>

    {{-- ═══════ MAIN ═══════ --}}
    <main class="main-content">

        {{-- ── DASHBOARD ── --}}
        <div id="dashboard" class="tab-pane active">
            <h1 class="page-title">Bienvenid@, {{ $adminActual->nombre }} 👋</h1>
            <p class="text-muted">
                <i class="fa-solid fa-shield-halved"></i>
                Módulo de Administrador — Revisa y gestiona usuarios, categorías, donaciones, solicitudes y eventos.
            </p>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                    <div><h3>{{ $totalUsuarios }}</h3><p>Usuarios totales</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fa-solid fa-box-open"></i></div>
                    <div><h3>{{ $totalDonaciones }}</h3><p>Donaciones</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fa-solid fa-clipboard-list"></i></div>
                    <div><h3>{{ $totalSolicitudes }}</h3><p>Solicitudes</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fa-solid fa-calendar-check"></i></div>
                    <div><h3>{{ $totalEventos }}</h3><p>Eventos</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
                    <div><h3>{{ $totalAprobadas }}</h3><p>Donaciones aprobadas</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-tags"></i></div>
                    <div><h3>{{ $totalCategorias }}</h3><p>Categorías</p></div>
                </div>
            </div>

            <h2 class="page-title">Accesos rápidos</h2>
            <div class="stats-grid stats-grid-sm">
                <a href="#usuarios"   class="stat-card" onclick="activarTab('#usuarios')">
                    <div class="stat-icon"><i class="fa-solid fa-user-plus"></i></div>
                    <div><p>Gestionar Usuarios</p></div>
                </a>
                <a href="#categorias" class="stat-card" onclick="activarTab('#categorias')">
                    <div class="stat-icon"><i class="fa-solid fa-tags"></i></div>
                    <div><p>Ver Categorías</p></div>
                </a>
                <a href="#donapp"     class="stat-card" onclick="activarTab('#donapp')">
                    <div class="stat-icon orange"><i class="fa-solid fa-hand-holding-heart"></i></div>
                    <div><p>Ver Donaciones y Solicitudes</p></div>
                </a>
                <a href="#eventos"    class="stat-card" onclick="activarTab('#eventos')">
                    <div class="stat-icon blue"><i class="fa-solid fa-calendar-days"></i></div>
                    <div><p>Gestionar Eventos</p></div>
                </a>
                <a href="#reportes"   class="stat-card" onclick="activarTab('#reportes')">
                    <div class="stat-icon"><i class="fa-solid fa-file-pdf"></i></div>
                    <div><p>Generar Reportes</p></div>
                </a>
            </div>
        </div>

        {{-- ── USUARIOS ── --}}
        <div id="usuarios" class="tab-pane">
            <div class="section-header">
                <h2 class="page-title">Gestión de Usuarios</h2>
                <button class="btn btn-primary" onclick="abrirModal('modalCrearUsuario')">
                    <i class="fa-solid fa-user-plus"></i> Nuevo Usuario
                </button>
            </div>
            <div class="card">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="filter-bar">
                    <input type="hidden" name="tab" value="usuarios">
                    <input type="text" name="search" placeholder="🔍 Buscar por nombre o email..."
                           value="{{ request('search') }}" class="form-input search-input" maxlength="200">
                    <select name="rol" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Todos los roles</option>
                        <option value="donante"       {{ request('rol')=='donante'       ? 'selected' : '' }}>Donante / Solicitante</option>
                        <option value="asistente"     {{ request('rol')=='asistente'     ? 'selected' : '' }}>Asistente</option>
                        <option value="administrador" {{ request('rol')=='administrador' ? 'selected' : '' }}>Administrador</option>
                    </select>
                    <select name="prioridad" class="form-input sel-small" onchange="this.form.submit()"
                            id="filtro_prioridad_select">
                        <option value="">Todas las prioridades</option>
                        <option value="alta"  {{ request('prioridad')=='alta'  ? 'selected' : '' }}>🔴 Alta</option>
                        <option value="media" {{ request('prioridad')=='media' ? 'selected' : '' }}>🟡 Media</option>
                        <option value="baja"  {{ request('prioridad')=='baja'  ? 'selected' : '' }}>🟢 Baja</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                    @if(request('search') || request('rol') || request('prioridad'))
                        <a href="{{ route('admin.dashboard') }}#usuarios" class="btn btn-secondary btn-sm">Limpiar</a>
                    @endif
                </form>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th><th>Nombre</th><th>Documento</th>
                                <th>Email</th><th>Teléfono</th><th>Rol</th>
                                <th>Estado</th><th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($usuarios as $u)
                            <tr>
                                <td>{{ $u->idUsuario }}</td>
                                <td>{{ $u->nombre }}</td>
                                <td><small>{{ $u->tipoDocumento }}: {{ $u->numDocumento }}</small></td>
                                <td>{{ $u->email }}</td>
                                <td>{{ $u->telefono }}</td>
                                <td><span class="badge {{ $u->rol }}">{{ $u->rol }}</span></td>
                                <td><span class="badge estado-{{ $u->estado }}">{{ $u->estado }}</span></td>
                                <td class="td-actions">
                                    <button onclick='abrirModalEditarUsuario({{ json_encode($u) }})'
                                            class="btn btn-sm btn-primary" title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <form action="{{ route('admin.usuarios.estado', $u->idUsuario) }}" method="POST" style="display:inline"
                                          onsubmit="return confirm('{{ $u->estado==='activo' ? '¿Inactivar este usuario?' : '¿Activar este usuario?' }}')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $u->estado==='activo' ? 'btn-warning' : 'btn-success' }}"
                                                title="{{ $u->estado==='activo' ? 'Inactivar' : 'Activar' }}">
                                            <i class="fa-solid {{ $u->estado==='activo' ? 'fa-ban' : 'fa-circle-check' }}"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="empty-row">No se encontraron usuarios.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── CATEGORÍAS ── --}}
        <div id="categorias" class="tab-pane">
            <div class="section-header">
                <h2 class="page-title">Gestión de Categorías</h2>
                <button class="btn btn-primary" onclick="abrirModal('modalCrearCategoria')">
                    <i class="fa-solid fa-plus"></i> Nueva Categoría
                </button>
            </div>
            <div class="card">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="filter-bar" style="margin-bottom:16px">
                    <input type="hidden" name="tab" value="categorias">
                    <input type="text" name="cat_search" placeholder="🔍 Buscar categoría por nombre..."
                           value="{{ request('cat_search') }}" class="form-input search-input" maxlength="200">
                    <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                    @if(request('cat_search'))
                        <a href="{{ route('admin.dashboard') }}#categorias" class="btn btn-secondary btn-sm">Limpiar</a>
                    @endif
                </form>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th><th>Nombre</th><th>Creador</th>
                                <th>Donaciones</th><th>Solicitudes</th><th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categorias as $cat)
                            <tr>
                                <td>{{ $cat->idCategoria }}</td>
                                <td>{{ $cat->nombre }}</td>
                                <td>{{ $cat->creadaPor?->nombre ?? '—' }}</td>
                                <td>{{ $cat->donaciones_count }}</td>
                                <td>{{ $cat->solicitudes_count }}</td>
                                <td class="td-actions">
                                    <button onclick='abrirModalEditarCategoria({{ json_encode(["idCategoria"=>$cat->idCategoria,"nombre"=>$cat->nombre]) }})'
                                            class="btn btn-sm btn-primary" title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    @if($cat->donaciones_count == 0 && $cat->solicitudes_count == 0)
                                    <form action="{{ route('admin.categorias.eliminar', $cat->idCategoria) }}" method="POST" style="display:inline"
                                          onsubmit="return confirm('¿Eliminar esta categoría?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button class="btn btn-sm btn-secondary" disabled title="Tiene registros asociados">
                                        <i class="fa-solid fa-lock"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="empty-row">No se encontraron categorías.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── DONACIONES / SOLICITUDES ── --}}
        <div id="donapp" class="tab-pane">
            <h2 class="page-title">Donaciones y Solicitudes</h2>
            <div class="tabs-inner">
                <button class="tab-btn {{ !request('sol_search') && !request('sol_estado') ? 'active' : '' }}"
                        onclick="switchInner(this,'don-panel')">Donaciones</button>
                <button class="tab-btn {{ request('sol_search') || request('sol_estado') ? 'active' : '' }}"
                        onclick="switchInner(this,'sol-panel')">Solicitudes</button>
            </div>

            {{-- Panel Donaciones --}}
            <div id="don-panel" class="inner-panel" {{ request('sol_search') || request('sol_estado') ? 'style=display:none' : '' }}>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="filter-bar" style="margin-bottom:16px">
                    <input type="hidden" name="tab" value="donapp">
                    <input type="text" name="don_search" placeholder="🔍 Buscar por descripción o donante..."
                           value="{{ request('don_search') }}" class="form-input search-input" maxlength="200">
                    <select name="don_estado" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('don_estado')=='pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="aprobada"  {{ request('don_estado')=='aprobada'  ? 'selected' : '' }}>Aprobada</option>
                        <option value="rechazada" {{ request('don_estado')=='rechazada' ? 'selected' : '' }}>Rechazada</option>
                    </select>
                    <select name="don_cat" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="0">Todas las categorías</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->idCategoria }}" {{ request('don_cat')==$cat->idCategoria ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    @if(request('don_search') || request('don_estado') || request('don_cat'))
                        <a href="{{ route('admin.dashboard') }}#donapp" class="btn btn-secondary btn-sm">Limpiar</a>
                    @endif
                </form>
                <div class="card">
                    <div class="table-wrap">
                        <table>
                            <thead><tr>
                                <th>#</th><th>Descripción</th><th>Categoría</th><th>Stock</th>
                                <th>Estado</th><th>Fecha</th><th>Donante</th><th>Observación</th><th>Acción</th>
                            </tr></thead>
                            <tbody>
                                @forelse($donaciones as $d)
                                <tr>
                                    <td>{{ $d->idDonacion }}</td>
                                    <td>{{ $d->descripcion }}</td>
                                    <td>{{ $d->categoria?->nombre ?? '—' }}</td>
                                    <td>{{ $d->stock }}</td>
                                    <td><span class="badge estado-{{ $d->estado }}">{{ $d->estado }}</span></td>
                                    <td>{{ $d->donantes->first()?->pivot->FechaCreacion ? \Carbon\Carbon::parse($d->donantes->first()->pivot->FechaCreacion)->format('d/m/Y') : '—' }}</td>
                                    <td>{{ $d->donantes->first()?->nombre ?? '—' }}</td>
                                    <td>{{ $d->observacion ?? '—' }}</td>
                                    <td>
                                        <button onclick='abrirModalDonacion({{ json_encode(["idDonacion"=>$d->idDonacion,"descripcion"=>$d->descripcion,"estado"=>$d->estado,"observacion"=>$d->observacion,"donante"=>$d->donantes->first()?->nombre,"categoria"=>$d->categoria?->nombre,"stock"=>$d->stock]) }})'
                                                class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="9" class="empty-row">No se encontraron donaciones.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Panel Solicitudes --}}
            <div id="sol-panel" class="inner-panel" {{ !request('sol_search') && !request('sol_estado') ? 'style=display:none' : '' }}>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="filter-bar" style="margin-bottom:16px">
                    <input type="hidden" name="tab" value="donapp">
                    <input type="text" name="sol_search" placeholder="🔍 Buscar por descripción o solicitante..."
                           value="{{ request('sol_search') }}" class="form-input search-input" maxlength="200">
                    <select name="sol_estado" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('sol_estado')=='pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="aprobada"  {{ request('sol_estado')=='aprobada'  ? 'selected' : '' }}>Aprobada</option>
                        <option value="rechazada" {{ request('sol_estado')=='rechazada' ? 'selected' : '' }}>Rechazada</option>
                    </select>
                    <select name="sol_cat" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="0">Todas las categorías</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->idCategoria }}" {{ request('sol_cat')==$cat->idCategoria ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    @if(request('sol_search') || request('sol_estado') || request('sol_cat'))
                        <a href="{{ route('admin.dashboard') }}#donapp" class="btn btn-secondary btn-sm">Limpiar</a>
                    @endif
                </form>
                <div class="card">
                    <div class="table-wrap">
                        <table>
                            <thead><tr>
                                <th>#</th><th>Descripción</th><th>Categoría</th><th>Estado</th>
                                <th>Fecha</th><th>Solicitante</th><th>Gestor</th><th>Observación</th><th>Acción</th>
                            </tr></thead>
                            <tbody>
                                @forelse($solicitudes as $s)
                                <tr>
                                    <td>{{ $s->idSolicitud }}</td>
                                    <td>{{ $s->descripcion }}</td>
                                    <td>{{ $s->categoria?->nombre ?? '—' }}</td>
                                    <td><span class="badge estado-{{ $s->estado }}">{{ $s->estado }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($s->created_at ?? now())->format('d/m/Y') }}</td>
                                    <td>{{ $s->solicitante?->nombre ?? '—' }}</td>
                                    <td>
                                        @if($s->gestor)
                                            <span class="badge-staff"><i class="fa-solid fa-user-shield"></i> {{ $s->gestor->nombre }}</span>
                                        @else
                                            <span class="text-muted"><i>Esperando revisión...</i></span>
                                        @endif
                                    </td>
                                    <td>{{ $s->observacion ?? '—' }}</td>
                                    <td>
                                        <button onclick='abrirModalSolicitud({{ json_encode(["idSolicitud"=>$s->idSolicitud,"descripcion"=>$s->descripcion,"estado"=>$s->estado,"observacion"=>$s->observacion,"solicitante"=>$s->solicitante?->nombre,"categoria"=>$s->categoria?->nombre]) }})'
                                                class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="9" class="empty-row">No se encontraron solicitudes.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── EVENTOS ── --}}
        <div id="eventos" class="tab-pane">
            <div class="section-header">
                <h2 class="page-title">Gestión de Eventos</h2>
                <button class="btn btn-primary" onclick="abrirModalCrearEvento()">
                    <i class="fa-solid fa-plus"></i> Nuevo Evento
                </button>
            </div>
            <div class="card">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="filter-bar" style="margin-bottom:16px">
                    <input type="hidden" name="tab" value="eventos">
                    <input type="text" name="ev_search" placeholder="🔍 Buscar evento por nombre..."
                           value="{{ request('ev_search') }}" class="form-input search-input" maxlength="200">
                    <select name="ev_estado" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="activo"   {{ request('ev_estado')=='activo'   ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ request('ev_estado')=='inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    @if(request('ev_search') || request('ev_estado'))
                        <a href="{{ route('admin.dashboard') }}#eventos" class="btn btn-secondary btn-sm">Limpiar</a>
                    @endif
                </form>
                <div class="table-wrap">
                    <table>
                        <thead><tr>
                            <th>ID</th><th>Nombre</th><th>Estado</th><th>Acciones</th>
                        </tr></thead>
                        <tbody>
                            @forelse($eventos as $ev)
                            @php
                                $evJson = json_encode([
                                    'idEvento'        => $ev->idEvento,
                                    'Nombre'          => $ev->Nombre,
                                    'estado'          => $ev->estado,
                                    'fecha_entrega'   => $ev->programacion?->FechaEntrega ?? '',
                                    'lugar_entrega'   => $ev->programacion?->Lugar ?? '',
                                    'titulo_pub'      => $ev->publicacion?->titulo ?? '',
                                    'contenido_pub'   => $ev->publicacion?->contenido ?? '',
                                    'idPublicacion'   => $ev->publicacion?->idPublicacion ?? '',
                                    'imagen'          => $ev->publicacion?->imagenBase64() ?? '',
                                ]);
                            @endphp
                            <tr>
                                <td>{{ $ev->idEvento }}</td>
                                <td>{{ $ev->Nombre }}</td>
                                <td><span class="badge estado-{{ $ev->estado }}">{{ $ev->estado }}</span></td>
                                <td class="td-actions">
                                    <button onclick='abrirModalEditarEvento({{ $evJson }})'
                                            class="btn btn-sm btn-primary"><i class="fa-solid fa-pen"></i></button>
                                    <form action="{{ route('admin.eventos.estado', $ev->idEvento) }}" method="POST" style="display:inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-warning">
                                            <i class="fa-solid fa-arrows-rotate"></i> Toggle
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="empty-row">No se encontraron eventos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── REPORTES ── --}}
        <div id="reportes" class="tab-pane">
            <h2 class="page-title">Generador de Reportes PDF</h2>
            <div class="reportes-grid">
                <div class="card reporte-card">
                    <div class="reporte-icon"><i class="fa-solid fa-box-open"></i></div>
                    <h3>Reporte de Donaciones</h3>
                    <p>Genera un PDF con el listado detallado de donaciones según los criterios que elijas.</p>
                    <div class="reporte-filters">
                        <label>Estado:</label>
                        <select id="rpt_don_estado" class="form-input">
                            <option value="todos">Todos</option>
                            <option value="aprobada">Aprobadas</option>
                            <option value="rechazada">Rechazadas</option>
                            <option value="pendiente">Pendientes</option>
                        </select>
                        <label>Fecha desde:</label>
                        <input type="date" id="rpt_don_desde" class="form-input">
                        <label>Fecha hasta:</label>
                        <input type="date" id="rpt_don_hasta" class="form-input">
                    </div>
                    <button class="btn btn-primary" onclick="generarReporteDonaciones()">
                        <i class="fa-solid fa-file-pdf"></i> Generar PDF
                    </button>
                </div>

                <div class="card reporte-card">
                    <div class="reporte-icon blue"><i class="fa-solid fa-clipboard-list"></i></div>
                    <h3>Reporte de Solicitudes</h3>
                    <p>Genera un PDF con el detalle de solicitudes completadas o según el estado que selecciones.</p>
                    <div class="reporte-filters">
                        <label>Estado:</label>
                        <select id="rpt_sol_estado" class="form-input">
                            <option value="todos">Todos</option>
                            <option value="aprobada">Aprobadas</option>
                            <option value="rechazada">Rechazadas</option>
                            <option value="pendiente">Pendientes</option>
                        </select>
                        <label>Fecha desde:</label>
                        <input type="date" id="rpt_sol_desde" class="form-input">
                        <label>Fecha hasta:</label>
                        <input type="date" id="rpt_sol_hasta" class="form-input">
                    </div>
                    <button class="btn btn-primary" onclick="generarReporteSolicitudes()">
                        <i class="fa-solid fa-file-pdf"></i> Generar PDF
                    </button>
                </div>
            </div>

            <script id="donacionesData" type="application/json">
                {!! json_encode($donacionesRpt, JSON_UNESCAPED_UNICODE) !!}
            </script>
            <script id="solicitudesData" type="application/json">
                {!! json_encode($solicitudesRpt, JSON_UNESCAPED_UNICODE) !!}
            </script>
        </div>

        {{-- ── PERFIL ── --}}
        <div id="perfil" class="tab-pane">
            <h2 class="page-title">Mi Perfil</h2>
            <div class="card card-perfil">
                <form action="{{ route('admin.perfil.update') }}" method="POST" id="formPerfil">
                    @csrf @method('PUT')
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Nombre completo</label>
                            <input type="text" name="nombre" class="form-input"
                                   value="{{ $adminActual->nombre }}"
                                   required minlength="3" maxlength="100"
                                   placeholder="Digita tus nombres y apellidos completos">
                        </div>
                        <div class="form-group">
                            <label>Tipo de documento</label>
                            <select name="tipoDocumento" class="form-input" required>
                                @foreach(['CC','TI','CE','PEP'] as $t)
                                    <option value="{{ $t }}" {{ $adminActual->tipoDocumento==$t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Número de documento</label>
                            <input type="text" name="numDocumento" class="form-input"
                                   value="{{ $adminActual->numDocumento }}"
                                   required maxlength="15" placeholder="Ingresa los dígitos de tu documento">
                        </div>
                        <div class="form-group">
                            <label>Fecha de nacimiento</label>
                            <input type="date" name="fechaNacimiento" class="form-input"
                                   value="{{ $adminActual->fechaNacimiento }}" required>
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="tel" name="telefono" class="form-input"
                                   value="{{ $adminActual->telefono }}"
                                   required pattern="[0-9]{10}" maxlength="10"
                                   placeholder="Digita tu número de teléfono celular">
                        </div>
                        <div class="form-group">
                            <label>Dirección</label>
                            <input type="text" name="direccion" class="form-input"
                                   value="{{ $adminActual->direccion }}"
                                   required minlength="5" maxlength="255"
                                   placeholder="Escribe tu dirección de residencia actual">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-input"
                                   value="{{ $adminActual->email }}" required maxlength="150"
                                   placeholder="Ingresa tu correo electrónico">
                        </div>
                    </div>
                    <hr>
                    <p class="text-muted"><i class="fa-solid fa-lock"></i> Cambiar contraseña (dejar en blanco para no cambiar)</p>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Nueva contraseña</label>
                            <div class="pass-wrap">
                                <input type="password" name="password" id="perfil_pass" class="form-input"
                                       minlength="6" maxlength="30"
                                       placeholder="Crea una nueva clave de seguridad">
                                <button type="button" class="eye-btn" onclick="togglePass('perfil_pass','perfil_eye')">
                                    <i class="fa-solid fa-eye" id="perfil_eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Confirmar contraseña</label>
                            <div class="pass-wrap">
                                <input type="password" name="password_confirmation" id="perfil_pass2" class="form-input"
                                       minlength="6" maxlength="30"
                                       placeholder="Repite la nueva clave para confirmar">
                                <button type="button" class="eye-btn" onclick="togglePass('perfil_pass2','perfil_eye2')">
                                    <i class="fa-solid fa-eye" id="perfil_eye2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="perfil_pass_err" class="field-error" style="display:none;">Las contraseñas no coinciden.</div>
                    <button type="submit" class="btn btn-primary" onclick="return validarPassPerfil()">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                    </button>
                </form>
            </div>
        </div>

    </main>
</div>

{{-- ═══════ MODALES ═══════ --}}

{{-- CREAR USUARIO --}}
<div id="modalCrearUsuario" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-plus"></i> Nuevo Usuario</h3>
            <button class="modal-close" onclick="cerrarModal('modalCrearUsuario')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('admin.usuarios.crear') }}" method="POST" id="formCrearUsuario"
              onsubmit="return validarPassModal('cu_pass','cu_pass2','cu_pass_err')">
            @csrf
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Nombre completo *</label>
                    <input type="text" name="nombre" class="form-input" required minlength="3" maxlength="100"
                           oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g,'')"
                           placeholder="Ingrese los nombres y apellidos del usuario">
                </div>
                <div class="form-group">
                    <label>Tipo de documento *</label>
                    <select name="tipoDocumento" class="form-input" required>
                        <option value="">Seleccione el tipo de documento</option>
                        @foreach(['CC','TI','CE','PEP'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Número de documento *</label>
                    <input type="text" name="numDocumento" class="form-input" required maxlength="15" pattern="[0-9]{4,15}"
                           placeholder="Ingrese el número de identificación">
                </div>
                <div class="form-group">
                    <label>Fecha de nacimiento *</label>
                    <input type="date" name="fechaNacimiento" class="form-input" required
                           max="{{ date('Y-m-d', strtotime('-5 years')) }}">
                </div>
                <div class="form-group">
                    <label>Dirección *</label>
                    <input type="text" name="direccion" class="form-input" required minlength="5" maxlength="255"
                           placeholder="Ingrese la dirección de residencia">
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-input" required maxlength="150"
                           placeholder="Ingrese el correo electrónico">
                </div>
                <div class="form-group">
                    <label>Teléfono *</label>
                    <input type="tel" name="telefono" class="form-input" required pattern="[0-9]{10}" maxlength="10"
                           placeholder="Ingrese el número de contacto">
                </div>
                <div class="form-group">
                    <label>Necesidad <small>(opcional)</small></label>
                    <input type="text" name="necesidad" id="nuevo_necesidad" class="form-input" maxlength="300"
                           placeholder="Describa la necesidad del usuario">
                </div>
                <div class="form-group" id="grp_nuevo_prioridad" style="display:none;">
                    <label>Prioridad</label>
                    <select name="prioridad" id="nuevo_prioridad" class="form-input">
                        <option value="">Sin prioridad</option>
                        <option value="alta">Alta</option><option value="media">Media</option><option value="baja">Baja</option>
                    </select>
                </div>
                <div class="form-group" id="grp_nuevo_obs" style="display:none;">
                    <label>Observación</label>
                    <textarea name="observacion_visita" id="nuevo_obs_visita" class="form-input" rows="3" maxlength="500"
                              placeholder="Observación del usuario"></textarea>
                </div>
                <div class="form-group">
                    <label>Rol *</label>
                    <select name="rol" id="nuevo_rol" class="form-input" required
                            onchange="toggleCamposDonante('nuevo_rol','grp_nuevo_prioridad','grp_nuevo_obs')">
                        <option value="donante">Donante / Solicitante</option>
                        <option value="asistente">Asistente</option>
                        <option value="administrador">Administrador</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Estado *</label>
                    <select name="estado" class="form-input" required>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Contraseña *</label>
                    <div class="pass-wrap">
                        <input type="password" name="password" id="cu_pass" class="form-input"
                               required minlength="6" maxlength="30" placeholder="Asigne una clave de acceso">
                        <button type="button" class="eye-btn" onclick="togglePass('cu_pass','cu_eye')">
                            <i class="fa-solid fa-eye" id="cu_eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label>Confirmar contraseña *</label>
                    <div class="pass-wrap">
                        <input type="password" name="password_confirmation" id="cu_pass2" class="form-input"
                               required minlength="6" maxlength="30" placeholder="Repita la clave de acceso">
                        <button type="button" class="eye-btn" onclick="togglePass('cu_pass2','cu_eye2')">
                            <i class="fa-solid fa-eye" id="cu_eye2"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div id="cu_pass_err" class="field-error" style="display:none;">Las contraseñas no coinciden.</div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Crear Usuario</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalCrearUsuario')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

{{-- EDITAR USUARIO --}}
<div id="modalEditarUsuario" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-pen"></i> Editar Usuario</h3>
            <button class="modal-close" onclick="cerrarModal('modalEditarUsuario')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="formEditarUsuario" method="POST"
              onsubmit="return validarPassModal('eu_pass','eu_pass2','eu_pass_err')">
            @csrf @method('PUT')
            <input type="hidden" name="_id" id="eu_id">
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Nombre completo *</label>
                    <input type="text" name="nombre" id="eu_nombre" class="form-input" required minlength="3" maxlength="100"
                           oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g,'')"
                           placeholder="Modifique los nombres del usuario">
                </div>
                <div class="form-group">
                    <label>Tipo de documento *</label>
                    <select name="tipoDocumento" id="eu_tipo" class="form-input" required>
                        @foreach(['CC','TI','CE','PEP'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Número de documento *</label>
                    <input type="text" name="numDocumento" id="eu_doc" class="form-input" required maxlength="15" pattern="[0-9]{4,15}">
                </div>
                <div class="form-group">
                    <label>Fecha de nacimiento *</label>
                    <input type="date" name="fechaNacimiento" id="eu_fnac" class="form-input" required
                           max="{{ date('Y-m-d', strtotime('-5 years')) }}">
                </div>
                <div class="form-group">
                    <label>Dirección *</label>
                    <input type="text" name="direccion" id="eu_dir" class="form-input" required minlength="5" maxlength="255">
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" id="eu_email" class="form-input" required maxlength="150">
                </div>
                <div class="form-group">
                    <label>Teléfono *</label>
                    <input type="tel" name="telefono" id="eu_tel" class="form-input" required pattern="[0-9]{10}" maxlength="10">
                </div>
                <div class="form-group">
                    <label>Necesidad</label>
                    <input type="text" name="necesidad" id="eu_nec" class="form-input" maxlength="300">
                </div>
                <div class="form-group" id="grp_eu_prioridad" style="display:none;">
                    <label>Prioridad</label>
                    <select name="prioridad" id="eu_prioridad" class="form-input">
                        <option value="">Sin prioridad</option>
                        <option value="alta">Alta</option><option value="media">Media</option><option value="baja">Baja</option>
                    </select>
                </div>
                <div class="form-group" id="grp_eu_obs" style="display:none;">
                    <label>Observación</label>
                    <textarea name="observacion_visita" id="eu_obs_visita" class="form-input" rows="3" maxlength="500"></textarea>
                </div>
                <div class="form-group">
                    <label>Rol *</label>
                    <select name="rol" id="eu_rol" class="form-input" required
                            onchange="toggleCamposDonante('eu_rol','grp_eu_prioridad','grp_eu_obs')">
                        <option value="donante">Donante / Solicitante</option>
                        <option value="asistente">Asistente</option>
                        <option value="administrador">Administrador</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Estado *</label>
                    <select name="estado" id="eu_estado" class="form-input" required>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nueva contraseña <small>(vacío = no cambiar)</small></label>
                    <div class="pass-wrap">
                        <input type="password" name="password" id="eu_pass" class="form-input" minlength="6" maxlength="30"
                               placeholder="Nueva clave si desea cambiarla">
                        <button type="button" class="eye-btn" onclick="togglePass('eu_pass','eu_eye')">
                            <i class="fa-solid fa-eye" id="eu_eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label>Confirmar contraseña</label>
                    <div class="pass-wrap">
                        <input type="password" name="password_confirmation" id="eu_pass2" class="form-input" minlength="6" maxlength="30"
                               placeholder="Repita la nueva clave">
                        <button type="button" class="eye-btn" onclick="togglePass('eu_pass2','eu_eye2')">
                            <i class="fa-solid fa-eye" id="eu_eye2"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div id="eu_pass_err" class="field-error" style="display:none;">Las contraseñas no coinciden.</div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalEditarUsuario')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

{{-- GESTIONAR DONACIÓN --}}
<div id="modalDonacion" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-box-open"></i> Gestionar Donación</h3>
            <button class="modal-close" onclick="cerrarModal('modalDonacion')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="don_detalle" class="detalle-box"></div>
        <form id="formDonacion" method="POST"
              onsubmit="return validarObservacionRequerida('don_estado','don_obs','don_obs_err')">
            @csrf @method('PATCH')
            <input type="hidden" name="_don_id" id="don_id">
            <div class="form-group">
                <label>Estado</label>
                <select name="estado" id="don_estado" class="form-input"
                        onchange="actualizarHintObservacion('don_estado','don_obs_hint','don_obs','don_obs_err')">
                    <option value="pendiente">Pendiente</option>
                    <option value="aprobada">Aprobada</option>
                    <option value="rechazada">Rechazada</option>
                </select>
            </div>
            <div class="form-group">
                <label>Observación <span id="don_obs_hint">(opcional)</span></label>
                <textarea name="observacion" id="don_obs" class="form-input" rows="3"
                          placeholder="Añade una observación..." maxlength="250"></textarea>
                <small id="don_obs_err" class="field-error" style="display:none;">
                    <i class="fa-solid fa-triangle-exclamation"></i> La observación es obligatoria al rechazar.
                </small>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalDonacion')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

{{-- GESTIONAR SOLICITUD --}}
<div id="modalSolicitud" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-clipboard-list"></i> Gestionar Solicitud</h3>
            <button class="modal-close" onclick="cerrarModal('modalSolicitud')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="sol_detalle" class="detalle-box"></div>
        <form id="formSolicitud" method="POST"
              onsubmit="return validarObservacionRequerida('sol_estado','sol_obs','sol_obs_err')">
            @csrf @method('PATCH')
            <input type="hidden" name="_sol_id" id="sol_id">
            <div class="form-group">
                <label>Estado</label>
                <select name="estado" id="sol_estado" class="form-input"
                        onchange="actualizarHintObservacion('sol_estado','sol_obs_hint','sol_obs','sol_obs_err')">
                    <option value="pendiente">Pendiente</option>
                    <option value="aprobada">Aprobada</option>
                    <option value="rechazada">Rechazada</option>
                </select>
            </div>
            <div class="form-group">
                <label>Observación <span id="sol_obs_hint">(opcional)</span></label>
                <textarea name="observacion" id="sol_obs" class="form-input" rows="3"
                          placeholder="Añade una observación..." maxlength="250"></textarea>
                <small id="sol_obs_err" class="field-error" style="display:none;">
                    <i class="fa-solid fa-triangle-exclamation"></i> La observación es obligatoria al rechazar.
                </small>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalSolicitud')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

{{-- CREAR EVENTO --}}
<div id="modalCrearEvento" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-calendar-plus"></i> Publicar Nuevo Evento</h3>
            <button class="modal-close" onclick="cerrarModal('modalCrearEvento')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('admin.eventos.crear') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Nombre del Evento *</label>
                    <input type="text" name="nombre_evento" class="form-input" required maxlength="150"
                           placeholder="Ingresa el nombre del evento">
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado_evento" class="form-input">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Fecha de la Entrega *</label>
                    <input type="date" name="fecha_entrega" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Lugar de Entrega *</label>
                    <input type="text" name="lugar_entrega" class="form-input" required maxlength="255"
                           value="Transversal 73 H Bis #75B 46 SUR Barrio Sierra Morena V Sector"
                           placeholder="Dirección del lugar de entrega">
                </div>
            </div>
            <hr>
            <div class="form-group">
                <label>Título de la Publicación *</label>
                <input type="text" name="titulo_pub" class="form-input" required maxlength="200"
                       placeholder="Ingresa el título de la publicación">
            </div>
            <div class="form-group">
                <label>Contenido / Detalles *</label>
                <textarea name="contenido_pub" class="form-input" rows="3" required maxlength="500"
                          placeholder="Describe los detalles del evento"></textarea>
            </div>
            <div class="form-group">
                <label>Imagen (Opcional)</label>
                <input type="file" name="imagen_pub" class="form-input" accept="image/*">
            </div>
            <div class="modal-footer">
                <button type="submit" name="crear_evento_completo" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Crear y Publicar
                </button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalCrearEvento')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

{{-- EDITAR EVENTO --}}
<div id="modalEditarEvento" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3><i class="fa-solid fa-calendar-check"></i> Editar Evento y Publicación</h3>
            <button class="modal-close" onclick="cerrarModal('modalEditarEvento')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="formEditarEvento" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <input type="hidden" name="idEvento" id="edit_idEvento">
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Nombre del Evento</label>
                    <input type="text" name="nombre_evento" id="edit_nombre" class="form-input" required maxlength="150"
                           placeholder="Nombre del evento">
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado_evento" id="edit_estado" class="form-input">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Fecha de Entrega</label>
                    <input type="date" name="fecha_entrega" id="edit_fecha_entrega" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Lugar</label>
                    <input type="text" name="lugar_entrega" id="edit_lugar_entrega" class="form-input" required maxlength="255">
                </div>
            </div>
            <hr>
            <div class="form-group">
                <label>Título de la Publicación</label>
                <input type="text" name="titulo_pub" id="edit_titulo_pub" class="form-input" required maxlength="200">
            </div>
            <div class="form-group">
                <label>Contenido</label>
                <textarea name="contenido_pub" id="edit_contenido_pub" class="form-input" rows="4" required maxlength="500"></textarea>
            </div>
            <div class="form-group">
                <label>Imagen de la Publicación</label>
                <div id="edit_img_preview_wrap" class="img-preview-wrap">
                    <p class="img-preview-label"><i class="fa-solid fa-image"></i> Imagen actual:</p>
                    <img id="edit_img_preview" src="" alt="Imagen actual" class="img-current-preview">
                </div>
                <input type="file" name="imagen_pub" id="edit_imagen_pub" class="form-input" accept="image/*"
                       onchange="previewNuevaImagen(this)">
                <small class="text-muted">Deja vacío para mantener la imagen actual.</small>
                <img id="edit_nueva_img_preview" src="" alt="Nueva imagen" class="img-new-preview" style="display:none;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalEditarEvento')">Cancelar</button>
                <button type="submit" name="editar_evento_completo" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

{{-- CREAR CATEGORÍA --}}
<div id="modalCrearCategoria" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-tags"></i> Nueva Categoría</h3>
            <button class="modal-close" onclick="cerrarModal('modalCrearCategoria')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('admin.categorias.crear') }}" method="POST"
              onsubmit="return validarCategoria('crear')">
            @csrf
            <div class="form-group">
                <label>Nombre de la categoría *</label>
                <input type="text" name="nombre_categoria" id="cat_nombre" class="form-input"
                       required minlength="3" maxlength="100"
                       placeholder="Ingrese la nueva categoría"
                       oninput="validarEntrada(this)">
                <small id="cat_err" class="field-error" style="display:none;"></small>
            </div>
            <div class="modal-footer">
                <button type="submit" name="crear_categoria" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Crear
                </button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalCrearCategoria')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

{{-- EDITAR CATEGORÍA --}}
<div id="modalEditarCategoria" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-tag"></i> Editar Categoría</h3>
            <button class="modal-close" onclick="cerrarModal('modalEditarCategoria')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="formEditarCategoria" method="POST"
              onsubmit="return validarCategoria('editar')">
            @csrf @method('PUT')
            <input type="hidden" name="idCategoria" id="ecat_id">
            <div class="form-group">
                <label>Nombre de la categoría *</label>
                <input type="text" name="nombre_categoria" id="ecat_nombre" class="form-input"
                       required minlength="3" maxlength="100" oninput="validarEntrada(this)">
                <small id="ecat_err" class="field-error" style="display:none;"></small>
            </div>
            <div class="modal-footer">
                <button type="submit" name="editar_categoria" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar
                </button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalEditarCategoria')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/admin.js') }}"></script>
<script src="{{ asset('assets/js/admin_dashboard.js') }}"></script>
<script>
// Conectar modales de donación/solicitud a rutas Laravel
const ROUTES = {
    donacion:  (id) => `{{ url('/admin/donaciones') }}/${id}/estado`,
    solicitud: (id) => `{{ url('/admin/solicitudes') }}/${id}/estado`,
    editarUsuario: (id) => `{{ url('/admin/usuarios') }}/${id}`,
    editarCategoria: (id) => `{{ url('/admin/categorias') }}/${id}`,
    editarEvento: (id) => `{{ url('/admin/eventos') }}/${id}`,
};

// Sobrescribir abrirModalDonacion para inyectar la ruta correcta
const _origAbrirDon = window.abrirModalDonacion;
window.abrirModalDonacion = function(d) {
    if (_origAbrirDon) _origAbrirDon(d);
    document.getElementById('formDonacion').action = ROUTES.donacion(d.idDonacion);
    document.getElementById('don_id').value = d.idDonacion;
};

const _origAbrirSol = window.abrirModalSolicitud;
window.abrirModalSolicitud = function(s) {
    if (_origAbrirSol) _origAbrirSol(s);
    document.getElementById('formSolicitud').action = ROUTES.solicitud(s.idSolicitud);
    document.getElementById('sol_id').value = s.idSolicitud;
};

// Editar usuario: inyectar acción con ID dinámico
const _origEditUsr = window.abrirModalEditarUsuario;
window.abrirModalEditarUsuario = function(u) {
    if (_origEditUsr) _origEditUsr(u);
    document.getElementById('formEditarUsuario').action = ROUTES.editarUsuario(u.idUsuario);
};

// Editar categoría: inyectar acción con ID dinámico
const _origEditCat = window.abrirModalEditarCategoria;
window.abrirModalEditarCategoria = function(c) {
    if (_origEditCat) _origEditCat(c);
    document.getElementById('formEditarCategoria').action = ROUTES.editarCategoria(c.idCategoria);
    document.getElementById('ecat_id').value = c.idCategoria;
    document.getElementById('ecat_nombre').value = c.nombre;
    abrirModal('modalEditarCategoria');
};

// Editar evento: inyectar acción con ID dinámico
const _origEditEvt = window.abrirModalEditarEvento;
window.abrirModalEditarEvento = function(ev) {
    if (_origEditEvt) _origEditEvt(ev);
    document.getElementById('formEditarEvento').action = ROUTES.editarEvento(ev.idEvento);
};
</script>
@endsection
