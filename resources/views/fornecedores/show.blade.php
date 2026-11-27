@extends('layouts.layout')
@section('title', 'Menu Fornecedor')

@section('conteudo')

@php
    $usuarioAtual = auth()->user();
    $isAdmin = (bool) ($usuarioAtual?->isAdmin());
    $podeGerenciarFornecedor = $usuarioAtual !== null
        && ((int) $usuarioAtual->id === (int) $fornecedor->user_id || $isAdmin);

    $rawFotoFornecedor = $fornecedor->imagem
        ?? $fornecedor->foto
        ?? $fornecedor->avatar
        ?? $fornecedor->usuario?->imagem
        ?? $fornecedor->usuario?->avatar
        ?? null;

    $fotoFornecedorUrl = null;
    if (! empty($rawFotoFornecedor)) {
        if (str_starts_with($rawFotoFornecedor, 'http://') || str_starts_with($rawFotoFornecedor, 'https://')) {
            $fotoFornecedorUrl = $rawFotoFornecedor;
        } elseif (str_contains($rawFotoFornecedor, 'assets/')) {
            $fotoFornecedorUrl = asset($rawFotoFornecedor);
        } else {
            $fotoFornecedorUrl = asset('storage/' . ltrim(str_replace('public/', '', $rawFotoFornecedor), '/'));
        }
    }

    $categoriasDisponiveis = collect($todasCategorias ?? \App\Models\Categoria::orderBy('nome')->get());

    $idsSelecionados = collect($categoriasSelecionadasIds ?? ($fornecedor->relationLoaded('categorias')
        ? $fornecedor->categorias->pluck('id')->all()
        : []))
        ->map(fn ($id) => (int) $id)
        ->all();

    if (empty($idsSelecionados) && $fornecedor->relationLoaded('ofertas')) {
        $idsSelecionados = $fornecedor->ofertas
            ->pluck('produto.categoria.id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    $categoriasMarcadas = $categoriasDisponiveis
        ->filter(fn ($categoria) => in_array((int) $categoria->id, $idsSelecionados, true))
        ->values();

    $ofertasDestaque = collect($principaisOfertas ?? ($fornecedor->relationLoaded('ofertas') ? $fornecedor->ofertas->take(6) : []));
@endphp

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">

        {{-- Item 1: Início --}}
        <li class="inline-flex items-center">
            <a
                href="{{ route('home') }}"
                class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors"
            >
                Início
            </a>
        </li>

        {{-- Item 2: Página Atual --}}
        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="w-full max-w-4xl mx-auto my-6 px-4 space-y-6">

    @if(session('sucesso'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border-l-4 border-emerald-600 dark:border-emerald-400 text-emerald-800 dark:text-emerald-200 rounded-r-md shadow-sm flex items-center gap-2 text-left">
            <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 leading-none shrink-0">check_circle</span>
            <span>{{ session('sucesso') }}</span>
        </div>
    @endif

    {{-- Card Principal --}}
    <div class="w-full bg-white dark:bg-[#111827] rounded-xl shadow-md border border-gray-200 dark:border-[#273449] p-6 sm:p-8 overflow-hidden">

        {{-- Cabeçalho Centralizado com Foto de Perfil --}}
        <div class="flex flex-col items-center justify-center text-center gap-4 mb-6 pb-6 border-b border-gray-200 dark:border-[#273449] w-full">

            {{-- Foto de Perfil do Fornecedor --}}
            <div class="w-24 h-24 rounded-full overflow-hidden bg-emerald-50 dark:bg-emerald-950/60 border-2 border-emerald-200 dark:border-emerald-700/60 shadow-sm flex items-center justify-center shrink-0">
                @if($fotoFornecedorUrl)
                    <img
                        src="{{ $fotoFornecedorUrl }}"
                        alt="Foto de perfil de {{ $fornecedor->nome }}"
                        class="w-full h-full object-cover"
                    >
                @else
                    <span class="material-symbols-outlined text-4xl text-[#236350] dark:text-emerald-400 leading-none">storefront</span>
                @endif
            </div>

            {{-- Título --}}
            <div class="w-full text-center">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100 leading-tight">{{ $fornecedor->nome }}</h1>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Detalhes completos do fornecedor cadastrado</p>
            </div>

            {{-- Botões no Meio --}}
            @if($podeGerenciarFornecedor)
                <div class="flex items-center justify-center gap-3 shrink-0">
                    <a
                        href="{{ route('fornecedores.edit', $fornecedor) }}"
                        class="inline-flex items-center justify-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm"
                    >
                        <span class="material-symbols-outlined text-base leading-none">edit</span>
                        <span>Editar</span>
                    </a>

                    <form action="{{ route('fornecedores.destroy', $fornecedor) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este fornecedor?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-1.5 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm cursor-pointer"
                        >
                            <span class="material-symbols-outlined text-base leading-none">delete</span>
                            <span>Excluir</span>
                        </button>
                    </form>
                </div>
            @endif

        </div>

        {{-- Grid de Informações Organizado --}}
        <div class="space-y-6 text-left">
            <div class="grid grid-cols-1 sm:grid-cols-2 {{ $isAdmin ? 'lg:grid-cols-3' : '' }} gap-6">
                <div>
                    <span class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">Telefone</span>
                    <p class="mt-1.5 text-sm sm:text-base text-gray-900 dark:text-gray-100 font-semibold">{{ $fornecedor->telefone }}</p>
                </div>

                <div>
                    <span class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">Status</span>
                    <div class="mt-1.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full {{ $fornecedor->status === 'ativo' ? 'bg-green-100 dark:bg-emerald-950/60 text-green-800 dark:text-emerald-300 border border-transparent dark:border-emerald-800/60' : 'bg-gray-100 dark:bg-slate-800 text-gray-800 dark:text-gray-300 border border-transparent dark:border-slate-700' }}">
                            <span class="material-symbols-outlined text-sm leading-none">{{ $fornecedor->status === 'ativo' ? 'check_circle' : 'info' }}</span>
                            <span>{{ ucfirst($fornecedor->status) }}</span>
                        </span>
                    </div>
                </div>

                @if($isAdmin)
                    <div>
                        <span class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">Documento</span>
                        <p class="mt-1.5 text-sm sm:text-base text-gray-900 dark:text-gray-100 font-semibold">{{ $fornecedor->documento }}</p>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-5 border-t border-gray-100 dark:border-[#273449]">
                <div>
                    <span class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">Endereço</span>
                    <p class="mt-1.5 text-sm sm:text-base text-gray-800 dark:text-gray-200">{{ $fornecedor->endereco }}</p>
                </div>

                <div>
                    <span class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">Cidade / Estado</span>
                    <p class="mt-1.5 text-sm sm:text-base text-gray-800 dark:text-gray-200">{{ $fornecedor->cidade }} - {{ $fornecedor->estado }}</p>
                </div>
            </div>

            <div class="pt-5 border-t border-gray-100 dark:border-[#273449]">
                <span class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2">Descrição</span>
                <div class="rounded-lg bg-gray-50 dark:bg-[#161f30] border border-gray-200/80 dark:border-[#273449] p-4">
                    <p class="text-sm sm:text-base text-gray-800 dark:text-gray-200 leading-relaxed whitespace-pre-line">
                        {{ $fornecedor->descricao ?? 'Não informada' }}
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- Tags de Categorias em Foco (apenas selecionadas) --}}
    <section class="w-full bg-white dark:bg-[#111827] rounded-xl shadow-md border border-gray-200 dark:border-[#273449] p-6 sm:p-8 text-left">
        <div class="flex items-center justify-between gap-2 mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">
                    Categorias de atuação
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Principais categorias disponíveis no site em que o fornecedor foca suas vendas.
                </p>
            </div>
        </div>

        @if($categoriasMarcadas->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                @foreach($categoriasMarcadas as $categoria)
                    <a
                        href="{{ route('categorias.show', $categoria) }}"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold border transition-colors bg-emerald-50 dark:bg-emerald-950/60 text-[#236350] dark:text-emerald-300 border-emerald-200/80 dark:border-emerald-800/60 hover:bg-emerald-100 dark:hover:bg-emerald-900/60"
                    >
                        <span class="material-symbols-outlined text-sm leading-none">label</span>
                        <span>{{ $categoria->nome }}</span>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Nenhuma categoria selecionada para este fornecedor no momento.
            </p>
        @endif
    </section>

    {{-- Principais Ofertas do Fornecedor --}}
    <section class="w-full bg-white dark:bg-[#111827] rounded-xl shadow-md border border-gray-200 dark:border-[#273449] p-6 sm:p-8 text-left">
        <div class="flex items-center justify-between gap-4 mb-5">
            <div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">
                    Principais ofertas
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Lotes publicados por {{ $fornecedor->nome }}.
                </p>
            </div>
        </div>

        @if($ofertasDestaque->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($ofertasDestaque as $itemOferta)
                    @php
                        $prod = $itemOferta->produto;
                        $imgOferta = ($prod && $prod->imagem)
                            ? (str_contains($prod->imagem, 'assets/')
                                ? asset($prod->imagem)
                                : asset('storage/' . ltrim(str_replace('public/', '', $prod->imagem), '/')))
                            : asset('assets/milho.png');
                    @endphp

                    <a
                        href="{{ route('ofertas.show', $itemOferta) }}"
                        class="group bg-gray-50 dark:bg-[#161f30] rounded-xl border border-gray-200/80 dark:border-[#273449] hover:border-[#236350] dark:hover:border-emerald-500 overflow-hidden shadow-xs hover:shadow-md transition-all flex flex-col"
                    >
                        <div class="h-36 w-full bg-gray-100 dark:bg-slate-800 overflow-hidden">
                            <img
                                src="{{ $imgOferta }}"
                                alt="{{ $prod->nome ?? 'Oferta' }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                onerror="this.onerror=null; this.src='{{ asset('assets/milho.png') }}';"
                            >
                        </div>

                        <div class="p-4 flex flex-col flex-1">
                            <h3 class="font-bold text-gray-900 dark:text-gray-100 line-clamp-1">
                                {{ $prod->nome ?? 'Produto sem nome' }}
                            </h3>

                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Estoque: <strong class="text-gray-700 dark:text-gray-200">{{ $itemOferta->quantidade }} {{ $itemOferta->unidade ?? '' }}</strong>
                            </p>

                            <div class="mt-auto pt-3 flex items-baseline justify-between gap-2">
                                <span class="text-base font-extrabold text-[#236350] dark:text-emerald-400">
                                    R$ {{ number_format((float) ($itemOferta->valor ?? 0), 2, ',', '.') }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    / {{ $itemOferta->unidade ?? 'Unidade' }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Este fornecedor ainda não possui ofertas publicadas.
            </p>
        @endif
    </section>

</div>

@endsection