@extends('layouts.app')
@section('title', 'Donapp — Panel de Moderación')
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/asis_style.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    <style>
        .perfil-lock-notice{display:flex;align-items:center;gap:14px;background:#fff8e6;border:1px solid #f0d78c;
            border-radius:10px;padding:14px 16px;margin-bottom:20px;}
        .perfil-lock-notice > i{font-size:22px;color:#b8860b;flex-shrink:0;}
        .perfil-lock-notice > div{flex:1;}
        .perfil-lock-notice .btn{flex-shrink:0;white-space:nowrap;}
        .form-group input:disabled, .form-group select:disabled{background:#f2f2f2;color:#666;cursor:not-allowed;}
        .field-error{color:#a12a22;font-size:13px;margin-top:4px;}
    </style>
@endsection

@section('content')
<div class="admin-wrapper">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <a href="{{ route('home') }}"><img src="{{ asset('assets/uploads/Red-Logo.png') }}" alt="Donapp"></a>
            <p class="sidebar-title">Panel de Moderación</p>
        </div>
        <ul class="nav-menu">
            <li><a href="#dashboard"  class="nav-link active"><i class="fa-solid fa-house"></i><span> Dashboard</span></a></li>
            <li><a href="#clientes"   class="nav-link"><i class="fa-solid fa-users"></i><span> Donantes / Solicitantes</span></a></li>
            <li><a href="#donapp"     class="nav-link"><i class="fa-solid fa-hand-holding-heart"></i><span> Donaciones/Sol.</span></a></li>
            <li><a href="#stock"      class="nav-link"><i class="fa-solid fa-warehouse"></i><span> Stock Disponible</span></a></li>
            <li><a href="#eventos"    class="nav-link"><i class="fa-solid fa-calendar-days"></i><span> Eventos</span></a></li>
            <li><a href="#visitas"    class="nav-link"><i class="fa-solid fa-house-chimney-user"></i><span> Visitas Domiciliarias</span>
                @if($totalVisitasPendientes > 0)<span class="nav-badge">{{ $totalVisitasPendientes }}</span>@endif
            </a></li>
            <li><a href="#categorias" class="nav-link"><i class="fa-solid fa-tags"></i><span> Categorías</span></a></li>
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

    <main class="main-content">

        {{-- DASHBOARD --}}
        <div id="dashboard" class="tab-pane active">
            <h1 class="page-title">Bienvenid@, {{ $asisActual->nombre }} 👋</h1>
            <p class="text-muted page-subtitle">
                <i class="fa-solid fa-shield-halved"></i> Módulo de Moderación — Revisa y gestiona donaciones, solicitudes y eventos.
            </p>
            <div class="stats-grid">
                <div class="stat-card alert-pending">
                    <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
                    <div><h3>{{ $totalPendientes }}</h3><p>Pendientes por revisar</p></div>
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
                    <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
                    <div><h3>{{ $totalAprobadas }}</h3><p>Donaciones aprobadas</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fa-solid fa-calendar-check"></i></div>
                    <div><h3>{{ $totalEventos }}</h3><p>Eventos</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                    <div><h3>{{ $totalClientes }}</h3><p>Donantes / Solicitantes</p></div>
                </div>
            </div>
            <h2 class="page-title subtitle-section">Accesos rápidos</h2>
            <div class="stats-grid stats-grid-sm">
                <a href="#donapp" class="stat-card" onclick="activarTab('#donapp')">
                    <div class="stat-icon orange"><i class="fa-solid fa-box-open"></i></div>
                    <div><p>Ver Donaciones</p></div>
                </a>
                <a href="#donapp" class="stat-card"
                   onclick="activarTab('#donapp'); setTimeout(()=>{ const b=document.querySelector('.tab-btn[onclick*=sol-panel]'); if(b) switchInner(b,'sol-panel'); },100);">
                    <div class="stat-icon blue"><i class="fa-solid fa-clipboard-list"></i></div>
                    <div><p>Ver Solicitudes</p></div>
                </a>
                <a href="#eventos" class="stat-card" onclick="activarTab('#eventos')">
                    <div class="stat-icon green"><i class="fa-solid fa-calendar-plus"></i></div>
                    <div><p>Nuevo Evento</p></div>
                </a>
                <a href="#reportes" class="stat-card" onclick="activarTab('#reportes')">
                    <div class="stat-icon"><i class="fa-solid fa-file-pdf"></i></div>
                    <div><p>Generar Reporte</p></div>
                </a>
            </div>
        </div>

        {{-- CLIENTES --}}
        <div id="clientes" class="tab-pane">
            <div class="section-header">
                <h2 class="page-title">Gestión de Donantes y Solicitantes</h2>
                <button class="btn btn-primary" onclick="abrirModal('modalCrearDonante')">
                    <i class="fa-solid fa-user-plus"></i> Nuevo Donante / Solicitante
                </button>
            </div>

            @if($misCorrecciones->isNotEmpty())
            <div class="card" style="border-left:4px solid #b8860b;">
                <h3 style="margin-top:0;"><i class="fa-solid fa-user-shield"></i> Mis solicitudes de corrección de datos</h3>
                <p class="text-muted" style="margin-top:-6px;">Solo un administrador puede aprobarlas o rechazarlas. Aquí solo consultas su estado.</p>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Datos de</th><th>Campo</th><th>Valor propuesto</th><th>Estado</th><th>Fecha</th></tr></thead>
                        <tbody>
                            @foreach($misCorrecciones as $c)
                            <tr>
                                <td>{{ $c->usuario?->nombre ?? '—' }}</td>
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

            <div class="card">
                <form method="GET" action="{{ route('asis.dashboard') }}" class="filter-bar">
                    <input type="hidden" name="tab" value="clientes">
                    <input type="text" name="cli_search" placeholder="🔍 Buscar donante/solicitante por nombre o email..."
                           value="{{ request('cli_search') }}" class="form-input search-input" maxlength="200">
                    <select name="cli_prioridad" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Todas las prioridades</option>
                        <option value="urgente" {{ request('cli_prioridad')=='urgente' ? 'selected' : '' }}>🟣 Urgente</option>
                        <option value="alta"  {{ request('cli_prioridad')=='alta'  ? 'selected' : '' }}>🔴 Alta</option>
                        <option value="media" {{ request('cli_prioridad')=='media' ? 'selected' : '' }}>🟡 Media</option>
                        <option value="baja"  {{ request('cli_prioridad')=='baja'  ? 'selected' : '' }}>🟢 Baja</option>
                    </select>
                    <select name="cli_sort" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Más recientes</option>
                        <option value="az" {{ request('cli_sort')=='az' ? 'selected' : '' }}>Nombre A-Z</option>
                        <option value="za" {{ request('cli_sort')=='za' ? 'selected' : '' }}>Nombre Z-A</option>
                        <option value="prioridad" {{ request('cli_sort')=='prioridad' ? 'selected' : '' }}>Prioridad (urgente → baja)</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                    @if(request('cli_search') || request('cli_prioridad') || request('cli_sort'))
                        <a href="{{ route('asis.dashboard') }}#clientes" class="btn btn-secondary btn-sm">Limpiar</a>
                    @endif
                </form>
                <div class="table-wrap">
                    <table>
                        <thead><tr>
                            <th>#</th><th>Nombre</th><th>Documento</th><th>Email</th>
                            <th>Teléfono</th><th>Necesidad</th><th>Prioridad</th><th>Estado</th><th>Acciones</th>
                        </tr></thead>
                        <tbody>
                            @forelse($clientes as $cli)
                            <tr>
                                <td>{{ $cli->idUsuario }}</td>
                                <td>{{ $cli->nombre }}</td>
                                <td><small>{{ $cli->tipoDocumento }}: {{ $cli->numDocumento }}</small></td>
                                <td>{{ $cli->email }}</td>
                                <td>{{ $cli->telefono }}</td>
                                <td class="necesidad-cell">{{ $cli->necesidad ?? '—' }}</td>
                                <td>
                                    @php $p = $cli->prioridad ?? ''; @endphp
                                    @if($p === 'alta') <span style="color:#c0392b;font-weight:700;">🔴 Alta</span>
                                    @elseif($p === 'media') <span style="color:#d68910;font-weight:700;">🟡 Media</span>
                                    @elseif($p === 'baja') <span style="color:#1e8449;font-weight:700;">🟢 Baja</span>
                                    @else —
                                    @endif
                                </td>
                                <td><span class="badge estado-{{ $cli->estado }}">{{ $cli->estado }}</span></td>
                                <td class="td-actions">
                                    {{-- Botón 1: Ver detalles --}}
                                    <button onclick='abrirModalVerDonante({{ json_encode($cli, JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) }})' class="btn btn-sm btn-primary" title="Ver detalles">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    {{-- Botón 2: Editar --}}
                                    <button onclick='abrirModalEditarDonante({{ json_encode($cli, JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) }})' class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    {{-- Botón 3: Ver donaciones y solicitudes del cliente --}}
                                    <button onclick='abrirModalHistorialCliente({{ json_encode(["idUsuario"=>$cli->idUsuario,"nombre"=>$cli->nombre], JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) }})'
                                            class="btn btn-sm btn-success" title="Ver donaciones y solicitudes">
                                        <i class="fa-solid fa-list-check"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="empty-row">No se encontraron donantes/solicitantes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- DONACIONES / SOLICITUDES --}}
        <div id="donapp" class="tab-pane">
            <h2 class="page-title">Donaciones y Solicitudes</h2>
            <div class="tabs-inner">
                <button class="tab-btn {{ !request('sol_search') && !request('sol_estado') ? 'active' : '' }}"
                        onclick="switchInner(this,'don-panel')">Donaciones</button>
                <button class="tab-btn {{ request('sol_search') || request('sol_estado') ? 'active' : '' }}"
                        onclick="switchInner(this,'sol-panel')">Solicitudes</button>
            </div>

            <div id="don-panel" class="inner-panel" {{ request('sol_search') || request('sol_estado') ? 'style=display:none' : '' }}>
                <form method="GET" action="{{ route('asis.dashboard') }}" class="filter-bar" style="margin-bottom:16px">
                    <input type="hidden" name="tab" value="donapp">
                    <input type="text" name="don_search" placeholder="🔍 Buscar por descripción o donante..."
                           value="{{ request('don_search') }}" class="form-input search-input" maxlength="200">
                    <select name="don_estado" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('don_estado')=='pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="aprobada"  {{ request('don_estado')=='aprobada'  ? 'selected' : '' }}>Aprobada</option>
                        <option value="rechazada" {{ request('don_estado')=='rechazada' ? 'selected' : '' }}>Rechazada</option>
                    <option value="completada" {{ request('don_estado')=='completada' ? 'selected' : '' }}>Completada</option>
                    <option value="cancelada" {{ request('don_estado')=='cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                    <select name="don_cat" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="0">Todas las categorías</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->idCategoria }}" {{ request('don_cat')==$cat->idCategoria ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                    <select name="don_sort" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Más recientes</option>
                        <option value="az" {{ request('don_sort')=='az' ? 'selected' : '' }}>Descripción A-Z</option>
                        <option value="za" {{ request('don_sort')=='za' ? 'selected' : '' }}>Descripción Z-A</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    @if(request('don_search') || request('don_estado') || request('don_cat') || request('don_sort'))
                        <a href="{{ route('asis.dashboard') }}#donapp" class="btn btn-secondary btn-sm">Limpiar</a>
                    @endif
                </form>
                <div class="card"><div class="table-wrap"><table>
                    <thead><tr><th>#</th><th>Descripción</th><th>Categoría</th><th>Stock</th><th>Estado</th><th>Fecha</th><th>Donante</th><th>Observación</th><th>Acción</th></tr></thead>
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
                                <button onclick='abrirModalDonacion({{ json_encode([
    "idDonacion"    => $d->idDonacion,
    "descripcion"   => $d->descripcion,
    "estado"        => $d->estado,
    "observacion"   => $d->observacion,
    "donante"       => $d->donantes->first()?->nombre,
    "categoria"     => $d->categoria?->nombre,
    "stock"         => $d->stock,
    "fechaCreacion" => $d->donantes->first()?->pivot?->FechaCreacion,
    "imagen"        => $d->imagenBase64(),
], JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) }})'
        class="btn btn-sm btn-primary"><i class="fa-solid fa-pen-to-square"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="empty-row">No se encontraron donaciones.</td></tr>
                        @endforelse
                    </tbody>
                </table></div></div>
            </div>

            <div id="sol-panel" class="inner-panel" {{ !request('sol_search') && !request('sol_estado') ? 'style=display:none' : '' }}>
                <form method="GET" action="{{ route('asis.dashboard') }}" class="filter-bar" style="margin-bottom:16px">
                    <input type="hidden" name="tab" value="donapp">
                    <input type="text" name="sol_search" placeholder="🔍 Buscar por descripción o solicitante..."
                           value="{{ request('sol_search') }}" class="form-input search-input" maxlength="200">
                    <select name="sol_estado" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('sol_estado')=='pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="aprobada"  {{ request('sol_estado')=='aprobada'  ? 'selected' : '' }}>Aprobada</option>
                        <option value="rechazada" {{ request('sol_estado')=='rechazada' ? 'selected' : '' }}>Rechazada</option>
                    <option value="completada" {{ request('sol_estado')=='completada' ? 'selected' : '' }}>Completada</option>
                    <option value="cancelada" {{ request('sol_estado')=='cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                    <select name="sol_cat" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="0">Todas las categorías</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->idCategoria }}" {{ request('sol_cat')==$cat->idCategoria ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                    <select name="sol_sort" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Más recientes</option>
                        <option value="az" {{ request('sol_sort')=='az' ? 'selected' : '' }}>Descripción A-Z</option>
                        <option value="prioridad" {{ request('sol_sort')=='prioridad' ? 'selected' : '' }}>Prioridad del beneficiario</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    @if(request('sol_search') || request('sol_estado') || request('sol_cat') || request('sol_sort'))
                        <a href="{{ route('asis.dashboard') }}#donapp" class="btn btn-secondary btn-sm">Limpiar</a>
                    @endif
                </form>
                <div class="card"><div class="table-wrap"><table>
                    <thead><tr><th>#</th><th>Descripción</th><th>Categoría</th><th>Estado</th><th>Fecha</th><th>Solicitante</th><th>Gestor</th><th>Observación</th><th>Acción</th></tr></thead>
                    <tbody>
                        @forelse($solicitudes as $s)
                        <tr>
                            <td>{{ $s->idSolicitud }}</td>
                            <td>{{ $s->descripcion }}</td>
                            <td>{{ $s->categoria?->nombre ?? '—' }}</td>
                            <td><span class="badge estado-{{ $s->estado }}">{{ $s->estado }}</span></td>
                            <td>—</td>
                            <td>
                                {{ $s->solicitante?->nombre ?? '—' }}
                                @if($s->solicitante?->prioridad)
                                    <span class="badge prioridad-{{ $s->solicitante->prioridad }}" title="Prioridad del beneficiario">{{ ucfirst($s->solicitante->prioridad) }}</span>
                                @endif
                            </td>
                            <td>{{ $s->gestor?->nombre ?? '—' }}</td>
                            <td>{{ $s->observacion ?? '—' }}</td>
                            <td>{{ $s->fechaCreacion ? \Carbon\Carbon::parse($s->fechaCreacion)->format('d/m/Y') : '—' }}</td>
                            <button onclick='abrirModalSolicitud({{ json_encode([
    "idSolicitud"   => $s->idSolicitud,
    "descripcion"   => $s->descripcion,
    "estado"        => $s->estado,
    "observacion"   => $s->observacion,
    "solicitante"   => $s->solicitante?->nombre,
    "prioridad"     => $s->solicitante?->prioridad,
    "categoria"     => $s->categoria?->nombre,
    "fechaCreacion" => $s->fechaCreacion,
<<<<<<< HEAD:resources/views/asis/dashboard.blade.php
=======
    "imagen"        => $s->imagenBase64(),
>>>>>>> 9842787a81702b94f49187c54bf92c9678891faa:donapp/resources/views/asis/dashboard.blade.php
], JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) }})'
        class="btn btn-sm btn-primary"><i class="fa-solid fa-pen-to-square"></i></button>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="empty-row">No se encontraron solicitudes.</td></tr>
                        @endforelse
                    </tbody>
                </table></div></div>
            </div>
        </div>

        {{-- STOCK DISPONIBLE --}}
        <div id="stock" class="tab-pane">
            <div class="section-header">
                <h2 class="page-title">Stock Disponible en la Fundación</h2>
                <p class="page-subtitle">Suma de todas las donaciones <strong>aprobadas</strong>, agrupadas por artículo — lo que hay listo para asignar a un beneficiario.</p>
            </div>

            <div class="stats-grid" style="margin-bottom:20px">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                    <div class="stat-info"><h3>{{ $totalUnidadesInventario }}</h3><p>Unidades totales disponibles</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fa-solid fa-list"></i></div>
                    <div class="stat-info"><h3>{{ $inventario->count() }}</h3><p>Artículos distintos en stock</p></div>
                </div>
            </div>

            <div class="card">
                <form method="GET" action="{{ route('asis.dashboard') }}" class="filter-bar" style="margin-bottom:16px">
                    <input type="hidden" name="tab" value="stock">
                    <input type="text" name="inv_search" placeholder="🔍 Buscar artículo..."
                           value="{{ request('inv_search') }}" class="form-input search-input" maxlength="200">
                    <select name="inv_cat" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="0">Todas las categorías</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->idCategoria }}" {{ request('inv_cat')==$cat->idCategoria ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                    <select name="inv_sort" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Mayor cantidad primero</option>
                        <option value="az" {{ request('inv_sort')=='az' ? 'selected' : '' }}>Artículo A-Z</option>
                        <option value="za" {{ request('inv_sort')=='za' ? 'selected' : '' }}>Artículo Z-A</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                    @if(request('inv_search') || request('inv_cat') || request('inv_sort'))
                        <a href="{{ route('asis.dashboard') }}#stock" class="btn btn-secondary btn-sm">Limpiar</a>
                    @endif
                </form>

                @if($inventario->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="fa-solid fa-box-open"></i></div>
                    <h3>Sin stock disponible</h3>
                    <p>Aún no hay donaciones aprobadas para mostrar en el inventario.</p>
                </div>
                @else
                <div class="table-wrap">
                    <table>
                        <thead><tr>
                            <th>Artículo</th><th>Categoría</th><th>Cantidad disponible</th><th># Donaciones que lo componen</th>
                        </tr></thead>
                        <tbody>
                            @foreach($inventario as $item)
                            <tr>
                                <td class="td-desc">{{ $item->descripcion }}</td>
                                <td>{{ $item->categoria?->nombre ?? '—' }}</td>
                                <td><span class="badge estado-aprobada" style="font-size:0.95rem">{{ $item->total_stock }} disponibles</span></td>
                                <td>{{ $item->num_donaciones }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- EVENTOS --}}
        <div id="eventos" class="tab-pane">
            <div class="section-header">
                <h2 class="page-title">Gestión de Eventos</h2>
                <button class="btn btn-primary" onclick="abrirModalCrearEvento()">
                    <i class="fa-solid fa-plus"></i> Nuevo Evento
                </button>
            </div>
            <div class="card">
                <form method="GET" action="{{ route('asis.dashboard') }}" class="filter-bar" style="margin-bottom:16px">
                    <input type="hidden" name="tab" value="eventos">
                    <input type="text" name="ev_search" placeholder="🔍 Buscar evento por nombre..."
                           value="{{ request('ev_search') }}" class="form-input search-input" maxlength="200">
                    <select name="ev_estado" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="borrador"   {{ request('ev_estado')=='borrador'   ? 'selected' : '' }}>Borrador</option>
                        <option value="publicado"  {{ request('ev_estado')=='publicado'  ? 'selected' : '' }}>Publicado</option>
                        <option value="en_curso"   {{ request('ev_estado')=='en_curso'   ? 'selected' : '' }}>En curso</option>
                        <option value="finalizado" {{ request('ev_estado')=='finalizado' ? 'selected' : '' }}>Finalizado</option>
                        <option value="cancelado"  {{ request('ev_estado')=='cancelado'  ? 'selected' : '' }}>Cancelado</option>
                    </select>
                    <select name="ev_sort" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Más recientes</option>
                        <option value="az" {{ request('ev_sort')=='az' ? 'selected' : '' }}>Nombre A-Z</option>
                        <option value="za" {{ request('ev_sort')=='za' ? 'selected' : '' }}>Nombre Z-A</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    @if(request('ev_search') || request('ev_estado') || request('ev_sort'))
                        <a href="{{ route('asis.dashboard') }}#eventos" class="btn btn-secondary btn-sm">Limpiar</a>
                    @endif
                </form>
                <div class="table-wrap"><table>
                    <thead><tr><th>#</th><th>Nombre</th><th>Fechas</th><th>Lugar</th><th>Estado</th><th>Acciones</th></tr></thead>
                    <tbody>
                        @forelse($eventos as $ev)
                        @php
