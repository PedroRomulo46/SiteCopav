@extends('layouts.layout')

@section('title', 'Home')

@section('conteudo')

{{-- Script Alpine.js --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<div
    x-data="{ abaAtiva: 'lotes' }"
    class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 bg-[#ebeae7] min-h-screen">

    {{-- COLUNA ESQUERDA: AÇÕES E GESTÃO --}}
    <div
        class="lg:col-span-5 h-fit p-4 rounded-2xl flex flex-col gap-4"
        style="background-color: #123228;">

        {{-- BLOCO SUPERIOR - AÇÕES --}}
        @auth
            @if(auth()->user()->fornecedor)

                {{-- Botões exclusivos para Fornecedores --}}
                <a
                    href="{{ route('ofertas.create') }}"
                    class="text-white btn-copav w-full border-none text-lg p-5 rounded-xl shadow-inner text-center flex items-center justify-center">
                    Cadastrar Nova Oferta +
                </a>
                <a
                    href="{{ route('produtos.index') }}"
                    class="text-white btn-copav w-full border-none text-lg p-5 rounded-xl shadow-md text-center flex items-center justify-center">
                    Meus Produtos
                </a>
            @else

                {{-- Card de expansão para cliente comum --}}
                <div class="bg-white p-6 rounded-xl text-center flex flex-col gap-3 shadow-md">
                    <h2 class="font-bold text-gray-800 text-base">
                        Deseja expandir seus negócios?
                    </h2>
                    <p class="text-xs text-gray-600">
                        Torne-se um fornecedor para começar a cadastrar lotes e gerenciar produtos.
                    </p>
                    <div class="flex justify-center mt-2">
                        <a
                            href="{{ route('fornecedores.create') }}"
                            class="btn-copav text-white btn-sm px-4 rounded-md">
                            Virar Fornecedor
                        </a>
                    </div>
                </div>
            @endif
        @endauth

        {{-- USUÁRIO NÃO AUTENTICADO --}}
        @guest

            <div class="bg-white p-6 rounded-xl text-center flex flex-col gap-3 shadow-md">
                <h2 class="font-bold text-gray-800 text-base">
                    Quer vender no marketplace?
                </h2>
                <p class="text-xs text-gray-600">
                    Acesse sua conta ou cadastre-se para criar lotes e enviar propostas.
                </p>
                <div class="flex gap-2 justify-center mt-2">
                    <a
                        href="{{ route('login') }}"
                        class="btn-copav text-white btn-sm px-4 rounded-md">
                        Entrar
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="btn-outline border border-gray-400 text-gray-700 hover:bg-gray-100 btn-sm px-4 rounded-md">
                        Cadastrar
                    </a>
                </div>
              </div>
        @endguest

        {{-- BLOCO INFERIOR - USUÁRIOS AUTENTICADOS --}}
        @auth
            <div
                class="bg-white rounded-xl p-4 flex flex-col gap-3 shadow-md"
                x-data="{ abaAtiva: '{{ auth()->user()->fornecedor ? 'lotes' : 'demandas' }}' }">

                {{-- ABAS --}}
                <div
                    role="tablist"
                    class="tabs tabs-border w-full flex justify-around border-b pb-2">

                    {{-- ABA MINHAS OFERTAS --}}
                    @if(auth()->user()->fornecedor)
                        <button
                            @click="abaAtiva = 'lotes'"
                            :class="{
                                'tab-active font-bold text-gray-800': abaAtiva === 'lotes',
                                'text-gray-500': abaAtiva !== 'lotes'
                            }"
                            class="tab transition-all pb-1 flex items-center justify-center gap-2">
                            <span>
                                Minhas Ofertas
                            </span>

                            @if(isset($novasPropostas) && $novasPropostas > 0)
                                <span
                                    class="rounded-full bg-yellow-400 text-gray-900 font-bold flex items-center justify-center transition-all duration-200"
                                    :class="abaAtiva === 'lotes'
                                        ? 'min-w-5 h-5 px-1.5 text-[10px]'
                                        : 'w-2 h-2'"
                                    title="{{ $novasPropostas }} {{ $novasPropostas === 1 ? 'nova proposta' : 'novas propostas' }}">
                                    <span x-show="abaAtiva === 'lotes'">
                                        {{ $novasPropostas }}
                                    </span>
                                </span>
                            @endif
                        </button>
                    @endif

                    {{-- ABA DEMANDAS --}}
                    <button
                        @click="abaAtiva = 'demandas'"
                        :class="{
                            'tab-active font-bold text-gray-800': abaAtiva === 'demandas',
                            'text-gray-500': abaAtiva !== 'demandas'
                        }"
                        class="tab transition-all pb-1 flex items-center justify-center gap-2">
                        <span>
                            Demandas da Empresa
                        </span>
                    </button>
                </div>

                {{-- ABA 1: MINHAS OFERTAS --}}
                @if(auth()->user()->fornecedor)
                    <div
                        x-show="abaAtiva === 'lotes'"
                        class="flex flex-col gap-3 mt-2">

                        @forelse($ofertas->take(5) as $oferta)
                            @php
                                $temNovaProposta =
                                    isset($ofertasComNovasPropostas)
                                    && $ofertasComNovasPropostas->has($oferta->id);
                                $negociacaoNova =
                                    $temNovaProposta
                                    ? $ofertasComNovasPropostas->get($oferta->id)
                                    : null;
                                $negociacao =
                                    $negociacaoNova
                                    ?? $oferta->negociacoes->sortByDesc('id')->first();
                            @endphp

                            {{-- CARD DA OFERTA --}}
                            <div
                                class="relative flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm transition-all hover:shadow-[0_0_20px_2px_rgba(0,0,0,0.15)]">

                                {{-- Indicador de nova proposta --}}
                                @if($temNovaProposta)
                                    <span
                                        class="absolute top-2 right-2 w-3 h-3 rounded-full bg-yellow-400"
                                        title="Nova proposta recebida"
                                    ></span>
                                @endif

                                {{-- IMAGEM DO PRODUTO --}}
                                <img
                                    class="w-20 h-20 object-cover rounded-lg shrink-0"
                                    src="{{ $oferta->produto->imagem
                                        ? (
                                            str_contains($oferta->produto->imagem, 'assets/')
                                            ? asset($oferta->produto->imagem)
                                            : asset('storage/' . $oferta->produto->imagem)
                                        )
                                        : asset('assets/milho.png') }}"
                                    alt="{{ $oferta->produto->nome ?? 'Produto' }}">

                                {{-- INFORMAÇÕES --}}
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-sm text-gray-800">
                                        [Oferta #{{ $oferta->id }}]:
                                        {{ number_format($oferta->quantidade, 2, ',', '.') }}
                                        {{ $oferta->unidade }}
                                        {{ $oferta->produto->nome ?? '' }}
                                    </h3>

                                    <p class="text-xs text-gray-500">
                                        Expira em:
                                        {{ $oferta->data_validade
                                            ? \Carbon\Carbon::parse($oferta->data_validade)->format('d/m/Y')
                                            : 'Sem data'
                                        }}
                                    </p>

                                    {{-- BOTÕES --}}
                                    <div class="flex justify-end gap-2 mt-2">
                                        @if($negociacao)

                                            <a
                                                href="{{ route('negociacoes.propostas', $oferta->id) }}"
                                                class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#236350] hover:text-[#174c3e] transition-colors">

                                                Ver negociação
                                                <span class="material-symbols-outlined text-base">
                                                    arrow_forward
                                                </span>
                                            </a>
                                        @endif

                                        <a
                                            href="{{ route('ofertas.show', $oferta->id) }}"
                                            class="btn-copav text-white p-2 btn-xs sm:btn-sm rounded-md">
                                            Ver detalhes
                                        </a>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <p class="text-gray-500 text-sm text-center py-4">
                                Você ainda não possui ofertas.
                            </p>
                        @endforelse

                        {{-- VER MAIS OFERTAS --}}
                        @if($ofertas->count() > 5)
                            <div class="flex justify-center mt-2">
                                <a
                                    href="{{ route('ofertas.index') }}"
                                    class="btn-copav w-full btn-sm rounded-md">
                                    Ver mais ofertas
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- ABA 2: DEMANDAS --}}
                <div
                    x-show="abaAtiva === 'demandas'"
                    @if(auth()->user()->fornecedor) x-cloak @endif
                    class="flex flex-col gap-3 mt-2">

                    @forelse($demandas ?? [] as $demanda)

                        {{-- CARD DA DEMANDA --}}
                        <div
                            class="flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-[0_0_8px_3px_rgba(0,0,0,0.2)] transition-shadow">

                            {{-- IMAGEM --}}
                            <img
                                class="w-20 h-20 rounded-lg object-cover shrink-0"
                                src="{{ $demanda->imagem
                                    ? (
                                        str_contains($demanda->imagem, 'assets/')
                                        ? asset($demanda->imagem)
                                        : asset('storage/' . str_replace('public/', '', $demanda->imagem))
                                    )
                                    : asset('assets/milho.png') }}"
                                alt="{{ $demanda->nome_produto ?? 'Demanda' }}"
                                onerror="this.onerror=null; this.src='{{ asset('assets/milho.png') }}';">

                            {{-- CONTEÚDO --}}
                            <div class="flex-1 min-w-0">

                                {{-- TÍTULO --}}
                                <h3 class="font-bold text-gray-800 text-sm leading-tight">
                                    [Demanda #{{ $demanda->id }}]:
                                    {{ $demanda->nome_produto }}
                                </h3>

                                {{-- DESCRIÇÃO --}}
                                @if($demanda->descricao)
                                    <p class="text-xs text-gray-500 mt-1 truncate">
                                        {{ $demanda->descricao }}
                                    </p>
                                @endif

                                {{-- INFORMAÇÕES --}}
                                <div class="grid grid-cols-2 gap-x-6 mt-1 text-xs leading-4 text-gray-500">

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
                                    @else
                                        <p></p>
                                    @endif

                                    @if($demanda->localizacao)
                                        <p>
                                            Localização:
                                            <strong class="text-gray-700">
                                                {{ $demanda->localizacao }}
                                            </strong>
                                        </p>
                                    @else
                                        <p></p>
                                    @endif

                                    @if($demanda->data_limite)
                                        <p>
                                            Prazo:
                                            <strong class="text-gray-700">
                                                {{ \Carbon\Carbon::parse($demanda->data_limite)->format('d/m/Y') }}
                                            </strong>
                                        </p>
                                    @else
                                        <p></p>
                                    @endif

                                    <p>
                                        Status:
                                        <strong class="text-gray-700">
                                            {{ ucfirst($demanda->status ?? 'aberta') }}
                                        </strong>
                                    </p>

                                </div>

                                {{-- BOTÃO --}}
                                <div class="flex justify-end mt-1">
                                    <a
                                        href="{{ route('demandas.show', $demanda->id) }}"
                                        class="btn-copav text-white px-3 py-1.5 text-xs rounded-md">
                                        Enviar Proposta
                                    </a>
                                </div>

                            </div>
                        </div>

                    @empty
                        <p class="text-gray-500 text-sm text-center py-4">
                            Nenhuma demanda corporativa aberta no momento.
                        </p>
                    @endforelse

                </div>
            </div>
        @endauth
    </div>

{{-- COLUNA DIREITA: VITRINE DE PRODUTOS --}}

    <div class="lg:col-span-7 flex flex-col gap-4 items-center">
        <h1 class="text-xl font-bold text-gray-800 self-start">
            Produtos que você pode se interessar...
        </h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 w-full">
            @forelse($produtos as $item)
                <a
                    href="{{ route('ofertas.show', $item->id) }}"
                    class="block h-full">
                    <div
                        class="bg-white shadow-md hover:shadow-[0_0_20px_2px_rgba(0,0,0,0.15)] p-3 rounded-2xl flex flex-col justify-between h-full transition-all border border-gray-100">
                        <div>

                            {{-- IMAGEM --}}
                            <img
                                class="w-full h-48 object-cover rounded-xl mb-3"
                                src="{{ $item->produto->imagem
                                    ? (
                                        str_contains($item->produto->imagem, 'assets/')
                                        ? asset($item->produto->imagem)
                                        : asset('storage/' . $item->produto->imagem)
                                    )
                                    : asset('assets/milho.png') }}"
                                alt="{{ $item->produto->nome ?? 'Produto' }}">

                            {{-- NOME --}}
                            <h2 class="font-bold text-gray-800 line-clamp-1">
                                {{ $item->produto->nome ?? 'Sem nome' }}
                            </h2>

                            {{-- DESCRIÇÃO --}}
                            <p class="text-xs text-gray-400 mt-1 line-clamp-2">
                                {{ $item->produto->descricao ?? '' }}
                            </p>
                        </div>

                        {{-- PREÇO / UNIDADE --}}
                        <div class="mt-3 flex justify-between items-center">
                            <span class="text-xs text-gray-500">
                                Unidade:
                                {{ $item->unidade }}
                            </span>
                            <span class="text-md font-bold text-[#79A961]">
                                R${{ number_format($item->valor ?? 0, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <p class="text-gray-500 col-span-full text-center py-6">
                    Nenhum lote disponível no mercado.
                </p>
            @endforelse
        </div>
    </div>
</div>

{{-- SCRIPTS --}}
@push('scripts')

<script>
    window.addEventListener('load', function () {
        if (!window.Echo) {
            console.error('Echo não foi carregado.');
            return;
        }
        @auth
            @if(auth()->user()->fornecedor)
                const fornecedorId =
                    {{ auth()->user()->fornecedor->id }};

                console.log(
                    'Escutando canal:',
                    `fornecedor.${fornecedorId}`
                );
                Echo
                    .private(`fornecedor.${fornecedorId}`)
                    .subscribed(() => {

                        console.log(
                            '✅ CANAL PRIVADO CONECTADO:',
                            `fornecedor.${fornecedorId}`
                        );
                    })
                    .error((error) => {

                        console.error(
                            '❌ ERRO NO CANAL PRIVADO:',
                            error
                        );
                    })
                    .listen('.proposta.criada', (event) => {

                        console.log(
                            '🔔 NOVA PROPOSTA RECEBIDA:',
                            event
                        );
                    });
            @endif
        @endauth
    });
</script>

@endpush
@endsection