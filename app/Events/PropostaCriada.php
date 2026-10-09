<?php

namespace App\Events;

use App\Models\Proposta;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PropostaCriada implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Proposta $proposta;

    public function __construct(Proposta $proposta)
    {
        $this->proposta = $proposta->load([
            'negociacao.oferta.produto',
            'negociacao.oferta.fornecedor',
            'usuario',
        ]);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                'fornecedor.' .
                $this->proposta->negociacao->oferta->fornecedor_id
            ),
        ];
    }

    public function broadcastAs(): string
    {
        return 'proposta.criada';
    }
}