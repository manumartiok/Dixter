<?php

namespace App\Mail;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Aviso de un pedido según su estado, para el cliente o para la casilla de ventas de Dixter.
 * Textos tomados del documento "Web 2026 Pedidos Detalles" (16/11/2026).
 *
 * Marcadores: {numero} "#00000013", {num} "00000013", {nombre} y {pago}.
 */
class PedidoEstadoMail extends Mailable
{
    use Queueable, SerializesModels;

    private const RECORDATORIO = 'Aquí tenés un recordatorio de lo que pediste:';

    /** estado => [asunto, título, párrafos] del mail al cliente */
    public const CLIENTE = [
        'en_espera' => ['¡Hemos recibido tu pedido en Dixter!', 'Gracias por tu pedido', [
            'Recibimos tu pedido y está actualmente en estado de espera hasta que podamos confirmar que se procesó el pago.',
            self::RECORDATORIO,
        ]],
        'procesando' => ['¡Hemos recibido tu pedido en Dixter!', 'Gracias por tu pedido', [
            'Queríamos avisarte — recibimos tu pedido y ahora lo estamos procesando.',
            self::RECORDATORIO,
        ]],
        'despachado' => ['Tu pedido en Dixter fue despachado', 'Tu pedido está en camino', [
            'Queríamos avisarte — tu pedido {numero} ya fue despachado.',
            self::RECORDATORIO,
        ]],
        'fallido' => ['Tu pedido en Dixter no se realizó correctamente', 'Lo siento, tu pedido no se realizó correctamente', [
            'Desafortunadamente no pudimos completar tu pedido debido a un problema con tu método de pago.',
            'Si querés continuar con la compra, volvé a Dixter y probá otro método de pago.',
            'Los detalles de tu pedido son los siguientes:',
        ]],
        'completado' => ['Tu pedido en Dixter está completo', 'Tu pedido está completo', [
            'Queríamos avisarte — terminamos de procesar tu pedido {numero}. ¡Gracias por elegirnos!',
            self::RECORDATORIO,
        ]],
        'cancelado' => ['Tu pedido en Dixter fue cancelado', 'Tu pedido fue cancelado', [
            'Te confirmamos que tu pedido {numero} fue cancelado.',
            'Los detalles del pedido cancelado son los siguientes:',
        ]],
    ];

    /** estado => [asunto, título, párrafos] del mail a la casilla de ventas */
    public const DIXTER = [
        'en_espera' => ['[Dixter]: Tenés un nuevo pedido: {numero}', 'Nuevo pedido: n.º {num}', [
            'Recibiste un nuevo pedido de {nombre}:',
        ]],
        'fallido' => ['Dixter: El pedido {num} ha fallado', 'Pedido fallido: {numero}', [
            'El pedido {num}, del cliente {nombre} ha dado error al realizar el pago con {pago} en la tienda online.',
        ]],
        'cancelado' => ['Dixter: El pedido {num} fue cancelado', 'Pedido cancelado: {numero}', [
            'El pedido {num}, del cliente {nombre}, fue cancelado {quien}.',
        ]],
    ];

    public string $titulo;
    public array $parrafos;

    public function __construct(
        public Pedido $pedido,
        public string $estado,
        public bool $paraDixter = false,
        public bool $porCliente = false,
    ) {
        [, $titulo, $parrafos] = $this->textos();
        $this->titulo = $this->reemplazar($titulo);
        $this->parrafos = array_map(fn ($p) => $this->reemplazar($p), $parrafos);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address((string) config('mail.from.address'), 'Dixter SA'),
            replyTo: $this->paraDixter && $this->pedido->email ? [new Address($this->pedido->email, (string) $this->pedido->nombre)] : [],
            subject: $this->reemplazar($this->textos()[0]),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.pedido-estado');
    }

    private function textos(): array
    {
        return ($this->paraDixter ? self::DIXTER : self::CLIENTE)[$this->estado];
    }

    private function reemplazar(string $texto): string
    {
        return strtr($texto, [
            '{numero}' => $this->pedido->numero(),
            '{num}' => ltrim($this->pedido->numero(), '#'),
            '{nombre}' => (string) $this->pedido->nombre,
            '{pago}' => $this->pedido->pago_texto,
            '{quien}' => $this->porCliente ? 'por el cliente desde Mi cuenta' : 'desde el administrador',
        ]);
    }
}
