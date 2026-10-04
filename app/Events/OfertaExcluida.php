<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfertaExcluida implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $ofertaId;

    public function __construct(int $ofertaId)
    {
        $this->ofertaId = $ofertaId;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('ofertas'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'oferta.excluida';
    }
}