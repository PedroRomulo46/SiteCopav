<h1>Negociações</h1>

@if(session('sucesso'))
    <p>{{ session('sucesso') }}</p>
@endif

<a href="{{ route('negociacoes.create') }}">
    Nova negociação
</a>

<hr>

@forelse($negociacoes as $negociacao)

    <div>
        <h2>
            {{ $negociacao->oferta->produto->nome }}
        </h2>

        <p>
            Fornecedor:
            {{ $negociacao->oferta->fornecedor->nome }}
        </p>

        <p>
            Cliente:
            {{ $negociacao->cliente->nome }}
        </p>

        <p>
            Status:
            {{ $negociacao->status }}
        </p>

        <a href="{{ route('negociacoes.show', $negociacao) }}">
            Ver negociação
        </a>
    </div>

    <hr>

@empty

    <p>Nenhuma negociação cadastrada.</p>

@endforelse
