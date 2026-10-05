@extends('layouts.layout')

@section('title', 'Propostas da Oferta')

@section('conteudo')

{{-- Botão Voltar --}}
<div class="mb-4">
    <a href="{{ route('negociacoes.index') }}"
       class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#236350] transition-colors mb-4">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Minhas ofertas
    </a>
</div>

<div class="max-w-6xl mx-auto mb-8 px-4">

    {{-- Cabeçalho da Oferta --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-5">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 text-left">
                <div>
                    <span class="text-xs uppercase tracking-wider font-semibold text-[#236350]">
                        OFERTA #{{ $oferta->id }}
                    </span>
                    <h1 class="text-3xl font-bold text-gray-800 mt-0.5">
                        {{ $oferta->produto->nome }}
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $oferta->fornecedor->nome }}
                    </p>
                </div>

                <div class="text-left sm:text-right">
                    <p class="text-xs uppercase tracking-wide text-gray-400 font-medium">
                        QUANTIDADE OFERTADA
                    </p>
                    <p class="text-xl text-left font-bold text-gray-800 mt-2">
                        {{ fmod($oferta->quantidade, 1) == 0
                            ? number_format($oferta->quantidade, 0, ',', '.')
                            : number_format($oferta->quantidade, 2, ',', '.') }}
                        {{ $oferta->unidade }}
                    </p>
                </div>
            </div>

            {{-- Resumo dos Cards em Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6 text-left">
                <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                    <p class="text-xs text-gray-500">Propostas</p>
                    <p class="text-xl font-bold text-gray-800 mt-1">{{ $propostas->count() }}</p>
                </div>

                <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4">
                    <p class="text-xs text-emerald-700">Melhor valor</p>
                    <p class="text-xl font-bold text-[#236350] mt-1">
                        @if($melhorValor !== null)
                            R$ {{ number_format($melhorValor, 2, ',', '.') }}
                        @else
                            —
                        @endif
                    </p>
                </div>

                <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                    <p class="text-xs text-gray-500">Maior proposta</p>
                    <p class="text-xl font-bold text-gray-800 mt-1">
                        @if($maiorValorTotal !== null)
                            R$ {{ number_format($maiorValorTotal, 2, ',', '.') }}
                        @else
                            —
                        @endif
                    </p>
                </div>

                <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                    <p class="text-xs text-gray-500">Quantidade aceita</p>
                    <p class="text-xl font-bold text-gray-800 mt-1">
                        {{ fmod($quantidadeAceita, 1) == 0
                            ? number_format($quantidadeAceita, 0, ',', '.')
                            : number_format($quantidadeAceita, 2, ',', '.') }}
                        <span class="text-sm font-medium">{{ $oferta->unidade }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Lista de Propostas --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        
        {{-- Topo da Lista de Propostas + Filtros --}}
        <div class="p-6 border-b border-gray-200 text-left">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Propostas recebidas</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Analise as propostas desta oferta.</p>
                </div>

                <span class="inline-block text-xs font-medium text-gray-600 bg-gray-100 px-3 py-1 rounded-full w-fit">
                    {{ $propostas->count() }} {{ $propostas->count() == 1 ? 'resultado' : 'resultados' }}
                </span>
            </div>

            {{-- Filtros e Ordenação --}}
            <div class="mt-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('negociacoes.propostas', ['oferta' => $oferta, 'ordenar' => $ordenar]) }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $status === 'todas' ? 'bg-[#236350] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Todas <span class="ml-1 opacity-80">{{ $totalPropostas }}</span>
                    </a>
                    <a href="{{ route('negociacoes.propostas', ['oferta' => $oferta, 'status' => 'pendente', 'ordenar' => $ordenar]) }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $status === 'pendente' ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Pendentes <span class="ml-1 opacity-80">{{ $pendentes }}</span>
                    </a>
                    <a href="{{ route('negociacoes.propostas', ['oferta' => $oferta, 'status' => 'aceita', 'ordenar' => $ordenar]) }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $status === 'aceita' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Aceitas <span class="ml-1 opacity-80">{{ $aceitas }}</span>
                    </a>
                    <a href="{{ route('negociacoes.propostas', ['oferta' => $oferta, 'status' => 'recusada', 'ordenar' => $ordenar]) }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $status === 'recusada' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Recusadas <span class="ml-1 opacity-80">{{ $recusadas }}</span>
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    <label for="ordenar" class="text-sm text-gray-500 whitespace-nowrap">Ordenar por:</label>
                    <select id="ordenar" onchange="window.location.href = this.value"
                            class="border border-gray-300 bg-white text-sm rounded-lg px-3 py-1.5 text-gray-700 focus:outline-none focus:border-[#236350]">
                        <option value="{{ route('negociacoes.propostas', ['oferta' => $oferta, 'status' => $status, 'ordenar' => 'valor_total']) }}" {{ $ordenar === 'valor_total' ? 'selected' : '' }}>Maior valor total</option>
                        <option value="{{ route('negociacoes.propostas', ['oferta' => $oferta, 'status' => $status, 'ordenar' => 'valor_unidade']) }}" {{ $ordenar === 'valor_unidade' ? 'selected' : '' }}>Maior valor por unidade</option>
                        <option value="{{ route('negociacoes.propostas', ['oferta' => $oferta, 'status' => $status, 'ordenar' => 'quantidade']) }}" {{ $ordenar === 'quantidade' ? 'selected' : '' }}>Maior quantidade</option>
                        <option value="{{ route('negociacoes.propostas', ['oferta' => $oferta, 'status' => $status, 'ordenar' => 'recentes']) }}" {{ $ordenar === 'recentes' ? 'selected' : '' }}>Mais recentes</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Lista de Itens --}}
        @if($propostas->count())
            <div class="divide-y divide-gray-100">
                @foreach($propostas as $index => $proposta)
                    @php
                        $valorTotal = $proposta->valor * ($proposta->quantidade ?? 0);
                    @endphp

                    <div class="relative p-6 hover:bg-gray-50/60 transition-colors {{ is_null($proposta->visualizada_em) ? 'bg-yellow-50/30' : '' }}">
                        @if(is_null($proposta->visualizada_em))
                            <span class="absolute top-4 right-4 w-2.5 h-2.5 rounded-full bg-yellow-400" title="Nova proposta"></span>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">

                            {{-- 1. ESQUERDA: Dados do Fornecedor (Ocupa 4 colunas) --}}
                            <div class="md:col-span-4 flex items-center gap-3.5 text-left">
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-gray-600">person</span>
                                </div>

                                <div class="text-left">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="font-bold text-gray-800 text-base">
                                            {{ $proposta->cliente->nome ?? ($proposta->usuario->nome ?? 'Fornecedor') }}
                                        </h3>

                                        @if($index === 0 && $status === 'todas')
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full">
                                                <span class="material-symbols-outlined text-sm">emoji_events</span>
                                                Maior valor
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-sm text-gray-500 mt-0.5">
                                        Negociação #{{ $proposta->negociacao_id }}
                                    </p>
                                </div>
                            </div>

                            {{-- 2. CENTRO: Quantidade, Valor Unitário e Valor Total (Ocupa 5 colunas) --}}
                            <div class="md:col-span-5 grid grid-cols-3 gap-2 text-left py-2 md:py-0">
                                <div>
                                    <p class="text-xs text-gray-400 font-medium">Quantidade</p>
                                    <p class="font-bold text-gray-800 mt-0.5 text-sm">
                                        @if($proposta->quantidade !== null)
                                            {{ fmod($proposta->quantidade, 1) == 0
                                                ? number_format($proposta->quantidade, 0, ',', '.')
                                                : number_format($proposta->quantidade, 2, ',', '.') }}
                                            <span class="text-xs font-normal text-gray-500">{{ $oferta->unidade }}</span>
                                        @else
                                            —
                                        @endif
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400 font-medium">Valor por unidade</p>
                                    <p class="font-bold text-[#236350] mt-0.5 text-sm">
                                        R$ {{ number_format($proposta->valor, 2, ',', '.') }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400 font-medium">Valor total</p>
                                    <p class="font-bold text-gray-800 mt-0.5 text-sm">
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
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 bg-amber-100 px-3 py-1 rounded-full w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Pendente
                                    </span>
                                @elseif($proposta->status === 'aceita')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-100 px-3 py-1 rounded-full w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Aceita
                                    </span>
                                @elseif($proposta->status === 'recusada')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-700 bg-red-100 px-3 py-1 rounded-full w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Recusada
                                    </span>
                                @endif
                                <a
                                    href="{{ route('propostas.visualizar', $proposta->id) }}"
                                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#236350] hover:text-[#174c3e] transition-colors">
                                    Ver negociação
                                    <span class="material-symbols-outlined text-base group-hover:translate-x-0.5 transition-transform">
                                        arrow_forward
                                    </span>
                                </a>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center">
                    <span class="material-symbols-outlined text-gray-400 text-2xl">inbox</span>
                </div>
                <p class="font-medium text-gray-600 mt-3">Nenhuma proposta recebida</p>
                <p class="text-sm text-gray-400 mt-0.5">Quando você receber propostas nesta oferta, elas aparecerão aqui.</p>
            </div>
        @endif

    </div>
</div>

@endsection