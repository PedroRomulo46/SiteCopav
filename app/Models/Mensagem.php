<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mensagem extends Model
{
    protected $table = 'mensagems';

    protected $fillable = [
        'chat_id',
        'assunto_chat_id',
        'usuario_id',
        'conteudo',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }

    public function assunto(): BelongsTo
    {
        return $this->belongsTo(AssuntoChat::class, 'assunto_chat_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
