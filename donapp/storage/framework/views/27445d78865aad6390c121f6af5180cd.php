<?php $__env->startSection('title', 'DONAPP — Mi Panel'); ?>
<?php $__env->startSection('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/usuario_style.css')); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="sidebar">
    <div class="sidebar-logo">
        <a href="<?php echo e(route('home')); ?>"><img src="<?php echo e(asset('assets/uploads/Red-Logo.png')); ?>" alt="Donapp" onerror="this.style.display='none'"></a>
        <span class="sidebar-role">Donante / Solicitante</span>
        <div class="sidebar-username"><?php echo e($usuario->nombre); ?></div>
    </div>
    <ul class="nav-menu">
        <li><a href="?tab=inicio"      class="nav-link <?php echo e($tabActivo==='inicio'      ? 'active' : ''); ?>"><i class="fa-solid fa-house"></i><span> Inicio</span></a></li>
        <li><a href="?tab=donaciones"  class="nav-link <?php echo e($tabActivo==='donaciones'  ? 'active' : ''); ?>"><i class="fa-solid fa-box-open"></i><span> Mis Donaciones</span></a></li>
        <li><a href="?tab=solicitudes" class="nav-link <?php echo e($tabActivo==='solicitudes' ? 'active' : ''); ?>"><i class="fa-solid fa-clipboard-list"></i><span> Mis Solicitudes</span></a></li>
        <li><a href="?tab=eventos"     class="nav-link <?php echo e($tabActivo==='eventos'     ? 'active' : ''); ?>"><i class="fa-solid fa-calendar-days"></i><span> Eventos</span></a></li>
        <li><a href="?tab=perfil"      class="nav-link <?php echo e($tabActivo==='perfil'      ? 'active' : ''); ?>"><i class="fa-solid fa-user-gear"></i><span> Mi Perfil</span></a></li>
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
</div>

