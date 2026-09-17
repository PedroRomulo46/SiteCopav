<h1>Cadastrar Produto</h1>

<form action="{{ route('produtos.store') }}" method="POST">

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
        <label>Categoria:</label>

        <select name="categoria_id" required>
            <option value="">Selecione a categoria</option>

            @foreach($categorias as $categoria)

                <option value="{{ $categoria->id }}">
                    {{ $categoria->nome }}
                </option>

            @endforeach
        </select>
    </div>

    <br>

    <div>
        <label>Nome do produto:</label>

        <input
            type="text"
            name="nome"
            value="{{ old('nome') }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Descrição:</label>

        <textarea name="descricao">{{ old('descricao') }}</textarea>
    </div>

    <br>

    <div>
        <label>Unidade:</label>

        <input
            type="text"
            name="unidade"
            value="{{ old('unidade') }}"
            placeholder="Ex: kg, saca, tonelada"
            required
        >
    </div>

    <br>

    <button type="submit">
        Cadastrar produto
    </button>

</form>

<br>

<a href="{{ route('produtos.index') }}">
    Voltar
</a>