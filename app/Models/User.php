<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $fillable = [
        'nome',
        'email',
        'password',
        'user_type',
        'imagem',
    ];

        public function fornecedor()
    {
        return $this->hasOne(Fornecedor::class);
    }

    public function demandas()
    {
        return $this->hasMany(Demanda::class, 'cliente_id');
    }

    public function negociacoes()
    {
        return $this->hasMany(Negociacao::class, 'cliente_id');
    }

    public function propostas()
    {
        return $this->hasMany(Proposta::class, 'usuario_id');
    }
}