{{-- Partial reutilizable: campos comunes del formulario de usuario --}}
{{-- Variables esperadas: $modo ('crear'|'editar'), $mostrarRolEstado (bool) --}}

<div class="form-grid-2">
    <div class="form-group">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-input" required minlength="3"
               value="{{ old('nombre', $usuario->nombre ?? '') }}">
    </div>
    <div class="form-group">
        <label>Apellido</label>
        <input type="text" name="apellido" class="form-input" required minlength="2"
               value="{{ old('apellido', $usuario->apellido ?? '') }}">
    </div>
    <div class="form-group">
        <label>Tipo Documento</label>
        <select name="tipoDocumento" class="form-input" required>
            @foreach(['CC'=>'Cédula de Ciudadanía','TI'=>'Tarjeta de Identidad','CE'=>'Cédula de Extranjería','TE'=>'Tarjeta de Extranjería','PPT'=>'Permiso por Protección Temporal','Pasaporte'=>'Pasaporte'] as $t => $label)
                <option value="{{ $t }}" {{ old('tipoDocumento', $usuario->tipoDocumento ?? '') == $t ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label>N° Documento</label>
        <input type="text" name="numDocumento" class="form-input" required maxlength="15"
               value="{{ old('numDocumento', $usuario->numDocumento ?? '') }}">
    </div>
    <div class="form-group">
        <label>Fecha Nacimiento</label>
        <input type="date" name="fechaNacimiento" class="form-input" required
               max="{{ now()->subYears(14)->format('Y-m-d') }}"
               value="{{ old('fechaNacimiento', $usuario->fechaNacimiento ?? '') }}">
        <p class="form-hint"><i class="fa-solid fa-circle-info"></i> Debe tener al menos 14 años.</p>
    </div>
    <div class="form-group" style="grid-column:1/-1">
        <label>Dirección</label>
        <input type="text" name="direccion" class="form-input" required
               value="{{ old('direccion', $usuario->direccion ?? '') }}">
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-input" required
               value="{{ old('email', $usuario->email ?? '') }}">
    </div>
    <div class="form-group">
        <label>Teléfono</label>
        <input type="text" name="telefono" class="form-input" required maxlength="10"
               value="{{ old('telefono', $usuario->telefono ?? '') }}">
    </div>

    @if($mostrarRolEstado ?? false)
    <div class="form-group">
        <label>Rol</label>
        <select name="rol" class="form-input" required>
            @foreach(['donante','asistente','administrador'] as $r)
                <option value="{{ $r }}" {{ old('rol', $usuario->rol ?? 'donante') == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label>Estado</label>
        <select name="estado" class="form-input" required>
            <option value="activo"   {{ old('estado', $usuario->estado ?? 'activo') == 'activo'   ? 'selected' : '' }}>Activo</option>
            <option value="inactivo" {{ old('estado', $usuario->estado ?? '') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
        </select>
    </div>
    @endif

    <div class="form-group" style="grid-column:1/-1">
        <label>Necesidad</label>
        <textarea name="necesidad" class="form-input" rows="2">{{ old('necesidad', $usuario->necesidad ?? '') }}</textarea>
    </div>
    <div class="form-group">
        <label>Prioridad</label>
        <select name="prioridad" class="form-input">
            <option value="">— Sin prioridad —</option>
            @foreach(['urgente','alta','media','baja'] as $p)
                <option value="{{ $p }}" {{ old('prioridad', $usuario->prioridad ?? '') == $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label>Observación visita</label>
        <input type="text" name="observacion_visita" class="form-input"
               value="{{ old('observacion_visita', $usuario->observacion_visita ?? '') }}">
    </div>
    <div class="form-group">
        <label>Contraseña {{ $modo === 'editar' ? '(dejar vacío para no cambiar)' : '' }}</label>
        <input type="password" name="password" class="form-input"
               {{ $modo === 'crear' ? 'required' : '' }} minlength="6" placeholder="Contraseña">
    </div>
    <div class="form-group">
        <label>Confirmar contraseña</label>
        <input type="password" name="password_confirmation" class="form-input"
               {{ $modo === 'crear' ? 'required' : '' }} minlength="6" placeholder="Confirmar">
    </div>
</div>
