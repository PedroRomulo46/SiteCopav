<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoriaSugestao extends Model
{
    protected $table = 'categoria_sugestoes';

    protected $fillable = [
        'user_id',
        'nome',
        'descricao',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}