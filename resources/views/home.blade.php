@extends('layouts.layout')

@section('title', 'Home')

@section('conteudo')

<style>
    [x-cloak] { display: none !important; }

    .copav-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }

    .copav-scrollbar::-webkit-scrollbar { width: 6px; }

    .copav-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 20px;
    }

    @keyframes copav-pulse {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(251, 191, 36, .35);
        }

        50% {
            box-shadow: 0 0 0 5px rgba(251, 191, 36, 0);
        }
    }

    .proposta-nova {
        animation: copav-pulse 1.8s infinite;
    }
</style>

@php
    $usuarioLogado = auth()->user();

    $isAdmin = auth()->check() && $usuarioLogado->isAdmin();
    $isFornecedor = auth()->check() && $usuarioLogado->isFornecedor();
    $isCliente = auth()->check() && $usuarioLogado->isCliente();

    $hasGestaoPanel = $isFornecedor || $isAdmin;

    $listaOfertas = $ofertas ?? collect();
    $listaDemandas = $demandas ?? collect();
    $minhasDemandas = $usuarioLogado
        ? $listaDemandas->filter(fn ($demanda) => (int) ($demanda->cliente_id ?? 0) === (int) $usuarioLogado->id)->values()
        : collect();
    $listaProdutos = $produtos ?? collect();
    $totalNovasPropostas = $novasPropostas ?? 0;
@endphp

