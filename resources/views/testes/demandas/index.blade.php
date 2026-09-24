<h1>Demandas</h1>

@if(session('sucesso'))
    <p>{{ session('sucesso') }}</p>
@endif

<a href="{{ route('demandas.create') }}">
    Cadastrar nova demanda
</a>

<hr>

@forelse($demandas as $demanda)

    <div>
        <h2>{{ $demanda->nome_produto }}</h2>

        <p>
            <strong>Empresa:</strong>
            {{ $demanda->cliente->nome }}
        </p>

        <p>
            <strong>Categoria:</strong>
            {{ $demanda->categoria->nome }}
        </p>

        <p>
            <strong>Quantidade:</strong>
            {{ $demanda->quantidade }}
            {{ $demanda->unidade }}
        </p>

        <p>
            <strong>Valor máximo:</strong>

            @if($demanda->valor_maximo)
                R$ {{ number_format($demanda->valor_maximo, 2, ',', '.') }}
            @else
                Não informado
            @endif
        </p>

        <p>
            <strong>Localização:</strong>
            {{ $demanda->localizacao ?? 'Não informada' }}
        </p>

        <p>
            <strong>Data limite:</strong>

            @if($demanda->data_limite)
                {{ $demanda->data_limite->format('d/m/Y') }}
            @else
                Não informada
            @endif
        </p>

        <p>
            <strong>Status:</strong>
            {{ $demanda->status }}
        </p>

        <a href="{{ route('demandas.show', $demanda) }}">
            Ver demanda
        </a>
    </div>

    <hr>

@empty

    <p>
        Nenhuma demanda cadastrada.
    </p>

@endforelse