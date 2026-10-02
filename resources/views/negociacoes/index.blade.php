<h1>Negociações</h1>

@if(session('sucesso'))
    <p>{{ session('sucesso') }}</p>
@endif

@if(auth()->user()->user_type !== 'fornecedor')
    <a href="{{ route('negociacoes.create') }}">
        Nova negociação
    </a>
@endif

<hr>

@forelse($negociacoes as $negociacao)

    <div>

        <h2>
            {{ $negociacao->oferta->produto->nome }}
        </h2>

        <p>
            <strong>Fornecedor:</strong>
            {{ $negociacao->oferta->fornecedor->nome }}
        </p>

        <p>
            <strong>Cliente:</strong>
            {{ $negociacao->cliente->nome }}
        </p>

        <p>
            <strong>Valor da oferta:</strong>
            R$ {{ $negociacao->oferta->valor }}
        </p>

        <p>
            <strong>Status:</strong>
            {{ $negociacao->status }}
        </p>

        <a href="{{ route('negociacoes.show', $negociacao) }}">
            Ver negociação
        </a>

    </div>

    <hr>

@empty

    <p>
        Nenhuma negociação cadastrada.
    </p>

@endforelse