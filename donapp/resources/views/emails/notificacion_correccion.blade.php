<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DM Sans', Arial, sans-serif; background: #f8f9fa; }
        .card { max-width: 600px; margin: 40px auto; background: #fff;
                border-radius: 16px; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
        h2   { color: #df0b0b; }
        .badge { display:inline-block; padding:6px 18px; border-radius:999px;
                 color:#fff; font-weight:700; text-transform:uppercase; }
        .badge-aprobada  { background:#1b9e5a; }
        .badge-rechazada { background:#df0b0b; }
        .valor { background:#f4f4f4; border-left:4px solid #df0b0b;
                 padding:12px 16px; border-radius:8px; margin-top:16px; }
        .obs  { background:#fff7e6; border-left:4px solid #e0a800;
                padding:12px 16px; border-radius:8px; margin-top:16px; }
        .footer { margin-top:32px; color:#777; font-size:13px; }
    </style>
</head>
<body>
<div class="card">
    <h2>Hola, {{ $nombreUsuario }}</h2>

    @if($resultado === 'aprobada')
        <p>Tu solicitud de corrección del campo <strong>{{ $campo }}</strong> fue revisada y <strong>aprobada</strong>. El cambio ya se aplicó a tu perfil.</p>
        <p><span class="badge badge-aprobada">Aprobada</span></p>
        @if($valorNuevo)
        <div class="valor">
            <strong>Nuevo valor registrado:</strong> {{ $valorNuevo }}
        </div>
        @endif
    @else
        <p>Tu solicitud de corrección del campo <strong>{{ $campo }}</strong> fue revisada y <strong>no fue aprobada</strong>. Tus datos actuales no cambiaron.</p>
        <p><span class="badge badge-rechazada">Rechazada</span></p>
    @endif

    @if($observacion)
    <div class="obs">
        <strong>Motivo indicado por la fundación:</strong> {{ $observacion }}
    </div>
    @endif

    <div class="footer">
        <p>Si tienes dudas sobre esta resolución, comunícate con la Fundación CES Waldorf.</p>
        <p>Saludos,<br><strong>Equipo Donapp</strong></p>
    </div>
</div>
</body>
</html>
