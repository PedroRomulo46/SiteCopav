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
    <strong>Quantidade:</strong>
    {{ $negociacao->oferta->quantidade }}
    {{ $negociacao->oferta->unidade }}
</p>

<p>
    <strong>Valor da oferta:</strong>
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
                <strong>Enviada por:</strong>
                {{ $proposta->usuario->nome }}
            </p>

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

        @if(
            auth()->id() === $negociacao->oferta->fornecedor->user_id &&
            $proposta->status === 'pendente'
        )
            <div class="flex gap-2 mt-3">

                <form
                    action="{{ route('propostas.aceitar', $proposta) }}"
                    method="POST"
                >
                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded"
                    >
                        Aceitar proposta
                    </button>
                </form>

                <form
                    action="{{ route('propostas.recusar', $proposta) }}"
                    method="POST"
                >
                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="bg-red-600 text-white px-4 py-2 rounded"
                    >
                        Recusar proposta
                    </button>
                </form>

            </div>
        @endif

    @endforeach

@else

    <p>
        Nenhuma proposta foi realizada.
    </p>

@endif

<a href="{{ route('propostas.create', $negociacao) }}">
    Fazer proposta
</a>

<br><br>

<a href="{{ route('negociacoes.index') }}">
    Voltar
</a>