<h1>Editar Fornecedor</h1>

<form action="{{ route('fornecedores.update', $fornecedor) }}" method="POST">
    @csrf
    @method('PUT')

    <div>
        <label>Nome:</label>
        <input
            type="text"
            name="nome"
            value="{{ old('nome', $fornecedor->nome) }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Documento:</label>
        <input
            type="text"
            name="documento"
            value="{{ old('documento', $fornecedor->documento) }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Telefone:</label>
        <input
            type="text"
            name="telefone"
            value="{{ old('telefone', $fornecedor->telefone) }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Descrição:</label>
        <textarea name="descricao">{{ old('descricao', $fornecedor->descricao) }}</textarea>
    </div>

    <br>

    <div>
        <label>Endereço:</label>
        <input
            type="text"
            name="endereco"
            value="{{ old('endereco', $fornecedor->endereco) }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Cidade:</label>
        <input
            type="text"
            name="cidade"
            value="{{ old('cidade', $fornecedor->cidade) }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Estado:</label>
        <input
            type="text"
            name="estado"
            maxlength="2"
            value="{{ old('estado', $fornecedor->estado) }}"
            required
        >
    </div>

    <br>

    <button type="submit">
        Salvar alterações
    </button>
</form>

<br>

<a href="{{ route('fornecedores.show', $fornecedor) }}">
    Cancelar
</a>