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
            'usuario',
            'negociacao.oferta.produto',
            'negociacao.oferta.fornecedor',
        ]);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                'negociacao.' . $this->proposta->negociacao_id
            ),
            new PrivateChannel(
                'oferta.' . $this->proposta->negociacao->oferta_id
            ),
        ];
    }

    public function broadcastAs(): string
    {
        return 'proposta.criada';
    }

    public function broadcastWith(): array
    {
        $proposta = $this->proposta;

        return [
            'proposta' => [
                'id' => $proposta->id,
                'negociacao_id' => $proposta->negociacao_id,
                'oferta_id' => $proposta->negociacao->oferta_id,
                'usuario_id' => $proposta->usuario_id,
                'usuario_nome' => $proposta->usuario->nome ?? 'Usuário',
                'usuario_tipo' => $proposta->usuario->user_type ?? 'cliente',
                'valor' => (float) $proposta->valor,
                'quantidade' => (float) $proposta->quantidade,
                'observacao' => $proposta->observacao,
                'status' => $proposta->status,
                'unidade' => $proposta->negociacao->oferta->unidade ?? '',
            ],
        ];
    }
}