<main class="main-content">

    
    <div id="inicio" class="tab-pane <?php echo e($tabActivo==='inicio' ? 'active' : ''); ?>">
        <div class="welcome-hero">
            <h1>Hola, <?php echo e(explode(' ', $usuario->nombre)[0]); ?> 👋</h1>
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
                <div><h3><?php echo e($stats['total_don']); ?></h3><p>Total donaciones</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
                <div><h3><?php echo e($stats['don_pendientes']); ?></h3><p>Donaciones pendientes</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
                <div><h3><?php echo e($stats['don_aprobadas']); ?></h3><p>Donaciones aprobadas</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa-solid fa-clipboard-list"></i></div>
                <div><h3><?php echo e($stats['total_sol']); ?></h3><p>Total solicitudes</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                <div><h3><?php echo e($stats['sol_pendientes']); ?></h3><p>Solicitudes pendientes</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa-solid fa-handshake-angle"></i></div>
                <div><h3><?php echo e($stats['sol_aprobadas']); ?></h3><p>Solicitudes aprobadas</p></div>
            </div>
        </div>

        <?php if($eventos->isNotEmpty()): ?>
        <div class="card">
            <div class="card-title"><i class="fa-solid fa-calendar-star"></i> Próximos Eventos Activos</div>
            <div class="eventos-grid">
                <?php $__currentLoopData = $eventos->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="event-card">
                    <?php if($ev->publicacion?->imagen): ?>
                        <img src="<?php echo e($ev->publicacion->imagenBase64()); ?>" alt="Evento" class="event-card-img">
                    <?php else: ?>
                        <div class="event-card-noimg"><i class="fa-solid fa-calendar-days"></i></div>
                    <?php endif; ?>
                    <div class="event-card-body">
                        <h3><?php echo e($ev->Nombre); ?></h3>
                        <?php if($ev->publicacion?->contenido): ?>
                        <p><?php echo e(mb_substr($ev->publicacion->contenido, 0, 100)); ?>...</p>
                        <?php endif; ?>
                        <div class="event-meta">
                            <?php if($ev->programacion?->FechaEntrega): ?>
                            <span><i class="fa-solid fa-calendar"></i> <?php echo e(\Carbon\Carbon::parse($ev->programacion->FechaEntrega)->format('d/m/Y')); ?></span>
                            <?php endif; ?>
                            <?php if($ev->programacion?->Lugar): ?>
                            <span><i class="fa-solid fa-location-dot"></i> <?php echo e(mb_substr($ev->programacion->Lugar, 0, 40)); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php if($eventos->count() > 3): ?>
            <div class="eventos-more">
                <a href="?tab=eventos" class="btn btn-secondary btn-sm">Ver todos los eventos <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    
    <div id="donaciones" class="tab-pane <?php echo e($tabActivo==='donaciones' ? 'active' : ''); ?>">
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
            <form method="GET" action="<?php echo e(route('usuario.dashboard')); ?>" class="filter-bar" id="formFiltrosDon">
                <input type="hidden" name="tab" value="donaciones">
                <input type="text" name="don_buscar" class="form-input search-input"
                       placeholder="🔍 Buscar por descripción..." value="<?php echo e(request('don_buscar')); ?>" maxlength="200"
                       onchange="this.form.submit()">
                <select name="don_estado" class="form-input" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" <?php echo e(request('don_estado')=='pendiente' ? 'selected' : ''); ?>>Pendiente</option>
                    <option value="aprobada"  <?php echo e(request('don_estado')=='aprobada'  ? 'selected' : ''); ?>>Aprobada</option>
                    <option value="rechazada" <?php echo e(request('don_estado')=='rechazada' ? 'selected' : ''); ?>>Rechazada</option>
                </select>
                <select name="don_cat" class="form-input" onchange="this.form.submit()">
                    <option value="0">Todas las categorías</option>
                    <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->idCategoria); ?>" <?php echo e(request('don_cat')==$cat->idCategoria ? 'selected' : ''); ?>><?php echo e($cat->nombre); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <a href="<?php echo e(route('usuario.dashboard', ['tab'=>'donaciones'])); ?>" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-xmark"></i> Limpiar filtros
                </a>
            </form>

            <?php if($misDonaciones->isEmpty()): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fa-solid fa-box-open"></i></div>
                <h3>Sin donaciones registradas</h3>
                <p>Aún no has realizado ninguna donación<?php echo e(request('don_estado') || request('don_cat') ? ' con estos filtros' : ''); ?>.</p>
                <?php if(!request('don_estado') && !request('don_cat')): ?>
                <button class="btn btn-primary" onclick="abrirModal('modalNuevaDonacion')">
                    <i class="fa-solid fa-plus"></i> Hacer mi primera donación
                </button>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead><tr>
                        <th>#</th><th>Descripción</th><th>Categoría</th><th>Stock</th>
                        <th>Estado</th><th>Fecha</th><th>Observación</th><th>Acciones</th>
                    </tr></thead>
                    <tbody>
                        <?php $__currentLoopData = $misDonaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $dJson = json_encode(['idDonacion'=>$d->idDonacion,'descripcion'=>$d->descripcion,'categoria'=>$d->categoria?->nombre??'—','stock'=>$d->stock,'estado'=>$d->estado,'fechaCreacion'=>$d->donantes->first()?->pivot->FechaCreacion??'','observacion'=>$d->observacion??'','imagen'=>$d->imagenBase64()??'','idCategoria'=>$d->idCategoria]); ?>
                        <tr>
                            <td><?php echo e($d->idDonacion); ?></td>
                            <td class="td-desc" title="<?php echo e($d->descripcion); ?>"><?php echo e($d->descripcion); ?></td>
                            <td class="td-cat"><?php echo e($d->categoria?->nombre ?? '—'); ?></td>
                            <td><?php echo e($d->stock); ?></td>
                            <td><span class="badge estado-<?php echo e($d->estado); ?>"><?php echo e($d->estado); ?></span></td>
                            <td><?php echo e($d->donantes->first()?->pivot->FechaCreacion ? \Carbon\Carbon::parse($d->donantes->first()->pivot->FechaCreacion)->format('d/m/Y') : '—'); ?></td>
                            <td class="td-obs"><?php echo e($d->observacion ?? '—'); ?></td>
                            <td class="td-actions">
                                <button onclick='verDetalleDonacion(<?php echo e($dJson); ?>)' class="btn btn-sm btn-secondary" title="Ver detalle">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <?php if($d->estado === 'pendiente'): ?>
                                <button onclick='abrirModalEditarDonacion(<?php echo e($dJson); ?>)' class="btn btn-sm btn-primary" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form action="<?php echo e(route('usuario.donaciones.cancelar', $d->idDonacion)); ?>" method="POST" style="display:inline"
                                      onsubmit="return confirm('¿Cancelar esta donación? Esta acción no se puede deshacer.')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger" title="Cancelar">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div id="solicitudes" class="tab-pane <?php echo e($tabActivo==='solicitudes' ? 'active' : ''); ?>">
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
            <form method="GET" action="<?php echo e(route('usuario.dashboard')); ?>" class="filter-bar" id="formFiltrosSol">
                <input type="hidden" name="tab" value="solicitudes">
                <input type="text" name="sol_buscar" class="form-input search-input"
                       placeholder="🔍 Buscar por descripción..." value="<?php echo e(request('sol_buscar')); ?>" maxlength="200"
                       onchange="this.form.submit()">
                <select name="sol_estado" class="form-input" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" <?php echo e(request('sol_estado')=='pendiente' ? 'selected' : ''); ?>>Pendiente</option>
                    <option value="aprobada"  <?php echo e(request('sol_estado')=='aprobada'  ? 'selected' : ''); ?>>Aprobada</option>
                    <option value="rechazada" <?php echo e(request('sol_estado')=='rechazada' ? 'selected' : ''); ?>>Rechazada</option>
                </select>
                <select name="sol_cat" class="form-input" onchange="this.form.submit()">
                    <option value="0">Todas las categorías</option>
                    <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->idCategoria); ?>" <?php echo e(request('sol_cat')==$cat->idCategoria ? 'selected' : ''); ?>><?php echo e($cat->nombre); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <a href="<?php echo e(route('usuario.dashboard', ['tab'=>'solicitudes'])); ?>" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-xmark"></i> Limpiar filtros
                </a>
            </form>

            <?php if($misSolicitudes->isEmpty()): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fa-solid fa-clipboard-list"></i></div>
                <h3>Sin solicitudes registradas</h3>
                <p>No tienes solicitudes<?php echo e(request('sol_estado') || request('sol_cat') ? ' con estos filtros' : ' registradas aún'); ?>.</p>
                <?php if(!request('sol_estado') && !request('sol_cat')): ?>
                <button class="btn btn-primary" onclick="abrirModal('modalNuevaSolicitud')">
                    <i class="fa-solid fa-plus"></i> Hacer mi primera solicitud
                </button>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead><tr>
                        <th>#</th><th>Descripción</th><th>Categoría</th>
                        <th>Estado</th><th>Fecha</th><th>Observación</th><th>Acciones</th>
                    </tr></thead>
                    <tbody>
                        <?php $__currentLoopData = $misSolicitudes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $sJson = json_encode(['idSolicitud'=>$s->idSolicitud,'descripcion'=>$s->descripcion,'categoria'=>$s->categoria?->nombre??'—','estado'=>$s->estado,'fechaCreacion'=>'','observacion'=>$s->observacion??'','imagen'=>$s->imagenBase64()??'','idCategoria'=>$s->idCategoria]); ?>
                        <tr>
                            <td><?php echo e($s->idSolicitud); ?></td>
                            <td class="td-desc"><?php echo e($s->descripcion); ?></td>
                            <td class="td-cat"><?php echo e($s->categoria?->nombre ?? '—'); ?></td>
                            <td><span class="badge estado-<?php echo e($s->estado); ?>"><?php echo e($s->estado); ?></span></td>
                            <td>—</td>
                            <td class="td-obs"><?php echo e($s->observacion ?? '—'); ?></td>
                            <td class="td-actions">
                                <button onclick='verDetalleSolicitud(<?php echo e($sJson); ?>)' class="btn btn-sm btn-secondary" title="Ver detalle">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <?php if($s->estado === 'pendiente'): ?>
                                <button onclick='abrirModalEditarSolicitud(<?php echo e($sJson); ?>)' class="btn btn-sm btn-primary" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form action="<?php echo e(route('usuario.solicitudes.cancelar', $s->idSolicitud)); ?>" method="POST" style="display:inline"
                                      onsubmit="return confirm('¿Cancelar esta solicitud? Esta acción no se puede deshacer.')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger" title="Cancelar">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div id="eventos" class="tab-pane <?php echo e($tabActivo==='eventos' ? 'active' : ''); ?>">
        <div>
            <h2 class="page-title">Eventos de la Fundación</h2>
            <p class="page-subtitle">Conoce las jornadas de entrega y actividades publicadas.</p>
        </div>
        <?php if($eventos->isEmpty()): ?>
        <div class="card">
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fa-solid fa-calendar-xmark"></i></div>
                <h3>No hay eventos activos</h3>
                <p>Por el momento no hay eventos publicados. ¡Vuelve pronto!</p>
            </div>
        </div>
        <?php else: ?>
        <div class="eventos-grid">
            <?php $__currentLoopData = $eventos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="event-card">
                <?php if($ev->publicacion?->imagen): ?>
                    <img src="<?php echo e($ev->publicacion->imagenBase64()); ?>" alt="Evento" class="event-card-img">
                <?php else: ?>
                    <div class="event-card-noimg"><i class="fa-solid fa-calendar-days"></i></div>
                <?php endif; ?>
                <div class="event-card-body">
                    <h3><?php echo e($ev->Nombre); ?></h3>
                    <?php if($ev->publicacion?->titulo): ?>
                    <p class="event-pub-title"><?php echo e($ev->publicacion->titulo); ?></p>
                    <?php endif; ?>
                    <?php if($ev->publicacion?->contenido): ?>
                    <p><?php echo e($ev->publicacion->contenido); ?></p>
                    <?php endif; ?>
                    <div class="event-meta">
                        <?php if($ev->programacion?->FechaEntrega): ?>
                        <span><i class="fa-solid fa-calendar-check"></i> <?php echo e(\Carbon\Carbon::parse($ev->programacion->FechaEntrega)->format('d \d\e F \d\e Y')); ?></span>
                        <?php endif; ?>
                        <?php if($ev->programacion?->Lugar): ?>
                        <span><i class="fa-solid fa-location-dot"></i> <?php echo e($ev->programacion->Lugar); ?></span>
                        <?php endif; ?>
                        <?php if($ev->publicacion?->fechaPublicacion): ?>
                        <span><i class="fa-solid fa-clock"></i> Publicado: <?php echo e(\Carbon\Carbon::parse($ev->publicacion->fechaPublicacion)->format('d/m/Y')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>

    
    <div id="perfil" class="tab-pane <?php echo e($tabActivo==='perfil' ? 'active' : ''); ?>">
        <h2 class="page-title">Mi Perfil</h2>
        <p class="page-subtitle">Actualiza tu información personal y contraseña.</p>
        <div class="card card-perfil">
            <div class="perfil-header">
                <div class="perfil-avatar"><?php echo e(mb_strtoupper(mb_substr($usuario->nombre, 0, 1))); ?></div>
                <div class="perfil-header-info">
                    <h2><?php echo e($usuario->nombre); ?></h2>
                    <p><i class="fa-solid fa-envelope"></i> <?php echo e($usuario->email); ?></p>
                    <p class="perfil-estado"><span class="badge estado-<?php echo e($usuario->estado); ?>"><?php echo e($usuario->estado); ?></span></p>
                </div>
            </div>
            <form action="<?php echo e(route('usuario.perfil.update')); ?>" method="POST" id="formPerfil">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="form-grid-2">
                    <div class="form-group"><label>Nombre completo *</label>
                        <input type="text" name="nombre" class="form-input" value="<?php echo e($usuario->nombre); ?>" required minlength="3" maxlength="100" placeholder="Nombres y apellidos" oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]/g,'')"></div>
                    <div class="form-group"><label>Tipo de documento *</label>
                        <select name="tipoDocumento" class="form-input">
                            <?php $__currentLoopData = ['CC','TI','CE','PEP']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($t); ?>" <?php echo e($usuario->tipoDocumento==$t?'selected':''); ?>><?php echo e($t); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select></div>
                    <div class="form-group"><label>Número de documento *</label>
                        <input type="text" name="numDocumento" class="form-input" value="<?php echo e($usuario->numDocumento); ?>" required maxlength="15" pattern="[0-9]{4,15}" oninput="this.value=this.value.replace(/\D/g,'').slice(0,15)"></div>
                    <div class="form-group"><label>Fecha de nacimiento *</label>
                        <input type="date" name="fechaNacimiento" class="form-input" value="<?php echo e($usuario->fechaNacimiento); ?>" required max="<?php echo e(date('Y-m-d', strtotime('-5 years'))); ?>"></div>
                    <div class="form-group"><label>Teléfono *</label>
                        <input type="tel" name="telefono" class="form-input" value="<?php echo e($usuario->telefono); ?>" required pattern="[0-9]{10}" maxlength="10" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)"></div>
                    <div class="form-group"><label>Dirección *</label>
                        <input type="text" name="direccion" class="form-input" value="<?php echo e($usuario->direccion); ?>" required minlength="5" maxlength="255"></div>
                    <div class="form-group form-group-full"><label>Email *</label>
                        <input type="email" name="email" class="form-input" value="<?php echo e($usuario->email); ?>" required maxlength="150"></div>
                    <div class="form-group form-group-full"><label>Necesidad <small class="text-muted">(describe tu situación)</small></label>
                        <textarea name="necesidad" class="form-input" maxlength="255" rows="3" placeholder="Describe brevemente tu necesidad..."><?php echo e($usuario->necesidad); ?></textarea></div>
                </div>
                <hr class="section-divider">
                <p class="hint-text"><i class="fa-solid fa-lock"></i> Cambiar contraseña — deja vacío para no cambiar</p>
                <div class="form-group"><label>Contraseña actual <small class="text-muted">(requerida para cambiar)</small></label>
                    <div class="pass-wrap">
                        <input type="password" name="password_actual" id="perfil_pass_actual" class="form-input" maxlength="30" placeholder="Ingresa tu contraseña actual para confirmar el cambio">
                        <button type="button" class="eye-btn" onclick="togglePass('perfil_pass_actual','eye_actual')"><i class="fa-solid fa-eye" id="eye_actual"></i></button>
                    </div>
                    <p class="hint-text">¿No recuerdas tu contraseña? <a href="<?php echo e(route('recuperar')); ?>" class="link-primary">Recupérala aquí</a></p>
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
    </div>