$evJson = json_encode([
    'idEvento'      => $ev->idEvento,
    'Nombre'        => $ev->Nombre,
    'estado'        => $ev->estado,
    'fecha_inicio'  => $ev->fechaInicio ?? '',
    'fecha_fin'     => $ev->fechaFin ?? '',
    'lugar_entrega' => $ev->lugar,
    'titulo_pub'    => $ev->Nombre,
    'contenido_pub' => $ev->contenido,
    'imagen'        => $ev->imagen
                        ? 'data:image/jpeg;base64,'.base64_encode($ev->imagen)
                        : null,
], JSON_HEX_APOS | JSON_UNESCAPED_UNICODE);
@endphp
                        <tr>
                            <td>{{ $ev->idEvento }}</td>
                            <td>{{ $ev->Nombre }}</td>
                            <td>
                                @if($ev->fechaInicio)
                                    {{ \Carbon\Carbon::parse($ev->fechaInicio)->format('d/m/Y') }}
                                    @if($ev->esMultidia())
                                        – {{ \Carbon\Carbon::parse($ev->fechaFin)->format('d/m/Y') }}
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $ev->lugar ?? '—' }}</td>
                            <td>
                                <form action="{{ route('asis.eventos.estado', $ev->idEvento) }}" method="POST" style="display:inline">
                                    @csrf @method('PATCH')
                                    <select name="estado" class="form-input sel-small badge-select estado-{{ $ev->estado }}" onchange="this.form.submit()">
                                        <option value="borrador"   {{ $ev->estado=='borrador'   ? 'selected' : '' }}>Borrador</option>
                                        <option value="publicado"  {{ $ev->estado=='publicado'  ? 'selected' : '' }}>Publicado</option>
                                        <option value="en_curso"   {{ $ev->estado=='en_curso'   ? 'selected' : '' }}>En curso</option>
                                        <option value="finalizado" {{ $ev->estado=='finalizado' ? 'selected' : '' }}>Finalizado</option>
                                        <option value="cancelado"  {{ $ev->estado=='cancelado'  ? 'selected' : '' }}>Cancelado</option>
                                    </select>
                                </form>
                            </td>
                            <td class="td-actions">
                                <button onclick='abrirModalEditarEvento({{ $evJson }})' class="btn btn-sm btn-primary"><i class="fa-solid fa-pen"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="empty-row">No se encontraron eventos.</td></tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>
        </div>

        {{-- CATEGORÍAS --}}
        <div id="categorias" class="tab-pane">
            <div class="section-header">
                <h2 class="page-title">Gestión de Categorías</h2>
                <button class="btn btn-primary" onclick="abrirModal('modalCrearCategoria')">
                    <i class="fa-solid fa-plus"></i> Nueva Categoría
                </button>
            </div>
            <div class="card">
                <form method="GET" action="{{ route('asis.dashboard') }}" class="filter-bar" style="margin-bottom:16px">
                    <input type="hidden" name="tab" value="categorias">
                    <input type="text" name="cat_search" placeholder="🔍 Buscar categoría..."
                           value="{{ request('cat_search') }}" class="form-input search-input" maxlength="200">
                    <select name="cat_sort" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Nombre A-Z</option>
                        <option value="za" {{ request('cat_sort')=='za' ? 'selected' : '' }}>Nombre Z-A</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                    @if(request('cat_search') || request('cat_sort'))
                        <a href="{{ route('asis.dashboard') }}#categorias" class="btn btn-secondary btn-sm">Limpiar</a>
                    @endif
                </form>
                <div class="table-wrap"><table>
                    <thead><tr><th>#</th><th>Nombre</th><th>Acciones</th></tr></thead>
                    <tbody>
                        @forelse($categorias as $cat)
                        <tr>
                            <td>{{ $cat->idCategoria }}</td>
                            <td>{{ $cat->nombre }}</td>
                            <td class="td-actions">
                                <button onclick='abrirModalEditarCategoria({{ json_encode(["idCategoria"=>$cat->idCategoria,"nombre"=>$cat->nombre], JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) }})' class="btn btn-sm btn-warning"><i class="fa-solid fa-pen"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="empty-row">No hay categorías registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>
        </div>

        {{-- VISITAS DOMICILIARIAS --}}
        <div id="visitas" class="tab-pane">
            <div class="section-header">
                <h2 class="page-title">Visitas Domiciliarias</h2>
            </div>
            <div class="card">
                <form method="GET" action="{{ route('asis.dashboard') }}" class="filter-bar" style="margin-bottom:16px">
                    <input type="hidden" name="tab" value="visitas">
                    <select name="vis_estado" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="pendiente"  {{ request('vis_estado')=='pendiente'  ? 'selected' : '' }}>Pendiente</option>
                        <option value="aprobada"   {{ request('vis_estado')=='aprobada'   ? 'selected' : '' }}>Aprobada</option>
                        <option value="rechazada"  {{ request('vis_estado')=='rechazada'  ? 'selected' : '' }}>Rechazada</option>
                        <option value="realizada"  {{ request('vis_estado')=='realizada'  ? 'selected' : '' }}>Realizada</option>
                        <option value="cancelada"  {{ request('vis_estado')=='cancelada'  ? 'selected' : '' }}>Cancelada</option>
                    </select>
                    <select name="vis_sort" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Más recientes / pendientes primero</option>
                        <option value="az" {{ request('vis_sort')=='az' ? 'selected' : '' }}>Ordenar por beneficiario</option>
                    </select>
                    @if(request('vis_estado') || request('vis_sort'))
                        <a href="{{ route('asis.dashboard') }}#visitas" class="btn btn-secondary btn-sm">Limpiar</a>
                    @endif
                </form>
                <div class="table-wrap">
                    <table>
                        <thead><tr>
                            <th>#</th><th>Beneficiario</th><th>Dirección</th><th>Motivo</th>
                            <th>Fecha preferida</th><th>Estado</th><th>Gestor</th><th>Acciones</th>
                        </tr></thead>
                        <tbody>
                            @forelse($visitas as $v)
                            <tr>
                                <td>{{ $v->idVisita }}</td>
                                <td>
                                    {{ $v->usuario?->nombre ?? '—' }}
                                    @if($v->usuario?->prioridad)
                                        <span class="badge prioridad-{{ $v->usuario->prioridad }}">{{ ucfirst($v->usuario->prioridad) }}</span>
                                    @endif
                                </td>
                                <td class="td-desc">{{ $v->direccion }}</td>
                                <td class="td-desc">{{ $v->motivo }}</td>
                                <td>{{ $v->fechaPreferida ? \Carbon\Carbon::parse($v->fechaPreferida)->format('d/m/Y') : '—' }}</td>
                                <td><span class="badge estado-{{ $v->estado }}">{{ $v->estado }}</span></td>
                                <td>{{ $v->gestor?->nombre ?? '—' }}</td>
                                <td class="td-actions">
                                    @if($v->estado === 'pendiente')
                                    <form action="{{ route('asis.visitas.estado', $v->idVisita) }}" method="POST" style="display:inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="estado" value="aprobada">
                                        <button type="submit" class="btn btn-sm btn-success" title="Aprobar"><i class="fa-solid fa-check"></i></button>
                                    </form>
                                    <form action="{{ route('asis.visitas.estado', $v->idVisita) }}" method="POST" style="display:inline"
                                          onsubmit="return confirm('¿Rechazar esta solicitud de visita?')">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="estado" value="rechazada">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Rechazar"><i class="fa-solid fa-xmark"></i></button>
                                    </form>
                                    @elseif($v->estado === 'aprobada')
                                    <form action="{{ route('asis.visitas.estado', $v->idVisita) }}" method="POST" style="display:inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="estado" value="realizada">
                                        <button type="submit" class="btn btn-sm btn-primary" title="Marcar como realizada"><i class="fa-solid fa-house-circle-check"></i></button>
                                    </form>
                                    @else
                                    —
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="empty-row">No hay visitas domiciliarias registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- REPORTES --}}
        <div id="reportes" class="tab-pane">
            <h2 class="page-title">Generador de Reportes PDF</h2>
            <div class="reportes-grid">
                <div class="card reporte-card">
                    <div class="reporte-icon"><i class="fa-solid fa-box-open"></i></div>
                    <h3>Reporte de Donaciones</h3>
                    <p>Genera un PDF con el listado detallado de donaciones según los criterios que elijas.</p>
                    <div class="reporte-filters">
                        <label>Estado:</label>
                        <select id="rpt_don_estado" class="form-input"><option value="todos">Todos</option><option value="aprobada">Aprobadas</option><option value="rechazada">Rechazadas</option><option value="pendiente">Pendientes</option><option value="completada">Completadas</option><option value="cancelada">Canceladas</option></select>
                        <label>Fecha desde:</label><input type="date" id="rpt_don_desde" class="form-input">
                        <label>Fecha hasta:</label><input type="date" id="rpt_don_hasta" class="form-input">
                    </div>
                    <button class="btn btn-primary" onclick="generarReporteDonaciones()"><i class="fa-solid fa-file-pdf"></i> Generar PDF</button>
                </div>
                <div class="card reporte-card">
                    <div class="reporte-icon blue"><i class="fa-solid fa-clipboard-list"></i></div>
                    <h3>Reporte de Solicitudes</h3>
                    <p>Genera un PDF con el detalle de solicitudes completadas o según el estado que selecciones.</p>
                    <div class="reporte-filters">
                        <label>Estado:</label>
                        <select id="rpt_sol_estado" class="form-input"><option value="todos">Todos</option><option value="aprobada">Aprobadas</option><option value="rechazada">Rechazadas</option><option value="pendiente">Pendientes</option><option value="completada">Completadas</option><option value="cancelada">Canceladas</option></select>
                        <label>Fecha desde:</label><input type="date" id="rpt_sol_desde" class="form-input">
                        <label>Fecha hasta:</label><input type="date" id="rpt_sol_hasta" class="form-input">
                    </div>
                    <button class="btn btn-primary" onclick="generarReporteSolicitudes()"><i class="fa-solid fa-file-pdf"></i> Generar PDF</button>
                </div>
            </div>
            <script id="donacionesData" type="application/json">{!! json_encode($donacionesRpt, JSON_UNESCAPED_UNICODE) !!}</script>
            <script id="solicitudesData" type="application/json">{!! json_encode($solicitudesRpt, JSON_UNESCAPED_UNICODE) !!}</script>
        </div>

        {{-- PERFIL --}}
        <div id="perfil" class="tab-pane">
            <h2 class="page-title">Mi Perfil</h2>

            @if(session('correccion_ok'))
            <div class="alert-box alert-success">
                <i class="fa-solid fa-circle-check"></i> Tu solicitud de corrección fue enviada y quedará pendiente de aprobación por un administrador.
            </div>
            @endif

            <div class="card card-perfil">
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

                <form action="{{ route('asis.perfil.update') }}" method="POST" id="formPerfil">
                    @csrf @method('PUT')
                    <div class="form-grid-2">
                        <div class="form-group"><label>Nombre completo <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                            <input type="text" class="form-input" value="{{ $asisActual->nombre }}" disabled readonly></div>
                        <div class="form-group"><label>Apellido <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                            <input type="text" class="form-input" value="{{ $asisActual->apellido }}" disabled readonly></div>
                        <div class="form-group"><label>Tipo de documento <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                            <input type="text" class="form-input" value="{{ $asisActual->tipoDocumento }}" disabled readonly></div>
                        <div class="form-group"><label>Número de documento <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                            <input type="text" class="form-input" value="{{ $asisActual->numDocumento }}" disabled readonly></div>
                        <div class="form-group"><label>Fecha de nacimiento <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                            <input type="date" class="form-input" value="{{ $asisActual->fechaNacimiento }}" disabled readonly></div>
                        <div class="form-group"><label>Teléfono</label>
                            <input type="tel" name="telefono" class="form-input" value="{{ $asisActual->telefono }}" required pattern="[0-9]{10}" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'')"></div>
                        <div class="form-group"><label>Dirección</label>
                            <input type="text" name="direccion" class="form-input" value="{{ $asisActual->direccion }}" required minlength="5" maxlength="255"></div>
                        <div class="form-group"><label>Email</label>
                            <input type="email" name="email" class="form-input" value="{{ $asisActual->email }}" required maxlength="100"></div>
                    </div>
                    <hr>
                    <p class="text-muted"><i class="fa-solid fa-lock"></i> Cambiar contraseña (dejar en blanco para no cambiar)</p>
                    <div class="form-grid-2">
                        <div class="form-group"><label>Nueva contraseña</label>
                            <div class="pass-wrap">
                                <input type="password" name="password" id="perfil_pass" class="form-input" minlength="6" maxlength="20" placeholder="Crea una nueva clave de seguridad">
                                <button type="button" class="eye-btn" onclick="togglePass('perfil_pass','perfil_eye')"><i class="fa-solid fa-eye" id="perfil_eye"></i></button>
                            </div></div>
                        <div class="form-group"><label>Confirmar contraseña</label>
                            <div class="pass-wrap">
                                <input type="password" name="password_confirmation" id="perfil_pass2" class="form-input" minlength="6" maxlength="20" placeholder="Repite la nueva clave para confirmar">
                                <button type="button" class="eye-btn" onclick="togglePass('perfil_pass2','perfil_eye2')"><i class="fa-solid fa-eye" id="perfil_eye2"></i></button>
                            </div></div>
                    </div>
                    <div id="perfil_pass_err" class="field-error" style="display:none;">Las contraseñas no coinciden.</div>
                    <button type="submit" class="btn btn-primary" onclick="return validarPassPerfil()"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</button>
                </form>
            </div>
        </div>

    </main>
