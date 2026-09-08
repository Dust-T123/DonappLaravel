<?php $__env->startSection('title', 'Restablecer Contraseña — Donapp'); ?>
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

    <h2><i class="fa-solid fa-key"></i> Nueva Contraseña</h2>

    <?php if($errors->has('msg')): ?>
        <div class="msg-err"><?php echo e($errors->first('msg')); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('restablecer.post')); ?>" onsubmit="return checkPass(this)">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="token" value="<?php echo e($token); ?>">

        <div class="form-group">
            <label>Nueva contraseña</label>
            <input type="password" name="password" id="rp_pass1" class="form-input"
                   required minlength="6" placeholder="Mínimo 6 caracteres">
        </div>
        <div class="form-group">
            <label>Confirmar contraseña</label>
            <input type="password" name="password_confirmation" id="rp_pass2" class="form-input"
                   required minlength="6" placeholder="Repite tu contraseña">
            <small id="rp_match_err" style="color:#c62828;display:none;">Las contraseñas no coinciden.</small>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-floppy-disk"></i> Restablecer contraseña
        </button>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset('assets/js/token.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DonappLaravel\donapp\resources\views/auth/restablecer.blade.php ENDPATH**/ ?>