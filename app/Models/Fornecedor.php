<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    protected $fillable = [ 
        'user_id', 
        'nome', 
        'documento', 
        'telefone', 
        'descricao', 
        'endereco', 
        'cidade', 
        'estado', 
        'status', 
    ]; 
    
    public function usuario() { 
        return $this->belongsTo(User::class, 'user_id');
    } 
    
    public function produtos() { 
        return $this->hasMany(Produto::class); 
    } 
    
    public function ofertas() { 
        return $this->hasMany(Oferta::class); 
    }
}