</div>

{{-- ============================================================ --}}
{{-- MODALES --}}
{{-- ============================================================ --}}

{{-- Ver detalle de donante --}}
<div id="modalVerDonante" class="modal"><div class="modal-content modal-sm">
    <div class="modal-header"><h3><i class="fa-solid fa-user"></i> Detalle del Donante / Solicitante</h3>
        <button class="modal-close" onclick="cerrarModal('modalVerDonante')"><i class="fa-solid fa-xmark"></i></button></div>
    <div id="donante_detalle" class="donante-detalle"></div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="cerrarModal('modalVerDonante')">Cerrar</button></div>
</div></div>

{{-- Historial de donaciones y solicitudes del cliente --}}
<div id="modalHistorialCliente" class="modal"><div class="modal-content modal-lg">
    <div class="modal-header">
        <h3><i class="fa-solid fa-list-check"></i> Donaciones y Solicitudes de <span id="historial_nombre"></span></h3>
        <button class="modal-close" onclick="cerrarModal('modalHistorialCliente')">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div id="historial_cuerpo" style="padding:16px;">
        <p class="text-muted">Cargando...</p>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalHistorialCliente')">Cerrar</button>
    </div>
</div></div>

{{-- Gestionar donación --}}
<div id="modalDonacion" class="modal"><div class="modal-content">
    <div class="modal-header"><h3><i class="fa-solid fa-box-open"></i> Gestionar Donación</h3>
        <button class="modal-close" onclick="cerrarModal('modalDonacion')"><i class="fa-solid fa-xmark"></i></button></div>
    <div id="don_detalle" class="detalle-box"></div>
    <form id="formDonacion" method="POST" onsubmit="return validarObservacionRequerida('don_estado','don_obs','don_obs_err')">
        @csrf @method('PATCH')
        <input type="hidden" id="don_id">
        <div class="form-group"><label>Estado</label>
            <select name="estado" id="don_estado" class="form-input" onchange="actualizarHintObservacion('don_estado','don_obs_hint','don_obs','don_obs_err')">
                <option value="pendiente">Pendiente</option><option value="aprobada">Aprobada</option><option value="rechazada">Rechazada</option><option value="completada">Completada</option><option value="cancelada">Cancelada</option>
            </select></div>
        <div class="form-group"><label>Observación <span id="don_obs_hint">(opcional)</span></label>
            <textarea name="observacion" id="don_obs" class="form-input" rows="3" placeholder="Añade una observación..." maxlength="250"></textarea>
            <small id="don_obs_err" class="field-error" style="display:none;"><i class="fa-solid fa-triangle-exclamation"></i> La observación es obligatoria al rechazar.</small></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
            <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalDonacion')">Cancelar</button>
        </div>
    </form>
