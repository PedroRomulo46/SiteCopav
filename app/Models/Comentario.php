<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comentario extends Model
{
    protected $table = 'comentarios';

    protected $fillable = [
        'oferta_id',
        'user_id',
        'conteudo',
    ];

    public function oferta(): BelongsTo
    {
        return $this->belongsTo(Oferta::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}