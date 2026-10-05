<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\PropostaCriada;

class Proposta extends Model
{
    protected $fillable = [
        'negociacao_id',
        'usuario_id',
        'valor',
        'quantidade',
        'observacao',
        'status',
        'visualizada_em',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'quantidade' => 'decimal:2',
        'visualizada_em' => 'datetime',
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