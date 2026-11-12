@extends('layouts.layout')

@section('title', 'Pagamento')

@section('conteudo')

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ambiente de Teste - Pix Falso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

    <div class="card shadow p-4 text-center" style="max-width: 400px; width: 100%;">
        <span class="badge bg-warning text-dark mb-2">MODO DE TESTE (SIMULADOR)</span>
        <h4 class="mb-3">Pagamento via Pix</h4>
        <p class="text-muted mb-1">Pedido ID: <strong>{{ $pedidoId }}</strong></p>
        <h3 class="text-success mb-4">R$ {{ number_format($valor, 2, ',', '.') }}</h3>

        <!-- Imagem ilustrativa de QR Code (padrão placeholder) -->
        <div class="mb-3">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=PixDeTesteFalso" alt="QR Code Falso" class="img-thumbnail">
        </div>

        <p class="small text-muted">Como você está em ambiente de desenvolvimento, clique no botão abaixo para simular que o cliente pagou pelo app do banco.</p>

        <!-- Formulário que simula a aprovação do pagamento -->
        <form action="{{ route('pagamento.fake.aprovar', $pedidoId) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success w-100 py-2">
                Simular Pagamento Aprovado 🚀
            </button>
        </form>

        <a href="{{ url()->previous() }}" class="btn btn-link text-secondary mt-3">Cancelar / Voltar</a>
    </div>

</body>
</html>
@endsection