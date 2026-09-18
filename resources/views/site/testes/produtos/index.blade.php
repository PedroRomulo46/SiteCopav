<h1>Lista de Produtos</h1>

@if(session('sucesso'))
    <p>{{ session('sucesso') }}</p>
@endif

<a href="{{ route('produtos.create') }}">
    Cadastrar produto
</a>

<hr>

@forelse($produtos as $produto)
    @if($produto->imagem)
        <img
            src="{{ asset('storage/' . $produto->imagem) }}"
            alt="{{ $produto->nome }}"
            width="200"
        >
    @else
        <p>Sem imagem</p>
    @endif

    <div>

        <h2>{{ $produto->nome }}</h2>

        <p>
            <strong>Fornecedor:</strong>
            {{ $produto->fornecedor->nome }}
        </p>

        <p>
            <strong>Categoria:</strong>
            {{ $produto->categoria->nome }}
        </p>

        <p>
            <strong>Descrição:</strong>
            {{ $produto->descricao ?? 'Sem descrição' }}
        </p>

        <p>
            <strong>Unidade:</strong>
            {{ $produto->unidade }}
        </p>

    </div>

    <hr>

@empty

    <p>Nenhum produto cadastrado.</p>

@endforelse