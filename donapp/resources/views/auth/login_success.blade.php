<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Donapp</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/uploads/Icon.png') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/StylesIniciarS.css') }}">
    <style>body { background: #f0f0f0; }</style>
</head>
<body>
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Bienvenido!',
        text: 'Hola {{ $nombre }}, redirigiéndote a tu panel...',
        confirmButtonColor: '#df0b0b',
        confirmButtonText: 'ENTENDIDO',
        allowOutsideClick: false,
        allowEscapeKey: false,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '{{ $ruta }}';
        }
    });
</script>
</body>
</html>