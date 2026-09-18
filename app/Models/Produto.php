<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $fillable = [
        'fornecedor_id',
        'categoria_id',
        'nome',
        'descricao',
        'unidade',
        'imagem',
    ];

    public function fornecedor() {
        return $this->belongsTo(Fornecedor::class); 
    }

    public function categoria() {
        return $this->belongsTo(Categoria::class);
    }

    public function ofertas() {
        return $this->hasMany(Oferta::class);
    }
}
