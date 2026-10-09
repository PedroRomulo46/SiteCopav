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

        {{-- BLOCO INFERIOR - FORNECEDORES E ADMINISTRADORES --}}
        @auth
            @if(auth()->user()->user_type !== 'cliente')
                <div
                    class="bg-white rounded-xl p-4 flex flex-col gap-3 shadow-md"
                    x-data="{ abaAtiva: '{{ auth()->user()->fornecedor ? 'lotes' : 'demandas' }}' }">

                    {{-- ABAS --}}
                    <div
                        role="tablist"
                        class="tabs tabs-border w-full flex justify-around border-b pb-2">

                        {{-- ABA MINHAS OFERTAS --}}
                        @if(auth()->user()->fornecedor)
                        <div
                            class="bg-white rounded-xl p-4 flex flex-col gap-3 shadow-md"
                            x-data="{ abaAtiva: 'lotes' }">


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

                            <span>Minhas Ofertas</span>

                            <span
                                id="indicador-novas-propostas"
                                class="rounded-full bg-yellow-400 text-gray-900 font-bold items-center justify-center transition-all duration-200"
                                :class="abaAtiva === 'lotes'
                                    ? 'min-w-5 h-5 px-1.5 text-[10px]'
                                    : 'w-2 h-2'"
                                style="{{ $novasPropostas > 0 ? 'display: flex;' : 'display: none;' }}"
                                title="{{ $novasPropostas }} {{ $novasPropostas === 1 ? 'nova proposta' : 'novas propostas' }}">

                                <span id="contador-novas-propostas">
                                    {{ $novasPropostas }}
                                </span>
                            </span>
                        </button>

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
                    <div
                        x-show="abaAtiva === 'lotes'"
                        class="flex flex-col gap-3 mt-2">

                        @forelse($ofertas->take(5) as $oferta)
                        
                            @php
                                $quantidadeNovasPropostas = $ofertasComNovasPropostas->get($oferta->id, 0);

                                $temNovaProposta = $quantidadeNovasPropostas > 0;

                                $negociacao = $oferta->negociacoes
                                    ->sortByDesc('id')
                                    ->first();
                            @endphp

                            {{-- CARD DA OFERTA --}}
                            <div
                                class="card-oferta-fornecedor relative flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm transition-all hover:shadow-[0_0_20px_2px_rgba(0,0,0,0.15)]"
                                data-oferta-id="{{ $oferta->id }}"
                                data-novas-propostas="{{ $quantidadeNovasPropostas }}"
                            >

                                
                            {{-- Contador de propostas não visualizadas --}}
                            <span
                                class="contador-propostas-oferta absolute top-2 right-2 min-w-6 h-6 px-1 rounded-full bg-yellow-400 text-gray-900 text-xs font-bold items-center justify-center shadow-sm"
                                style="{{ $quantidadeNovasPropostas > 0 ? 'display: flex;' : 'display: none;' }}"
                                title="{{ $quantidadeNovasPropostas }} {{ $quantidadeNovasPropostas === 1 ? 'nova proposta' : 'novas propostas' }}"
                            >
                                {{ $quantidadeNovasPropostas }}
                            </span>

                                {{-- Imagem do produto --}}
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

                                {{-- Informações --}}
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

                                    {{-- Botões --}}
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

                        {{-- Ver mais ofertas --}}
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

                    {{-- ABA 2: DEMANDAS --}}
                    <div
                        x-show="abaAtiva === 'demandas'"
                        x-cloak
                        class="flex flex-col gap-3 mt-2">

                        @forelse($demandas ?? [] as $demanda)

                            {{-- CARD DA DEMANDA --}}
                            <div
                                class="flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-[0_0_8px_3px_rgba(0,0,0,0.2)] transition-shadow">

                                {{-- Imagem --}}
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

                                {{-- Conteúdo --}}
                                <div class="flex-1 min-w-0">

                                    {{-- Título --}}
                                    <h3 class="font-bold text-gray-800 text-sm leading-tight">
                                        [Demanda #{{ $demanda->id }}]:
                                        {{ $demanda->nome_produto }}
                                    </h3>

                                    {{-- Descrição --}}
                                    @if($demanda->descricao)
                                        <p class="text-xs text-gray-500 mt-1 truncate">
                                            {{ $demanda->descricao }}
                                        </p>
                                    @endif

                                    {{-- Informações --}}
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

                                    {{-- Botão Enviar Propost --}}
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
<<<<<<< HEAD
            </div>
=======
>>>>>>> 80a113f9c4705b8257e1c5446e78617d094a3526
            @endif
        @endauth
    </div>

{{-- COLUNA DIREITA: VITRINE DE PRODUTOS --}}
    <div class="lg:col-span-7 flex flex-col gap-4 items-center">
        <h1 class="text-xl font-bold text-gray-800 self-start">
            Produtos que você pode se interessar...
        </h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 w-full">
            @forelse($produtos as $item)
                <a href="{{ route('ofertas.show', $item->id) }}"
                    class="block h-full group">
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

                        {{-- BOTÃO COM CONTRASTE CORRIGIDO NO HOVER --}}
                        <div class="mt-2 w-full text-emerald-700 border border-emerald-600 group-hover:bg-emerald-700 group-hover:!text-white text-center py-1.5 rounded-md text-xs font-semibold transition-colors">
                            Ver detalhes
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
                const fornecedorId = {{ auth()->user()->fornecedor->id }};

                const canal = window.Echo.private(`fornecedor.${fornecedorId}`);

                console.log('Escutando canal:', `fornecedor.${fornecedorId}`);

                canal.subscribed(() => {
                    console.log('✅ Canal privado conectado:', `fornecedor.${fornecedorId}`);
                });

                canal.error((error) => {
                    console.error('❌ Erro no canal privado:', error);
                });

                canal.listen('.proposta.criada', (event) => {
                    console.log('🔔 Nova proposta recebida:', event);

                    const proposta = event.proposta;

                    if (!proposta || !proposta.negociacao) {
                        console.warn('Evento recebido sem os dados da negociação.');
                        return;
                    }

                    const ofertaId = Number(proposta.negociacao.oferta_id);

                    if (!ofertaId) {
                        console.warn('Não foi possível identificar a oferta.');
                        return;
                    }

                    const card = document.querySelector(
                        `.card-oferta-fornecedor[data-oferta-id="${ofertaId}"]`
                    );

                    // Atualiza o card apenas se ele estiver na lista atual.
                    if (!card) {
                        console.info(
                            'A oferta não está entre os cards exibidos:',
                            ofertaId
                        );
                    } else {
                        const contador = card.querySelector('.contador-propostas-oferta');

                        if (contador) {
                            const quantidadeAtual = Number(card.dataset.novasPropostas || 0);
                            const novaQuantidade = quantidadeAtual + 1;

                            card.dataset.novasPropostas = novaQuantidade;
                            contador.textContent = novaQuantidade;
                            contador.style.display = 'flex';
}

                        // Indicador visual de nova atividade
                        if (!card.querySelector('.indicador-proposta-recente')) {
                            const indicador = document.createElement('span');
                            indicador.className = 'indicador-proposta-recente';
                            indicador.setAttribute('aria-label', 'Nova proposta recebida');
                            card.appendChild(indicador);
                        }
                    }

                    // Atualiza o total da aba
                    const contadorGeral = document.getElementById(
                        'contador-novas-propostas'
                    );

                    if (contadorGeral) {
                        const totalAtual = Number(
                            contadorGeral.textContent.trim() || 0
                        );

                        contadorGeral.textContent = totalAtual + 1;
                    }

                    // Reordena os cards: mais propostas primeiro.
                    const lista = card?.parentElement;

                    if (lista) {
                        const cards = Array.from(
                            lista.querySelectorAll('.card-oferta-fornecedor')
                        );

                        cards.sort((a, b) => {
                            const totalA = Number(a.dataset.novasPropostas || 0);
                            const totalB = Number(b.dataset.novasPropostas || 0);

                            if (totalA !== totalB) {
                                return totalB - totalA;
                            }

                            return Number(b.dataset.ofertaId) -
                                   Number(a.dataset.ofertaId);
                        });

                        cards.forEach(item => lista.appendChild(item));
                    }
                });
            @endif
        @endauth
    });
</script>
@endpush
@endsection