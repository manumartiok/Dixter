<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mail genérico de formularios de la web (Contacto y Post Venta): una tabla "campo: valor"
 * y, opcionalmente, un archivo adjunto. Responder al mail le contesta a quien completó el formulario.
 */
class FormularioMail extends Mailable
{
    use Queueable, SerializesModels;

    private ?string $rutaAdjunto = null;
    private ?string $nombreAdjunto = null;

    public function __construct(
        public string $asunto,
        public array $campos,
        public ?string $responderA = null,
        ?UploadedFile $adjunto = null,
    ) {
        if ($adjunto) {
            $this->rutaAdjunto = $adjunto->getRealPath();
            $this->nombreAdjunto = $adjunto->getClientOriginalName();
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->asunto . ' - Web Dixter',
            replyTo: $this->responderA ? [new Address($this->responderA)] : [],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.formulario');
    }

    public function attachments(): array
    {
        return $this->rutaAdjunto
            ? [Attachment::fromPath($this->rutaAdjunto)->as($this->nombreAdjunto)]
            : [];
    }
}
