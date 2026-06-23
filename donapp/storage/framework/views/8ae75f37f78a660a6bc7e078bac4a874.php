<?php $__env->startSection('title', 'Iniciar Sesión | Donapp'); ?>
<?php $__env->startSection('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/StylesIniciarS.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="caja-exterior">
    <a href="<?php echo e(route('home')); ?>" class="logo-container">
        <img src="<?php echo e(asset('assets/uploads/Red-Logo.png')); ?>" alt="Logo Donapp" class="form-logo">
    </a>

    <h2>¡Bienvenido de nuevo!</h2>
    <p class="subtitle">Ingresa tus credenciales para continuar</p>

    <form action="<?php echo e(route('login.post')); ?>" method="POST" class="form-container">
        <?php echo csrf_field(); ?>

        <div class="input-group">
            <label for="usuario">
                <i class="fa-solid fa-envelope"></i> Correo Electrónico
            </label>
            <input type="email" name="email" id="usuario"
                   placeholder="Ingresa tu correo registrado"
                   value="<?php echo e(old('email')); ?>" required maxlength="150">
        </div>

        <div class="input-group">
            <label for="contrasena">
                <i class="fa-solid fa-lock"></i> Contraseña
            </label>
            <div class="password-wrapper">
                <input type="password" name="contrasena" id="contrasena"
                       placeholder="Ingresa tu contraseña" required maxlength="30">
                <button type="button" id="togglePassword" class="eye-btn">
                    <i class="fa-solid fa-eye" id="eyeIcon"></i>
                </button>
            </div>
            <a href="<?php echo e(route('recuperar')); ?>" class="forgot-pass">¿Olvidaste tu contraseña?</a>
        </div>

        <button type="submit" class="boton-moderno">
            <span>Ingresar</span>
            <i class="fa-solid fa-arrow-right"></i>
        </button>

        <div class="auth-links">
            <p>¿No tienes cuenta? <a href="<?php echo e(route('registro')); ?>">Regístrate aquí</a></p>
        </div>
    </form>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/login.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\DONAPP_Laravel11\donapp\resources\views/auth/login.blade.php ENDPATH**/ ?>