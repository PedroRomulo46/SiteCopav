<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MensagensGeraisAtualizadas implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Chat $chat
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->chat->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'mensagens.gerais.atualizadas';
    }

    public function broadcastWith(): array
    {
        return [
            'chat_id' => $this->chat->id,
            'status' => $this->chat->mensagens_gerais_status,
            'solicitado_por' => $this->chat->mensagens_gerais_solicitado_por,
            'solicitada_em' => $this->chat->mensagens_gerais_solicitada_em?->toISOString(),
            'aprovado_por' => $this->chat->mensagens_gerais_aprovado_por,
            'ativada_em' => $this->chat->mensagens_gerais_ativada_em?->toISOString(),
        ];
    }
}
