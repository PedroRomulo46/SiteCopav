@php
    $usuarioLogado = auth()->user();

    $podeEditarOferta = $usuarioLogado !== null && (
        $usuarioLogado->isAdmin() ||
        ($usuarioLogado->fornecedor && (int) $usuarioLogado->fornecedor->id === (int) $oferta->fornecedor_id)
    );

    $statusClasses = [
        'rascunho' => 'text-gray-600 bg-gray-100',
        'publicada' => 'text-green-700 bg-green-50',
        'encerrada' => 'text-yellow-700 bg-yellow-50',
        'cancelada' => 'text-red-700 bg-red-50',
    ];

    $statusAtual = strtolower($oferta->status ?? '');
    $statusClass = $statusClasses[$statusAtual] ?? 'text-gray-600 bg-gray-100';

    $imagemProduto = ($oferta->produto && $oferta->produto->imagem)
        ? (str_contains($oferta->produto->imagem, 'assets/')
            ? asset($oferta->produto->imagem)
            : asset('storage/' . ltrim(str_replace('public/', '', $oferta->produto->imagem), '/')))
        : asset('assets/milho.png');
@endphp

<div
    class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow"
    data-oferta-id="{{ $oferta->id }}"
>
    {{-- Imagem do produto --}}
    <div class="w-full h-48 bg-gray-100 overflow-hidden">
        <img
            src="{{ $imagemProduto }}"
            alt="{{ $oferta->produto->nome ?? 'Produto' }}"
            class="w-full h-full object-cover"
            onerror="this.onerror=null; this.src='{{ asset('assets/milho.png') }}';"
        >
    </div>

    {{-- Conteúdo --}}
    <div class="p-4">

        {{-- Status --}}
        <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full mb-2 {{ $statusClass }}">
            {{ ucfirst($oferta->status ?? 'Indefinido') }}
        </span>

        {{-- Produto --}}
        <h2 class="text-lg font-bold text-gray-800 line-clamp-1">
            {{ $oferta->produto->nome ?? 'Produto não informado' }}
        </h2>

        {{-- Fornecedor --}}
        <p class="text-sm text-gray-500 mt-1 line-clamp-1">
            {{ $oferta->fornecedor->nome ?? 'Fornecedor não informado' }}
        </p>

        {{-- Informações --}}
        <div class="mt-4 space-y-2 text-sm">

            {{-- Quantidade --}}
            <div class="flex items-center justify-between">
                <span class="text-gray-500">
                    Quantidade
                </span>

                <span class="font-semibold text-gray-700">
                    {{ $oferta->quantidade }} {{ $oferta->unidade }}
                </span>
            </div>

            {{-- Valor --}}
            <div class="flex items-center justify-between">
                <span class="text-gray-500">
                    Valor
                </span>

                <span class="font-bold text-[#236350]">
                    R$ {{ number_format($oferta->valor ?? 0, 2, ',', '.') }}
                </span>
            </div>

            {{-- Localização --}}
            <div class="flex items-center justify-between gap-2">
                <span class="text-gray-500">
                    Localização
                </span>

                <span class="font-semibold text-gray-700 truncate max-w-170px">
                    {{ $oferta->localizacao ?? 'Não informada' }}
                </span>
            </div>
        </div>

        {{-- Botões --}}
        <div class="flex gap-2 mt-5 pt-4 border-t border-gray-100">

            <a
                href="{{ route('ofertas.show', $oferta) }}"
                class="btn-copav flex-1 flex items-center justify-center text-center text-white px-3 py-2 rounded-md text-sm font-semibold transition-colors"
            >
                Ver detalhes
            </a>

            @if($podeEditarOferta)
                <a
                    href="{{ route('ofertas.edit', $oferta) }}"
                    class="flex items-center justify-center w-10 h-10 border border-gray-300 text-gray-600 hover:bg-gray-100 rounded-md transition-colors"
                    title="Editar"
                    aria-label="Editar oferta"
                >
                    <span class="material-symbols-outlined text-lg">
                        settings
                    </span>
                </a>
            @endif

        </div>
    </div>
</div>