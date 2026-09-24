<h1>Nova Proposta</h1>

<h2>
    {{ $negociacao->oferta->produto->nome }}
</h2>

<p>
    <strong>Fornecedor:</strong>
    {{ $negociacao->oferta->fornecedor->nome }}
</p>

<p>
    <strong>Valor atual da oferta:</strong>
    R$ {{ $negociacao->oferta->valor }}
</p>

<p>
    <strong>Quantidade disponível:</strong>
    {{ $negociacao->oferta->quantidade }}
    {{ $negociacao->oferta->unidade }}
</p>

<hr>

<form action="{{ route('propostas.store') }}" method="POST">

    @csrf

    <input
        type="hidden"
        name="negociacao_id"
        value="{{ $negociacao->id }}"
    >

    <div>
        <label>Usuário:</label>

        <select name="usuario_id" required>

            <option value="">
                Selecione o usuário
            </option>

            @foreach($usuarios as $usuario)

                <option value="{{ $usuario->id }}">
                    {{ $usuario->nome }}
                    -
                    {{ $usuario->user_type }}
                </option>

            @endforeach

        </select>
    </div>

    <br>

    <div>
        <label>Valor da proposta:</label>

        <input
            type="number"
            name="valor"
            step="0.01"
            min="0"
            value="{{ old('valor') }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Quantidade:</label>

        <input
            type="number"
            name="quantidade"
            step="0.01"
            min="0"
            value="{{ old('quantidade') }}"
        >
    </div>

    <br>

    <div>
        <label>Observação:</label>

        <textarea
            name="observacao"
        >{{ old('observacao') }}</textarea>
    </div>

    <br>

    <div>
        <label>Status:</label>

        <select name="status" required>

            <option value="pendente">
                Pendente
            </option>

        </select>
    </div>

    <br>

    <button type="submit">
        Enviar proposta
    </button>

</form>

<br>

<a href="{{ route('negociacoes.show', $negociacao) }}">
    Voltar para negociação
</a>