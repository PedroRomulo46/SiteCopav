<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function fornecedor() {
            return $this->hasOne(Fornecedor::class);
    }

    public function negociacoes() {
            return $this->hasMany(Negociacao::class, 'cliente_id');
    }

    public function propostas() {
            return $this->hasMany(Proposta::class, 'usuario_id');
        }
    
    protected $fillable = [
        'nome',
        'email',
        'password',
        'user_type',
    ];
}
