@extends('layouts.layout')

@section('title', 'Minhas Compras')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-4 text-gray-600" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">

        {{-- Item 1: Início --}}
        <li>
            <a href="{{route('home')}}">
                Início
            </a>
        </li>

        {{-- Item 2: Página Atual --}}
        <li class="flex items-center space-x-2 hover:cursor-pointer">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <span class="font-semibold text-gray-800">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="w-full max-w-7xl mx-auto px-6 py-6 flex flex-col items-start text-left">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8 w-full text-left">
        <div class="text-left w-full">
            <h1 class="text-2xl font-bold text-gray-800 text-left">
                Painel de Compras
            </h1>
            <p class="text-gray-500 mt-1 text-left">
                Acompanhe as propostas enviadas e o andamento das suas negociações com fornecedores.
            </p>
        </div>
    </div>

    {{-- Mensagens --}}
    @if(session('sucesso'))
        <div class="mb-5 w-full flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            {{ session('sucesso') }}
        </div>
    @endif

    @if(session('erro'))
        <div class="mb-5 w-full flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <span class="material-symbols-outlined text-lg">error</span>
            {{ session('erro') }}
        </div>
    @endif

    @if(isset($negociacoes) && $negociacoes->count())

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 w-full text-left">

            @foreach($negociacoes as $negociacao)

            @php
                $oferta = $negociacao->oferta;

                $minhasPropostas = $negociacao->propostas
                    ->where('usuario_id', auth()->id());

                $ultimaProposta = $minhasPropostas->last();

                $minhaPropostaAceita = $minhasPropostas
                    ->first(function ($proposta) {
                        return $proposta->status === 'aceita';
                    });

                // Verifica se a negociação já foi paga/aprovada (você pode adaptar para a sua regra de status da negociação, ex: status === 'pago' ou 'aprovado')
                $compraAprovada = $negociacao->status === 'pago' || $negociacao->status === 'aprovado';
            @endphp

            {{-- Card da Compra / Negociação --}}
            <div
                id="compra-negociacao-{{ $negociacao->id }}"
                data-negociacao-id="{{ $negociacao->id }}"
                data-status="{{ $compraAprovada ? 'aprovada' : ($minhaPropostaAceita ? 'aceita' : 'em_negociacao') }}"
                class="group bg-white rounded-2xl border-3 overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-left
                    {{ $compraAprovada ? 'border-sky-500' : ($minhaPropostaAceita ? 'border-emerald-600' : 'border-amber-400') }}"
            >

                {{-- Imagem --}}
                <div class="relative h-44 bg-gray-100 overflow-hidden">
                    <img
                        src="{{ $oferta->produto && $oferta->produto->imagem 
                            ? (str_contains($oferta->produto->imagem, 'assets/') 
                                ? asset($oferta->produto->imagem) 
                                : asset('storage/' . $oferta->produto->imagem)) 
                            : asset('assets/milho.png') }}"
                        alt="{{ $oferta->produto->nome ?? 'Produto' }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                    {{-- Badge de Status da Negociação --}}
                    <div class="absolute top-3 left-3">
                        <span
                            id="status-compra-{{ $negociacao->id }}"
                            class="status-compra inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-white text-xs font-semibold shadow-sm
                                {{ $compraAprovada ? 'bg-sky-600' : ($minhaPropostaAceita ? 'bg-emerald-600' : 'bg-amber-500') }}"
                        >
                            <span class="material-symbols-outlined text-sm">
                                {{ $compraAprovada ? 'verified' : ($minhaPropostaAceita ? 'check_circle' : 'pending') }}
                            </span>

                            <span class="texto-status-compra">
                                {{ $compraAprovada ? 'Compra Aprovada' : ($minhaPropostaAceita ? 'Proposta Aceita' : 'Em Negociação') }}
                            </span>
                        </span>
                    </div>
                </div>

                {{-- Conteúdo --}}
                <div class="p-5 text-left">

                    {{-- Produto e Fornecedor --}}
                    <div class="mb-4 text-left">
                        <h2 class="text-xl font-bold text-gray-800 text-left">
                            {{ $oferta->produto->nome }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1 text-left flex items-center gap-1">
                            <span class="material-symbols-outlined text-base">store</span>
                            Fornecedor: <strong>{{ $oferta->fornecedor->nome }}</strong>
                        </p>
                    </div>

                    {{-- Quantidade Solicitada e Valor Enviado --}}
                    @if($ultimaProposta)
                        <div class="rounded-xl bg-gray-50 border border-gray-100 p-4 mb-4 text-left">
                            <p class="text-xs uppercase tracking-wide font-medium text-gray-400 mb-1">
                                Sua última proposta
                            </p>
                            <p class="text-2xl font-bold text-[#236350]">
                                R$ {{ number_format($ultimaProposta->valor, 2, ',', '.') }}
                                <span class="text-xs text-gray-500 font-normal">/ {{ $oferta->unidade }}</span>
                            </p>
                            <p class="text-xs text-gray-600 mt-1">
                                Qtd. proposta: <strong>{{ number_format($ultimaProposta->quantidade ?? 0, 0, ',', '.') }} {{ $oferta->unidade }}</strong>
                            </p>
                        </div>
                    @endif

                    {{-- Botão de negociação --}}
                    <div>
                        @php
                            if ($compraAprovada) {
                                $rotaBotao = route('compras.encomenda', $negociacao); // Ajuste para a sua rota de acompanhar encomenda
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
                            class="btn-copav flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                        >
                            <span class="material-symbols-outlined text-lg icone-botao-compra">
                                {{ $compraAprovada ? 'local_shipping' : ($minhaPropostaAceita ? 'payments' : 'library_books') }}
                            </span>

                            <span class="texto-botao-compra">
                                {{ $compraAprovada ? 'Acompanhar encomenda' : ($minhaPropostaAceita ? 'Seguir para o pagamento' : 'Ver Negociação') }}
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    @else

        {{-- Estado Vazio para Compras --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-sm w-full">
            <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-gray-400 text-3xl">shopping_bag</span>
            </div>
            <h2 class="text-xl font-bold text-gray-700 mt-4">
                Nenhuma compra em andamento
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Você ainda não enviou propostas para ofertas. Explore os lotes disponíveis para iniciar uma negociação!
            </p>
            {{-- Trocar rota depois --}}
            <a href="{{ route('ofertas.index') }}" class="btn-copav inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold mt-4 text-sm">
                Explorar Lotes
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

    document.querySelectorAll('[data-negociacao-id]').forEach(function (card) {
        const negociacaoId = Number(card.dataset.negociacaoId);

        window.Echo
            .private(`negociacao.${negociacaoId}`)
            .listen('.pagamento.aprovado', function (evento) {
                const status = document.getElementById(`status-compra-${negociacaoId}`);
                const botao = document.getElementById(`botao-compra-${negociacaoId}`);

                // Atualiza a borda do card para Azul (sky-600)
                card.classList.remove('border-amber-500', 'border-emerald-600');
                card.classList.add('border-sky-600');

                if (status) {
                    status.classList.remove('bg-amber-500', 'bg-emerald-600');
                    status.classList.add('bg-sky-600'); // Mantém o badge azul conforme o seu exemplo

                    const icone = status.querySelector('.material-symbols-outlined');
                    const texto = status.querySelector('.texto-status-compra');

                    if (icone) icone.textContent = 'verified';
                    if (texto) texto.textContent = 'Compra Aprovada';
                }

                if (botao) {
                    const textoBotao = botao.querySelector('.texto-botao-compra');
                    const iconeBotao = botao.querySelector('.icone-botao-compra');

                    botao.href = `/compras/${negociacaoId}/encomenda`;

                    if (textoBotao) textoBotao.textContent = 'Acompanhar encomenda';
                    if (iconeBotao) iconeBotao.textContent = 'local_shipping';
                }

                card.dataset.status = 'aprovada';
            });
    });
});
</script>
@endpush