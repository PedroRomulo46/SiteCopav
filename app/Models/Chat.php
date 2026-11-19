<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    protected $fillable = [
    'cliente_id',
    'fornecedor_id',
    'ultima_mensagem_em',
    'cliente_ultima_leitura_id',
    'fornecedor_ultima_leitura_id',
];

    protected function casts(): array
    {
        return [
            'ultima_mensagem_em' => 'datetime',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id');
    }

    public function assuntos(): HasMany
    {
        return $this->hasMany(AssuntoChat::class, 'chat_id');
    }

    public function mensagens(): HasMany
    {
        return $this->hasMany(Mensagem::class, 'chat_id');
    }
    // Compatibilidade temporária com a estrutura antiga.
    public function negociacao(): BelongsTo
    {
        return $this->belongsTo(Negociacao::class, 'negociacao_id');
    }
}
