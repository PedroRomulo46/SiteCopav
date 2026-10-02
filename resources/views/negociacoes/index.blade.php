@extends('layouts.layout')

@section('title', 'Minhas Negociações')

@section('conteudo')

<div class="max-w-6xl mx-auto my-6">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                Negociações
            </span>

            <h1 class="text-2xl font-bold text-gray-800">
                Minhas negociações
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Acompanhe suas negociações e propostas.
            </p>
        </div>

        {{-- Apenas clientes podem iniciar uma negociação manualmente --}}
        @if(auth()->user()->user_type !== 'fornecedor')
            <a
                href="{{ route('negociacoes.create') }}"
                class="inline-flex items-center justify-center gap-2 bg-[#236350] hover:bg-[#1B4D3E] text-white px-4 py-2.5 rounded-md text-sm font-medium transition-colors shadow-sm"
            >
                <span class="material-symbols-outlined text-lg">
                    add
                </span>

                Nova negociação
            </a>
        @endif

    </div>


    {{-- Mensagens --}}
    @if(session('sucesso'))

        <div class="mb-6 bg-emerald-100 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg">
            {{ session('sucesso') }}
        </div>

    @endif

    @if(session('erro'))

        <div class="mb-6 bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('erro') }}
        </div>

    @endif


    {{-- Lista de negociações --}}
    @if($negociacoes->count())

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            @foreach($negociacoes as $negociacao)

                <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden">

                    {{-- Cabeçalho do card --}}
                    <div class="p-5 border-b border-gray-100">

                        <div class="flex items-start justify-between gap-4">

                            <div class="flex items-start gap-3">

                                <div class="w-11 h-11 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">

                                    <span class="material-symbols-outlined text-[#236350]">
                                        forum
                                    </span>

                                </div>

                                <div>

                                    <h2 class="font-bold text-lg text-gray-800">
                                        {{ $negociacao->oferta->produto->nome }}
                                    </h2>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Negociação #{{ $negociacao->id }}
                                    </p>

                                </div>

                            </div>


                            {{-- Status --}}
                            @php
                                $statusClasses = [
                                    'pendente' => 'bg-yellow-100 text-yellow-800',
                                    'em_negociacao' => 'bg-blue-100 text-blue-800',
                                    'aceita' => 'bg-green-100 text-green-800',
                                    'recusada' => 'bg-red-100 text-red-800',
                                    'concluida' => 'bg-emerald-100 text-emerald-800',
                                    'cancelada' => 'bg-gray-100 text-gray-700',
                                ];

                                $statusNomes = [
                                    'pendente' => 'Pendente',
                                    'em_negociacao' => 'Em negociação',
                                    'aceita' => 'Aceita',
                                    'recusada' => 'Recusada',
                                    'concluida' => 'Concluída',
                                    'cancelada' => 'Cancelada',
                                ];
                            @endphp

                            <span class="shrink-0 text-xs px-2.5 py-1 rounded-full font-medium {{ $statusClasses[$negociacao->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $statusNomes[$negociacao->status] ?? ucfirst($negociacao->status) }}
                            </span>

                        </div>

                    </div>


                    {{-- Informações --}}
                    <div class="p-5 space-y-3">

                        <div class="flex justify-between text-sm">

                            <span class="text-gray-500">
                                Fornecedor
                            </span>

                            <span class="font-medium text-gray-800">
                                {{ $negociacao->oferta->fornecedor->nome }}
                            </span>

                        </div>


                        <div class="flex justify-between text-sm">

                            <span class="text-gray-500">
                                Cliente
                            </span>

                            <span class="font-medium text-gray-800">
                                {{ $negociacao->cliente->nome }}
                            </span>

                        </div>


                        <div class="flex justify-between text-sm">

                            <span class="text-gray-500">
                                Quantidade
                            </span>

                            <span class="font-medium text-gray-800">

                                @if(strtolower($negociacao->oferta->unidade) === 'saca')

                                    {{ number_format($negociacao->oferta->quantidade, 0, ',', '.') }}

                                @else

                                    {{ fmod($negociacao->oferta->quantidade, 1) == 0
                                        ? number_format($negociacao->oferta->quantidade, 0, ',', '.')
                                        : number_format($negociacao->oferta->quantidade, 2, ',', '.') }}

                                @endif

                                {{ $negociacao->oferta->unidade }}

                            </span>

                        </div>


                        <div class="flex justify-between text-sm">

                            <span class="text-gray-500">
                                Valor da oferta
                            </span>

                            <span class="font-semibold text-gray-800">
                                R$ {{ number_format($negociacao->oferta->valor, 2, ',', '.') }}
                            </span>

                        </div>


                        {{-- Número de propostas --}}
                        <div class="flex justify-between text-sm pt-3 border-t border-gray-100">

                            <span class="text-gray-500">
                                Propostas
                            </span>

                            <span class="font-medium text-gray-800">
                                {{ $negociacao->propostas->count() }}
                            </span>

                        </div>

                    </div>


                    {{-- Rodapé --}}
                    <div class="px-5 pb-5">

                        <a
                            href="{{ route('negociacoes.show', $negociacao) }}"
                            class="w-full inline-flex items-center justify-center gap-2 bg-[#236350] hover:bg-[#1B4D3E] text-white px-4 py-2.5 rounded-md text-sm font-medium transition-colors"
                        >

                            <span class="material-symbols-outlined text-lg">
                                visibility
                            </span>

                            Ver negociação

                        </a>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        {{-- Nenhuma negociação --}}
        <div class="bg-white rounded-lg shadow-md p-10 text-center">

            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">

                <span class="material-symbols-outlined text-gray-400 text-3xl">
                    forum
                </span>

            </div>

            <h2 class="text-lg font-semibold text-gray-800">
                Nenhuma negociação encontrada
            </h2>

            <p class="text-sm text-gray-500 mt-2 mb-6">
                Você ainda não possui nenhuma negociação.
            </p>

            @if(auth()->user()->user_type !== 'fornecedor')

                <a
                    href="{{ route('ofertas.index') }}"
                    class="inline-flex items-center gap-2 bg-[#236350] hover:bg-[#1B4D3E] text-white px-4 py-2.5 rounded-md text-sm font-medium transition-colors"
                >
                    <span class="material-symbols-outlined text-lg">
                        search
                    </span>

                    Ver ofertas
                </a>

            @endif

        </div>

    @endif

</div>

@endsection