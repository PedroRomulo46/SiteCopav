<?php

namespace App\Events;

use App\Models\Oferta;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfertaAtualizada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Oferta $oferta;

    public function __construct(Oferta $oferta)
    {
        $this->oferta = $oferta->load([
            'produto',
            'fornecedor'
        ]);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('ofertas'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'oferta.atualizada';
    }
}