<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Negociacao extends Model
{
    protected $table = 'negociacoes';

    protected $fillable = [
        'oferta_id',
        'cliente_id',
        'status',
        'fornecedor_visualizada_em',
        'entrega_confirmada_em',
        'encerrado_em',
        'excluir_mensagens_em',
    ];

    protected function casts(): array
    {
        return [
            'fornecedor_visualizada_em' => 'datetime',
            'entrega_confirmada_em' => 'datetime',
            'encerrado_em' => 'datetime',
            'excluir_mensagens_em' => 'datetime',
        ];
    }

    public function oferta(): BelongsTo
    {
        return $this->belongsTo(Oferta::class, 'oferta_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function propostas(): HasMany
    {
        return $this->hasMany(Proposta::class);
    }

    public function assuntos(): BelongsToMany
    {
        return $this->belongsToMany(
            AssuntoChat::class,
            'assunto_negociacao',
            'negociacao_id',
            'assunto_chat_id'
        )->withTimestamps();
    }
}
