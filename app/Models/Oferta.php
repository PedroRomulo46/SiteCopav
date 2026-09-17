<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Oferta extends Model
{
    protected $fillable = [
        'fornecedor_id',
        'produto_id',
        'quantidade',
        'valor',
        'unidade',
        'localizacao',
        'data_inicio',
        'data_validade',
        'status',
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
        'valor' => 'decimal:2',
        'data_inicio' => 'date',
        'data_validade' => 'date',
    ];

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function negociacoes()
    {
        return $this->hasMany(Negociacao::class);
    }
}