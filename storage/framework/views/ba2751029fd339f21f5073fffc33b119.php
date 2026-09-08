<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DM Sans', Arial, sans-serif; background: #f8f9fa; }
        .card { max-width: 600px; margin: 40px auto; background: #fff;
                border-radius: 16px; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
        h2   { color: #0B5AA6; }
        .badge { display:inline-block; padding:6px 18px; border-radius:999px;
                 background:#0B5AA6; color:#fff; font-weight:700; text-transform:uppercase; }
        .obs  { background:#f4f4f4; border-left:4px solid #0B5AA6;
                padding:12px 16px; border-radius:8px; margin-top:16px; }
        .footer { margin-top:32px; color:#777; font-size:13px; }
    </style>
</head>
<body>
<div class="card">
    <h2>Hola, <?php echo e($nombreUsuario); ?></h2>
    <p>El estado de tu <strong><?php echo e($tipo); ?></strong> ha sido actualizado:</p>
    <p><span class="badge"><?php echo e(strtoupper($nuevoEstado)); ?></span></p>

    <?php if($observacion): ?>
    <div class="obs">
        <strong>Observación:</strong> <?php echo e($observacion); ?>

    </div>
    <?php endif; ?>

    <div class="footer">
        <p>Si tienes dudas, comunícate con la Fundación CES Waldorf.</p>
        <p>Saludos,<br><strong>Equipo Donapp</strong></p>
    </div>
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\DonappLaravel\resources\views/emails/notificacion_estado.blade.php ENDPATH**/ ?>