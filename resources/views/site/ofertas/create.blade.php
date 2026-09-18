<h1>Cadastrar Oferta</h1>

<form action="{{ route('ofertas.store') }}" method="POST">

    @csrf

    <div>
        <label>Fornecedor:</label>

        <select name="fornecedor_id" required>
            <option value="">Selecione o fornecedor</option>

            @foreach($fornecedores as $fornecedor)

                <option value="{{ $fornecedor->id }}">
                    {{ $fornecedor->nome }}
                </option>

            @endforeach
        </select>
    </div>

    <br>

    <div>
        <label>Produto:</label>

        <select name="produto_id" required>
            <option value="">Selecione o produto</option>

            @foreach($produtos as $produto)

                <option value="{{ $produto->id }}">
                    {{ $produto->nome }}
                    -
                    {{ $produto->categoria->nome }}
                </option>

            @endforeach
        </select>
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
            required
        >
    </div>

    <br>

    <div>
        <label>Valor:</label>

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
        <label>Unidade:</label>

        <input
            type="text"
            name="unidade"
            placeholder="Ex: kg, saca, tonelada"
            value="{{ old('unidade') }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Localização:</label>

        <input
            type="text"
            name="localizacao"
            value="{{ old('localizacao') }}"
            placeholder="Ex: Icapuí - CE"
        >
    </div>

    <br>

    <div>
        <label>Data de início:</label>

        <input
            type="date"
            name="data_inicio"
            value="{{ old('data_inicio') }}"
        >
    </div>

    <br>

    <div>
        <label>Data de validade:</label>

        <input
            type="date"
            name="data_validade"
            value="{{ old('data_validade') }}"
        >
    </div>

    <br>

    <div>
        <label>Status:</label>

        <select name="status" required>

            <option value="rascunho">
                Rascunho
            </option>

            <option value="publicada">
                Publicada
            </option>

        </select>
    </div>

    <br>

    <button type="submit">
        Cadastrar oferta
    </button>

</form>

<br>

<a href="{{ route('ofertas.index') }}">
    Voltar
</a>