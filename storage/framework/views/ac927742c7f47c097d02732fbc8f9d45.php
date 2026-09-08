<?php $__env->startSection('title', 'Donapp — Panel Administrativo'); ?>
<?php $__env->startSection('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/admin_style.css')); ?>">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    <style>
        .perfil-lock-notice{display:flex;align-items:center;gap:14px;background:#fff8e6;border:1px solid #f0d78c;
            border-radius:10px;padding:14px 16px;margin-bottom:20px;}
        .perfil-lock-notice > i{font-size:22px;color:#b8860b;flex-shrink:0;}
        .perfil-lock-notice > div{flex:1;}
        .perfil-lock-notice .btn{flex-shrink:0;white-space:nowrap;}
        .form-group input:disabled, .form-group select:disabled{background:#f2f2f2;color:#666;cursor:not-allowed;}
        .alert-box{padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;}
        .alert-success{background:#e6f6ea;border:1px solid #9bd8ac;color:#1e7a34;}
        .alert-danger{background:#fdecea;border:1px solid #f3a6a0;color:#a12a22;}
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="admin-wrapper">

    
    <aside class="sidebar">
        <div class="sidebar-logo">
            <a href="<?php echo e(route('home')); ?>">
                <img src="<?php echo e(asset('assets/uploads/Red-Logo.png')); ?>" alt="Donapp">
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
                <form action="<?php echo e(route('logout')); ?>" method="POST" style="margin:0">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="nav-link logout">
                        <i class="fa-solid fa-power-off"></i><span> Cerrar Sesión</span>
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    
    <main class="main-content">

        
        <div id="dashboard" class="tab-pane active">
            <h1 class="page-title">Bienvenid@, <?php echo e($adminActual->nombre); ?> 👋</h1>
            <p class="text-muted">
                <i class="fa-solid fa-shield-halved"></i>
                Módulo de Administrador — Revisa y gestiona usuarios, categorías, donaciones, solicitudes y eventos.
            </p>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                    <div><h3><?php echo e($totalUsuarios); ?></h3><p>Usuarios totales</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fa-solid fa-box-open"></i></div>
                    <div><h3><?php echo e($totalDonaciones); ?></h3><p>Donaciones</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fa-solid fa-clipboard-list"></i></div>
                    <div><h3><?php echo e($totalSolicitudes); ?></h3><p>Solicitudes</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fa-solid fa-calendar-check"></i></div>
                    <div><h3><?php echo e($totalEventos); ?></h3><p>Eventos</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
                    <div><h3><?php echo e($totalAprobadas); ?></h3><p>Donaciones aprobadas</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-tags"></i></div>
                    <div><h3><?php echo e($totalCategorias); ?></h3><p>Categorías</p></div>
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

        
        <div id="usuarios" class="tab-pane">
            <div class="section-header">
                <h2 class="page-title">Gestión de Usuarios</h2>
                <button class="btn btn-primary" onclick="abrirModal('modalCrearUsuario')">
                    <i class="fa-solid fa-user-plus"></i> Nuevo Usuario
                </button>
            </div>

            <?php if($correcciones->isNotEmpty()): ?>
            <div class="card" style="border-left:4px solid #b8860b;">
                <h3 style="margin-top:0;"><i class="fa-solid fa-user-shield"></i> Correcciones de datos pendientes (<?php echo e($correcciones->count()); ?>)</h3>
                <p class="text-muted" style="margin-top:-6px;">Solicitudes de cambio sobre campos de identidad. Quien solicita un cambio no puede aprobarlo, aunque sea de otro usuario.</p>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Datos de</th><th>Solicitado por</th><th>Campo</th><th>Valor actual</th><th>Valor propuesto</th>
                                <th>Justificación</th><th>Soporte</th><th>Fecha solicitud</th><th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $correcciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($c->usuario?->nombre ?? '—'); ?></td>
                                <td>
                                    <?php if($c->idSolicitante === $c->idUsuario): ?>
                                        <span class="text-muted">El mismo usuario</span>
                                    <?php else: ?>
                                        <?php echo e($c->solicitante?->nombre ?? '—'); ?>

                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($c->campo); ?></td>
                                <td><?php echo e($c->valorAnterior); ?></td>
                                <td><strong><?php echo e($c->valorNuevo); ?></strong></td>
                                <td class="td-obs"><?php echo e($c->justificacion); ?></td>
                                <td>
                                    <?php if($c->idSolicitante === $adminActual->idUsuario): ?>
                                        <span class="text-muted" title="No puedes ver el soporte de tu propia solicitud"><i class="fa-solid fa-lock"></i></span>
                                    <?php elseif($c->soporteRuta): ?>
                                        <a href="<?php echo e(route('admin.correcciones.soporte', $c->idCorreccion)); ?>" target="_blank" class="btn btn-sm btn-secondary" title="Ver documento de identidad adjunto">
                                            <i class="fa-solid fa-file-shield"></i> Ver
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($c->fechaSolicitud?->format('d/m/Y') ?? '—'); ?></td>
                                <td class="td-actions">
                                    <?php if($c->idSolicitante === $adminActual->idUsuario): ?>
                                        <span class="text-muted" title="No puedes aprobar una solicitud que tú mismo pediste"><i class="fa-solid fa-ban"></i> La pediste tú</span>
                                    <?php else: ?>
                                        <form action="<?php echo e(route('admin.correcciones.aprobar', $c->idCorreccion)); ?>" method="POST" style="display:inline" onsubmit="return confirm('¿Aprobar y aplicar este cambio al usuario?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <button type="submit" class="btn btn-sm btn-success" title="Aprobar"><i class="fa-solid fa-check"></i></button>
                                        </form>
                                        <form action="<?php echo e(route('admin.correcciones.rechazar', $c->idCorreccion)); ?>" method="POST" style="display:inline" onsubmit="return confirm('¿Rechazar esta solicitud?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <button type="submit" class="btn btn-sm btn-danger" title="Rechazar"><i class="fa-solid fa-xmark"></i></button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            <div class="card">
                <form method="GET" action="<?php echo e(route('admin.dashboard')); ?>" class="filter-bar">
                    <input type="hidden" name="tab" value="usuarios">
                    <input type="text" name="search" placeholder="🔍 Buscar por nombre o email..."
                           value="<?php echo e(request('search')); ?>" class="form-input search-input" maxlength="200">
                    <select name="rol" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Todos los roles</option>
                        <option value="donante"       <?php echo e(request('rol')=='donante'       ? 'selected' : ''); ?>>Donante / Solicitante</option>
                        <option value="asistente"     <?php echo e(request('rol')=='asistente'     ? 'selected' : ''); ?>>Asistente</option>
                        <option value="administrador" <?php echo e(request('rol')=='administrador' ? 'selected' : ''); ?>>Administrador</option>
                    </select>
                    <select name="prioridad" class="form-input sel-small" onchange="this.form.submit()"
                            id="filtro_prioridad_select">
                        <option value="">Todas las prioridades</option>
                        <option value="alta"  <?php echo e(request('prioridad')=='alta'  ? 'selected' : ''); ?>>🔴 Alta</option>
                        <option value="media" <?php echo e(request('prioridad')=='media' ? 'selected' : ''); ?>>🟡 Media</option>
                        <option value="baja"  <?php echo e(request('prioridad')=='baja'  ? 'selected' : ''); ?>>🟢 Baja</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                    <?php if(request('search') || request('rol') || request('prioridad')): ?>
                        <a href="<?php echo e(route('admin.dashboard')); ?>#usuarios" class="btn btn-secondary btn-sm">Limpiar</a>
                    <?php endif; ?>
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
                            <?php $__empty_1 = true; $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($u->idUsuario); ?></td>
                                <td><?php echo e($u->nombre); ?></td>
                                <td><small><?php echo e($u->tipoDocumento); ?>: <?php echo e($u->numDocumento); ?></small></td>
                                <td><?php echo e($u->email); ?></td>
                                <td><?php echo e($u->telefono); ?></td>
                                <td><span class="badge <?php echo e($u->rol); ?>"><?php echo e($u->rol); ?></span></td>
                                <td><span class="badge estado-<?php echo e($u->estado); ?>"><?php echo e($u->estado); ?></span></td>
                                <td class="td-actions">
                                    <button onclick='abrirModalEditarUsuario(<?php echo e(json_encode($u)); ?>)'
                                            class="btn btn-sm btn-primary" title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <form action="<?php echo e(route('admin.usuarios.estado', $u->idUsuario)); ?>" method="POST" style="display:inline"
                                          onsubmit="return confirm('<?php echo e($u->estado==='activo' ? '¿Inactivar este usuario?' : '¿Activar este usuario?'); ?>')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="btn btn-sm <?php echo e($u->estado==='activo' ? 'btn-warning' : 'btn-success'); ?>"
                                                title="<?php echo e($u->estado==='activo' ? 'Inactivar' : 'Activar'); ?>">
                                            <i class="fa-solid <?php echo e($u->estado==='activo' ? 'fa-ban' : 'fa-circle-check'); ?>"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="8" class="empty-row">No se encontraron usuarios.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <div id="categorias" class="tab-pane">
            <div class="section-header">
                <h2 class="page-title">Gestión de Categorías</h2>
                <button class="btn btn-primary" onclick="abrirModal('modalCrearCategoria')">
                    <i class="fa-solid fa-plus"></i> Nueva Categoría
                </button>
            </div>
            <?php $__errorArgs = ['nombre_categoria'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="alert-box alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo e($message); ?>

            </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <div class="card">
                <form method="GET" action="<?php echo e(route('admin.dashboard')); ?>" class="filter-bar" style="margin-bottom:16px">
                    <input type="hidden" name="tab" value="categorias">
                    <input type="text" name="cat_search" placeholder="🔍 Buscar categoría por nombre..."
                           value="<?php echo e(request('cat_search')); ?>" class="form-input search-input" maxlength="200">
                    <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                    <?php if(request('cat_search')): ?>
                        <a href="<?php echo e(route('admin.dashboard')); ?>#categorias" class="btn btn-secondary btn-sm">Limpiar</a>
                    <?php endif; ?>
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
                            <?php $__empty_1 = true; $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($cat->idCategoria); ?></td>
                                <td><?php echo e($cat->nombre); ?></td>
                                <td><?php echo e($cat->creadaPor?->nombre ?? '—'); ?></td>
                                <td><?php echo e($cat->donaciones_count); ?></td>
                                <td><?php echo e($cat->solicitudes_count); ?></td>
                                <td class="td-actions">
                                    <button onclick='abrirModalEditarCategoria(<?php echo e(json_encode(["idCategoria"=>$cat->idCategoria,"nombre"=>$cat->nombre])); ?>)'
                                            class="btn btn-sm btn-primary" title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <?php if($cat->donaciones_count == 0 && $cat->solicitudes_count == 0): ?>
                                    <form action="<?php echo e(route('admin.categorias.eliminar', $cat->idCategoria)); ?>" method="POST" style="display:inline"
                                          onsubmit="return confirm('¿Eliminar esta categoría?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled title="Tiene registros asociados">
                                        <i class="fa-solid fa-lock"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="6" class="empty-row">No se encontraron categorías.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <script id="categoriasExistentes" type="application/json">
                <?php echo json_encode($categorias->map(fn($c) => ['id' => $c->idCategoria, 'nombre' => mb_strtolower(trim($c->nombre))])->values(), JSON_UNESCAPED_UNICODE); ?>

            </script>
        </div>

        
        <div id="donapp" class="tab-pane">
            <h2 class="page-title">Donaciones y Solicitudes</h2>
            <div class="tabs-inner">
                <button class="tab-btn <?php echo e(!request('sol_search') && !request('sol_estado') ? 'active' : ''); ?>"
                        onclick="switchInner(this,'don-panel')">Donaciones</button>
                <button class="tab-btn <?php echo e(request('sol_search') || request('sol_estado') ? 'active' : ''); ?>"
                        onclick="switchInner(this,'sol-panel')">Solicitudes</button>
            </div>

            
            <div id="don-panel" class="inner-panel" <?php echo e(request('sol_search') || request('sol_estado') ? 'style=display:none' : ''); ?>>
                <form method="GET" action="<?php echo e(route('admin.dashboard')); ?>" class="filter-bar" style="margin-bottom:16px">
                    <input type="hidden" name="tab" value="donapp">
                    <input type="text" name="don_search" placeholder="🔍 Buscar por descripción o donante..."
                           value="<?php echo e(request('don_search')); ?>" class="form-input search-input" maxlength="200">
                    <select name="don_estado" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" <?php echo e(request('don_estado')=='pendiente' ? 'selected' : ''); ?>>Pendiente</option>
                        <option value="aprobada"  <?php echo e(request('don_estado')=='aprobada'  ? 'selected' : ''); ?>>Aprobada</option>
                        <option value="rechazada" <?php echo e(request('don_estado')=='rechazada' ? 'selected' : ''); ?>>Rechazada</option>
                    </select>
                    <select name="don_cat" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="0">Todas las categorías</option>
                        <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->idCategoria); ?>" <?php echo e(request('don_cat')==$cat->idCategoria ? 'selected' : ''); ?>>
                                <?php echo e($cat->nombre); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    <?php if(request('don_search') || request('don_estado') || request('don_cat')): ?>
                        <a href="<?php echo e(route('admin.dashboard')); ?>#donapp" class="btn btn-secondary btn-sm">Limpiar</a>
                    <?php endif; ?>
                </form>
                <div class="card">
                    <div class="table-wrap">
                        <table>
                            <thead><tr>
                                <th>#</th><th>Descripción</th><th>Categoría</th><th>Stock</th>
                                <th>Estado</th><th>Fecha</th><th>Donante</th><th>Observación</th><th>Acción</th>
                            </tr></thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $donaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($d->idDonacion); ?></td>
                                    <td><?php echo e($d->descripcion); ?></td>
                                    <td><?php echo e($d->categoria?->nombre ?? '—'); ?></td>
                                    <td><?php echo e($d->stock); ?></td>
                                    <td><span class="badge estado-<?php echo e($d->estado); ?>"><?php echo e($d->estado); ?></span></td>
                                    <td><?php echo e($d->donantes->first()?->pivot->FechaCreacion ? \Carbon\Carbon::parse($d->donantes->first()->pivot->FechaCreacion)->format('d/m/Y') : '—'); ?></td>
                                    <td><?php echo e($d->donantes->first()?->nombre ?? '—'); ?></td>
                                    <td><?php echo e($d->observacion ?? '—'); ?></td>
                                    <td>
                                        <button onclick='abrirModalDonacion(<?php echo e(json_encode(["idDonacion"=>$d->idDonacion,"descripcion"=>$d->descripcion,"estado"=>$d->estado,"observacion"=>$d->observacion,"donante"=>$d->donantes->first()?->nombre,"categoria"=>$d->categoria?->nombre,"stock"=>$d->stock])); ?>)'
                                                class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="9" class="empty-row">No se encontraron donaciones.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div id="sol-panel" class="inner-panel" <?php echo e(!request('sol_search') && !request('sol_estado') ? 'style=display:none' : ''); ?>>
                <form method="GET" action="<?php echo e(route('admin.dashboard')); ?>" class="filter-bar" style="margin-bottom:16px">
                    <input type="hidden" name="tab" value="donapp">
                    <input type="text" name="sol_search" placeholder="🔍 Buscar por descripción o solicitante..."
                           value="<?php echo e(request('sol_search')); ?>" class="form-input search-input" maxlength="200">
                    <select name="sol_estado" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" <?php echo e(request('sol_estado')=='pendiente' ? 'selected' : ''); ?>>Pendiente</option>
                        <option value="aprobada"  <?php echo e(request('sol_estado')=='aprobada'  ? 'selected' : ''); ?>>Aprobada</option>
                        <option value="rechazada" <?php echo e(request('sol_estado')=='rechazada' ? 'selected' : ''); ?>>Rechazada</option>
                    </select>
                    <select name="sol_cat" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="0">Todas las categorías</option>
                        <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->idCategoria); ?>" <?php echo e(request('sol_cat')==$cat->idCategoria ? 'selected' : ''); ?>>
                                <?php echo e($cat->nombre); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    <?php if(request('sol_search') || request('sol_estado') || request('sol_cat')): ?>
                        <a href="<?php echo e(route('admin.dashboard')); ?>#donapp" class="btn btn-secondary btn-sm">Limpiar</a>
                    <?php endif; ?>
                </form>
                <div class="card">
                    <div class="table-wrap">
                        <table>
                            <thead><tr>
                                <th>#</th><th>Descripción</th><th>Categoría</th><th>Estado</th>
                                <th>Fecha</th><th>Solicitante</th><th>Gestor</th><th>Observación</th><th>Acción</th>
                            </tr></thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $solicitudes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($s->idSolicitud); ?></td>
                                    <td><?php echo e($s->descripcion); ?></td>
                                    <td><?php echo e($s->categoria?->nombre ?? '—'); ?></td>
                                    <td><span class="badge estado-<?php echo e($s->estado); ?>"><?php echo e($s->estado); ?></span></td>
                                    <td><?php echo e($s->fechaCreacion ? \Carbon\Carbon::parse($s->fechaCreacion)->format('d/m/Y') : '—'); ?></td>
                                    <td><?php echo e($s->solicitante?->nombre ?? '—'); ?></td>
                                    <td>
                                        <?php if($s->gestor): ?>
                                            <span class="badge-staff"><i class="fa-solid fa-user-shield"></i> <?php echo e($s->gestor->nombre); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted"><i>Esperando revisión...</i></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($s->observacion ?? '—'); ?></td>
                                    <td>
                                        <button onclick='abrirModalSolicitud(<?php echo e(json_encode(["idSolicitud"=>$s->idSolicitud,"descripcion"=>$s->descripcion,"estado"=>$s->estado,"observacion"=>$s->observacion,"solicitante"=>$s->solicitante?->nombre,"categoria"=>$s->categoria?->nombre])); ?>)'
                                                class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="9" class="empty-row">No se encontraron solicitudes.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        
        <div id="eventos" class="tab-pane">
            <div class="section-header">
                <h2 class="page-title">Gestión de Eventos</h2>
                <button class="btn btn-primary" onclick="abrirModalCrearEvento()">
                    <i class="fa-solid fa-plus"></i> Nuevo Evento
                </button>
            </div>
            <div class="card">
                <form method="GET" action="<?php echo e(route('admin.dashboard')); ?>" class="filter-bar" style="margin-bottom:16px">
                    <input type="hidden" name="tab" value="eventos">
                    <input type="text" name="ev_search" placeholder="🔍 Buscar evento por nombre..."
                           value="<?php echo e(request('ev_search')); ?>" class="form-input search-input" maxlength="200">
                    <select name="ev_estado" class="form-input sel-small" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="activo"   <?php echo e(request('ev_estado')=='activo'   ? 'selected' : ''); ?>>Activo</option>
                        <option value="inactivo" <?php echo e(request('ev_estado')=='inactivo' ? 'selected' : ''); ?>>Inactivo</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    <?php if(request('ev_search') || request('ev_estado')): ?>
                        <a href="<?php echo e(route('admin.dashboard')); ?>#eventos" class="btn btn-secondary btn-sm">Limpiar</a>
                    <?php endif; ?>
                </form>
                <div class="table-wrap">
                    <table>
                        <thead><tr>
                            <th>ID</th><th>Nombre</th><th>Estado</th><th>Acciones</th>
                        </tr></thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $eventos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
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
                            ?>
                            <tr>
                                <td><?php echo e($ev->idEvento); ?></td>
                                <td><?php echo e($ev->Nombre); ?></td>
                                <td><span class="badge estado-<?php echo e($ev->estado); ?>"><?php echo e($ev->estado); ?></span></td>
                                <td class="td-actions">
                                    <button onclick='abrirModalEditarEvento(<?php echo e($evJson); ?>)'
                                            class="btn btn-sm btn-primary"><i class="fa-solid fa-pen"></i></button>
                                    <form action="<?php echo e(route('admin.eventos.estado', $ev->idEvento)); ?>" method="POST" style="display:inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="btn btn-sm btn-warning">
                                            <i class="fa-solid fa-arrows-rotate"></i> Toggle
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4" class="empty-row">No se encontraron eventos.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
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
                        <input type="date" id="rpt_don_desde" class="form-input" max="<?php echo e(date('Y-m-d')); ?>">
                        <label>Fecha hasta:</label>
                        <input type="date" id="rpt_don_hasta" class="form-input" max="<?php echo e(date('Y-m-d')); ?>">
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
                        <input type="date" id="rpt_sol_desde" class="form-input" max="<?php echo e(date('Y-m-d')); ?>">
                        <label>Fecha hasta:</label>
                        <input type="date" id="rpt_sol_hasta" class="form-input" max="<?php echo e(date('Y-m-d')); ?>">
                    </div>
                    <button class="btn btn-primary" onclick="generarReporteSolicitudes()">
                        <i class="fa-solid fa-file-pdf"></i> Generar PDF
                    </button>
                </div>
            </div>

            <script id="donacionesData" type="application/json">
                <?php echo json_encode($donacionesRpt, JSON_UNESCAPED_UNICODE); ?>

            </script>
            <script id="solicitudesData" type="application/json">
                <?php echo json_encode($solicitudesRpt, JSON_UNESCAPED_UNICODE); ?>

            </script>
        </div>

        
        <div id="perfil" class="tab-pane">
            <h2 class="page-title">Mi Perfil</h2>

            <?php if(session('correccion_ok')): ?>
            <div class="alert-box alert-success">
                <i class="fa-solid fa-circle-check"></i> Tu solicitud de corrección fue enviada y quedará pendiente de aprobación por otro administrador.
            </div>
            <?php endif; ?>

            <div class="card card-perfil">
                <div class="perfil-lock-notice">
                    <i class="fa-solid fa-lock"></i>
                    <div>
                        <strong>Nombre, tipo/número de documento y fecha de nacimiento no se pueden editar directamente.</strong>
                        <p class="text-muted" style="margin:4px 0 0;">
                            Estos campos son datos de identidad. Si detectas un error o necesitas actualizarlos,
                            usa el botón <b>"Solicitar corrección de datos"</b>; un administrador distinto revisará y aprobará el cambio. Si no hay administradores disponibles, la solicitud quedará pendiente hasta que alguien pueda revisarla.
                        </p>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="abrirModal('modalSolicitarCorreccion')">
                        <i class="fa-solid fa-pen-to-square"></i> Solicitar corrección de datos
                    </button>
                </div>

                <form action="<?php echo e(route('admin.perfil.update')); ?>" method="POST" id="formPerfil">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Nombre completo <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                            <input type="text" class="form-input"
                                   value="<?php echo e($adminActual->nombre); ?>"
                                   disabled readonly>
                        </div>
                        <div class="form-group">
                            <label>Tipo de documento <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                            <input type="text" class="form-input" value="<?php echo e($adminActual->tipoDocumento); ?>" disabled readonly>
                        </div>
                        <div class="form-group">
                            <label>Número de documento <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                            <input type="text" class="form-input"
                                   value="<?php echo e($adminActual->numDocumento); ?>"
                                   disabled readonly>
                        </div>
                        <div class="form-group">
                            <label>Fecha de nacimiento <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                            <input type="date" class="form-input"
                                   value="<?php echo e($adminActual->fechaNacimiento); ?>" disabled readonly>
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="tel" name="telefono" class="form-input"
                                   value="<?php echo e($adminActual->telefono); ?>"
                                   required pattern="[0-9]{10}" maxlength="10"
                                   placeholder="Digita tu número de teléfono celular">
                        </div>
                        <div class="form-group">
                            <label>Dirección</label>
                            <input type="text" name="direccion" class="form-input"
                                   value="<?php echo e($adminActual->direccion); ?>"
                                   required minlength="5" maxlength="255"
                                   placeholder="Escribe tu dirección de residencia actual">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-input"
                                   value="<?php echo e($adminActual->email); ?>" required maxlength="150"
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




<div id="modalCrearUsuario" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-plus"></i> Nuevo Usuario</h3>
            <button class="modal-close" onclick="cerrarModal('modalCrearUsuario')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?php echo e(route('admin.usuarios.crear')); ?>" method="POST" id="formCrearUsuario"
              onsubmit="return validarPassModal('cu_pass','cu_pass2','cu_pass_err')">
            <?php echo csrf_field(); ?>
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
                        <?php $__currentLoopData = ['CC','TI','CE','PEP']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($t); ?>"><?php echo e($t); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                           max="<?php echo e(date('Y-m-d', strtotime('-5 years'))); ?>">
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


<div id="modalEditarUsuario" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-pen"></i> Editar Usuario</h3>
            <button class="modal-close" onclick="cerrarModal('modalEditarUsuario')"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="perfil-lock-notice" style="margin:0 24px 16px;">
            <i class="fa-solid fa-lock"></i>
            <div>
                <strong>Nombre, documento y fecha de nacimiento no se editan directamente, ni siquiera desde este panel.</strong>
                <p class="text-muted" style="margin:4px 0 0;">Si el usuario tiene un dato de identidad mal registrado, solicita la corrección; quedará pendiente hasta que otro administrador la apruebe.</p>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" onclick="abrirModalCorreccionUsuario()">
                <i class="fa-solid fa-pen-to-square"></i> Solicitar corrección
            </button>
        </div>

        <form id="formEditarUsuario" method="POST"
              onsubmit="return validarPassModal('eu_pass','eu_pass2','eu_pass_err')">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <input type="hidden" name="_id" id="eu_id">
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Nombre completo <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                    <input type="text" id="eu_nombre" class="form-input" disabled readonly>
                </div>
                <div class="form-group">
                    <label>Tipo de documento <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                    <input type="text" id="eu_tipo_display" class="form-input" disabled readonly>
                </div>
                <div class="form-group">
                    <label>Número de documento <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                    <input type="text" id="eu_doc" class="form-input" disabled readonly>
                </div>
                <div class="form-group">
                    <label>Fecha de nacimiento <i class="fa-solid fa-lock text-muted" title="Campo protegido"></i></label>
                    <input type="date" id="eu_fnac" class="form-input" disabled readonly>
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


<div id="modalDonacion" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-box-open"></i> Gestionar Donación</h3>
            <button class="modal-close" onclick="cerrarModal('modalDonacion')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="don_detalle" class="detalle-box"></div>
        <form id="formDonacion" method="POST"
              onsubmit="return validarObservacionRequerida('don_estado','don_obs','don_obs_err')">
            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
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


<div id="modalSolicitud" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-clipboard-list"></i> Gestionar Solicitud</h3>
            <button class="modal-close" onclick="cerrarModal('modalSolicitud')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="sol_detalle" class="detalle-box"></div>
        <form id="formSolicitud" method="POST"
              onsubmit="return validarObservacionRequerida('sol_estado','sol_obs','sol_obs_err')">
            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
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


<div id="modalCrearEvento" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-calendar-plus"></i> Publicar Nuevo Evento</h3>
            <button class="modal-close" onclick="cerrarModal('modalCrearEvento')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?php echo e(route('admin.eventos.crear')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
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


<div id="modalEditarEvento" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3><i class="fa-solid fa-calendar-check"></i> Editar Evento y Publicación</h3>
            <button class="modal-close" onclick="cerrarModal('modalEditarEvento')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="formEditarEvento" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
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


<div id="modalCrearCategoria" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-tags"></i> Nueva Categoría</h3>
            <button class="modal-close" onclick="cerrarModal('modalCrearCategoria')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?php echo e(route('admin.categorias.crear')); ?>" method="POST"
              onsubmit="return validarCategoria('crear')">
            <?php echo csrf_field(); ?>
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


<div id="modalEditarCategoria" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-tag"></i> Editar Categoría</h3>
            <button class="modal-close" onclick="cerrarModal('modalEditarCategoria')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="formEditarCategoria" method="POST"
              onsubmit="return validarCategoria('editar')">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
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


<div id="modalSolicitarCorreccion" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-shield"></i> Solicitar corrección de datos</h3>
            <button class="modal-close" onclick="cerrarModal('modalSolicitarCorreccion')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?php echo e(route('admin.perfil.solicitarCorreccion')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <p class="text-muted" style="margin-top:0;">
                Esta solicitud quedará <b>pendiente</b> hasta que otro administrador la revise y apruebe. No modifica tus datos de inmediato.
            </p>
            <div class="form-group">
                <label>Campo a corregir *</label>
                <select name="campo" class="form-input" required>
                    <option value="">Selecciona el campo</option>
                    <option value="nombre">Nombre completo</option>
                    <option value="tipoDocumento">Tipo de documento</option>
                    <option value="numDocumento">Número de documento</option>
                    <option value="fechaNacimiento">Fecha de nacimiento</option>
                </select>
            </div>
            <div class="form-group">
                <label>Valor correcto propuesto *</label>
                <input type="text" name="valorNuevo" class="form-input" required minlength="1" maxlength="150"
                       placeholder="Escribe el valor correcto">
            </div>
            <div class="form-group">
                <label>Justificación *</label>
                <textarea name="justificacion" class="form-input" rows="3" required minlength="10" maxlength="300"
                          placeholder="Explica por qué necesitas este cambio (ej. error de digitación, actualización de documento, etc.)"></textarea>
            </div>
            <div class="form-group">
                <label>Documento de identidad (foto o PDF) *</label>
                <input type="file" name="soporte" class="form-input" accept=".jpg,.jpeg,.png,.pdf" required>
                <small class="text-muted">Máx. 4MB. Solo lo verá un administrador distinto a ti, y se elimina automáticamente al resolverse la solicitud.</small>
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:flex-start;gap:8px;font-weight:normal;">
                    <input type="checkbox" name="consentimiento" required style="margin-top:3px;">
                    <span>Autorizo el tratamiento de este documento únicamente para verificar mi identidad en esta solicitud, conforme a la Política de Tratamiento de Datos Personales de Donapp.</span>
                </label>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Enviar solicitud</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalSolicitarCorreccion')">Cancelar</button>
            </div>
        </form>
    </div>
</div>


<div id="modalCorreccionUsuario" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-shield"></i> Solicitar corrección para <span id="cu_nombre_usuario"></span></h3>
            <button class="modal-close" onclick="cerrarModal('modalCorreccionUsuario')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="formCorreccionUsuario" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <p class="text-muted" style="margin-top:0;">
                Quedará <b>pendiente</b> hasta que otro administrador la apruebe. Tú, como quien la solicita, no podrás aprobarla.
            </p>
            <div class="form-group">
                <label>Campo a corregir *</label>
                <select name="campo" class="form-input" required>
                    <option value="">Selecciona el campo</option>
                    <option value="nombre">Nombre completo</option>
                    <option value="tipoDocumento">Tipo de documento</option>
                    <option value="numDocumento">Número de documento</option>
                    <option value="fechaNacimiento">Fecha de nacimiento</option>
                </select>
            </div>
            <div class="form-group">
                <label>Valor correcto propuesto *</label>
                <input type="text" name="valorNuevo" class="form-input" required minlength="1" maxlength="150"
                       placeholder="Escribe el valor correcto">
            </div>
            <div class="form-group">
                <label>Justificación *</label>
                <textarea name="justificacion" class="form-input" rows="3" required minlength="10" maxlength="300"
                          placeholder="Explica el motivo del cambio (ej. error de digitación detectado por el equipo)"></textarea>
            </div>
            <div class="form-group">
                <label>Documento de identidad (foto o PDF) *</label>
                <input type="file" name="soporte" class="form-input" accept=".jpg,.jpeg,.png,.pdf" required>
                <small class="text-muted">Máx. 4MB. Se elimina automáticamente al resolverse la solicitud.</small>
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:flex-start;gap:8px;font-weight:normal;">
                    <input type="checkbox" name="consentimiento" required style="margin-top:3px;">
                    <span>Se deja constancia del consentimiento para el tratamiento de este documento con fines de verificación de identidad.</span>
                </label>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Enviar solicitud</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalCorreccionUsuario')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset('assets/js/admin.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/admin_dashboard.js')); ?>"></script>
<script>
// Conectar modales de donación/solicitud a rutas Laravel
const ROUTES = {
    donacion:  (id) => `<?php echo e(url('/admin/donaciones')); ?>/${id}/estado`,
    solicitud: (id) => `<?php echo e(url('/admin/solicitudes')); ?>/${id}/estado`,
    editarUsuario: (id) => `<?php echo e(url('/admin/usuarios')); ?>/${id}`,
    editarCategoria: (id) => `<?php echo e(url('/admin/categorias')); ?>/${id}`,
    editarEvento: (id) => `<?php echo e(url('/admin/eventos')); ?>/${id}`,
    solicitarCorreccionUsuario: (id) => `<?php echo e(url('/admin/usuarios')); ?>/${id}/solicitar-correccion`,
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
// Reescribimos por completo (no delegamos al JS externo) porque los campos
// sensibles ahora son <input disabled> de solo lectura, no <select>/<input> editables.
window.abrirModalEditarUsuario = function(u) {
    document.getElementById('formEditarUsuario').action = ROUTES.editarUsuario(u.idUsuario);
    document.getElementById('eu_id').value          = u.idUsuario;
    document.getElementById('eu_nombre').value       = u.nombre;
    document.getElementById('eu_tipo_display').value = u.tipoDocumento;
    document.getElementById('eu_doc').value          = u.numDocumento;
    document.getElementById('eu_fnac').value         = u.fechaNacimiento;
    document.getElementById('eu_dir').value          = u.direccion;
    document.getElementById('eu_email').value        = u.email;
    document.getElementById('eu_tel').value          = u.telefono;
    document.getElementById('eu_nec').value          = u.necesidad || '';
    document.getElementById('eu_rol').value          = u.rol;
    document.getElementById('eu_estado').value       = u.estado;
    document.getElementById('eu_pass').value         = '';
    document.getElementById('eu_pass2').value        = '';

    const prioEl = document.getElementById('eu_prioridad');
    const obsEl  = document.getElementById('eu_obs_visita');
    if (prioEl) prioEl.value = u.prioridad || '';
    if (obsEl)  obsEl.value  = u.observacion_visita || '';

    toggleCamposDonante('eu_rol', 'grp_eu_prioridad', 'grp_eu_obs');

    // Guardamos el usuario objetivo para el botón "Solicitar corrección"
    window._usuarioEnEdicion = { idUsuario: u.idUsuario, nombre: u.nombre };

    abrirModal('modalEditarUsuario');
};

// Abre el modal de solicitud de corrección para el usuario que se está editando
function abrirModalCorreccionUsuario() {
    const u = window._usuarioEnEdicion;
    if (!u) return;
    document.getElementById('cu_nombre_usuario').textContent = u.nombre;
    document.getElementById('formCorreccionUsuario').action  = ROUTES.solicitarCorreccionUsuario(u.idUsuario);
    document.getElementById('formCorreccionUsuario').reset();
    abrirModal('modalCorreccionUsuario');
}

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

// ══════════ VALIDACIÓN: CATEGORÍAS DUPLICADAS (cliente) ══════════
// El backend SIEMPRE debe validar de nuevo (ver trigger tg_validar_categoria_duplicada);
// esto solo evita un viaje al servidor innecesario y da feedback inmediato.
function categoriasExistentesList() {
    const el = document.getElementById('categoriasExistentes');
    if (!el) return [];
    try { return JSON.parse(el.textContent); } catch (e) { return []; }
}

function nombreCategoriaDuplicado(nombre, idExcluir = null) {
    const normalizado = nombre.trim().toLowerCase();
    return categoriasExistentesList().some(c =>
        c.nombre === normalizado && (idExcluir === null || String(c.id) !== String(idExcluir))
    );
}

const _origValidarCategoria = window.validarCategoria;
window.validarCategoria = function(modo) {
    // Deja que la validación original (longitud, caracteres, etc.) corra primero si existe
    if (typeof _origValidarCategoria === 'function' && _origValidarCategoria(modo) === false) {
        return false;
    }
    const inputId = modo === 'crear' ? 'cat_nombre' : 'ecat_nombre';
    const errId    = modo === 'crear' ? 'cat_err'    : 'ecat_err';
    const input    = document.getElementById(inputId);
    const errEl    = document.getElementById(errId);
    const idExcluir = modo === 'editar' ? document.getElementById('ecat_id').value : null;

    if (!input.value.trim()) {
        errEl.textContent = 'El nombre de la categoría es obligatorio.';
        errEl.style.display = 'block';
        return false;
    }

    if (nombreCategoriaDuplicado(input.value, idExcluir)) {
        errEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Ya existe una categoría con este nombre. Verifica antes de crear una nueva.';
        errEl.style.display = 'block';
        return false;
    }

    errEl.style.display = 'none';
    return true;
};

// ══════════ VALIDACIÓN: EVENTOS SOLO CON FECHAS FUTURAS ══════════
function fechaEsPasada(valorFecha) {
    if (!valorFecha) return false;
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    const fecha = new Date(valorFecha + 'T00:00:00');
    return fecha < hoy;
}

function validarFechaEventoForm(form) {
    const input = form.querySelector('input[name="fecha_entrega"]');
    if (!input) return true;
    if (fechaEsPasada(input.value)) {
        alert('No se pueden programar eventos con fechas pasadas. Selecciona hoy o una fecha futura.');
        input.focus();
        return false;
    }
    return true;
}

document.addEventListener('DOMContentLoaded', function () {
    const hoyStr = new Date().toISOString().split('T')[0];

    // Fecha mínima = hoy en ambos formularios de evento
    const fCrear  = document.querySelector('#modalCrearEvento input[name="fecha_entrega"]');
    const fEditar = document.querySelector('#modalEditarEvento input[name="fecha_entrega"]');
    if (fCrear)  fCrear.min  = hoyStr;
    if (fEditar) fEditar.min = hoyStr;

    const formCrearEvento  = document.querySelector('#modalCrearEvento form');
    const formEditarEvento = document.getElementById('formEditarEvento');
    if (formCrearEvento)  formCrearEvento.addEventListener('submit', (e) => { if (!validarFechaEventoForm(formCrearEvento))  e.preventDefault(); });
    if (formEditarEvento) formEditarEvento.addEventListener('submit', (e) => { if (!validarFechaEventoForm(formEditarEvento)) e.preventDefault(); });
});

// ══════════ VALIDACIÓN: FECHAS DE REPORTES ══════════
// Envuelve las funciones generadoras de PDF (definidas en admin_dashboard.js)
// para asegurar que "desde" <= "hasta" y que ninguna sea futura, antes de generar el reporte.
function rangoFechasValido(desdeId, hastaId) {
    const desdeEl = document.getElementById(desdeId);
    const hastaEl = document.getElementById(hastaId);
    const desde = desdeEl.value;
    const hasta = hastaEl.value;
    const hoyStr = new Date().toISOString().split('T')[0];

    if (desde && desde > hoyStr) {
        alert('La fecha "desde" no puede ser futura.');
        return false;
    }
    if (hasta && hasta > hoyStr) {
        alert('La fecha "hasta" no puede ser futura.');
        return false;
    }
    if (desde && hasta && desde > hasta) {
        alert('La fecha "desde" no puede ser posterior a la fecha "hasta".');
        return false;
    }
    return true;
}

const _origGenerarDonaciones = window.generarReporteDonaciones;
window.generarReporteDonaciones = function () {
    if (!rangoFechasValido('rpt_don_desde', 'rpt_don_hasta')) return;
    if (typeof _origGenerarDonaciones === 'function') _origGenerarDonaciones();
};

const _origGenerarSolicitudes = window.generarReporteSolicitudes;
window.generarReporteSolicitudes = function () {
    if (!rangoFechasValido('rpt_sol_desde', 'rpt_sol_hasta')) return;
    if (typeof _origGenerarSolicitudes === 'function') _origGenerarSolicitudes();
};
document.addEventListener('DOMContentLoaded', function () {
    <?php $__errorArgs = ['nombre_categoria'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        // Si venía del modal de edición (trae idCategoria en el input viejo), reabrimos ese modal
        <?php if(old('idCategoria')): ?>
            document.getElementById('ecat_id').value = '<?php echo e(old('idCategoria')); ?>';
            document.getElementById('ecat_nombre').value = '<?php echo e(old('nombre_categoria')); ?>';
            document.getElementById('formEditarCategoria').action = ROUTES.editarCategoria('<?php echo e(old('idCategoria')); ?>');
            document.getElementById('ecat_err').textContent = <?php echo json_encode($message, 15, 512) ?>;
            document.getElementById('ecat_err').style.display = 'block';
            abrirModal('modalEditarCategoria');
        <?php else: ?>
            document.getElementById('cat_nombre').value = '<?php echo e(old('nombre_categoria')); ?>';
            document.getElementById('cat_err').textContent = <?php echo json_encode($message, 15, 512) ?>;
            document.getElementById('cat_err').style.display = 'block';
            abrirModal('modalCrearCategoria');
        <?php endif; ?>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DonappLaravel\donapp\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>