<div
    x-data="{
        abaAtiva: '{{ $hasGestaoPanel ? 'lotes' : 'demandas' }}',
        ordenarPor: 'recentes'
    }"
    class="container-home w-full max-w-7xl mx-auto px-3 sm:px-5 lg:px-6 py-5 space-y-7 min-h-screen">

    {{-- Painel de gestão --}}
    @auth
        @if($hasGestaoPanel)
            <section class="bg-[#123228] dark:bg-[#0f172a] border border-transparent dark:border-[#1e293b] text-white p-4 sm:p-6 rounded-2xl shadow-md">

                {{-- Cabeçalho do painel --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4 pb-4 border-b border-emerald-800 dark:border-[#1e293b]">
                    <div>
                        @if($isFornecedor)
                            <h1 class="text-lg sm:text-2xl font-bold mt-1">
                                Gestão de Ofertas
                            </h1>

                            <p class="text-sm text-emerald-50 dark:text-slate-300 mt-1">
                                Gerencie suas ofertas, acompanhe propostas e encontre oportunidades.
                            </p>
                        @elseif($isAdmin)
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-50 dark:text-emerald-300">
                                Painel administrativo
                            </span>

                            <h1 class="text-xl sm:text-2xl font-extrabold mt-1">
                                Gestão do marketplace
                            </h1>

                            <p class="text-xs sm:text-sm text-emerald-50 dark:text-slate-300 mt-1">
                                Acompanhe as demandas e oportunidades do marketplace.
                            </p>
                        @endif
                    </div>

                    @if($isFornecedor)
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('ofertas.create') }}"
                               class="btn-copav text-white text-sm px-4 py-2.5 rounded-lg font-bold flex items-center justify-center gap-1.5 shadow-sm active:scale-95 transition-colors">
                                <span class="material-symbols-outlined text-lg">add</span>
                                Nova Oferta
                            </a>

                            <a href="{{ route('produtos.index') }}"
                               class="btn-copav text-white text-sm px-4 py-2.5 rounded-lg font-bold flex items-center justify-center gap-1.5 transition-colors">
                                <span class="material-symbols-outlined text-lg">inventory_2</span>
                                Meus Produtos
                            </a>
                        </div>
                    @endif
                </div>

                <div class="bg-white dark:bg-[#111827] border border-transparent dark:border-[#273449] rounded-xl p-3 sm:p-4 text-gray-800 dark:text-gray-100 shadow-inner">

                    {{-- Abas do painel --}}
                    <div role="tablist"
                         aria-label="Painel de gestão"
                         class="flex flex-wrap items-center gap-4 sm:gap-6 border-b border-gray-200 dark:border-[#273449] pb-3 mb-3 text-sm font-bold">

                        <button type="button"
                            role="tab"
                            @click="abaAtiva = 'lotes'"
                            :aria-selected="abaAtiva === 'lotes'"
                            :class="abaAtiva === 'lotes' ? 'text-[#236350] dark:text-emerald-400 border-b-2 border-[#236350] dark:border-emerald-400' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300'"
                            class="relative pb-2 transition-colors">

                            {{ $isFornecedor ? 'Minhas Ofertas' : 'Lotes da empresa' }}

                            <span id="indicador-novas-propostas"
                                class="ml-1.5 inline-flex items-center justify-center rounded-full bg-amber-400 text-gray-900 text-[10px] font-extrabold min-w-5 h-5 px-1"
                                style="{{ $totalNovasPropostas > 0 ? 'display: inline-flex;' : 'display: none;' }}"
                                title="{{ $totalNovasPropostas }} {{ $totalNovasPropostas === 1 ? 'nova proposta' : 'novas propostas' }}">

                                <span id="contador-novas-propostas">
                                    {{ $totalNovasPropostas }}
                                </span>
                            </span>
                        </button>

                        <button type="button"
                            role="tab"
                            @click="abaAtiva = 'demandas'"
                            :aria-selected="abaAtiva === 'demandas'"
                            :class="abaAtiva === 'demandas' ? 'text-[#236350] dark:text-emerald-400 border-b-2 border-[#236350] dark:border-emerald-400' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300'"
                            class="pb-2 transition-colors">

                            Minhas Demandas

                            @if($minhasDemandas->count() > 0)
                                <span class="ml-1 text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-full px-1.5 py-0.5">
                                    {{ $minhasDemandas->count() }}
                                </span>
                            @endif
                        </button>
                    </div>

                    {{-- Aba de ofertas --}}
                    <div x-show="abaAtiva === 'lotes'" x-cloak class="space-y-2">

                        @forelse($listaOfertas->take(3) as $oferta)
                            @php
                                $quantidadeNovas = isset($ofertasComNovasPropostas)
                                    ? (int) $ofertasComNovasPropostas->get($oferta->id, 0)
                                    : 0;

                                $imagemProduto = $oferta->produto->imagem ?? null;
                            @endphp

                            <article
                                class="card-oferta-fornecedor relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 p-2 sm:p-3 rounded-xl border border-gray-200 dark:border-[#273449] bg-white dark:bg-[#161f30] hover:bg-gray-50 dark:hover:bg-[#1c273b] transition-colors"
                                data-oferta-id="{{ $oferta->id }}"
                                data-novas-propostas="{{ $quantidadeNovas }}">

                                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1 sm:mr-auto">
                                    <img
                                        class="w-20 h-20 sm:w-16 sm:h-16 object-cover rounded-lg shrink-0 bg-gray-100 dark:bg-slate-800"
                                        src="{{ $imagemProduto ? (str_contains($imagemProduto, 'assets/') ? asset($imagemProduto) : asset('storage/' . ltrim(str_replace('public/', '', $imagemProduto), '/'))) : asset('assets/milho.png') }}"
                                        alt="{{ $oferta->produto->nome ?? 'Produto' }}"
                                        onerror="this.onerror=null; this.src='{{ asset('assets/milho.png') }}';">

                                    <div class="min-w-0 flex-1">
                                        <h2 class="font-bold text-gray-800 dark:text-gray-100 text-sm sm:text-base truncate">
                                            {{ $oferta->produto->nome ?? 'Produto sem nome' }}
                                        </h2>

                                        <p class="text-xs text-gray-700 dark:text-gray-300 mt-0.5">
                                            Unidade:
                                            <strong class="text-gray-900 dark:text-white">
                                                {{ number_format($oferta->quantidade, 2, ',', '.') }}
                                                {{ $oferta->unidade }}
                                            </strong>
                                        </p>

                                        <p class="text-[11px] text-gray-700 dark:text-gray-400 mt-0.5">
                                            Validade:
                                            <strong class="text-gray-900 dark:text-gray-200">
                                                {{ $oferta->data_validade ? \Carbon\Carbon::parse($oferta->data_validade)->format('d/m/Y') : 'Sem prazo definido' }}
                                            </strong>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-2 w-full sm:w-auto sm:shrink-0">
                                    @if($quantidadeNovas > 0)
                                        <span
                                            class="contador-propostas-oferta proposta-nova inline-flex items-center justify-center min-w-6 h-6 px-1 rounded-full bg-amber-400 text-gray-900 text-xs font-extrabold"
                                            title="{{ $quantidadeNovas }} {{ $quantidadeNovas === 1 ? 'nova proposta' : 'novas propostas' }}">
                                            {{ $quantidadeNovas }}
                                        </span>
                                    @else
                                        <span class="contador-propostas-oferta hidden min-w-6 h-6 px-1 rounded-full bg-amber-400 text-gray-900 text-xs font-extrabold items-center justify-center"></span>
                                    @endif

                                    <a href="{{ route('negociacoes.propostas', ['oferta' => $oferta->id]) }}"
                                       class="bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 font-bold px-3 py-2 rounded-md text-xs transition-colors whitespace-nowrap">
                                        Negociações
                                    </a>

                                    <a href="{{ route('ofertas.show', $oferta->id) }}"
                                       class="btn-copav text-white px-3 py-2 rounded-md font-bold text-xs whitespace-nowrap transition-colors">
                                        Detalhes
                                    </a>
                                </div>
                            </article>
                        @empty
                            <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-6">
                                {{ $isFornecedor ? 'Você ainda não possui ofertas cadastradas.' : 'Nenhum lote da empresa foi encontrado.' }}
                            </p>
                        @endforelse

                        @if($listaOfertas->count() > 3)
                            <div class="pt-2 text-center">
                                <a href="{{ route('ofertas.index') }}"
                                   class="inline-flex items-center justify-center gap-1 text-xs font-bold bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 px-4 py-2.5 rounded-lg transition-colors">
                                    Ver todas as ofertas
                                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Aba de demandas --}}
                    <div x-show="abaAtiva === 'demandas'"
                        x-cloak
                        class="space-y-2">

                        @forelse($minhasDemandas->take(3) as $demanda)
                            @php
                                $imagemDemanda = $demanda->imagem ?? null;
                            @endphp

                            <article
                                class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 p-2 sm:p-3 rounded-xl border border-gray-200 dark:border-[#273449] bg-white dark:bg-[#161f30] hover:bg-gray-50 dark:hover:bg-[#1c273b] transition-colors">

                                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1 sm:mr-auto">
                                    <img
                                        class="w-20 h-20 sm:w-16 sm:h-16 object-cover rounded-lg shrink-0 bg-gray-100 dark:bg-slate-800"
                                        src="{{ $imagemDemanda
                                            ? (str_contains($imagemDemanda, 'assets/')
                                                ? asset($imagemDemanda)
                                                : asset('storage/' . ltrim(str_replace('public/', '', $imagemDemanda), '/')))
                                            : asset('assets/milho.png') }}"
                                        alt="{{ $demanda->nome_produto ?? 'Demanda' }}"
                                        onerror="this.onerror=null; this.src='{{ asset('assets/milho.png') }}';">

                                    <div class="min-w-0 flex-1">
                                        <h2 class="font-extrabold text-gray-800 dark:text-gray-100 text-sm sm:text-base truncate">
                                            {{ $demanda->nome_produto ?? 'Produto solicitado' }}
                                        </h2>

                                        @if($demanda->descricao)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">
                                                {{ $demanda->descricao }}
                                            </p>
                                        @endif

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            <p>
                                                Quantidade:
                                                <strong class="text-gray-700 dark:text-gray-200">
                                                    {{ number_format($demanda->quantidade, 2, ',', '.') }}
                                                    {{ $demanda->unidade }}
                                                </strong>
                                            </p>

                                            @if($demanda->valor_maximo !== null)
                                                <p>
                                                    Valor máximo:
                                                    <strong class="text-gray-700 dark:text-gray-200">
                                                        R$ {{ number_format($demanda->valor_maximo, 2, ',', '.') }}
                                                    </strong>
                                                </p>
                                            @endif

                                            @if($demanda->localizacao)
                                                <p>
                                                    Local:
                                                    <strong class="text-gray-700 dark:text-gray-200">
                                                        {{ $demanda->localizacao }}
                                                    </strong>
                                                </p>
                                            @endif

                                            @if($demanda->data_limite)
                                                <p>
                                                    Prazo:
                                                    <strong class="text-gray-700 dark:text-gray-200">
                                                        {{ \Carbon\Carbon::parse($demanda->data_limite)->format('d/m/Y') }}
                                                    </strong>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-2 w-full sm:w-auto sm:shrink-0">
                                    <span class="inline-flex items-center justify-center min-w-fit px-2 h-6 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-transparent dark:border-emerald-800/50 text-[10px] font-extrabold uppercase">
                                        {{ ucfirst($demanda->status ?? 'aberta') }}
                                    </span>

                                    <a href="{{ route('demandas.show', $demanda->id) }}"
                                       class="btn-copav text-white px-3 py-2 rounded-md font-bold text-xs whitespace-nowrap transition-colors">
                                        Ver demanda
                                    </a>
                                </div>
                            </article>
                        @empty
                            <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-6">
                                Você ainda não cadastrou nenhuma demanda.
                            </p>
                        @endforelse

                        @if($minhasDemandas->count() > 3)
                            <div class="pt-2 text-center">
                                <a href="{{ route('demandas.index') }}"
                                   class="inline-flex items-center justify-center gap-1 text-xs font-bold bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 px-4 py-2.5 text-[#236350] dark:text-emerald-400 rounded-lg transition-colors">
                                    Ver todas as minhas demandas
                                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                </a>
                            </div>
                        @endif
                    </div>

                </div>
            </section>

        @elseif($isCliente)
            <section class="bg-[#123228] dark:bg-[#0f172a] border border-transparent dark:border-[#1e293b] text-white p-4 sm:p-5 rounded-2xl shadow-md flex flex-row items-center justify-between gap-3">
                <div>
                    <h2 class="font-bold text-base">
                        Quer vender no marketplace?
                    </h2>

                    <p class="text-sm text-emerald-50 dark:text-slate-300 mt-1">
                        Torne-se um fornecedor para cadastrar lotes e gerenciar seus produtos.
                    </p>
                </div>

                <a href="{{ route('fornecedores.create') }}"
                   class="btn-copav text-white text-sm px-4 py-2.5 rounded-lg font-bold shrink-0">
                    Virar fornecedor
                </a>
            </section>
        @endif
    @endauth

    {{-- Mensagem para visitantes --}}
    @guest
        <section class="bg-[#123228] dark:bg-[#0f172a] border border-transparent dark:border-[#1e293b] text-white p-4 sm:p-5 rounded-2xl shadow-md flex flex-col sm:flex-row items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-base">
                    Quer comprar ou vender produtos agrícolas?
                </h2>

                <p class="text-sm text-emerald-50 dark:text-slate-300 mt-1">
                    Entre na sua conta ou cadastre-se para negociar no marketplace.
                </p>
            </div>

            <div class="flex gap-2 shrink-0">
                <a href="{{ route('login') }}"
                   class="btn-copav text-white text-sm px-4 py-2 rounded-lg font-bold">
                    Entrar
                </a>

                <a href="{{ route('register') }}"
                   class="bg-white/10 hover:bg-white/20 text-white text-sm px-4 py-2 rounded-lg font-bold transition-colors">
                    Cadastrar
                </a>
            </div>
        </section>
    @endguest

    {{-- Vitrine de lotes --}}
    <section class="space-y-3">

        <div class="flex flex-col items-start gap-3">
            <div class="w-full text-left">
                <h2 class="text-2xl sm:text-xl font-bold text-gray-800 dark:text-gray-100">
                    Produtos que você pode se interessar...
                </h2>

                <p class="text-md sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Explore os lotes disponíveis no marketplace.
                </p>
            </div>

            <div class="flex justify-start shrink-0">
                <label class="sr-only" for="ordenar-vitrine">
                    Ordenar lotes
                </label>

                <select id="ordenar-vitrine"
                    class="bg-white dark:bg-[#111827] border border-gray-200 dark:border-[#273449] rounded-lg px-4 py-2 text-sm text-gray-700 dark:text-gray-100 outline-none focus:border-[#236350] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-900/40">
                    <option value="recentes">Mais recentes</option>
                    <option value="menor-preco">Menor preço</option>
                    <option value="maior-preco">Maior preço</option>
                </select>
            </div>
        </div>

        <div id="grade-vitrine"
             class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 sm:gap-4">

            @forelse($listaProdutos as $item)
                @php
                    $produtoVitrine = $item->produto ?? null;
                    $nomeVitrine = $produtoVitrine->nome ?? 'Produto sem nome';
                    $descricaoVitrine = $produtoVitrine->descricao ?? '';
                    $imagemVitrine = $produtoVitrine->imagem ?? null;
                    $valorVitrine = (float) ($item->valor ?? 0);
                    $quantidadeVitrine = (float) ($item->quantidade ?? 0);
                @endphp

                <a
                    href="{{ route('ofertas.show', $item->id) }}"
                    class="card-vitrine group block h-full"
                    data-nome="{{ mb_strtolower($nomeVitrine) }}"
                    data-valor="{{ $valorVitrine }}"
                    data-id="{{ $item->id }}">

                    <div class="bg-white dark:bg-[#111827] rounded-xl shadow-sm group-hover:shadow-lg transition-all duration-300 border border-gray-200 dark:border-[#273449] overflow-hidden flex flex-col h-full group-hover:-translate-y-0.5">

                        <div class="w-full aspect-square bg-gray-100 dark:bg-slate-800 overflow-hidden relative">
                            <img
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out"
                                src="{{ $imagemVitrine ? (str_contains($imagemVitrine, 'assets/') ? asset($imagemVitrine) : asset('storage/' . ltrim(str_replace('public/', '', $imagemVitrine), '/'))) : asset('assets/milho.png') }}"
                                alt="{{ $nomeVitrine }}"
                                loading="lazy"
                                onerror="this.onerror=null; this.src='{{ asset('assets/milho.png') }}';">
                        </div>

                        <div class="p-2.5 sm:p-3 flex flex-col flex-1 justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-xs sm:text-lg text-gray-800 dark:text-gray-100 line-clamp-2 leading-tight group-hover:text-[#236350] dark:group-hover:text-emerald-400 transition-colors">
                                    {{ $nomeVitrine }}
                                </h3>

                                @if($descricaoVitrine)
                                    <p class="text-[14px] text-gray-400 dark:text-gray-400 line-clamp-2 mt-1">
                                        {{ $descricaoVitrine }}
                                    </p>
                                @endif
                            </div>

                            <div class="pt-2 border-t border-gray-200 dark:border-[#273449]">
                                <span class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 block font-medium">
                                    {{ fmod($quantidadeVitrine, 1) == 0 ? number_format($quantidadeVitrine, 0, ',', '.') : number_format($quantidadeVitrine, 2, ',', '.') }}
                                    {{ $item->unidade ?? '' }}
                                </span>

                                <span class="text-sm sm:text-base font-extrabold text-[#1B4D3E] dark:text-emerald-400 block mt-0.5">
                                    R$ {{ number_format($valorVitrine, 2, ',', '.') }}
                                </span>
                            </div>

                            <div class="pt-2 border-t border-gray-200 dark:border-[#273449]">
                                <span class="mt-2 block w-full text-center text-[11px] font-bold text-[#236350] dark:text-emerald-400 border border-emerald-700 dark:border-emerald-500/60 group-hover:bg-emerald-700 dark:group-hover:bg-emerald-600 group-hover:text-white rounded-md py-1.5 transition-colors">
                                    Ver detalhes
                                </span>
                            </div>
                        </div>
                    </div>
                </a>

            @empty
                <div class="col-span-full bg-white dark:bg-[#111827] border border-gray-200 dark:border-[#273449] rounded-xl text-center py-10 px-4">
                    <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600">
                        inventory_2
                    </span>

                    <p class="text-gray-600 dark:text-gray-300 font-semibold mt-2">
                        Nenhum lote disponível no mercado.
                    </p>

                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">
                        Quando houver ofertas publicadas, elas aparecerão aqui.
                    </p>
                </div>
            @endforelse
        </div>
    </section>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const grade = document.getElementById('grade-vitrine');
        const ordenar = document.getElementById('ordenar-vitrine');

        if (!grade || !ordenar) return;

        const ordenarVitrine = () => {
            const cards = Array.from(
                grade.querySelectorAll('.card-vitrine')
            );

            cards.sort((a, b) => {
                if (ordenar.value === 'menor-preco') {
                    return Number(a.dataset.valor || 0) - Number(b.dataset.valor || 0);
                }

                if (ordenar.value === 'maior-preco') {
                    return Number(b.dataset.valor || 0) - Number(a.dataset.valor || 0);
                }

                if (ordenar.value === 'nome') {
                    return (a.dataset.nome || '').localeCompare(
                        b.dataset.nome || '',
                        'pt-BR'
                    );
                }

                return Number(b.dataset.id || 0) - Number(a.dataset.id || 0);
            });

            cards.forEach(card => grade.appendChild(card));
        };

        ordenar.addEventListener('change', ordenarVitrine);
        ordenarVitrine();
    });

    window.addEventListener('load', function () {
        if (!window.Echo) {
            console.info('Laravel Echo não está disponível nesta página.');
            return;
        }

        @auth
            @if(auth()->user()->isFornecedor() && auth()->user()->fornecedor)
                const fornecedorId = {{ auth()->user()->fornecedor->id }};
                const canal = window.Echo.private(`fornecedor.${fornecedorId}`);

                canal.listen('.proposta.criada', (event) => {
                    const proposta = event.proposta;

                    if (!proposta || !proposta.oferta_id) return;

                    const ofertaId = Number(proposta.oferta_id);
                    const card = document.querySelector(
                        `.card-oferta-fornecedor[data-oferta-id="${ofertaId}"]`
                    );

                    if (card) {
                        const contador = card.querySelector('.contador-propostas-oferta');
                        const atual = Number(card.dataset.novasPropostas || 0);
                        const novoTotal = atual + 1;

                        card.dataset.novasPropostas = novoTotal;

                        if (contador) {
                            contador.textContent = novoTotal;
                            contador.classList.remove('hidden');
                            contador.classList.add('inline-flex', 'proposta-nova');
                            contador.style.display = 'inline-flex';
                        }

                        const lista = card.parentElement;
                        if (lista) {
                            lista.prepend(card);
                        }
                    }

                    const contadorGeral = document.getElementById('contador-novas-propostas');
                    const indicadorGeral = document.getElementById('indicador-novas-propostas');

                    if (contadorGeral && indicadorGeral) {
                        const novoTotal = Number(contadorGeral.textContent.trim() || 0) + 1;
                        contadorGeral.textContent = novoTotal;
                        indicadorGeral.style.display = 'inline-flex';
                        indicadorGeral.title = `${novoTotal} ${novoTotal === 1 ? 'nova proposta' : 'novas propostas'}`;
                    }
                });
            @endif
        @endauth
    });
</script>
@endpush
@endsection