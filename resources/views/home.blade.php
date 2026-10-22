@extends('layouts.layout')

@section('title', 'Home')

@section('conteudo')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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

    $is_Admin = auth()->check()
        && $usuarioLogado->user_type === 'admin';

    $is_Empresa = auth()->check()
        && $usuarioLogado->user_type === 'empresa';

    // Admin e empresa não são tratados como fornecedores.
    $is_Fornecedor = auth()->check()
        && !$is_Admin
        && !$is_Empresa
        && $usuarioLogado->fornecedor !== null;

    $is_Cliente = auth()->check()
        && !$is_Admin
        && !$is_Empresa
        && !$is_Fornecedor
        && $usuarioLogado->user_type === 'cliente';

    $listaOfertas = $ofertas ?? collect();
    $listaDemandas = $demandas ?? collect();
    $listaProdutos = $produtos ?? collect();
    $totalNovasPropostas = $novasPropostas ?? 0;
@endphp

<div
    x-data="{
        abaAtiva: '{{ ($is_Fornecedor || $is_Admin || $is_Empresa) ? 'lotes' : 'demandas' }}',
        ordenarPor: 'recentes'
    }"
    class="w-full max-w-7xl mx-auto px-3 sm:px-5 lg:px-6 py-5 space-y-7 bg-[#ebeae7] min-h-screen">

    {{-- Painel de gestão --}}
    @auth
        @if($is_Fornecedor || $is_Admin || $is_Empresa)
            <section class="bg-[#123228] text-white p-4 sm:p-6 rounded-2xl shadow-md">

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4 pb-4 border-b border-emerald-800">
                    <div>
                        @if($is_Fornecedor)
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-50">
                                Painel do fornecedor
                            </span>

                            <h1 class="text-xl sm:text-2xl font-extrabold mt-1">
                                Gestão dos seus lotes
                            </h1>

                            <p class="text-xs sm:text-sm text-emerald-50 mt-1">
                                Gerencie suas ofertas, acompanhe propostas e encontre oportunidades.
                            </p>

                        @elseif($is_Admin)
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-50">
                                Painel administrativo
                            </span>

                            <h1 class="text-xl sm:text-2xl font-extrabold mt-1">
                                Gestão do marketplace
                            </h1>

                            <p class="text-xs sm:text-sm text-emerald-50 mt-1">
                                Acompanhe as demandas e oportunidades do marketplace.
                            </p>

                        @elseif($is_Empresa)
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-50">
                                Painel da empresa
                            </span>


                            <p class="text-xs sm:text-sm text-emerald-50 mt-1">
                                Acompanhe as demandas e oportunidades do marketplace.
                            </p>

                        @elseif($is_Empresa)
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-50">
                                Painel da empresa
                            </span>

                            <h1 class="text-xl sm:text-2xl font-extrabold mt-1">
                                Gestão da empresa
                            </h1>

                            <p class="text-xs sm:text-sm text-emerald-50 mt-1">
                                Acompanhe as demandas e oportunidades da empresa.
                            </p>
                        @endif
                    </div>

                    @if($is_Fornecedor)
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('ofertas.create') }}"
                               class="btn-copav text-white text-sm px-4 py-2.5 rounded-lg font-bold flex items-center justify-center gap-1.5 shadow-sm transition-transform active:scale-95">
                                <span class="material-symbols-outlined text-lg">add</span>
                                Nova Oferta
                            </a>

                            <a href="{{ route('produtos.index') }}"
                               class="bg-white/10 hover:bg-white/20 text-white text-sm px-4 py-2.5 rounded-lg font-bold flex items-center justify-center gap-1.5 transition-colors">
                                <span class="material-symbols-outlined text-lg">inventory_2</span>
                                Meus Produtos
                            </a>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-xl p-3 sm:p-4 text-gray-800 shadow-inner">

                    {{-- Abas do painel --}}
                    <div role="tablist"
                         aria-label="Painel de gestão"
                         class="flex flex-wrap items-center gap-4 sm:gap-6 border-b border-gray-200 pb-3 mb-3 text-sm font-bold">

                        @if($is_Fornecedor || $is_Admin || $is_Empresa)
                            <button type="button"
                                role="tab"
                                @click="abaAtiva = 'lotes'"
                                :aria-selected="abaAtiva === 'lotes'"
                                :class="abaAtiva === 'lotes' ? 'text-[#236350] border-b-2 border-[#236350]' : 'text-gray-400 hover:text-gray-600'"
                                class="relative pb-2 transition-colors">

                                @if($is_Fornecedor)
                                    Minhas Ofertas
                                @elseif($is_Admin)
                                    Lotes da empresa
                                @elseif($is_Empresa)
                                    Lotes da empresa
                                @endif

                                <span id="indicador-novas-propostas"
                                    class="ml-1.5 inline-flex items-center justify-center rounded-full bg-amber-400 text-gray-900 text-[10px] font-extrabold min-w-5 h-5 px-1"
                                    style="{{ $totalNovasPropostas > 0 ? 'display: inline-flex;' : 'display: none;' }}"
                                    title="{{ $totalNovasPropostas }} {{ $totalNovasPropostas === 1 ? 'nova proposta' : 'novas propostas' }}">

                                    <span id="contador-novas-propostas">
                                        {{ $totalNovasPropostas }}
                                    </span>
                                </span>
                            </button>
                        @endif

                        <button type="button"
                            role="tab"
                            @click="abaAtiva = 'demandas'"
                            :aria-selected="abaAtiva === 'demandas'"
                            :class="abaAtiva === 'demandas' ? 'text-[#236350] border-b-2 border-[#236350]' : 'text-gray-400 hover:text-gray-600'"
                            class="pb-2 transition-colors">

                            Demandas da Empresa

                            @if($listaDemandas->count() > 0)
                                <span class="ml-1 text-[10px] bg-slate-100 text-slate-600 rounded-full px-1.5 py-0.5">
                                    {{ $listaDemandas->count() }}
                                </span>
                            @endif
                        </button>
                    </div>

                    {{-- Aba de ofertas --}}
                    @if($is_Fornecedor || $is_Admin || $is_Empresa)
                        <div x-show="abaAtiva === 'lotes'" x-cloak class="space-y-2">

                            @forelse($listaOfertas->take(3) as $oferta)
                                @php
                                    $quantidadeNovas = isset($ofertasComNovasPropostas)
                                        ? (int) $ofertasComNovasPropostas->get($oferta->id, 0)
                                        : 0;

                                    $imagemProduto = $oferta->produto->imagem ?? null;
                                @endphp

                                <article
                                class="card-oferta-fornecedor relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-2 sm:p-3 rounded-xl border border-gray-100 bg-white hover:bg-gray-50 transition-colors w-full"                                data-oferta-id="{{ $oferta->id }}"
                                data-novas-propostas="{{ $quantidadeNovas }}">

                                {{-- Informações do lote --}}
                                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1 sm:mr-auto">

                                    <img
                                        class="w-16 h-16 sm:w-16 sm:h-16 object-cover rounded-lg shrink-0 bg-gray-100"
                                        class="w-14 h-14 sm:w-16 sm:h-16 object-cover rounded-lg shrink-0 bg-gray-100"
                                        src="{{ $imagemProduto ? (str_contains($imagemProduto, 'assets/') ? asset($imagemProduto) : asset('storage/' . ltrim(str_replace('public/', '', $imagemProduto), '/'))) : asset('assets/milho.png') }}"
                                        alt="{{ $oferta->produto->nome ?? 'Produto' }}"
                                        onerror="this.onerror=null; this.src='{{ asset('assets/milho.png') }}';">

                                    <div class="min-w-0 flex-1">
                                        <h2 class="font-extrabold text-gray-800 text-sm sm:text-base truncate">
                                            {{ $oferta->id }} - {{ $oferta->produto->nome ?? 'Produto sem nome' }}
                                        </h2>

                                        <p class="text-xs text-gray-700 mt-0.5">
                                            Unidade:
                                            <strong>
                                                {{ number_format($oferta->quantidade, 2, ',', '.') }}
                                                {{ $oferta->unidade }}
                                            </strong>
                                        </p>

                                        <p class="text-[11px] text-gray-700 mt-0.5">
                                            Validade:
                                            <strong>
                                                {{ $oferta->data_validade ? \Carbon\Carbon::parse($oferta->data_validade)->format('d/m/Y') : 'Sem prazo definido' }}
                                            </strong>
                                        </h2>
                                    </div>
                                </div>

                                {{-- Botões abaixo no celular e à direita no computador --}}
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
                                    class="bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold px-3 py-2 rounded-md text-xs transition-colors whitespace-nowrap">
                                        Negociação
                                    </a>

                                    <a href="{{ route('ofertas.show', $oferta->id) }}"
                                    class="btn-copav text-white px-3 py-2 rounded-md font-bold text-xs whitespace-nowrap">
                                        Detalhes
                                    </a>
                                </div>
                            </article>

                            @empty
                                <p class="text-sm text-gray-400 text-center py-6">
                                    @if($is_Fornecedor)
                                        Você ainda não possui ofertas cadastradas.
                                    @elseif($is_Admin)
                                        Nenhum lote da empresa foi encontrado.
                                    @elseif($is_Empresa)
                                        Nenhum lote da empresa foi encontrado.
                                    @endif
                                </p>
                            @endforelse

                            @if($listaOfertas->count() > 3)
                                <div class="pt-2 text-center">
                                    <a href="{{ route('ofertas.index') }}"
                                    class="inline-flex items-center justify-center gap-1 text-xs font-bold bg-slate-100 hover:bg-slate-200 px-4 py-2.5 text-[#236350] rounded-lg transition-colors">
                                        Ver todas as ofertas
                                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Aba de demandas --}}
                    <div x-show="abaAtiva === 'demandas'" x-cloak class="space-y-2">

                        @forelse($listaDemandas->take(3) as $demanda)
                            @php
                                $imagemDemanda = $demanda->imagem ?? null;
                            @endphp

                            <article
                            class="flex flex-col sm:flex-row sm:items-center gap-3 p-3 rounded-xl border border-gray-100 bg-white hover:bg-gray-50 transition-colors">

                                <div class="flex items-start gap-3 min-w-0 flex-1 sm:mr-auto">
                            <article class="flex flex-col sm:flex-row sm:items-center gap-3 p-3 rounded-xl border border-gray-100 bg-white hover:bg-gray-50 transition-colors">

                                <div class="flex items-start gap-3 min-w-0 flex-1">
                                    <img
                                        class="w-16 h-16 sm:w-16 sm:h-16 rounded-lg object-cover shrink-0 bg-gray-100"
                                        src="{{ $imagemDemanda ? (str_contains($imagemDemanda, 'assets/') ? asset($imagemDemanda) : asset('storage/' . ltrim(str_replace('public/', '', $imagemDemanda), '/'))) : asset('assets/milho.png') }}"
                                        alt="{{ $demanda->nome_produto ?? 'Demanda' }}"
                                        onerror="this.onerror=null; this.src='{{ asset('assets/milho.png') }}';">

                                    <div class="min-w-0 flex-1">
                                        <h2 class="font-extrabold text-gray-800 text-sm sm:text-base">
                                            {{ $demanda->nome_produto ?? 'Produto solicitado' }}
                                        </h2>

                                        @if($demanda->descricao)
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                                {{ $demanda->descricao }}
                                            </p>
                                        @endif

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1 mt-2 text-xs text-gray-500">

                                            <p>
                                                Quantidade:
                                                <strong class="text-gray-700">
                                                    {{ number_format($demanda->quantidade, 2, ',', '.') }}
                                                    {{ $demanda->unidade }}
                                                </strong>
                                            </p>

                                            @if($demanda->valor_maximo !== null)
                                                <p>
                                                    Valor máximo:
                                                    <strong class="text-gray-700">
                                                        R$ {{ number_format($demanda->valor_maximo, 2, ',', '.') }}
                                                    </strong>
                                                </p>
                                            @endif

                                            @if($demanda->localizacao)
                                                <p>
                                                    Local:
                                                    <strong class="text-gray-700">
                                                        {{ $demanda->localizacao }}
                                                    </strong>
                                                </p>
                                            @endif

                                            @if($demanda->data_limite)
                                                <p>
                                                    Prazo:
                                                    <strong class="text-gray-700">
                                                        {{ \Carbon\Carbon::parse($demanda->data_limite)->format('d/m/Y') }}
                                                    </strong>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-2 sm:shrink-0">
                                    <span class="text-[10px] font-bold uppercase rounded-full px-2 py-1 bg-emerald-50 text-emerald-700">
                                        {{ ucfirst($demanda->status ?? 'aberta') }}
                                    </span>

                                    <a href="{{ route('demandas.show', $demanda->id) }}"
                                       class="btn-copav text-white px-2 py-2 rounded-md text-xs font-bold whitespace-nowrap">
                                        Enviar proposta
                                    </a>
                                </div>
                            </article>

                        @empty
                            <p class="text-sm text-gray-400 text-center py-6">
                                Nenhuma demanda corporativa aberta no momento.
                            </p>
                        @endforelse
                    </div>
                </div>
            </section>

        @elseif($is_Cliente)
            <section class="bg-[#123228] text-white p-4 sm:p-5 rounded-2xl shadow-md flex flex-col sm:flex-row items-center justify-between gap-3">
                <div>
                    <h2 class="font-bold text-base">
                        Quer vender no marketplace?
                    </h2>

                    <p class="text-sm text-emerald-50 mt-1">
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
        <section class="bg-[#123228] text-white p-4 sm:p-5 rounded-2xl shadow-md flex flex-col sm:flex-row items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-base">
                    Quer comprar ou vender produtos agrícolas?
                </h2>

                <p class="text-sm text-emerald-50 mt-1">
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

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div class="w-full text-left">
                <h2 class="text-lg sm:text-xl font-extrabold text-gray-800">
                    Produtos que você pode se interessar...
                </h2>

                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                    Explore os lotes disponíveis no marketplace.
                </p>
            </div>

            <div class="flex justify-start sm:justify-end shrink-0">
                <label class="sr-only" for="ordenar-vitrine">
                    Ordenar lotes
                </label>

                {{-- Filtro de ordenação --}}
                <select id="ordenar-vitrine"
                    class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 outline-none focus:border-[#236350] focus:ring-2 focus:ring-emerald-100">
                    <option value="recentes">Mais recentes</option>
                    <option value="menor-preco">Menor preço</option>
                    <option value="maior-preco">Maior preço</option>
                    <option value="nome">Nome do produto</option>
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

                    <div class="bg-white rounded-xl shadow-sm group-hover:shadow-lg transition-all duration-300 border border-gray-200 overflow-hidden flex flex-col h-full group-hover:-translate-y-0.5">

                        <div class="w-full aspect-square bg-gray-100 overflow-hidden relative">
                            <img
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out"
                                src="{{ $imagemVitrine ? (str_contains($imagemVitrine, 'assets/') ? asset($imagemVitrine) : asset('storage/' . ltrim(str_replace('public/', '', $imagemVitrine), '/'))) : asset('assets/milho.png') }}"
                                alt="{{ $nomeVitrine }}"
                                loading="lazy"
                                onerror="this.onerror=null; this.src='{{ asset('assets/milho.png') }}';">

                            <span class="absolute top-2 left-2 bg-white/95 text-[#236350] text-[10px] font-extrabold px-2 py-1 rounded-full shadow-sm">
                                Lote {{ $item->id }}
                            </span>
                        </div>

                        <div class="p-2.5 sm:p-3 flex flex-col flex-1 justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-xs sm:text-sm text-gray-800 line-clamp-2 leading-tight group-hover:text-[#236350] transition-colors">
                                    {{ $nomeVitrine }}
                                </h3>

                                @if($descricaoVitrine)
                                    <p class="text-[11px] text-gray-400 line-clamp-2 mt-1">
                                        {{ $descricaoVitrine }}
                                    </p>
                                @endif
                            </div>

                            <div class="pt-2 border-t border-gray-100">
                                <span class="text-[10px] sm:text-xs text-gray-500 block font-medium">
                                    {{ fmod($quantidadeVitrine, 1) == 0 ? number_format($quantidadeVitrine, 0, ',', '.') : number_format($quantidadeVitrine, 2, ',', '.') }}
                                    {{ $item->unidade ?? '' }}
                                </span>

                                <span class="text-sm sm:text-base font-extrabold text-[#1B4D3E] block mt-0.5">
                                    R$ {{ number_format($valorVitrine, 2, ',', '.') }}
                                </span>


                                @if($descricaoVitrine)
                                    <p class="text-[11px] text-gray-400 line-clamp-2 mt-1">
                                        {{ $descricaoVitrine }}
                                    </p>
                                @endif
                            </div>

                            <div class="pt-2 border-t border-gray-100">
                                <span class="mt-2 block w-full text-center text-[11px] font-bold text-[#236350] border border-emerald-700 group-hover:bg-emerald-700 group-hover:text-white rounded-md py-1.5 transition-colors">
                                    Ver detalhes
                                </span>
                            </div>
                        </div>
                    </div>
                </a>

            @empty
                <div class="col-span-full bg-white border border-gray-200 rounded-xl text-center py-10 px-4">
                    <span class="material-symbols-outlined text-4xl text-gray-300">
                        inventory_2
                    </span>

                    <p class="text-gray-600 font-semibold mt-2">
                        Nenhum lote disponível no mercado.
                    </p>

                    <p class="text-gray-400 text-sm mt-1">
                        Quando houver ofertas publicadas, elas aparecerão aqui.
                    </p>
                </div>
            @endforelse
        </div>
    </section>
</div>

{{-- Ordenação da vitrine --}}
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
                    return Number(a.dataset.valor || 0)
                        - Number(b.dataset.valor || 0);
                }

                if (ordenar.value === 'maior-preco') {
                    return Number(b.dataset.valor || 0)
                        - Number(a.dataset.valor || 0);
                }

                if (ordenar.value === 'nome') {
                    return (a.dataset.nome || '').localeCompare(
                        b.dataset.nome || '',
                        'pt-BR'
                    );
                }

                return Number(b.dataset.id || 0)
                    - Number(a.dataset.id || 0);
            });

            cards.forEach(card => grade.appendChild(card));
        };

        ordenar.addEventListener('change', ordenarVitrine);
        ordenarVitrine();
    });

    window.addEventListener('load', function () {
        if (!window.Echo) {
            console.info(
                'Laravel Echo não está disponível nesta página.'
            );
            return;
        }

        @auth
            @if(auth()->user()->fornecedor)
                const fornecedorId = {{ auth()->user()->fornecedor->id }};
                const canal = window.Echo.private(
                    `fornecedor.${fornecedorId}`
                );

                canal.listen('.proposta.criada', (event) => {
                    const proposta = event.proposta;

                    if (!proposta || !proposta.negociacao) return;

                    const ofertaId = Number(
                        proposta.negociacao.oferta_id
                    );

                    const card = document.querySelector(
                        `.card-oferta-fornecedor[data-oferta-id="${ofertaId}"]`
                    );

                    if (card) {
                        const contador = card.querySelector(
                            '.contador-propostas-oferta'
                        );

                        const atual = Number(
                            card.dataset.novasPropostas || 0
                        );

                        const novoTotal = atual + 1;

                        card.dataset.novasPropostas = novoTotal;

                        if (contador) {
                            contador.textContent = novoTotal;
                            contador.classList.remove('hidden');
                            contador.classList.add(
                                'inline-flex',
                                'proposta-nova'
                            );
                            contador.style.display = 'inline-flex';
                        }

                        // Coloca a oferta com nova proposta no início.
                        const lista = card.parentElement;

                        if (lista) {
                            lista.prepend(card);
                        }
                    }

                    const contadorGeral = document.getElementById(
                        'contador-novas-propostas'
                    );

                    const indicadorGeral = document.getElementById(
                        'indicador-novas-propostas'
                    );

                    if (contadorGeral && indicadorGeral) {
                        const novoTotal = Number(
                            contadorGeral.textContent.trim() || 0
                        ) + 1;

                        contadorGeral.textContent = novoTotal;
                        indicadorGeral.style.display = 'inline-flex';

                        indicadorGeral.title =
                            `${novoTotal} ${
                                novoTotal === 1
                                    ? 'nova proposta'
                                    : 'novas propostas'
                            }`;
                    }
                });
            @endif
        @endauth
    });
</script>
@endpush
@endsection