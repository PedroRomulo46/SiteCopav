<h1>Detalhes da Oferta Direta</h1>

<h2>
    {{ $ofertaDireta->demanda->nome_produto }}
</h2>

<hr>

<h3>Demanda da empresa</h3>

<p>
    <strong>Empresa:</strong>
    {{ $ofertaDireta->demanda->cliente->nome }}
</p>

<p>
    <strong>Categoria:</strong>
    {{ $ofertaDireta->demanda->categoria->nome }}
</p>

<p>
    <strong>Quantidade solicitada:</strong>
    {{ $ofertaDireta->demanda->quantidade }}
    {{ $ofertaDireta->demanda->unidade }}
</p>

<p>
    <strong>Valor máximo:</strong>

    @if($ofertaDireta->demanda->valor_maximo)
        R$
        {{ number_format($ofertaDireta->demanda->valor_maximo, 2, ',', '.') }}
    @else
        Não informado
    @endif
</p>

<hr>

<h3>Oferta do fornecedor</h3>

<p>
    <strong>Fornecedor:</strong>
    {{ $ofertaDireta->fornecedor->nome }}
</p>

<p>
    <strong>Quantidade oferecida:</strong>
    {{ $ofertaDireta->quantidade }}
    {{ $ofertaDireta->demanda->unidade }}
</p>

<p>
    <strong>Valor:</strong>
    R$ {{ number_format($ofertaDireta->valor, 2, ',', '.') }}
</p>

<p>
    <strong>Observação:</strong>
    {{ $ofertaDireta->observacao ?? 'Sem observação' }}
</p>

<p>
    <strong>Status:</strong>
    {{ $ofertaDireta->status }}
</p>

<hr>

<a href="{{ route('ofertas-diretas.index') }}">
    Voltar
</a>

<br><br>

<a href="{{ route('demandas.show', $ofertaDireta->demanda) }}">
    Voltar para a demanda
</a>    