</main>


<div id="modalNuevaDonacion" class="modal"><div class="modal-content">
    <div class="modal-header"><h3><i class="fa-solid fa-box-open"></i> Registrar Donación</h3>
        <button class="modal-close" onclick="cerrarModal('modalNuevaDonacion')"><i class="fa-solid fa-xmark"></i></button></div>
    <form action="<?php echo e(route('usuario.donaciones.crear')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="form-group"><label>Descripción del artículo *</label>
            <textarea name="descripcion" class="form-input" required maxlength="200" rows="3" placeholder="Describe el artículo que deseas donar..."></textarea></div>
        <div class="form-grid-2">
            <div class="form-group"><label>Categoría *</label>
                <select name="idCategoria" class="form-input" required>
                    <option value="">Selecciona una categoría</option>
                    <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($cat->idCategoria); ?>"><?php echo e($cat->nombre); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <input type="hidden" name="idDonacion" id="ed_id">
        <div class="form-group"><label>Descripción *</label>
            <textarea name="descripcion" id="ed_desc" class="form-input" required maxlength="200" rows="3"></textarea></div>
        <div class="form-grid-2">
            <div class="form-group"><label>Categoría *</label>
                <select name="idCategoria" id="ed_cat" class="form-input" required>
                    <option value="">Selecciona una categoría</option>
                    <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($cat->idCategoria); ?>"><?php echo e($cat->nombre); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

