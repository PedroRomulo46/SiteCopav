<?php

namespace App\Events;

use App\Models\Proposta;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PropostaAtualizada implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Proposta $proposta;

    public function __construct(Proposta $proposta)
    {
        $this->proposta = $proposta->load([
            'negociacao.oferta.produto',
            'negociacao.oferta.fornecedor',
            'negociacao.cliente',
            'usuario',
        ]);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                'negociacao.' . $this->proposta->negociacao_id
            ),
        ];
    }

    public function broadcastAs(): string
    {
        return 'proposta.atualizada';
    }

    public function broadcastWith(): array
    {
        return [
            'proposta' => [
                'id' => $this->proposta->id,
                'status' => $this->proposta->status,
                'negociacao_id' => $this->proposta->negociacao_id,
                'valor' => $this->proposta->valor,
                'quantidade' => $this->proposta->quantidade,
            ],
        ];
    }
}