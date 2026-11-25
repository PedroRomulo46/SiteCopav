<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'nome',
        'email',
        'password',
        'imagem',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function fornecedor()
    {
        return $this->hasOne(Fornecedor::class);
    }

    public function negociacoes()
    {
        return $this->hasMany(Negociacao::class, 'cliente_id');
    }

    public function isAdmin(): bool
    {
        return $this->user_type === 'admin' || (bool) $this->is_admin;
    }

    public function isFornecedor(): bool
    {
        return $this->user_type === 'fornecedor' && $this->fornecedor !== null;
    }

    public function isCliente(): bool
    {
        return ! $this->isAdmin()
            && ! $this->isFornecedor()
            && $this->user_type === 'cliente';
    }

    public function podeVender(): bool
    {
        return $this->isFornecedor();
    }

    public function podeGerenciarDemandas(): bool
    {
        return $this->isFornecedor() || $this->isAdmin();
    }
}