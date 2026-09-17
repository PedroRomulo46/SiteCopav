<h1>Cadastrar Fornecedor</h1>

<form action="{{ route('fornecedores.store') }}" method="POST">

    @csrf

    <div>
        <label>Usuário:</label>

        <select name="user_id" required>
            <option value="">Selecione o usuário</option>

            @foreach($usuarios as $usuario)
                <option value="{{ $usuario->id }}">
                    {{ $usuario->nome }} - {{ $usuario->email }}
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

    <div>
        <label>Status:</label>

        <select name="status" required>
            <option value="pendente">Pendente</option>
            <option value="ativo">Ativo</option>
        </select>
    </div>

    <br>

    <button type="submit">
        Cadastrar fornecedor
    </button>

</form>

<br>

<a href="{{ route('fornecedores.index') }}">
    Voltar
</a>