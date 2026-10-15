<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    protected $fillable = [
        'negociacao_id',
        'entrega_confirmada_em',
        'ultima_mensagem_em',
        'encerrado_em',
        'excluir_mensagens_em',
    ];

    protected function casts(): array
    {
        return [
            'entrega_confirmada_em' => 'datetime',
            'ultima_mensagem_em' => 'datetime',
            'encerrado_em' => 'datetime',
            'excluir_mensagens_em' => 'datetime',
        ];
    }

    public function negociacao(): BelongsTo
    {
        return $this->belongsTo(Negociacao::class);
    }

    public function mensagens(): HasMany
    {
        return $this->hasMany(Mensagem::class, 'chat_id');
    }
}
