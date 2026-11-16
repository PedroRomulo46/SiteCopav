<?php

namespace App\Events;

use App\Models\Negociacao;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PagamentoAprovado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $negociacao;

    public function __construct(Negociacao $negociacao)
    {
        $this->negociacao = $negociacao;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('negociacao.' . $this->negociacao->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'pagamento.aprovado';
    }
}
