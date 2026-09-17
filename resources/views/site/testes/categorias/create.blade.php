
<h1>Cadastrar Categoria</h1>

<form action="{{ route('categorias.store') }}" method="POST">

    @csrf

    <div>
        <label for="nome">Nome:</label>

        <input
            type="text"
            id="nome"
            name="nome"
            value="{{ old('nome') }}"
            required
        >
    </div>

    <br>

    <div>
        <label for="descricao">Descrição:</label>

        <textarea
            id="descricao"
            name="descricao"
        >{{ old('descricao') }}</textarea>
    </div>

    <br>

    <button type="submit">
        Cadastrar
    </button>

</form>

<br>

<a href="{{ route('categorias.index') }}">
    Voltar para categorias
</a>

