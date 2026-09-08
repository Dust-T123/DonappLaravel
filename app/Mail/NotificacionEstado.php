<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable: NotificacionEstado
 * Notifica a un usuario el cambio de estado de su donación o solicitud.
 */
class NotificacionEstado extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombreUsuario,
        public string $tipo,          // 'donación' | 'solicitud'
        public string $nuevoEstado,
        public string $observacion = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Donapp – Actualización de tu ' . $this->tipo,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notificacion_estado',
        );
    }
}