</div></div>

{{-- Gestionar solicitud --}}
<div id="modalSolicitud" class="modal"><div class="modal-content">
    <div class="modal-header"><h3><i class="fa-solid fa-clipboard-list"></i> Gestionar Solicitud</h3>
        <button class="modal-close" onclick="cerrarModal('modalSolicitud')"><i class="fa-solid fa-xmark"></i></button></div>
    <div id="sol_detalle" class="detalle-box"></div>
    <form id="formSolicitud" method="POST" onsubmit="return validarObservacionRequerida('sol_estado','sol_obs','sol_obs_err')">
        @csrf @method('PATCH')
        <input type="hidden" id="sol_id">
        <div class="form-group"><label>Estado</label>
            <select name="estado" id="sol_estado" class="form-input" onchange="actualizarHintObservacion('sol_estado','sol_obs_hint','sol_obs','sol_obs_err')">
                <option value="pendiente">Pendiente</option><option value="aprobada">Aprobada</option><option value="rechazada">Rechazada</option><option value="completada">Completada</option><option value="cancelada">Cancelada</option>
            </select></div>
        <div class="form-group"><label>Observación <span id="sol_obs_hint">(opcional)</span></label>
            <textarea name="observacion" id="sol_obs" class="form-input" rows="3" placeholder="Añade una observación..." maxlength="250"></textarea>
            <small id="sol_obs_err" class="field-error" style="display:none;"><i class="fa-solid fa-triangle-exclamation"></i> La observación es obligatoria al rechazar.</small></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
            <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalSolicitud')">Cancelar</button>
        </div>
    </form>
