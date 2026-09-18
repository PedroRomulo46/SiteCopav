<h1>Detalhes da Demanda</h1>

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
    <strong>Descrição:</strong>
    {{ $demanda->descricao ?? 'Sem descrição' }}
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

<hr>

<h2>Ofertas dos fornecedores</h2>

@if($demanda->ofertasDiretas->count())

    @foreach($demanda->ofertasDiretas as $oferta)

        <div>
            <p>
                <strong>Fornecedor:</strong>
                {{ $oferta->fornecedor->nome }}
            </p>

            <p>
                <strong>Quantidade:</strong>
                {{ $oferta->quantidade }}
                {{ $demanda->unidade }}
            </p>

            <p>
                <strong>Valor:</strong>
                R$ {{ number_format($oferta->valor, 2, ',', '.') }}
            </p>

            <p>
                <strong>Observação:</strong>
                {{ $oferta->observacao ?? 'Sem observação' }}
            </p>

            <p>
                <strong>Status:</strong>
                {{ $oferta->status }}
            </p>
        </div>

        <hr>

    @endforeach

@else

    <p>
        Nenhum fornecedor fez uma oferta ainda.
    </p>

@endif

<a href="{{ route('ofertas-diretas.create') }}">
    Fazer oferta para esta demanda
</a>

<br><br>

<a href="{{ route('demandas.index') }}">
    Voltar
</a>