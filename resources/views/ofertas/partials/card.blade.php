@php
    $usuarioLogado = auth()->user();

    $podeEditarOferta = $usuarioLogado !== null && (
        $usuarioLogado->isAdmin() ||
        ($usuarioLogado->fornecedor && (int) $usuarioLogado->fornecedor->id === (int) $oferta->fornecedor_id)
    );

    $statusClasses = [
        'rascunho' => 'text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-slate-800 border border-transparent dark:border-slate-700',
        'publicada' => 'text-green-700 dark:text-emerald-300 bg-green-50 dark:bg-emerald-950/60 border border-transparent dark:border-emerald-800/60',
        'encerrada' => 'text-yellow-700 dark:text-amber-300 bg-yellow-50 dark:bg-amber-950/60 border border-transparent dark:border-amber-800/60',
        'cancelada' => 'text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-950/60 border border-transparent dark:border-red-800/60',
    ];

    $statusAtual = strtolower($oferta->status ?? '');
    $statusClass = $statusClasses[$statusAtual] ?? 'text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-slate-800 border border-transparent dark:border-slate-700';

    $imagemProduto = ($oferta->produto && $oferta->produto->imagem)
        ? (str_contains($oferta->produto->imagem, 'assets/')
            ? asset($oferta->produto->imagem)
            : asset('storage/' . ltrim(str_replace('public/', '', $oferta->produto->imagem), '/')))
        : asset('assets/milho.png');
@endphp

<div
    class="bg-white dark:bg-[#111827] rounded-xl shadow-md border border-gray-200 dark:border-[#273449] overflow-hidden hover:shadow-lg transition-all text-left"
    data-oferta-id="{{ $oferta->id }}"
>
    {{-- Imagem do produto --}}
    <div class="w-full h-48 bg-gray-100 dark:bg-slate-800 overflow-hidden">
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
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 line-clamp-1">
            {{ $oferta->produto->nome ?? 'Produto não informado' }}
        </h2>

        {{-- Fornecedor --}}
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">
            {{ $oferta->fornecedor->nome ?? 'Fornecedor não informado' }}
        </p>

        {{-- Informações --}}
        <div class="mt-4 space-y-2 text-sm">

            {{-- Quantidade --}}
            <div class="flex items-center justify-between">
                <span class="text-gray-500 dark:text-gray-400">
                    Quantidade
                </span>

                <span class="font-semibold text-gray-700 dark:text-gray-200">
                    {{ $oferta->quantidade }} {{ $oferta->unidade }}
                </span>
            </div>

            {{-- Valor --}}
            <div class="flex items-center justify-between">
                <span class="text-gray-500 dark:text-gray-400">
                    Valor
                </span>

                <span class="font-bold text-[#236350] dark:text-emerald-400">
                    R$ {{ number_format($oferta->valor ?? 0, 2, ',', '.') }}
                </span>
            </div>

            {{-- Localização --}}
            <div class="flex items-center justify-between gap-2">
                <span class="text-gray-500 dark:text-gray-400 shrink-0">
                    Localização
                </span>

                <span class="font-semibold text-gray-700 dark:text-gray-200 truncate max-w-170px">
                    {{ $oferta->localizacao ?? 'Não informada' }}
                </span>
            </div>
        </div>

        {{-- Botões --}}
        <div class="flex items-center gap-2 mt-5 pt-4 border-t border-gray-100 dark:border-[#273449]">

            <a
                href="{{ route('ofertas.show', $oferta) }}"
                class="btn-copav flex-1 inline-flex items-center justify-center text-center text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
            >
                <span>Ver detalhes</span>
            </a>

            @if($podeEditarOferta)
                <a
                    href="{{ route('ofertas.edit', $oferta) }}"
                    class="inline-flex items-center justify-center w-10 h-10 border border-gray-300 dark:border-[#273449] text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white rounded-lg transition-colors shrink-0"
                    title="Editar"
                    aria-label="Editar oferta"
                >
                    <span class="material-symbols-outlined text-lg leading-none">
                        settings
                    </span>
                </a>
            @endif

        </div>
    </div>
</div>