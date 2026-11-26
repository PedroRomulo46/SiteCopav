@extends('layouts.layout')

@section('title', 'Minhas Compras')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-4 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">

        {{-- Item 1: Início --}}
        <li class="inline-flex items-center">
            <a
                href="{{ route('home') }}"
                class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#236350] dark:hover:text-emerald-400 transition-colors"
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

<div class="w-full max-w-7xl mx-auto px-6 py-6 flex flex-col items-start text-left">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8 w-full text-left">
        <div class="text-left w-full">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 text-left">
                Painel de Compras
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 text-left">
                Acompanhe as propostas enviadas e o andamento das suas negociações com fornecedores.
            </p>
        </div>
    </div>

    {{-- Mensagens --}}
    @if(session('sucesso'))
        <div class="mb-5 w-full flex items-center gap-3 rounded-lg border border-green-200 dark:border-emerald-800/60 bg-green-50 dark:bg-emerald-950/50 px-4 py-3 text-sm text-green-800 dark:text-emerald-200">
            <span class="material-symbols-outlined text-lg leading-none">check_circle</span>
            <span>{{ session('sucesso') }}</span>
        </div>
    @endif

    @if(session('erro'))
        <div class="mb-5 w-full flex items-center gap-3 rounded-lg border border-red-200 dark:border-red-800/60 bg-red-50 dark:bg-red-950/50 px-4 py-3 text-sm text-red-800 dark:text-red-200">
            <span class="material-symbols-outlined text-lg leading-none">error</span>
            <span>{{ session('erro') }}</span>
        </div>
    @endif

    @if(isset($negociacoes) && $negociacoes->count())

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 w-full text-left">

            @foreach($negociacoes as $negociacao)

            @php
                $oferta = $negociacao->oferta;
                $produto = $oferta?->produto;
                $fornecedor = $oferta?->fornecedor;
                $unidade = $oferta?->unidade ?? 'un.';

                $minhasPropostas = ($negociacao->propostas ?? collect())
                    ->where('usuario_id', auth()->id());

                $ultimaProposta = $minhasPropostas->last();

                $minhaPropostaAceita = $minhasPropostas
                    ->first(function ($proposta) {
                        return $proposta->status === 'aceita';
                    });

                $compraAprovada = in_array($negociacao->status, ['pago', 'aprovado'], true);
            @endphp

            {{-- Card da Compra / Negociação --}}
            <div
                id="compra-negociacao-{{ $negociacao->id }}"
                data-negociacao-id="{{ $negociacao->id }}"
                data-status="{{ $compraAprovada ? 'aprovada' : ($minhaPropostaAceita ? 'aceita' : 'em_negociacao') }}"
                class="group bg-white dark:bg-[#111827] rounded-2xl border-2 overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-left
                    {{ $compraAprovada ? 'border-sky-500 dark:border-sky-400' : ($minhaPropostaAceita ? 'border-emerald-600 dark:border-emerald-400' : 'border-amber-400 dark:border-amber-400/80') }}"
            >

                {{-- Imagem --}}
                <div class="relative h-44 bg-gray-100 dark:bg-slate-800 overflow-hidden">
                    <img
                        src="{{ $produto && $produto->imagem
                            ? (str_contains($produto->imagem, 'assets/')
                                ? asset($produto->imagem)
                                : asset('storage/' . $produto->imagem))
                            : asset('assets/milho.png') }}"
                        alt="{{ $produto->nome ?? 'Produto' }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                    {{-- Badge de Status da Negociação --}}
                    <div class="absolute top-3 left-3">
                        <span
                            id="status-compra-{{ $negociacao->id }}"
                            class="status-compra inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-white text-xs font-semibold shadow-sm
                                {{ $compraAprovada ? 'bg-sky-600' : ($minhaPropostaAceita ? 'bg-emerald-600' : 'bg-amber-500') }}"
                        >
                            <span class="material-symbols-outlined text-sm leading-none">
                                {{ $compraAprovada ? 'verified' : ($minhaPropostaAceita ? 'check_circle' : 'pending') }}
                            </span>

                            <span class="texto-status-compra leading-none">
                                {{ $compraAprovada ? 'Compra Aprovada' : ($minhaPropostaAceita ? 'Proposta Aceita' : 'Em Negociação') }}
                            </span>
                        </span>
                    </div>
                </div>

                {{-- Conteúdo --}}
                <div class="p-5 text-left">

                    {{-- Produto e Fornecedor --}}
                    <div class="mb-4 text-left">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 text-left">
                            {{ $produto->nome ?? 'Produto' }}
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 text-left flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base leading-none shrink-0">store</span>
                            <span>Fornecedor: <strong class="text-gray-700 dark:text-gray-200">{{ $fornecedor->nome ?? 'Copav' }}</strong></span>
                        </p>
                    </div>

                    {{-- Quantidade Solicitada e Valor Enviado --}}
                    @if($ultimaProposta)
                        <div class="rounded-xl bg-gray-50 dark:bg-[#161f30] border border-gray-200/80 dark:border-[#273449] p-4 mb-4 text-left">
                            <p class="text-xs uppercase tracking-wide font-medium text-gray-400 dark:text-gray-500 mb-1">
                                Sua última proposta
                            </p>
                            <p class="text-2xl font-bold text-[#236350] dark:text-emerald-400">
                                R$ {{ number_format($ultimaProposta->valor, 2, ',', '.') }}
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-normal">/ {{ $unidade }}</span>
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                Qtd. proposta: <strong class="text-gray-800 dark:text-gray-100">{{ number_format($ultimaProposta->quantidade ?? 0, 0, ',', '.') }} {{ $unidade }}</strong>
                            </p>
                        </div>
                    @endif

                    {{-- Botão de negociação --}}
                    <div>
                        @php
                            if ($compraAprovada) {
                                $rotaBotao = route('compras.encomenda', $negociacao);
                                $textoBotao = 'Acompanhar encomenda';
                                $iconeBotao = 'local_shipping';
                            } elseif ($minhaPropostaAceita) {
                                $rotaBotao = route('pagamentos.show', $negociacao);
                                $textoBotao = 'Seguir para o pagamento';
                                $iconeBotao = 'payments';
                            } else {
                                $rotaBotao = route('negociacoes.show', $negociacao);
                                $textoBotao = 'Ver Negociação';
                                $iconeBotao = 'library_books';
                            }
                        @endphp

                        <a
                            id="botao-compra-{{ $negociacao->id }}"
                            href="{{ $rotaBotao }}"
                            class="btn-copav flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white transition-colors"
                        >
                            <span class="material-symbols-outlined text-lg leading-none icone-botao-compra">
                                {{ $iconeBotao }}
                            </span>

                            <span class="texto-botao-compra">
                                {{ $textoBotao }}
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    @else

        {{-- Estado Vazio para Compras --}}
        <div class="bg-white dark:bg-[#111827] rounded-2xl border border-gray-200 dark:border-[#273449] p-12 text-center shadow-sm w-full">
            <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-3xl leading-none">shopping_bag</span>
            </div>
            <h2 class="text-xl font-bold text-gray-700 dark:text-gray-100 mt-4">
                Nenhuma compra em andamento
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Você ainda não enviou propostas para ofertas. Explore os lotes disponíveis para iniciar uma negociação!
            </p>
            <a
                href="{{ route('ofertas.index') }}"
                class="btn-copav inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-white mt-4 text-sm transition-colors"
            >
                <span>Explorar Lotes</span>
            </a>
        </div>

    @endif

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.Echo) {
        console.error('Echo não foi carregado na página Minhas Compras.');
        return;
    }

    const rotaEncomendaTemplate = @json(route('compras.encomenda', ['negociacao' => '__ID__']));

    document.querySelectorAll('[data-negociacao-id]').forEach(function (card) {
        const negociacaoId = Number(card.dataset.negociacaoId);

        window.Echo
            .private(`negociacao.${negociacaoId}`)
            .listen('.pagamento.aprovado', function (evento) {
                const status = document.getElementById(`status-compra-${negociacaoId}`);
                const botao = document.getElementById(`botao-compra-${negociacaoId}`);

                card.classList.remove('border-amber-400', 'border-amber-500', 'border-emerald-600', 'dark:border-emerald-400');
                card.classList.add('border-sky-500', 'dark:border-sky-400');

                if (status) {
                    status.classList.remove('bg-amber-500', 'bg-emerald-600');
                    status.classList.add('bg-sky-600');

                    const icone = status.querySelector('.material-symbols-outlined');
                    const texto = status.querySelector('.texto-status-compra');

                    if (icone) icone.textContent = 'verified';
                    if (texto) texto.textContent = 'Compra Aprovada';
                }

                if (botao) {
                    const textoBotao = botao.querySelector('.texto-botao-compra');
                    const iconeBotao = botao.querySelector('.icone-botao-compra');

                    botao.href = rotaEncomendaTemplate.replace('__ID__', negociacaoId);

                    if (textoBotao) textoBotao.textContent = 'Acompanhar encomenda';
                    if (iconeBotao) iconeBotao.textContent = 'local_shipping';
                }

                card.dataset.status = 'aprovada';
            });
    });
});
</script>
@endpush