<div id="modalNuevaSolicitud" class="modal"><div class="modal-content">
    <div class="modal-header"><h3><i class="fa-solid fa-clipboard-list"></i> Registrar Solicitud</h3>
        <button class="modal-close" onclick="cerrarModal('modalNuevaSolicitud')"><i class="fa-solid fa-xmark"></i></button></div>
    <form action="<?php echo e(route('usuario.solicitudes.crear')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="form-group"><label>Descripción de la solicitud *</label>
            <textarea name="descripcion" class="form-input" required maxlength="300" rows="3" placeholder="Describe qué necesitas..."></textarea></div>
        <div class="form-group"><label>Categoría *</label>
            <select name="idCategoria" class="form-input" required>
                <option value="">Selecciona la categoría de tu necesidad</option>
                <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($cat->idCategoria); ?>"><?php echo e($cat->nombre); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select></div>
        <div class="form-group"><label>Imagen de soporte <small class="text-muted">(opcional)</small></label>
            <input type="file" name="imagen" class="form-input" accept="image/*" onchange="previewImg(this,'prev_sol')">
            <img id="prev_sol" src="" alt="" class="img-file-preview" style="display:none;">
            <p class="form-hint"><i class="fa-solid fa-circle-info"></i> Puedes adjuntar una foto que respalde tu solicitud.</p></div>
        <div class="modal-footer">
            <button type="submit" name="crear_solicitud" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Enviar Solicitud</button>
            <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalNuevaSolicitud')">Cancelar</button>
        </div>
    </form>
