<?php

namespace App\Events;

use App\Models\Negociacao;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NegociacaoCriada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Negociacao $negociacao;

    public function __construct(Negociacao $negociacao)
    {
        $this->negociacao = $negociacao->load([
            'oferta.produto',
            'oferta.fornecedor',
            'cliente',
        ]);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                'fornecedor.' . $this->negociacao->oferta->fornecedor_id
            ),
        ];
    }

    public function broadcastAs(): string
    {
        return 'negociacao.criada';
    }
}