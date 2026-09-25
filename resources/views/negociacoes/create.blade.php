<h1>Nova Negociação</h1>

<form action="{{ route('negociacoes.store') }}" method="POST">

    @csrf

    <div>
        <label>Oferta:</label>

        <select name="oferta_id" required>

            <option value="">
                Selecione uma oferta
            </option>

            @foreach($ofertas as $oferta)

                <option value="{{ $oferta->id }}">

                    {{ $oferta->produto->nome }}

                    -

                    {{ $oferta->fornecedor->nome }}

                    -

                    R$ {{ $oferta->valor }}

                </option>

            @endforeach

        </select>
    </div>

    <br>

    <div>
        <label>Cliente:</label>

        <select name="cliente_id" required>

            <option value="">
                Selecione o cliente
            </option>

            @foreach($clientes as $cliente)

                <option value="{{ $cliente->id }}">
                    {{ $cliente->nome }}
                </option>

            @endforeach

        </select>
    </div>

    <br>

    <div>
        <label>Status:</label>

        <select name="status" required>

            <option value="pendente">
                Pendente
            </option>

            <option value="em_negociacao">
                Em negociação
            </option>

        </select>
    </div>

    <br>

    <button type="submit">
        Criar negociação
    </button>

</form>

<br>

<a href="{{ route('negociacoes.index') }}">
    Voltar
</a>