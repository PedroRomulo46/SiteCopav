@extends('layouts.layout')

@section('title', 'Propostas da Oferta')

@section('conteudo')

<div class="max-w-6xl mx-auto my-8 px-4">

    {{-- Botão de Voltar --}}
    <div class="mb-4">
        <a
            href="{{ route('negociacoes.index') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#236350] transition-colors">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Minhas ofertas
        </a>
    </div>

    {{-- Cabeçalho da Oferta --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-5">
        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                <div>
                    <span class="text-xs uppercase tracking-wider font-semibold text-[#236350]">
                        Oferta #{{ $oferta->id }}
                    </span>
                    <h1 class="text-3xl font-bold text-gray-800 mt-1">
                        {{ $oferta->produto->nome }}
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $oferta->fornecedor->nome }}
                    </p>
                </div>

                <div class="text-left md:text-right">
                    <p class="text-xs uppercase tracking-wide text-gray-400 font-medium">
                        Quantidade ofertada
                    </p>
                    <p class="text-xl font-bold text-gray-800 mt-1">
                        {{ fmod($oferta->quantidade, 1) == 0
                            ? number_format($oferta->quantidade, 0, ',', '.')
                            : number_format($oferta->quantidade, 2, ',', '.') }}
                        {{ $oferta->unidade }}
                    </p>
                </div>
            </div>

            {{-- Resumo --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6">
                <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                    <p class="text-xs text-gray-500">
                        Propostas
                    </p>
                    <p class="text-xl font-bold text-gray-800 mt-1">
                        {{ $propostas->count() }}
                    </p>
                </div>

                <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4">
                    <p class="text-xs text-emerald-700">
                        Melhor valor
                    </p>
                    <p class="text-xl font-bold text-[#236350] mt-1">
                        @if($melhorValor !== null)

                            R$ {{ number_format($melhorValor, 2, ',', '.') }}
                        @else
                            —
                        @endif
                    </p>
                </div>

                <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                    <p class="text-xs text-gray-500">
                        Maior proposta
                    </p>
                    <p class="text-xl font-bold text-gray-800 mt-1">
                        @if($maiorValorTotal !== null)

                            R$ {{ number_format($maiorValorTotal, 2, ',', '.') }}
                        @else
                            —
                        @endif
                    </p>
                </div>

                <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                    <p class="text-xs text-gray-500">
                        Quantidade aceita
                    </p>
                    <p class="text-xl font-bold text-gray-800 mt-1">
                        {{ fmod($quantidadeAceita, 1) == 0
                            ? number_format($quantidadeAceita, 0, ',', '.')
                            : number_format($quantidadeAceita, 2, ',', '.') }}
                        <span class="text-sm font-medium">
                            {{ $oferta->unidade }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Lista de Propostas --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">
                        Propostas recebidas
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Analise as propostas desta oferta.
                    </p>
                </div>

                <span class="text-xs font-medium text-gray-500 bg-gray-100 px-3 py-1.5 rounded-full w-fit">
                    {{ $propostas->count() }}

                    {{ $propostas->count() == 1 ? 'resultado' : 'resultados' }}
                </span>
            </div>

            {{-- Filtros --}}
            <div class="mt-5">
                <div class="flex flex-wrap gap-2">
                    <a
                        href="{{ route('ofertas.propostas', [
                            'oferta' => $oferta,
                            'ordenar' => $ordenar
                        ]) }}"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                        {{ $status === 'todas'
                            ? 'bg-[#236350] text-white'
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Todas
                        <span class="ml-1 opacity-70">
                            {{ $totalPropostas }}
                        </span>
                    </a>
                    <a
                        href="{{ route('ofertas.propostas', [
                            'oferta' => $oferta,
                            'status' => 'pendente',
                            'ordenar' => $ordenar
                        ]) }}"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                        {{ $status === 'pendente'
                            ? 'bg-amber-500 text-white'
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Pendentes
                        <span class="ml-1 opacity-70">
                            {{ $pendentes }}
                        </span>
                    </a>

                    <a
                        href="{{ route('ofertas.propostas', [
                            'oferta' => $oferta,
                            'status' => 'aceita',
                            'ordenar' => $ordenar
                        ]) }}"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                        {{ $status === 'aceita'
                            ? 'bg-green-600 text-white'
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Aceitas
                        <span class="ml-1 opacity-70">
                            {{ $aceitas }}
                        </span>
                    </a>

                    <a
                        href="{{ route('ofertas.propostas', [
                            'oferta' => $oferta,
                            'status' => 'recusada',
                            'ordenar' => $ordenar
                        ]) }}"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                        {{ $status === 'recusada'
                            ? 'bg-red-500 text-white'
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Recusadas
                        <span class="ml-1 opacity-70">
                            {{ $recusadas }}
                        </span>
                    </a>
                </div>

                {{-- Ordenação --}}
                <div class="mt-4 flex flex-col sm:flex-row sm:items-center gap-2">

                    <label
                        for="ordenar"
                        class="text-sm text-gray-500">
                        Ordenar por:
                    </label>

                    <select
                        id="ordenar"
                        onchange="window.location.href = this.value"
                        class="select select-sm border-gray-200 bg-gray-50 text-sm focus:outline-none focus:border-[#236350]">
                        <option
                            value="{{ route('ofertas.propostas', [
                                'oferta' => $oferta,
                                'status' => $status,
                                'ordenar' => 'valor_total'
                            ]) }}"
                            {{ $ordenar === 'valor_total' ? 'selected' : '' }}>
                            Maior valor total
                        </option>

                        <option
                            value="{{ route('ofertas.propostas', [
                                'oferta' => $oferta,
                                'status' => $status,
                                'ordenar' => 'valor_unidade'
                            ]) }}"
                            {{ $ordenar === 'valor_unidade' ? 'selected' : '' }}>
                            Maior valor por unidade
                        </option>

                        <option
                            value="{{ route('ofertas.propostas', [
                                'oferta' => $oferta,
                                'status' => $status,
                                'ordenar' => 'quantidade'
                            ]) }}"
                            {{ $ordenar === 'quantidade' ? 'selected' : '' }}>
                            Maior quantidade
                        </option>

                        <option
                            value="{{ route('ofertas.propostas', [
                                'oferta' => $oferta,
                                'status' => $status,
                                'ordenar' => 'recentes'
                            ]) }}"
                            {{ $ordenar === 'recentes' ? 'selected' : '' }}>
                            Mais recentes
                        </option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Propostas --}}
        @if($propostas->count())
            <div class="divide-y divide-gray-100">
                @foreach($propostas as $index => $proposta)
                    @php

                        $valorTotal =
                            $proposta->valor *
                            ($proposta->quantidade ?? 0);

                    @endphp

                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

                            {{-- Informações do Cliente --}}
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 rounded-full bg-[#236350]/10 flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-[#236350]">
                                        person
                                    </span>
                                </div>

                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-bold text-gray-800">
                                            {{ $proposta->cliente->nome ?? $proposta->usuario->nome }}
                                        </h3>

                                        @if($index === 0 && $status === 'todas')
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 bg-amber-100 px-2 py-1 rounded-full">
                                                <span class="material-symbols-outlined text-sm">
                                                    emoji_events
                                                </span>
                                                Maior valor
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Negociação #{{ $proposta->negociacao_id }}
                                    </p>

                                    @if($proposta->observacao)
                                        <p class="text-sm text-gray-500 mt-2">
                                            "{{ $proposta->observacao }}"
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Valores --}}
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 lg:min-w-[430px]">

                                {{-- Quantidade --}}
                                <div>
                                    <p class="text-xs text-gray-400">
                                        Quantidade
                                    </p>
                                    <p class="font-semibold text-gray-800 mt-1">
                                        @if($proposta->quantidade !== null)
                                            {{ fmod($proposta->quantidade, 1) == 0
                                                ? number_format($proposta->quantidade, 0, ',', '.')
                                                : number_format($proposta->quantidade, 2, ',', '.') }}
                                            <span class="text-xs font-medium text-gray-500">
                                                {{ $oferta->unidade }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </p>
                                </div>

                                {{-- Valor Unitário --}}
                                <div>
                                    <p class="text-xs text-gray-400">
                                        Valor por unidade
                                    </p>
                                    <p class="font-semibold text-[#236350] mt-1">
                                        R$ {{ number_format($proposta->valor, 2, ',', '.') }}
                                    </p>
                                </div>

                                {{-- Valor Total --}}
                                <div>
                                    <p class="text-xs text-gray-400">
                                        Valor total
                                    </p>
                                    <p class="font-bold text-gray-800 mt-1">
                                        @if($proposta->quantidade !== null)

                                            R$ {{ number_format($valorTotal, 2, ',', '.') }}
                                        @else
                                            —
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Status + Ação --}}
                            <div class="flex flex-row lg:flex-col items-center lg:items-end gap-3">

                                @if($proposta->status === 'pendente')
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 bg-amber-100 px-3 py-1.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Pendente
                                    </span>

                                @elseif($proposta->status === 'aceita')
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-100 px-3 py-1.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Aceita
                                    </span>

                                @elseif($proposta->status === 'recusada')
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 bg-red-100 px-3 py-1.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Recusada
                                    </span>

                                @endif
                                <a
                                    href="{{ route('negociacoes.show', $proposta->negociacao_id) }}"
                                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#236350] hover:text-[#174c3e] transition-colors">
                                    Ver negociação
                                    <span class="material-symbols-outlined text-base">
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
                <div class="w-14 h-14 mx-auto rounded-full bg-gray-100 flex items-center justify-center">
                    <span class="material-symbols-outlined text-gray-400 text-3xl">
                        inbox
                    </span>
                </div>
                <p class="font-medium text-gray-600 mt-4">
                    Nenhuma proposta recebida
                </p>
                <p class="text-sm text-gray-400 mt-1">
                    Quando alguém enviar uma proposta, ela aparecerá aqui.
                </p>
            </div>
        @endif
    </div>
</div>

@endsection