</div></div>

<div id="modalEditarSolicitud" class="modal"><div class="modal-content">
    <div class="modal-header"><h3><i class="fa-solid fa-pen"></i> Editar Solicitud</h3>
        <button class="modal-close" onclick="cerrarModal('modalEditarSolicitud')"><i class="fa-solid fa-xmark"></i></button></div>
    <form id="formEditarSolicitud" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <input type="hidden" name="idSolicitud" id="es_id">
        <div class="form-group"><label>Descripción *</label>
            <textarea name="descripcion" id="es_desc" class="form-input" required maxlength="300" rows="3"></textarea></div>
        <div class="form-group"><label>Categoría *</label>
            <select name="idCategoria" id="es_cat" class="form-input" required>
                <option value="">Selecciona una categoría</option>
                <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($cat->idCategoria); ?>"><?php echo e($cat->nombre); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select></div>
        <div class="form-group"><label>Nueva imagen <small class="text-muted">(deja vacío para mantener la actual)</small></label>
            <input type="file" name="imagen" class="form-input" accept="image/*" onchange="previewImg(this,'prev_ed_sol')">
            <img id="prev_ed_sol" src="" alt="" class="img-file-preview" style="display:none;"></div>
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

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset('assets/js/usuario.js')); ?>"></script>
<script>
// Conectar formularios de edición con rutas Laravel
const _origEditDon = window.abrirModalEditarDonacion;
window.abrirModalEditarDonacion = function(d) {
    if(_origEditDon) _origEditDon(d);
    document.getElementById('formEditarDonacion').action = `<?php echo e(url('/usuario/donaciones')); ?>/${d.idDonacion}`;
    document.getElementById('ed_id').value = d.idDonacion;
};
const _origEditSol = window.abrirModalEditarSolicitud;
window.abrirModalEditarSolicitud = function(s) {
    if(_origEditSol) _origEditSol(s);
    document.getElementById('formEditarSolicitud').action = `<?php echo e(url('/usuario/solicitudes')); ?>/${s.idSolicitud}`;
    document.getElementById('es_id').value = s.idSolicitud;
};
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DONAPP_Laravel11\donapp\resources\views/user/dashboard.blade.php ENDPATH**/ ?>