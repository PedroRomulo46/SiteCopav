<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Fornecedor;
use App\Models\AssuntoChat;
use App\Models\Negociacao;

class Chat extends Model
{
    protected $fillable = [
        'cliente_id',
        'fornecedor_id',
        'ultima_mensagem_em',
        'cliente_ultima_leitura_id',
        'fornecedor_ultima_leitura_id',

        // Controle das mensagens gerais
        'mensagens_gerais_status',
        'mensagens_gerais_solicitado_por',
        'mensagens_gerais_solicitada_em',
        'mensagens_gerais_aprovado_por',
        'mensagens_gerais_ativada_em',
    ];

    protected function casts(): array
    {
        return [
            'ultima_mensagem_em' => 'datetime',
            'mensagens_gerais_solicitada_em' => 'datetime',
            'mensagens_gerais_ativada_em' => 'datetime',
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

    public function solicitanteMensagensGerais(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'mensagens_gerais_solicitado_por'
        );
    }

    public function aprovadorMensagensGerais(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'mensagens_gerais_aprovado_por'
        );
    }

    // Compatibilidade temporária com a estrutura antiga.
    public function negociacao(): BelongsTo
    {
        return $this->belongsTo(Negociacao::class, 'negociacao_id');
    }
}
