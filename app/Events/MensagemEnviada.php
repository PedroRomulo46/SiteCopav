<?php

namespace App\Events;

use App\Models\Mensagem;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MensagemEnviada implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Mensagem $mensagem
    ) {
        $this->mensagem->loadMissing('usuario', 'chat');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                'chat.' . $this->mensagem->chat_id
            ),
        ];
    }

    public function broadcastAs(): string
    {
        return 'mensagem.enviada';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->mensagem->id,
            'chat_id' => $this->mensagem->chat_id,
            'usuario_id' => $this->mensagem->usuario_id,
            'nome' => $this->mensagem->usuario->nome ?? 'Participante',
            'conteudo' => $this->mensagem->conteudo,
            'created_at' => $this->mensagem->created_at
                ->format('d/m/Y H:i'),
        ];
    }
}
