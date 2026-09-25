<h1>Cadastrar Produto</h1>

<form
    action="{{ route('produtos.store') }}"
    method="POST"
    enctype="multipart/form-data"
>
    @csrf

    <div>
        <label>Categoria:</label>

        <select name="categoria_id" required>
            <option value="">Selecione uma categoria</option>

            @foreach($categorias as $categoria)
                <option
                    value="{{ $categoria->id }}"
                    {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}
                >
                    {{ $categoria->nome }}
                </option>
            @endforeach
        </select>
    </div>

    <br>

    <div>
        <label>Nome:</label>

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
            required
        >
    </div>

    <br>

    <div>
        <label>Imagem:</label>

        <input
            type="file"
            name="imagem"
            accept="image/jpeg,image/png,image/jpg,image/webp"
        >
    </div>

    <br>

    <button type="submit">
        Cadastrar produto
    </button>
</form>