
<h1>Cadastrar Demanda</h1>

<p>
    Informe o produto que sua empresa está procurando.
</p>

<form action="{{ route('demandas.store') }}" method="POST">
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
        <label>Produto que procura:</label>

        <input
            type="text"
            name="nome_produto"
            value="{{ old('nome_produto') }}"
            placeholder="Ex: Milho"
            required
        >
    </div>

    <br>

    <div>
        <label>Descrição:</label>

        <textarea
            name="descricao"
            placeholder="Descreva o que sua empresa precisa"
        >{{ old('descricao') }}</textarea>
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

    <div>
        <label>Valor máximo que deseja pagar:</label>

        <input
            type="number"
            name="valor_maximo"
            step="0.01"
            min="0"
            value="{{ old('valor_maximo') }}"
            placeholder="Ex: 1200.00"
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
        <label>Data limite:</label>

        <input
            type="date"
            name="data_limite"
            value="{{ old('data_limite') }}"
        >
    </div>

    <br>

    <button type="submit">
        Cadastrar demanda
    </button>
</form>

<br>

<a href="{{ route('demandas.index') }}">
    Voltar
</a>