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
            'usuario',
            'negociacao.oferta',
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
        return 'proposta.atualizada';
    }

    public function broadcastWith(): array
    {
        return [
            'proposta' => [
                'id' => $this->proposta->id,
                'negociacao_id' => $this->proposta->negociacao_id,
                'oferta_id' => $this->proposta->negociacao->oferta_id,
                'usuario_id' => $this->proposta->usuario_id,
                'usuario_nome' => $this->proposta->usuario->nome ?? 'Usuário',
                'usuario_tipo' => $this->proposta->usuario->user_type ?? 'cliente',
                'valor' => (float) $this->proposta->valor,
                'quantidade' => (float) $this->proposta->quantidade,
                'observacao' => $this->proposta->observacao,
                'status' => $this->proposta->status,
                'unidade' => $this->proposta->negociacao->oferta->unidade ?? '',
            ],
        ];
    }
}
