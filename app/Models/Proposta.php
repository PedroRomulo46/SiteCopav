<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proposta extends Model
{
    protected $fillable = [
        'negociacao_id',
        'usuario_id',
        'valor',
        'quantidade',
        'observacao',
        'status',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'quantidade' => 'decimal:2',
    ];

    public function negociacao()
    {
        return $this->belongsTo(Negociacao::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