</div></div>

{{-- Crear evento --}}
<div id="modalCrearEvento" class="modal"><div class="modal-content">
    <div class="modal-header"><h3><i class="fa-solid fa-calendar-plus"></i> Publicar Nuevo Evento</h3>
        <button class="modal-close" onclick="cerrarModal('modalCrearEvento')"><i class="fa-solid fa-xmark"></i></button></div>
    <form action="{{ route('asis.eventos.crear') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-grid-2">
            <div class="form-group"><label>Nombre del Evento *</label><input type="text" name="nombre_evento" class="form-input" required maxlength="150" placeholder="Nombre del evento"></div>
            <div class="form-group"><label>Estado</label><select name="estado_evento" class="form-input">
                <option value="borrador">Borrador</option><option value="publicado">Publicado</option>
                <option value="en_curso">En curso</option><option value="finalizado">Finalizado</option>
                <option value="cancelado">Cancelado</option></select></div>
        </div>
        <div class="form-grid-2">
            <div class="form-group"><label>Fecha de inicio *</label><input type="date" name="fecha_inicio" class="form-input" required></div>
            <div class="form-group"><label>Fecha de fin *</label><input type="date" name="fecha_fin" class="form-input" required>
                <p class="form-hint"><i class="fa-solid fa-circle-info"></i> Usa la misma fecha si dura un solo día.</p></div>
        </div>
        <div class="form-grid-2">
            <div class="form-group"><label>Lugar de Entrega *</label><input type="text" name="lugar_entrega" class="form-input" required maxlength="255" value="Transversal 73 H Bis #75B 46 SUR Barrio Sierra Morena V Sector"></div>
        </div>
        <hr>
        <div class="form-group"><label>Título de la Publicación *</label><input type="text" name="titulo_pub" class="form-input" required maxlength="200" placeholder="Título de la publicación"></div>
        <div class="form-group"><label>Contenido / Detalles *</label><textarea name="contenido_pub" class="form-input" rows="3" required maxlength="500" placeholder="Describe los detalles del evento"></textarea></div>
        <div class="form-group"><label>Imagen (Opcional)</label><input type="file" name="imagen_pub" class="form-input" accept="image/*"></div>
        <div class="modal-footer">
            <button type="submit" name="crear_evento_completo" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Crear y Publicar</button>
            <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalCrearEvento')">Cancelar</button>
        </div>
    </form>
</div></div>

{{-- Editar evento --}}
<div id="modalEditarEvento" class="modal"><div class="modal-content modal-lg">
    <div class="modal-header"><h3><i class="fa-solid fa-calendar-check"></i> Editar Evento y Publicación</h3>
        <button class="modal-close" onclick="cerrarModal('modalEditarEvento')"><i class="fa-solid fa-xmark"></i></button></div>
    <form id="formEditarEvento" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <input type="hidden" name="idEvento" id="edit_idEvento">
        <div class="form-grid-2">
            <div class="form-group"><label>Nombre del Evento</label><input type="text" name="nombre_evento" id="edit_nombre" class="form-input" required maxlength="150"></div>
            <div class="form-group"><label>Estado</label><select name="estado_evento" id="edit_estado" class="form-input">
                <option value="borrador">Borrador</option><option value="publicado">Publicado</option>
                <option value="en_curso">En curso</option><option value="finalizado">Finalizado</option>
                <option value="cancelado">Cancelado</option></select></div>
        </div>
        <div class="form-grid-2">
            <div class="form-group"><label>Fecha de inicio</label><input type="date" name="fecha_inicio" id="edit_fecha_inicio" class="form-input" required></div>
            <div class="form-group"><label>Fecha de fin</label><input type="date" name="fecha_fin" id="edit_fecha_fin" class="form-input" required></div>
        </div>
        <div class="form-grid-2">
            <div class="form-group"><label>Lugar</label><input type="text" name="lugar_entrega" id="edit_lugar_entrega" class="form-input" required maxlength="255"></div>
        </div>
        <hr>
        <div class="form-group"><label>Título de la Publicación</label><input type="text" name="titulo_pub" id="edit_titulo_pub" class="form-input" required maxlength="200"></div>
        <div class="form-group"><label>Contenido</label><textarea name="contenido_pub" id="edit_contenido_pub" class="form-input" rows="4" required maxlength="500"></textarea></div>
        <div class="form-group"><label>Imagen de la Publicación</label>
            <div id="edit_img_preview_wrap" class="img-preview-wrap"><p class="img-preview-label"><i class="fa-solid fa-image"></i> Imagen actual:</p><img id="edit_img_preview" src="" alt="Imagen actual" class="img-current-preview"></div>
            <input type="file" name="imagen_pub" id="edit_imagen_pub" class="form-input" accept="image/*" onchange="previewNuevaImagen(this)">
            <small class="text-muted">Deja vacío para mantener la imagen actual.</small>
            <img id="edit_nueva_img_preview" src="" alt="Nueva imagen" class="img-new-preview" style="display:none;">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalEditarEvento')">Cancelar</button>
            <button type="submit" name="editar_evento_completo" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div></div>

