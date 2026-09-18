<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfertaDireta extends Model
{
    protected $table = 'ofertas_diretas';

    protected $fillable = [
        'demanda_id',
        'fornecedor_id',
        'quantidade',
        'valor',
        'observacao',
        'status',
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
        'valor' => 'decimal:2',
    ];

    public function demanda()
    {
        return $this->belongsTo(Demanda::class);
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class);
    }
}