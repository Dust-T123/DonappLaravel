<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Donapp')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/uploads/Icon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @yield('styles')
</head>
<body>
    @yield('content')

    @if(session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ addslashes(session('success')) }}',
            confirmButtonColor: '#df0b0b',
            confirmButtonText: 'ACEPTAR',
            customClass: { popup: 'donapp-popup', confirmButton: 'donapp-confirm-btn' }
        });
    </script>
    @endif

    @if($errors->any())
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ addslashes($errors->first('msg') ?? $errors->first()) }}',
            confirmButtonColor: '#df0b0b',
            confirmButtonText: 'ENTENDIDO',
        });
    </script>
    @endif

    @yield('scripts')
</body>
</html>
