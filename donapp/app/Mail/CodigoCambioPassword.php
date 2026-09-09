<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable: CodigoCambioPassword
 *
 * Se envía cuando un administrador o asistente pide cambiar su propia
 * contraseña desde el panel. El código debe ingresarse para confirmar el
 * cambio, evitando que alguien con la sesión abierta (pero sin acceso al
 * correo) pueda tomar control total de la cuenta.
 */
class CodigoCambioPassword extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombreUsuario,
        public string $codigo
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Donapp – Código para cambiar tu contraseña');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.codigo_cambio_password');
    }
}
