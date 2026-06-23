@extends('layouts.app')
@section('title', 'Restablecer Contraseña — Donapp')
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

    <h2><i class="fa-solid fa-key"></i> Nueva Contraseña</h2>

    @if($errors->has('msg'))
        <div class="msg-err">{{ $errors->first('msg') }}</div>
    @endif

    <form method="POST" action="{{ route('restablecer.post') }}" onsubmit="return checkPass(this)">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

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
@endsection
@section('scripts')
<script src="{{ asset('assets/js/token.js') }}"></script>
@endsection
