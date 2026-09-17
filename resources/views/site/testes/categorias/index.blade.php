<h1>Lista de Categorias</h1>

@if(session('sucesso'))
    <p>{{ session('sucesso') }}</p>
@endif

<a href="{{ route('categorias.create') }}">
    Cadastrar categoria
</a>

<hr>

@forelse($categorias as $categoria)

    <div>
        <h2>{{ $categoria->nome }}</h2>

        <p>
            {{ $categoria->descricao ?? 'Sem descrição' }}
        </p>

        <a href="{{ route('categorias.show', $categoria) }}">
            Ver
        </a>

        <a href="{{ route('categorias.edit', $categoria) }}">
            Editar
        </a>

        <form
            action="{{ route('categorias.destroy', $categoria) }}"
            method="POST"
            style="display: inline;"
        >
            @csrf
            @method('DELETE')

            <button type="submit">
                Excluir
            </button>
        </form>
    </div>

    <hr>

@empty

    <p>Nenhuma categoria cadastrada.</p>

@endforelse