{{-- Crear donante --}}
<div id="modalCrearDonante" class="modal"><div class="modal-content">
    <div class="modal-header"><h3><i class="fa-solid fa-user-plus"></i> Nuevo Donante / Solicitante</h3>
        <button class="modal-close" onclick="cerrarModal('modalCrearDonante')"><i class="fa-solid fa-xmark"></i></button></div>
    <form action="{{ route('asis.clientes.crear') }}" method="POST">
        @csrf
        <div class="form-grid-2">
            <div class="form-group"><label>Nombre *</label><input type="text" name="nombre" class="form-input" required minlength="3" maxlength="100" oninput="this.value=this.value.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑüÜ\s]/g,'')" placeholder="Nombre del cliente"></div>
            <div class="form-group"><label>Apellido *</label><input type="text" name="apellido" class="form-input" required minlength="2" maxlength="100" oninput="this.value=this.value.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑüÜ\s]/g,'')" placeholder="Apellido del cliente"></div>
            <div class="form-group"><label>Tipo de documento *</label><select name="tipoDocumento" class="form-input" required><option value="" disabled selected>Selecciona tipo</option>@foreach(['CC'=>'Cédula de Ciudadanía','TI'=>'Tarjeta de Identidad','CE'=>'Cédula de Extranjería','TE'=>'Tarjeta de Extranjería','PPT'=>'Permiso por Protección Temporal'] as $t => $label)<option value="{{ $t }}">{{ $label }}</option>@endforeach</select></div>
            <div class="form-group"><label>Número de documento *</label><input type="text" name="numDocumento" class="form-input" required pattern="[A-Za-z0-9]{4,15}" maxlength="15"></div>
            <div class="form-group"><label>Fecha de nacimiento *</label><input type="date" name="fechaNacimiento" class="form-input" required max="{{ date('Y-m-d', strtotime('-14 years')) }}">
                <p class="form-hint"><i class="fa-solid fa-circle-info"></i> Debe tener al menos 14 años.</p></div>
            <div class="form-group"><label>Email *</label><input type="email" name="email" class="form-input" required maxlength="150"></div>
            <div class="form-group"><label>Teléfono *</label><input type="tel" name="telefono" class="form-input" required pattern="[0-9]{10}" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'')"></div>
            <div class="form-group"><label>Dirección *</label><input type="text" name="direccion" class="form-input" required minlength="5" maxlength="255"></div>
            <div class="form-group"><label>Necesidad</label><input type="text" name="necesidad" class="form-input" maxlength="255" placeholder="Necesidad principal (Opcional)"></div>
            <div class="form-group"><label>Prioridad</label><select name="prioridad" class="form-input"><option value="">Sin prioridad</option><option value="urgente">Urgente</option><option value="alta">Alta</option><option value="media">Media</option><option value="baja">Baja</option></select></div>
        </div>
        <div class="form-group"><label>Observación de visita</label><textarea name="observacion_visita" class="form-input" rows="3" maxlength="500" placeholder="Observaciones de la visita (Opcional)"></textarea></div>
        <div class="form-grid-2">
            <div class="form-group"><label>Contraseña *</label><div class="pass-wrap"><input type="password" name="password" id="crear_cli_pass" class="form-input" required minlength="6" maxlength="20" placeholder="Clave de acceso"><button type="button" class="eye-btn" onclick="togglePass('crear_cli_pass','crear_cli_eye')"><i class="fa-solid fa-eye" id="crear_cli_eye"></i></button></div></div>
            <div class="form-group"><label>Confirmar contraseña *</label><div class="pass-wrap"><input type="password" name="password_confirmation" id="crear_cli_pass2" class="form-input" required minlength="6" maxlength="20" placeholder="Repetir clave"><button type="button" class="eye-btn" onclick="togglePass('crear_cli_pass2','crear_cli_eye2')"><i class="fa-solid fa-eye" id="crear_cli_eye2"></i></button></div></div>
        </div>
        <div id="crear_cli_pass_err" class="field-error" style="display:none;">Las contraseñas no coinciden.</div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" onclick="return validarPassModal('crear_cli_pass','crear_cli_pass2','crear_cli_pass_err')"><i class="fa-solid fa-floppy-disk"></i> Crear Cliente</button>
            <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalCrearDonante')">Cancelar</button>
        </div>
    </form>
</div></div>

{{-- Editar donante --}}
<div id="modalEditarDonante" class="modal"><div class="modal-content">
    <div class="modal-header"><h3><i class="fa-solid fa-user-pen"></i> Editar Cliente</h3>
        <button class="modal-close" onclick="cerrarModal('modalEditarDonante')"><i class="fa-solid fa-xmark"></i></button></div>
    <div class="perfil-lock-notice">
        <i class="fa-solid fa-lock"></i>
        <div>Nombre, tipo/número de documento y fecha de nacimiento son datos de identidad protegidos. No se editan aquí.</div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="abrirModalCorreccionCliente()">Solicitar corrección</button>
    </div>
    <form id="formEditarDonante" method="POST">
        @csrf @method('PUT')
        <div class="form-grid-2">
            <div class="form-group"><label>Nombre completo</label><input type="text" id="edit_cli_nombre" class="form-input" disabled></div>
            <div class="form-group"><label>Apellido</label><input type="text" id="edit_cli_apellido" class="form-input" disabled></div>
            <div class="form-group"><label>Tipo de documento</label><input type="text" id="edit_cli_tipoDoc" class="form-input" disabled></div>
            <div class="form-group"><label>Número de documento</label><input type="text" id="edit_cli_numDoc" class="form-input" disabled></div>
            <div class="form-group"><label>Fecha de nacimiento</label><input type="text" id="edit_cli_fechaNac" class="form-input" disabled></div>
            <div class="form-group"><label>Email *</label><input type="email" name="email" id="edit_cli_email" class="form-input" required maxlength="150"></div>
            <div class="form-group"><label>Teléfono *</label><input type="tel" name="telefono" id="edit_cli_telefono" class="form-input" required pattern="[0-9]{10}" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'')"></div>
            <div class="form-group"><label>Dirección *</label><input type="text" name="direccion" id="edit_cli_direccion" class="form-input" required minlength="5" maxlength="255"></div>
            <div class="form-group"><label>Necesidad</label><input type="text" name="necesidad" id="edit_cli_necesidad" class="form-input" maxlength="255"></div>
            <div class="form-group"><label>Prioridad</label><select name="prioridad" id="edit_cli_prioridad" class="form-input"><option value="">Sin prioridad</option><option value="urgente">Urgente</option><option value="alta">Alta</option><option value="media">Media</option><option value="baja">Baja</option></select></div>
            <div class="form-group"><label>Nueva contraseña <small>(vacío = no cambiar)</small></label><div class="pass-wrap"><input type="password" name="password" id="edit_cli_pass" class="form-input" minlength="6" maxlength="20"><button type="button" class="eye-btn" onclick="togglePass('edit_cli_pass','edit_cli_eye')"><i class="fa-solid fa-eye" id="edit_cli_eye"></i></button></div></div>
            <div class="form-group"><label>Confirmar contraseña</label><div class="pass-wrap"><input type="password" name="password_confirmation" id="edit_cli_pass2" class="form-input" minlength="6" maxlength="20"><button type="button" class="eye-btn" onclick="togglePass('edit_cli_pass2','edit_cli_eye2')"><i class="fa-solid fa-eye" id="edit_cli_eye2"></i></button></div></div>
        </div>
        <div class="form-group"><label>Observación de visita</label><textarea name="observacion_visita" id="edit_cli_obs" class="form-input" rows="3" maxlength="500"></textarea></div>
        <div id="edit_cli_pass_err" class="field-error" style="display:none;">Las contraseñas no coinciden.</div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" onclick="return validarPassModal('edit_cli_pass','edit_cli_pass2','edit_cli_pass_err')"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</button>
            <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalEditarDonante')">Cancelar</button>
        </div>
    </form>
