@extends('layouts.layout')

@section('title', 'Home')

@section('conteudo')

{{-- Script Alpine.js --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    [x-cloak] { display: none !important; }
</style>

<div 
    x-data="{ abaAtiva: '{{ (auth()->check() && auth()->user()->fornecedor) ? 'lotes' : 'demandas' }}' }"
    class="w-full max-w-7xl mx-auto px-2 sm:px-4 py-4 space-y-6 bg-[#ebeae7] min-h-screen">

    {{-- Painel do fornecedor --}}
    @auth
        <div class="bg-[#123228] text-white p-3 sm:p-5 rounded-2xl shadow-md">
            
            {{-- Se for fornecedor --}}
            @if(auth()->user()->fornecedor)
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-3 pb-3 border-b border-emerald-800">
                    <div>
                        <span class="text-xs text-white font-semibold uppercase tracking-wider block">Painel do Fornecedor</span>
                        <h2 class="text-base sm:text-lg font-bold">Gestão dos seus Lotes</h2>
                    </div>

                    {{-- Botões de Ação no Topo --}}
                    <div class="flex items-center gap-2">
                        <a href="{{ route('ofertas.create') }}" 
                           class="btn-copav text-xs sm:text-sm px-3 sm:px-4 py-2 rounded-lg font-semibold flex items-center justify-center gap-1 shadow-sm flex-1 md:flex-initial transition-transform active:scale-95">
                            <span class="material-symbols-outlined text-base">add</span>
                            <span>Nova Oferta</span>
                        </a>
                        <a href="{{ route('produtos.index') }}" 
                           class="btn-copav text-white text-xs whitespace-nowrap sm:text-sm px-3 sm:px-4 py-2 rounded-lg font-semibold flex items-center justify-center gap-1 transition-colors duration-200 flex-1 md:flex-initial shadow-sm">
                            <span class="material-symbols-outlined text-base">inventory_2</span>
                            <span>Meus Produtos</span>
                        </a>
                    </div>
                </div>

                {{-- ABAS DE GESTÃO (FORNECEDOR / ADMIN) --}}
                <div class="bg-white rounded-xl p-4 flex flex-col gap-3 shadow-md text-gray-800">
                    
                    <div role="tablist" class="tabs tabs-border w-full flex justify-around border-b pb-2">
                        {{-- ABA MINHAS OFERTAS --}}
                        <button
                            @click="abaAtiva = 'lotes'"
                            :class="{
                                'tab-active font-bold text-[#236350] border-b-2 border-[#236350] pb-1': abaAtiva === 'lotes',
                                'text-gray-400 hover:text-gray-600': abaAtiva !== 'lotes'
                            }"
                            class="tab transition-all pb-1 flex items-center justify-center gap-2 cursor-pointer">
                            <span>Minhas Ofertas</span>
                            <span
                                id="indicador-novas-propostas"
                                class="rounded-full bg-yellow-400 text-gray-900 font-bold items-center justify-center transition-all duration-200"
                                :class="abaAtiva === 'lotes' ? 'min-w-5 h-5 px-1.5 text-[10px]' : 'w-2 h-2'"
                                style="{{ isset($novasPropostas) &&$novasPropostas > 0 ? 'display: flex;' : 'display: none;' }}"
                                title="{{ \$novasPropostas ?? 0 }} novas propostas">
                                <span id="contador-novas-propostas" x-show="abaAtiva === 'lotes'">
                                    {{ \$novasPropostas ?? 0 }}
                                </span>
                            </span>
                        </button>

                        {{-- ABA DEMANDAS --}}
                        <button 
                            @click="abaAtiva = 'demandas'"
                            :class="{
                                'tab-active font-bold text-[#236350] border-b-2 border-[#236350] pb-1': abaAtiva === 'demandas',
                                'text-gray-400 hover:text-gray-600': abaAtiva !== 'demandas'
                            }"
                            class="tab transition-all pb-1 flex items-center justify-center gap-2 cursor-pointer">
                            Demandas da Empresa
                        </button>
                    </div>

                    {{-- CONTEÚDO DA ABA: MINHAS OFERTAS --}}
                    <div x-show="abaAtiva === 'lotes'" class="flex flex-col gap-3 mt-2">
                        @forelse($ofertas->take(5) as$oferta)
                            @php
                                $quantidadeNovasPropostas = isset($ofertasComNovasPropostas) ? $ofertasComNovasPropostas->get($oferta->id, 0) : 0;
                                $negociacao =$oferta->negociacoes->sortByDesc('id')->first();
                            @endphp

                            <div class="card-oferta-fornecedor relative flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm transition-all hover:shadow-md"
                                 data-oferta-id="{{ \$oferta->id }}"
                                 data-novas-propostas="{{ \$quantidadeNovasPropostas }}">

                                <span class="contador-propostas-oferta absolute top-2 right-2 min-w-6 h-6 px-1 rounded-full bg-yellow-400 text-gray-900 text-xs font-bold items-center justify-center shadow-sm"
                                      style="{{ \$quantidadeNovasPropostas > 0 ? 'display: flex;' : 'display: none;' }}">
                                    {{ \$quantidadeNovasPropostas }}
                                </span>

                                <img class="w-20 h-20 object-cover rounded-lg shrink-0" 
                                     src="{{ $oferta->produto->imagem ? (str_contains($oferta->produto->imagem, 'assets/') ? asset($oferta->produto->imagem) : asset('storage/' .$oferta->produto->imagem)) : asset('assets/milho.png') }}" 
                                     alt="{{ \$oferta->produto->nome ?? 'Produto' }}">

                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-sm text-gray-800">
                                        [Oferta #{{ \$oferta->id }}]: 
                                        {{ number_format($oferta->quantidade, 0, ',', '.') }} {{$oferta->unidade }} 
                                        {{ \$oferta->produto->nome ?? '' }}
                                    </h3>
                                    <p class="text-xs text-gray-500">
                                        Expira em: {{ $oferta->data_validade ? \Carbon\Carbon::parse($oferta->data_validade)->format('d/m/Y') : 'Sem data' }}
                                    </p>

                                    <div class="flex justify-end gap-2 mt-2">
                                        @if(\$negociacao)
                                            <a href="{{ route('negociacoes.propostas', \$oferta->id) }}" 
                                               class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#236350] hover:text-[#174c3e] transition-colors">
                                                Ver negociação
                                                <span class="material-symbols-outlined text-base">arrow_forward</span>
                                            </a>
                                        @endif

                                        <a href="{{ route('ofertas.show', \$oferta->id) }}" 
                                           class="btn-copav text-white p-2 btn-xs sm:btn-sm rounded-md">
                                            Ver detalhes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-2">Você ainda não possui ofertas ativas.</p>
                        @endforelse

                        @if(isset($ofertas) &&$ofertas->count() > 5)
                            <div class="pt-2 text-center">
                                <a href="{{ route('ofertas.index') }}" 
                                   class="inline-block text-xs font-bold bg-slate-200 hover:bg-slate-300 p-2 text-[#236350] rounded-md">
                                    Ver mais ofertas
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- CONTEÚDO DA ABA: DEMANDAS --}}
                    <div x-show="abaAtiva === 'demandas'" x-cloak class="space-y-2 mt-2">
                        @forelse($demandas->take(3) ?? [] as$demanda)
                            <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg border border-gray-100 text-xs gap-2">
                                <div class="truncate">
                                    <p class="font-bold text-gray-800 truncate">{{ \$demanda->nome_produto }}</p>
                                    <p class="text-[11px] text-gray-500">{{ number_format($demanda->quantidade, 0, ',', '.') }} {{$demanda->unidade }}</p>
                                </div>
                                <a href="{{ route('demandas.show', \$demanda->id) }}" 
                                   class="btn-copav text-white px-2.5 py-1 rounded text-[11px] font-semibold shrink-0">
                                    Enviar Proposta
                                </a>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-2">Nenhuma demanda corporativa aberta.</p>
                        @endforelse
                    </div>

                </div>

            {{-- Se for cliente comum --}}
            @else
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
                    <div>
                        <h2 class="font-bold text-sm sm:text-base">Deseja vender no marketplace?</h2>
                        <p class="text-xs text-white">Torne-se um fornecedor e comece a cadastrar seus lotes.</p>
                    </div>
                    <a href="{{ route('fornecedores.create') }}" class="btn-copav text-xs px-4 py-2 rounded-lg font-bold shrink-0">
                        Virar Fornecedor
                    </a>
                </div>
            @endif
        </div>
    @endauth

    {{-- Usuário guest --}}
    @guest
        <div class="bg-[#123228] text-white p-4 rounded-2xl shadow-md flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
            <div>
                <h2 class="font-bold text-sm sm:text-base">Quer comprar ou vender produtos agrícolas?</h2>
                <p class="text-xs text-white">Cadastre-se ou entre na sua conta para negociar lotes diretamente.</p>
            </div>
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('login') }}" class="btn-copav text-xs px-4 py-2 rounded-lg font-bold">Entrar</a>
                <a href="{{ route('register') }}" class="bg-white/10 hover:bg-white/20 text-white text-xs px-4 py-2 rounded-lg font-bold">Cadastrar</a>
            </div>
        </div>
    @endguest

    {{-- Vitrine de produtos --}}
    <div class="space-y-3">
        <h1 class="text-lg sm:text-xl font-bold text-gray-800">
            Produtos que você pode se interessar...
        </h1>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 sm:gap-4">
            @forelse($produtos as$item)
                <a href="{{ route('ofertas.show', \$item->id) }}" class="group block h-full">
                    <div class="bg-white rounded-xl shadow-sm group-hover:shadow-lg transition-all duration-300 border border-gray-200 overflow-hidden flex flex-col h-full group-hover:-translate-y-0.5">
                        <div class="w-full aspect-square bg-gray-100 overflow-hidden relative">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out"
                                 src="{{ $item->produto->imagem ? (str_contains($item->produto->imagem, 'assets/') ? asset($item->produto->imagem) : asset('storage/' .$item->produto->imagem)) : asset('assets/milho.png') }}" 
                                 alt="{{ \$item->produto->nome ?? 'Produto' }}">
                        </div>

                        <div class="p-2.5 sm:p-3 flex flex-col flex-1 justify-between gap-1.5">
                            <div>
                                <h2 class="font-medium text-xs sm:text-sm text-gray-800 line-clamp-2 leading-tight group-hover:text-[#236350] transition-colors">
                                    {{ \$item->produto->nome ?? 'Sem nome' }}
                                </h2>
                                @if(\$item->produto->descricao)
                                    <p class="text-[11px] text-gray-400 line-clamp-1 mt-0.5">
                                        {{ \$item->produto->descricao }}
                                    </p>
                                @endif
                            </div>

                            <div class="pt-1 border-t border-gray-100">
                                <span class="text-[10px] sm:text-xs text-gray-500 block font-medium">
                                    @if(strtolower(\$item->unidade) === 'saca')
                                        {{ number_format(\$item->quantidade, 0, ',', '.') }}
                                    @else
                                        {{ fmod(\$item->quantidade, 1) == 0 ? number_format($item->quantidade, 0, ',', '.') : number_format($item->quantidade, 2, ',', '.') }}
                                    @endif
                                    {{ \$item->unidade }}
                                </span>
                                <span class="text-sm sm:text-base font-extrabold text-[#1B4D3E]">
                                    R${{ number_format($item->valor ?? 0, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <p class="text-gray-500 col-span-full text-center py-8 text-xs sm:text-sm">
                    Nenhum produto cadastrado até o momento.
                </p>
            @endforelse
        </div>
    </div>
</div>

{{-- SCRIPTS DE WEBSOCKET --}}
@push('scripts')
<script>
    window.addEventListener('load', function () {
        if (!window.Echo) return;

        @auth
            @if(auth()->user()->fornecedor)
                const fornecedorId = {{ auth()->user()->fornecedor->id }};
                window.Echo.private(`fornecedor.${fornecedorId}`)
                    .listen('.proposta.criada', (event) => {
                        console.log('🔔 Nova proposta recebida:', event);
                    });
            @endif
        @endauth
    });
</script>
@endpush
@endsection