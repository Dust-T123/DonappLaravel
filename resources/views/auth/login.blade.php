@extends('layouts.app')
@section('title', 'Iniciar Sesión | Donapp')
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/StylesIniciarS.css') }}">
@endsection

@section('content')

<div class="caja-exterior">
    <a href="{{ route('home') }}" class="logo-container">
        <img src="{{ asset('assets/uploads/Red-Logo.png') }}" alt="Logo Donapp" class="form-logo">
    </a>

    <h2>¡Bienvenido de nuevo!</h2>
    <p class="subtitle">Ingresa tus credenciales para continuar</p>

    <form action="{{ route('login.post') }}" method="POST" class="form-container">
        @csrf

        <div class="input-group">
            <label for="usuario">
                <i class="fa-solid fa-envelope"></i> Correo Electrónico
            </label>
            <input type="email" name="email" id="usuario"
                   placeholder="Ingresa tu correo registrado"
                   value="{{ old('email') }}" required maxlength="150">
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
            <a href="{{ route('recuperar') }}" class="forgot-pass">¿Olvidaste tu contraseña?</a>
        </div>

        <button type="submit" class="boton-moderno">
            <span>Ingresar</span>
            <i class="fa-solid fa-arrow-right"></i>
        </button>

        <div class="auth-links">
            <p>¿No tienes cuenta? <a href="{{ route('registro') }}">Regístrate aquí</a></p>
        </div>
    </form>
</div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/login.js') }}"></script>
@endsection