</div></div>

{{-- Solicitar corrección de datos sensibles del PROPIO asistente --}}
<div id="modalSolicitarCorreccionPerfil" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-shield"></i> Solicitar corrección de datos</h3>
            <button class="modal-close" onclick="cerrarModal('modalSolicitarCorreccionPerfil')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('asis.perfil.solicitarCorreccion') }}" method="POST" enctype="multipart/form-data">
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

{{-- Solicitar corrección de datos sensibles de un cliente --}}
<div id="modalCorreccionCliente" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-shield"></i> Solicitar corrección para <span id="cc_nombre_cliente"></span></h3>
            <button class="modal-close" onclick="cerrarModal('modalCorreccionCliente')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="formCorreccionCliente" method="POST" enctype="multipart/form-data">
            @csrf
            <p class="text-muted" style="margin-top:0;">
                Quedará <b>pendiente</b> hasta que un administrador la apruebe. Como asistente, no puedes aprobar tus propias solicitudes.
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
                          placeholder="Explica el motivo del cambio (ej. error de digitación detectado en la visita)"></textarea>
            </div>
            <div class="form-group">
                <label>Documento de identidad (foto o PDF) *</label>
                <input type="file" name="soporte" class="form-input" accept=".jpg,.jpeg,.png,.pdf" required>
                <small class="text-muted">Máx. 4MB. Solo lo verá un administrador; se elimina automáticamente al resolverse.</small>
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:flex-start;gap:8px;font-weight:normal;">
                    <input type="checkbox" name="consentimiento" required style="margin-top:3px;">
                    <span>Se deja constancia del consentimiento del titular para el tratamiento de este documento con fines de verificación de identidad.</span>
                </label>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Enviar solicitud</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalCorreccionCliente')">Cancelar</button>
            </div>
        </form>
    </div>
</div>
<script id="categoriasExistentesAsis" type="application/json">{!! json_encode($categorias->map(fn($c) => ['id' => $c->idCategoria, 'nombre' => mb_strtolower(trim($c->nombre))])) !!}</script>

<div id="modalCrearCategoria" class="modal"><div class="modal-content modal-xs">
    <div class="modal-header"><h3><i class="fa-solid fa-tag"></i> Nueva Categoría</h3>
        <button class="modal-close" onclick="cerrarModal('modalCrearCategoria')"><i class="fa-solid fa-xmark"></i></button></div>
    <form action="{{ route('asis.categorias.crear') }}" method="POST">
        @csrf
        <div class="form-group"><label>Nombre de la categoría *</label>
            <input type="text" name="nombre_categoria" id="asis_cat_nombre" class="form-input" required minlength="3" maxlength="100" placeholder="Nombre de la categoría" oninput="validarCategoriaAsistente(this)">
            <small id="asis_cat_err" class="field-error" style="display:none;"></small></div>
        <div class="modal-footer">
            <button type="submit" name="crear_categoria" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Crear</button>
            <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalCrearCategoria')">Cancelar</button>
        </div>
    </form>
</div></div>

{{-- Editar categoría --}}
<div id="modalEditarCategoria" class="modal"><div class="modal-content modal-xs">
    <div class="modal-header"><h3><i class="fa-solid fa-tag"></i> Editar Categoría</h3>
        <button class="modal-close" onclick="cerrarModal('modalEditarCategoria')"><i class="fa-solid fa-xmark"></i></button></div>
    <form id="formEditarCategoria" method="POST">
        @csrf @method('PUT')
        <input type="hidden" name="idCategoria" id="edit_cat_id">
        <div class="form-group"><label>Nombre de la categoría *</label>
            <input type="text" name="nombre_categoria" id="edit_cat_nombre" class="form-input" required minlength="3" maxlength="100" oninput="validarCategoriaAsistente(this)">
            <small id="edit_cat_err" class="field-error" style="display:none;"></small></div>
        <div class="modal-footer">
            <button type="submit" name="editar_categoria" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
            <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalEditarCategoria')">Cancelar</button>
        </div>
    </form>
</div></div>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/asistente.js') }}"></script>
<script>
const ROUTES_ASIS = {
    donacion:       (id) => `{{ url('/asis/donaciones') }}/${id}/estado`,
    solicitud:      (id) => `{{ url('/asis/solicitudes') }}/${id}/estado`,
    editarCliente:  (id) => `{{ url('/asis/clientes') }}/${id}`,
    editarCategoria:(id) => `{{ url('/asis/categorias') }}/${id}`,
    editarEvento:   (id) => `{{ url('/asis/eventos') }}/${id}`,
    historialCliente:(id)=> `{{ url('/asis/clientes') }}/${id}/historial`,
    solicitarCorreccionCliente: (id) => `{{ url('/asis/clientes') }}/${id}/solicitar-correccion`,
};

let _clienteActivo = null;

// ── Donación ──────────────────────────────────────────────────
const _origDon = window.abrirModalDonacion;
window.abrirModalDonacion = function(d) {
    if (_origDon) _origDon(d);
    document.getElementById('formDonacion').action = ROUTES_ASIS.donacion(d.idDonacion);
    document.getElementById('don_id').value = d.idDonacion;
};

// ── Solicitud ─────────────────────────────────────────────────
const _origSol = window.abrirModalSolicitud;
window.abrirModalSolicitud = function(s) {
    if (_origSol) _origSol(s);
    document.getElementById('formSolicitud').action = ROUTES_ASIS.solicitud(s.idSolicitud);
    document.getElementById('sol_id').value = s.idSolicitud;
};

// ── Categoría ─────────────────────────────────────────────────
const _origEditCat = window.abrirModalEditarCategoria;
window.abrirModalEditarCategoria = function(c) {
    if (_origEditCat) _origEditCat(c);
    document.getElementById('formEditarCategoria').action = ROUTES_ASIS.editarCategoria(c.idCategoria);
    document.getElementById('edit_cat_id').value   = c.idCategoria;
    document.getElementById('edit_cat_nombre').value = c.nombre;
    abrirModal('modalEditarCategoria');
};

// ── Evento ────────────────────────────────────────────────────
const _origEditEvt = window.abrirModalEditarEvento;
window.abrirModalEditarEvento = function(ev) {
    if (_origEditEvt) _origEditEvt(ev);
    document.getElementById('formEditarEvento').action = ROUTES_ASIS.editarEvento(ev.idEvento);
};

// ── Editar cliente ────────────────────────────────────────────
window.abrirModalEditarDonante = function(cli) {
    _clienteActivo = cli;
    document.getElementById('formEditarDonante').action      = ROUTES_ASIS.editarCliente(cli.idUsuario);
    document.getElementById('edit_cli_nombre').value         = cli.nombre            ?? '';
    document.getElementById('edit_cli_apellido').value       = cli.apellido          ?? '';
    document.getElementById('edit_cli_tipoDoc').value        = cli.tipoDocumento      ?? '';
    document.getElementById('edit_cli_numDoc').value         = cli.numDocumento       ?? '';
    document.getElementById('edit_cli_fechaNac').value       = cli.fechaNacimiento    ?? '';
    document.getElementById('edit_cli_email').value          = cli.email              ?? '';
    document.getElementById('edit_cli_telefono').value       = cli.telefono           ?? '';
    document.getElementById('edit_cli_direccion').value      = cli.direccion          ?? '';
    document.getElementById('edit_cli_necesidad').value      = cli.necesidad          ?? '';
    document.getElementById('edit_cli_prioridad').value      = cli.prioridad          ?? '';
    document.getElementById('edit_cli_obs').value            = cli.observacion_visita ?? '';
    abrirModal('modalEditarDonante');
};

// ── Solicitar corrección de datos sensibles de un cliente ─────
window.abrirModalCorreccionCliente = function() {
    if (!_clienteActivo) return;
    document.getElementById('cc_nombre_cliente').textContent = _clienteActivo.nombre ?? '';
    document.getElementById('formCorreccionCliente').action  = ROUTES_ASIS.solicitarCorreccionCliente(_clienteActivo.idUsuario);
    document.getElementById('formCorreccionCliente').reset();
    cerrarModal('modalEditarDonante');
    abrirModal('modalCorreccionCliente');
};

