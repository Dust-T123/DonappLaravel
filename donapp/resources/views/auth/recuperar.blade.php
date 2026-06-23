@extends('layouts.app')
@section('title', 'Recuperar Contraseña — Donapp')
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin_style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/token.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
@endsection

@section('content')
<div class="recover-card">
    <a href="{{ route('home') }}" class="logo-container">
        <img src="{{ asset('assets/uploads/Red-Logo.png') }}" alt="Logo Donapp" class="form-logo">
    </a>

    <h2><i class="fa-solid fa-lock-open"></i> Recuperar Contraseña</h2>

    @if(session('success'))
        <div class="msg-ok">{{ session('success') }}</div>
    @endif
    @if($errors->has('msg'))
        <div class="msg-err">{{ $errors->first('msg') }}</div>
    @endif

    <form method="POST" action="{{ route('recuperar.post') }}">
        @csrf
        <div class="form-group">
            <label>Correo electrónico</label>
            <input type="email" name="email" class="form-input" required
                   placeholder="Ingresa tu correo registrado" value="{{ old('email') }}">
        </div>
        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-paper-plane"></i> Enviar instrucciones
        </button>
        <a href="{{ route('login') }}" class="back-link" style="text-decoration:none;">
            ← Volver al inicio de sesión
        </a>
    </form>
</div>
@endsection
