@extends('layouts.layout')

@section('title', 'Propostas da Oferta')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
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
                Negociações de {{ $oferta->produto->nome ?? 'Produto' }}
            </span>
        </li>
    </ol>
</nav>

<div class="max-w-6xl mx-auto mb-8 px-4">

    {{-- Cabeçalho da Oferta --}}
    <div class="bg-white dark:bg-[#111827] rounded-2xl border border-gray-200 dark:border-[#273449] shadow-sm overflow-hidden mb-5">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 text-left">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-0.5">
                        {{ $oferta->produto->nome ?? 'Produto' }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $oferta->fornecedor->nome ?? 'Fornecedor' }}
                    </p>
                </div>

                <div class="text-left sm:text-right">
                    <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500 font-medium">
                        QUANTIDADE OFERTADA
                    </p>
                    <p class="text-xl font-bold text-gray-800 dark:text-gray-100 mt-2">
                        {{ fmod($oferta->quantidade, 1) == 0
                            ? number_format($oferta->quantidade, 0, ',', '.')
                            : number_format($oferta->quantidade, 2, ',', '.') }}
                        {{ $oferta->unidade }}
                    </p>
                </div>
            </div>

            {{-- Resumo dos Cards em Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6 text-left">
                <div class="rounded-xl bg-gray-50 dark:bg-[#161f30] border border-gray-200/80 dark:border-[#273449] p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Propostas</p>
                    <p id="contador-propostas"
                        class="text-xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $totalPropostas }}</p>
                </div>

                <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-100 dark:border-emerald-800/60 p-4">
                    <p class="text-xs text-emerald-700 dark:text-emerald-300">Melhor valor</p>
                    <p id="card-melhor-valor"
                        class="text-xl font-bold text-[#236350] dark:text-emerald-400 mt-1">
                        @if($melhorValor !== null)
                            R$ {{ number_format($melhorValor, 2, ',', '.') }}
                        @else
                            —
                        @endif
                    </p>
                </div>

                <div class="rounded-xl bg-gray-50 dark:bg-[#161f30] border border-gray-200/80 dark:border-[#273449] p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Maior proposta</p>
                    <p id="card-maior-valor-total" class="text-xl font-bold text-gray-800 dark:text-gray-100 mt-1">
                        @if($maiorValorTotal !== null)
                            R$ {{ number_format($maiorValorTotal, 2, ',', '.') }}
                        @else
                            —
                        @endif
                    </p>
                </div>

                <div class="rounded-xl bg-gray-50 dark:bg-[#161f30] border border-gray-200/80 dark:border-[#273449] p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Quantidade aceita</p>
                    <p id="card-quantidade-aceita"
                        class="text-xl font-bold text-gray-800 dark:text-gray-100 mt-1">
                        {{ fmod($quantidadeAceita, 1) == 0
                            ? number_format($quantidadeAceita, 0, ',', '.')
                            : number_format($quantidadeAceita, 2, ',', '.') }}
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $oferta->unidade }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Lista de Propostas --}}
    <div class="bg-white dark:bg-[#111827] rounded-2xl border border-gray-200 dark:border-[#273449] shadow-sm overflow-hidden">

        {{-- Topo da Lista de Propostas + Filtros --}}
        <div class="p-6 border-b border-gray-200 dark:border-[#273449] text-left">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Propostas recebidas</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Analise as propostas desta oferta.</p>
                </div>

                <span id="contador-resultados"
                    class="inline-block text-xs font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-slate-800 border border-transparent dark:border-[#273449] px-3 py-1 rounded-full w-fit">
                    {{ $propostas->count() }}
                    {{ $propostas->count() == 1 ? 'resultado' : 'resultados' }}
                </span>
            </div>

            {{-- Filtros e Ordenação --}}
            <div class="mt-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('negociacoes.propostas', ['oferta' => $oferta, 'status' => 'todas', 'ordenar' => $ordenar]) }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $status === 'todas' ? 'bg-[#236350] text-white' : 'bg-gray-100 dark:bg-[#161f30] text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-800' }}">
                        Todas <span class="ml-1 opacity-80">{{ $totalPropostas }}</span>
                    </a>
                    <a href="{{ route('negociacoes.propostas', ['oferta' => $oferta, 'status' => 'pendente', 'ordenar' => $ordenar]) }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $status === 'pendente' ? 'bg-amber-500 text-white' : 'bg-gray-100 dark:bg-[#161f30] text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-800' }}">
                        Pendentes <span class="ml-1 opacity-80">{{ $pendentes }}</span>
                    </a>
                    <a href="{{ route('negociacoes.propostas', ['oferta' => $oferta, 'status' => 'aceita', 'ordenar' => $ordenar]) }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $status === 'aceita' ? 'bg-green-600 text-white' : 'bg-gray-100 dark:bg-[#161f30] text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-800' }}">
                        Aceitas <span class="ml-1 opacity-80">{{ $aceitas }}</span>
                    </a>
                    <a href="{{ route('negociacoes.propostas', ['oferta' => $oferta, 'status' => 'recusada', 'ordenar' => $ordenar]) }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $status === 'recusada' ? 'bg-red-600 text-white' : 'bg-gray-100 dark:bg-[#161f30] text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-800' }}">
                        Recusadas <span class="ml-1 opacity-80">{{ $recusadas }}</span>
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    <label for="ordenar" class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">Ordenar por:</label>
                    <select id="ordenar" onchange="window.location.href = this.value"
                            class="border border-gray-300 dark:border-[#273449] bg-white dark:bg-[#161f30] text-sm rounded-lg px-3 py-1.5 text-gray-700 dark:text-gray-200 outline-none focus:border-[#236350] dark:focus:border-emerald-400">
                        <option value="{{ route('negociacoes.propostas', ['oferta' => $oferta, 'status' => $status, 'ordenar' => 'valor_total']) }}" {{ $ordenar === 'valor_total' ? 'selected' : '' }}>Maior valor total</option>
                        <option value="{{ route('negociacoes.propostas', ['oferta' => $oferta, 'status' => $status, 'ordenar' => 'valor_unidade']) }}" {{ $ordenar === 'valor_unidade' ? 'selected' : '' }}>Maior valor por unidade</option>
                        <option value="{{ route('negociacoes.propostas', ['oferta' => $oferta, 'status' => $status, 'ordenar' => 'quantidade']) }}" {{ $ordenar === 'quantidade' ? 'selected' : '' }}>Maior quantidade</option>
                        <option value="{{ route('negociacoes.propostas', ['oferta' => $oferta, 'status' => $status, 'ordenar' => 'recentes']) }}" {{ $ordenar === 'recentes' ? 'selected' : '' }}>Mais recentes</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Lista de Itens --}}
        <div id="lista-propostas">
            @if($propostas->count())
                <div class="divide-y divide-gray-100 dark:divide-[#273449]">
                @foreach($propostas as $index => $proposta)
                    @php
                        $valorTotal = $proposta->valor * ($proposta->quantidade ?? 0);
                    @endphp

                    <div
                        class="group relative p-6 hover:bg-gray-50/60 dark:hover:bg-slate-800/40 transition-colors {{ is_null($proposta->visualizada_em) && $proposta->status === 'pendente' ? 'bg-yellow-50/30 dark:bg-amber-950/20' : '' }}"
                        data-proposta-id="{{ $proposta->id }}"
                    >
                        @if(is_null($proposta->visualizada_em) && $proposta->status === 'pendente')
                            <span
                                class="indicador-nova-proposta absolute top-4 right-4 w-2.5 h-2.5 rounded-full bg-yellow-400 dark:bg-amber-400"
                                title="Nova proposta"
                            ></span>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">

                            {{-- 1. ESQUERDA: Dados do Fornecedor (Ocupa 4 colunas) --}}
                            <div class="md:col-span-4 flex items-center gap-3.5 text-left">
                                <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-gray-600 dark:text-gray-300 leading-none">person</span>
                                </div>

                                <div class="text-left">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="font-bold text-gray-800 dark:text-gray-100 text-base">
                                            {{ $proposta->cliente->nome ?? ($proposta->usuario->nome ?? 'Fornecedor') }}
                                        </h3>

                                        @if($index === 0 && $status === 'todas')
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-800 dark:text-amber-300 bg-amber-100 dark:bg-amber-950/60 border border-transparent dark:border-amber-800/60 px-2 py-0.5 rounded-full">
                                                <span class="material-symbols-outlined text-sm leading-none">emoji_events</span>
                                                <span>Maior valor</span>
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                        Negociação #{{ $proposta->negociacao_id }}
                                    </p>
                                </div>
                            </div>

                            {{-- 2. CENTRO: Quantidade, Valor Unitário e Valor Total (Ocupa 5 colunas) --}}
                            <div class="md:col-span-5 grid grid-cols-3 gap-2 text-left py-2 md:py-0">
                                <div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">Quantidade</p>
                                    <p class="font-bold text-gray-800 dark:text-gray-100 mt-0.5 text-sm">
                                        @if($proposta->quantidade !== null)
                                            {{ fmod($proposta->quantidade, 1) == 0
                                                ? number_format($proposta->quantidade, 0, ',', '.')
                                                : number_format($proposta->quantidade, 2, ',', '.') }}
                                            <span class="text-xs font-normal text-gray-500 dark:text-gray-400">{{ $oferta->unidade }}</span>
                                        @else
                                            —
                                        @endif
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">Valor por unidade</p>
                                    <p class="font-bold text-[#236350] dark:text-emerald-400 mt-0.5 text-sm">
                                        R$ {{ number_format($proposta->valor, 2, ',', '.') }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">Valor total</p>
                                    <p class="font-bold text-gray-800 dark:text-gray-100 mt-0.5 text-sm">
                                        @if($proposta->quantidade !== null)
                                            R$ {{ number_format($valorTotal, 2, ',', '.') }}
                                        @else
                                            —
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- 3. DIREITA: Status e Botão Ver Negociação (Ocupa 3 colunas) --}}
                            <div class="md:col-span-3 flex flex-col md:items-end justify-center gap-1.5 text-left md:text-right">
                                @if($proposta->status === 'pendente')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-950/60 border border-transparent dark:border-amber-800/60 px-3 py-1 rounded-full w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-amber-400"></span>
                                        Pendente
                                    </span>
                                @elseif($proposta->status === 'aceita')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 dark:text-emerald-300 bg-green-100 dark:bg-emerald-950/60 border border-transparent dark:border-emerald-800/60 px-3 py-1 rounded-full w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 dark:bg-emerald-400"></span>
                                        Aceita
                                    </span>
                                @elseif($proposta->status === 'recusada')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-950/60 border border-transparent dark:border-red-800/60 px-3 py-1 rounded-full w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 dark:bg-red-400"></span>
                                        Recusada
                                    </span>
                                @elseif($proposta->status === 'cancelada')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-slate-800 border border-transparent dark:border-slate-700 px-3 py-1 rounded-full w-fit">
                                        Cancelada
                                    </span>
                                @endif
                                <a
                                    href="{{ route('propostas.visualizar', $proposta->id) }}"
                                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#236350] dark:text-emerald-400 hover:text-[#174c3e] dark:hover:text-emerald-300 transition-colors">
                                    <span>Ver negociação</span>
                                    <span class="material-symbols-outlined text-base leading-none group-hover:translate-x-0.5 transition-transform">
                                        arrow_forward
                                    </span>
                                </a>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center mensagem-sem-propostas">
                <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                    <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-2xl leading-none">inbox</span>
                </div>
                <p class="font-medium text-gray-600 dark:text-gray-300 mt-3">Nenhuma proposta recebida</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">Quando você receber propostas nesta oferta, elas aparecerão aqui.</p>
            </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const lista = document.getElementById('lista-propostas');

    if (!lista) {
        console.error('Lista de propostas não encontrada.');
        return;
    }

    if (!window.Echo) {
        console.error('Laravel Echo não foi carregado.');
        return;
    }

    const ofertaId = {{ $oferta->id }};
    const rotaCardPropostaTemplate = @json(route('propostas.card', ['proposta' => '__ID__']));

    window.Echo.private(`oferta.${ofertaId}`)
        .listen('.proposta.criada', function (evento) {
            const proposta = evento.proposta;

            if (!proposta || Number(proposta.oferta_id) !== Number(ofertaId)) {
                return;
            }

            if (lista.querySelector(`[data-proposta-id="${proposta.id}"]`)) {
                return;
            }

            fetch(rotaCardPropostaTemplate.replace('__ID__', proposta.id))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro ao carregar o cartão da proposta.');
                    }

                    return response.text();
                })
                .then(html => {
                    lista.querySelector('.mensagem-sem-propostas')?.remove();
                    lista.insertAdjacentHTML('afterbegin', html);

                    const contador = document.getElementById('contador-propostas');

                    if (contador) {
                        contador.textContent = Number(contador.textContent) + 1;
                    }
                })
                .catch(error => {
                    console.error('Erro ao inserir proposta:', error);
                });
        })
        .listen('.proposta.atualizada', function (evento) {
            const proposta = evento.proposta;

            if (!proposta || Number(proposta.oferta_id) !== Number(ofertaId)) {
                return;
            }

            const cartao = lista.querySelector(
                `[data-proposta-id="${proposta.id}"]`
            );

            if (!cartao) {
                console.log('Cartão da proposta atualizada não encontrado:', proposta.id);
                return;
            }

            fetch(rotaCardPropostaTemplate.replace('__ID__', proposta.id))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro ao carregar o cartão atualizado.');
                    }

                    return response.text();
                })
                .then(html => {
                    cartao.outerHTML = html;

                    if (['aceita', 'recusada', 'cancelada'].includes(proposta.status)) {
                        const indicador = document.querySelector(
                            `[data-proposta-id="${proposta.id}"] .indicador-nova-proposta`
                        );

                        indicador?.remove();
                    }
                    console.log('Cartão da proposta atualizado sem recarregar a página.');
                })
                .catch(error => {
                    console.error('Erro ao atualizar cartão:', error);
                });
        });
});
</script>
@endpush

@endsection