// ── Historial de donaciones / solicitudes del cliente ─────────
window.abrirModalHistorialCliente = async function(cli) {
    document.getElementById('historial_nombre').textContent = cli.nombre;
    document.getElementById('historial_cuerpo').innerHTML   = '<p class="text-muted"><i class="fa-solid fa-spinner fa-spin"></i> Cargando...</p>';
    abrirModal('modalHistorialCliente');

    try {
        const res  = await fetch(ROUTES_ASIS.historialCliente(cli.idUsuario));

        if (!res.ok) throw new Error('Error al obtener el historial.');

        const data = await res.json();

        const donRows = data.donaciones && data.donaciones.length
            ? data.donaciones.map(d => `
                <tr>
                    <td>${d.idDonacion}</td>
                    <td>${d.descripcion ?? '—'}</td>
                    <td>${d.categoria   ?? '—'}</td>
                    <td><span class="badge estado-${d.estado}">${d.estado}</span></td>
                    <td>${d.fecha       ?? '—'}</td>
                </tr>`).join('')
            : `<tr><td colspan="5" class="empty-row">Sin donaciones registradas.</td></tr>`;

        const solRows = data.solicitudes && data.solicitudes.length
            ? data.solicitudes.map(s => `
                <tr>
                    <td>${s.idSolicitud}</td>
                    <td>${s.descripcion ?? '—'}</td>
                    <td>${s.categoria   ?? '—'}</td>
                    <td><span class="badge estado-${s.estado}">${s.estado}</span></td>
                    <td>${s.fecha       ?? '—'}</td>
                </tr>`).join('')
            : `<tr><td colspan="5" class="empty-row">Sin solicitudes registradas.</td></tr>`;

        document.getElementById('historial_cuerpo').innerHTML = `
            <h4 style="margin-bottom:8px"><i class="fa-solid fa-box-open"></i> Donaciones</h4>
            <div class="table-wrap" style="margin-bottom:20px">
                <table>
                    <thead>
                        <tr><th>#</th><th>Descripción</th><th>Categoría</th><th>Estado</th><th>Fecha</th></tr>
                    </thead>
                    <tbody>${donRows}</tbody>
                </table>
            </div>
            <h4 style="margin-bottom:8px"><i class="fa-solid fa-clipboard-list"></i> Solicitudes</h4>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>#</th><th>Descripción</th><th>Categoría</th><th>Estado</th><th>Fecha</th></tr>
                    </thead>
                    <tbody>${solRows}</tbody>
                </table>
            </div>`;
    } catch (e) {
        document.getElementById('historial_cuerpo').innerHTML =
            `<p style="color:red"><i class="fa-solid fa-triangle-exclamation"></i> Error al cargar el historial. Intenta de nuevo.</p>`;
    }
};
// ── VALIDACIÓN FECHAS DE EVENTOS ─────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const hoyStr = new Date().toISOString().split('T')[0];

    // Fecha mínima = hoy en ambos formularios
    const fCrear  = document.querySelector('#modalCrearEvento input[name="fecha_inicio"]');
    const fEditar = document.querySelector('#modalEditarEvento input[name="fecha_inicio"]');
    if (fCrear)  fCrear.min  = hoyStr;
    if (fEditar) fEditar.min = hoyStr;

    // Validar antes de enviar
    const formCrear  = document.querySelector('#modalCrearEvento form');
    const formEditar = document.getElementById('formEditarEvento');

    function fechaEsPasada(valor) {
        if (!valor) return false;
        const hoy = new Date(); hoy.setHours(0,0,0,0);
        return new Date(valor + 'T00:00:00') < hoy;
    }

    function validarFechaEvento(form) {
        const inicio = form.querySelector('input[name="fecha_inicio"]');
        const fin    = form.querySelector('input[name="fecha_fin"]');
        if (inicio && fechaEsPasada(inicio.value)) {
            alert('No se pueden programar eventos con fechas pasadas. Selecciona hoy o una fecha futura.');
            inicio.focus();
            return false;
        }
        if (inicio && fin && fin.value && inicio.value && fin.value < inicio.value) {
            alert('La fecha de fin no puede ser anterior a la fecha de inicio.');
            fin.focus();
            return false;
        }
        return true;
    }

    if (formCrear)  formCrear.addEventListener('submit',  e => { if (!validarFechaEvento(formCrear))  e.preventDefault(); });
    if (formEditar) formEditar.addEventListener('submit', e => { if (!validarFechaEvento(formEditar)) e.preventDefault(); });
});

// ══════════ VALIDACIÓN: CATEGORÍAS DUPLICADAS (cliente) ══════════
// El backend SIEMPRE valida de nuevo (unique + regex en AsisController); esto
// solo evita un viaje al servidor innecesario y da feedback inmediato.
function categoriasExistentesListAsis() {
    const el = document.getElementById('categoriasExistentesAsis');
    if (!el) return [];
    try { return JSON.parse(el.textContent); } catch (e) { return []; }
}

function nombreCategoriaDuplicadoAsis(nombre, idExcluir = null) {
    const normalizado = nombre.trim().toLowerCase();
    return categoriasExistentesListAsis().some(c =>
        c.nombre === normalizado && (idExcluir === null || String(c.id) !== String(idExcluir))
    );
}

window.validarCategoriaAsistente = function(input) {
    const esEdicion = input.id === 'edit_cat_nombre';
    const errEl      = document.getElementById(esEdicion ? 'edit_cat_err' : 'asis_cat_err');
    const idExcluir  = esEdicion ? document.getElementById('edit_cat_id').value : null;

    if (!input.value.trim()) {
        errEl.textContent = 'El nombre de la categoría es obligatorio.';
        errEl.style.display = 'block';
        return false;
    }
    if (nombreCategoriaDuplicadoAsis(input.value, idExcluir)) {
        errEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Ya existe una categoría con este nombre. Verifica antes de guardar.';
        errEl.style.display = 'block';
        return false;
    }
    errEl.style.display = 'none';
    return true;
};

// Si el backend rechazó la categoría (ej. otro usuario la creó justo antes),
// reabre el modal correspondiente mostrando el mensaje real de validación.
document.addEventListener('DOMContentLoaded', function () {
    @error('nombre_categoria')
        @if(old('idCategoria'))
            document.getElementById('edit_cat_id').value = '{{ old('idCategoria') }}';
            document.getElementById('edit_cat_nombre').value = '{{ old('nombre_categoria') }}';
            document.getElementById('formEditarCategoria').action = ROUTES_ASIS.editarCategoria('{{ old('idCategoria') }}');
            document.getElementById('edit_cat_err').textContent = @json($message);
            document.getElementById('edit_cat_err').style.display = 'block';
            abrirModal('modalEditarCategoria');
        @else
            document.getElementById('asis_cat_nombre').value = '{{ old('nombre_categoria') }}';
            document.getElementById('asis_cat_err').textContent = @json($message);
            document.getElementById('asis_cat_err').style.display = 'block';
            abrirModal('modalCrearCategoria');
        @endif
    @enderror
});

// ══════════ VALIDACIÓN: FECHAS DE REPORTES ══════════
// Envuelve las funciones generadoras de PDF (definidas en asistente.js) para
// asegurar que "desde" <= "hasta" y que ninguna sea futura, antes de generar.
function rangoFechasValidoAsis(desdeId, hastaId) {
    const desde = document.getElementById(desdeId).value;
    const hasta = document.getElementById(hastaId).value;
    const hoyStr = new Date().toISOString().split('T')[0];

    if (desde && desde > hoyStr) { alert('La fecha "desde" no puede ser futura.'); return false; }
    if (hasta && hasta > hoyStr) { alert('La fecha "hasta" no puede ser futura.'); return false; }
    if (desde && hasta && desde > hasta) { alert('La fecha "desde" no puede ser posterior a la fecha "hasta".'); return false; }
    return true;
}

const _origGenerarDonacionesAsis = window.generarReporteDonaciones;
window.generarReporteDonaciones = function () {
    if (!rangoFechasValidoAsis('rpt_don_desde', 'rpt_don_hasta')) return;
    if (typeof _origGenerarDonacionesAsis === 'function') _origGenerarDonacionesAsis();
};

const _origGenerarSolicitudesAsis = window.generarReporteSolicitudes;
window.generarReporteSolicitudes = function () {
    if (!rangoFechasValidoAsis('rpt_sol_desde', 'rpt_sol_hasta')) return;
    if (typeof _origGenerarSolicitudesAsis === 'function') _origGenerarSolicitudesAsis();
};
</script>
@endsection