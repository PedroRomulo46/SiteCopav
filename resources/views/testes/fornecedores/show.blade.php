<h1>Detalhes do Fornecedor</h1>

<hr>

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
    <strong>Descrição:</strong>
    {{ $fornecedor->descricao ?? 'Não informada' }}
</p>

<p>
    <strong>Endereço:</strong>
    {{ $fornecedor->endereco }}
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

<hr>

<a href="{{ route('fornecedores.index') }}">
    Voltar
</a>