<?php

use App\Models\User;
use App\Models\Chat;
use App\Models\Negociacao;
use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('fornecedor.{fornecedorId}', function (User $user, $fornecedorId) {
    return $user->fornecedor
        && (int) $user->fornecedor->id === (int) $fornecedorId;
});

Broadcast::channel('oferta.{ofertaId}', function (User $user, $ofertaId) {
    $oferta = \App\Models\Oferta::find($ofertaId);

    if (!$oferta) {
        return false;
    }

    if ($user->user_type === 'admin') {
        return true;
    }

    return $user->fornecedor
        && (int) $oferta->fornecedor_id === (int) $user->fornecedor->id;
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


// Chat — autorização pela conversa entre cliente e fornecedor
Broadcast::channel('chat.{chatId}', function (User $user, int $chatId) {
    $chat = Chat::find($chatId);

    if (!$chat) {
        return false;
    }

    // Administrador pode acompanhar qualquer conversa
    if ($user->user_type === 'admin') {
        return true;
    }

    // Cliente participante
    if ((int) $chat->cliente_id === (int) $user->id) {
        return true;
    }

    // Fornecedor participante
    return $user->fornecedor
        && (int) $chat->fornecedor_id === (int) $user->fornecedor->id;
});