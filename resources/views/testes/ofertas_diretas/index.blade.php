<h1>Ofertas Diretas</h1>

@if(session('sucesso'))
    <p>{{ session('sucesso') }}</p>
@endif

<a href="{{ route('ofertas-diretas.create') }}">
    Fazer nova oferta
</a>

<hr>

@forelse($ofertasDiretas as $oferta)

    <div>
        <h2>
            {{ $oferta->demanda->nome_produto }}
        </h2>

        <p>
            <strong>Empresa:</strong>
            {{ $oferta->demanda->cliente->nome }}
        </p>

        <p>
            <strong>Fornecedor:</strong>
            {{ $oferta->fornecedor->nome }}
        </p>

        <p>
            <strong>Quantidade oferecida:</strong>
            {{ $oferta->quantidade }}
            {{ $oferta->demanda->unidade }}
        </p>

        <p>
            <strong>Valor:</strong>
            R$ {{ number_format($oferta->valor, 2, ',', '.') }}
        </p>

        <p>
            <strong>Status:</strong>
            {{ $oferta->status }}
        </p>

        <a href="{{ route('ofertas-diretas.show', $oferta) }}">
            Ver oferta
        </a>
    </div>

    <hr>

@empty

    <p>
        Nenhuma oferta direta cadastrada.
    </p>

@endforelse