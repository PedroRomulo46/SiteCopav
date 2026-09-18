<h1>Cadastrar-se como Fornecedor</h1>

<form action="{{ route('fornecedores.store') }}" method="POST">
    @csrf

    <div>
        <label>Nome da empresa:</label>
        <input
            type="text"
            name="nome"
            value="{{ old('nome') }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Documento:</label>
        <input
            type="text"
            name="documento"
            value="{{ old('documento') }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Telefone:</label>
        <input
            type="text"
            name="telefone"
            value="{{ old('telefone') }}"
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
        <label>Endereço:</label>
        <input
            type="text"
            name="endereco"
            value="{{ old('endereco') }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Cidade:</label>
        <input
            type="text"
            name="cidade"
            value="{{ old('cidade') }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Estado:</label>
        <input
            type="text"
            name="estado"
            value="{{ old('estado') }}"
            maxlength="2"
            required
        >
    </div>

    <br>

    <button type="submit">
        Cadastrar como fornecedor
    </button>
</form>

<br>

<a href="{{ route('dashboard') }}">
    Voltar
</a>