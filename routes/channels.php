<?php

use App\Models\User;
use App\Models\Negociacao;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('fornecedor.{fornecedorId}', function (User $user, $fornecedorId) {
    return $user->fornecedor
        && (int) $user->fornecedor->id === (int) $fornecedorId;
});

Broadcast::channel('negociacao.{negociacaoId}', function (User $user, $negociacaoId) {
    $negociacao = Negociacao::with('oferta')->find($negociacaoId);

    if (!$negociacao) {
        return false;
    }

    // Administrador pode acompanhar qualquer negociação
    if ($user->user_type === 'admin') {
        return true;
    }

    // Cliente que iniciou a negociação
    if ((int) $negociacao->cliente_id === (int) $user->id) {
        return true;
    }

    // Fornecedor dono da oferta
    return $user->fornecedor
        && (int) $negociacao->oferta->fornecedor_id
            === (int) $user->fornecedor->id;
});