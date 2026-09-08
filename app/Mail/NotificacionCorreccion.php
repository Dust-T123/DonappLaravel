<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable: NotificacionCorreccion
 * Notifica al usuario titular de los datos que su solicitud de corrección
 * (nombre, tipo/numero de documento o fecha de nacimiento) fue aprobada o
 * rechazada por un administrador.
 */
class NotificacionCorreccion extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombreUsuario,
        public string $campo,          // 'nombre' | 'tipoDocumento' | 'numDocumento' | 'fechaNacimiento'
        public string $resultado,      // 'aprobada' | 'rechazada'
        public string $valorNuevo = '',
        public string $observacion = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Donapp – Tu solicitud de corrección de datos fue ' . $this->resultado,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notificacion_correccion',
        );
    }
}
