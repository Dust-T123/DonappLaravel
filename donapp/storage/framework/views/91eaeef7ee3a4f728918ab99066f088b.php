<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Donapp</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('assets/uploads/Icon.png')); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/StylesIniciarS.css')); ?>">
    <style>body { background: #f0f0f0; }</style>
</head>
<body>
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Bienvenido!',
        text: 'Hola <?php echo e($nombre); ?>, redirigiéndote a tu panel...',
        confirmButtonColor: '#df0b0b',
        confirmButtonText: 'ENTENDIDO',
        allowOutsideClick: false,
        allowEscapeKey: false,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?php echo e($ruta); ?>';
        }
    });
</script>
</body>
</html><?php /**PATH C:\xampp\htdocs\DONAPP_Laravel11\donapp\resources\views/auth/login_success.blade.php ENDPATH**/ ?>