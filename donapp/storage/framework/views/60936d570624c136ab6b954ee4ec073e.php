<?php $__env->startSection('title', 'Recuperar Contraseña — Donapp'); ?>
<?php $__env->startSection('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/admin_style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/token.css')); ?>">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="recover-card">
    <a href="<?php echo e(route('home')); ?>" class="logo-container">
        <img src="<?php echo e(asset('assets/uploads/Red-Logo.png')); ?>" alt="Logo Donapp" class="form-logo">
    </a>

    <h2><i class="fa-solid fa-lock-open"></i> Recuperar Contraseña</h2>

    <?php if(session('success')): ?>
        <div class="msg-ok"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if($errors->has('msg')): ?>
        <div class="msg-err"><?php echo e($errors->first('msg')); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('recuperar.post')); ?>">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label>Correo electrónico</label>
            <input type="email" name="email" class="form-input" required
                   placeholder="Ingresa tu correo registrado" value="<?php echo e(old('email')); ?>">
        </div>
        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-paper-plane"></i> Enviar instrucciones
        </button>
        <a href="<?php echo e(route('login')); ?>" class="back-link" style="text-decoration:none;">
            ← Volver al inicio de sesión
        </a>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DonappLaravel\donapp\resources\views/auth/recuperar.blade.php ENDPATH**/ ?>