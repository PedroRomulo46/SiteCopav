<h1>Lista de Ofertas</h1>

@if(session('sucesso'))
    <p>{{ session('sucesso') }}</p>
@endif

<a href="{{ route('ofertas.create') }}">
    Cadastrar oferta
</a>

<hr>

@forelse($ofertas as $oferta)

    <div>

        <h2>
            {{ $oferta->produto->nome }}
        </h2>

        <p>
            <strong>Fornecedor:</strong>
            {{ $oferta->fornecedor->nome }}
        </p>

        <p>
            <strong>Categoria:</strong>
            {{ $oferta->produto->categoria->nome }}
        </p>

        <p>
            <strong>Quantidade:</strong>
            {{ $oferta->quantidade }}
            {{ $oferta->unidade }}
        </p>

        <p>
            <strong>Valor:</strong>
            R$ {{ $oferta->valor }}
        </p>

        <p>
            <strong>Localização:</strong>
            {{ $oferta->localizacao ?? 'Não informada' }}
        </p>

        <p>
            <strong>Status:</strong>
            {{ $oferta->status }}
        </p>

        <a href="{{ route('ofertas.show', $oferta) }}">
            Ver oferta
        </a>

    </div>

    <hr>

@empty

    <p>Nenhuma oferta cadastrada.</p>

@endforelse