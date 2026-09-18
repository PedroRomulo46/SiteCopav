<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demanda extends Model
{
    protected $fillable = [
        'cliente_id',
        'categoria_id',
        'nome_produto',
        'descricao',
        'quantidade',
        'unidade',
        'valor_maximo',
        'localizacao',
        'data_limite',
        'status',
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
        'valor_maximo' => 'decimal:2',
        'data_limite' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function ofertasDiretas()
    {
        return $this->hasMany(OfertaDireta::class);
    }
}