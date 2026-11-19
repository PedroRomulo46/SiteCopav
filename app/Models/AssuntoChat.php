<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssuntoChat extends Model
{
    protected $table = 'assuntos_chat';

    protected $fillable = [
        'chat_id',
        'titulo',
        'cliente_ultima_leitura_id',
        'fornecedor_ultima_leitura_id',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }

    public function mensagens(): HasMany
    {
        return $this->hasMany(Mensagem::class, 'assunto_chat_id');
    }

    public function negociacoes(): BelongsToMany
    {
        return $this->belongsToMany(
            Negociacao::class,
            'assunto_negociacao',
            'assunto_chat_id',
            'negociacao_id'
        )->withTimestamps();
    }
}
