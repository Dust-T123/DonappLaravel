<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable: RecuperacionContrasena
 */
class RecuperacionContrasena extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombreUsuario,
        public string $link
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Donapp – Recuperación de contraseña');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.recuperacion_contrasena');
    }
}
