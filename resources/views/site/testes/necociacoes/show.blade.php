<h1>Detalhes da Negociação</h1>

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
    <strong>Quantidade da oferta:</strong>
    {{ $negociacao->oferta->quantidade }}
    {{ $negociacao->oferta->unidade }}
</p>

<p>
    <strong>Valor:</strong>
    R$ {{ $negociacao->oferta->valor }}
</p>

<p>
    <strong>Status:</strong>
    {{ $negociacao->status }}
</p>

<hr>

<h2>Propostas</h2>

@if($negociacao->propostas->count())

    @foreach($negociacao->propostas as $proposta)

        <div>

            <p>
                <strong>Valor:</strong>
                R$ {{ $proposta->valor }}
            </p>

            <p>
                <strong>Quantidade:</strong>
                {{ $proposta->quantidade ?? 'Não informada' }}
            </p>

            <p>
                <strong>Observação:</strong>
                {{ $proposta->observacao ?? 'Sem observação' }}
            </p>

            <p>
                <strong>Status:</strong>
                {{ $proposta->status }}
            </p>

        </div>

        <hr>

    @endforeach

@else

    <p>Nenhuma proposta foi realizada.</p>

@endif

<a href="{{ route('negociacoes.index') }}">
    Voltar
</a>
