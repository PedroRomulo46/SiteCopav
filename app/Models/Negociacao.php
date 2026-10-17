<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Negociacao extends Model
{
    protected $table = 'negociacoes';

    protected $fillable = [
        'oferta_id',
        'cliente_id',
        'status',
        'fornecedor_visualizada_em',
    ];

    public function oferta()
    {
        return $this->belongsTo(Oferta::class, 'oferta_id');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function propostas()
    {
        return $this->hasMany(Proposta::class);
    }

    public function chat(): HasOne
    {
        return $this->hasOne(Chat::class, 'negociacao_id');
    }
}