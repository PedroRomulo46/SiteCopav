<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('fornecedor.{fornecedorId}', function (User $user, $fornecedorId) {
    return $user->fornecedor
        && (int) $user->fornecedor->id === (int) $fornecedorId;
});