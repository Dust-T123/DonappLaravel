<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Donapp'); ?></title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('assets/uploads/Icon.png')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php echo $__env->yieldContent('styles'); ?>
</head>
<body>
    <?php echo $__env->yieldContent('content'); ?>

    <?php if(session('success')): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '<?php echo e(addslashes(session('success'))); ?>',
            confirmButtonColor: '#df0b0b',
            confirmButtonText: 'ACEPTAR',
            customClass: { popup: 'donapp-popup', confirmButton: 'donapp-confirm-btn' }
        });
    </script>
    <?php endif; ?>

    <?php if($errors->any()): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo e(addslashes($errors->first('msg') ?? $errors->first())); ?>',
            confirmButtonColor: '#df0b0b',
            confirmButtonText: 'ENTENDIDO',
        });
    </script>
    <?php endif; ?>

    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\adminsena\Desktop\DonappLaravel\donapp\resources\views/layouts/app.blade.php ENDPATH**/ ?>