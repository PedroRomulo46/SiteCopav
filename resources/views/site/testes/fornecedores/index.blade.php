<h1>Lista de Fornecedores</h1>

@if(session('sucesso'))
    <p>{{ session('sucesso') }}</p>
@endif

<a href="{{ route('fornecedores.create') }}">
    Cadastrar fornecedor
</a>

<hr>

@forelse($fornecedores as $fornecedor)

    <div>

        <h2>{{ $fornecedor->nome }}</h2>

        <p>
            <strong>Documento:</strong>
            {{ $fornecedor->documento }}
        </p>

        <p>
            <strong>Telefone:</strong>
            {{ $fornecedor->telefone }}
        </p>

        <p>
            <strong>Cidade:</strong>
            {{ $fornecedor->cidade }}
        </p>

        <p>
            <strong>Estado:</strong>
            {{ $fornecedor->estado }}
        </p>

        <p>
            <strong>Status:</strong>
            {{ $fornecedor->status }}
        </p>

        <p>
            <strong>Usuário:</strong>
            {{ $fornecedor->usuario->nome }}
        </p>

    </div>

    <hr>

@empty

    <p>Nenhum fornecedor cadastrado.</p>

@endforelse