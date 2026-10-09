@extends('layouts.layout')

@section('title', 'Detalhes da Demanda')

@section('conteudo')

{{-- Botão Voltar --}}
<div class="mb-5">
    <a
        href="{{ route('demandas.index') }}"
        class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#236350] transition-colors">
        <span class="material-symbols-outlined text-lg">
            arrow_back
        </span>
        Voltar para as demandas
    </a>
</div>

<div class="max-w-6xl mx-auto px-4 py-6">

    {{-- CARD PRINCIPAL DA DEMANDA --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">

        {{-- Cabeçalho --}}
        <div class="p-6 border-b border-gray-100">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[#236350]">
                            shopping_cart
                        </span>
                        <span class="text-sm font-medium text-gray-500">
                            Demanda
                        </span>
                    </div>

                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                        {{ $demanda->nome_produto }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $demanda->categoria->nome ?? 'Sem categoria' }}
                    </p>
                </div>

                {{-- Status --}}
                <div>
                    @php
                        $statusClasses = match($demanda->status) {
                            'aberta', 'publicada', 'ativa' =>
                                'bg-emerald-50 text-emerald-700 border-emerald-200',

                            'encerrada', 'finalizada' =>
                                'bg-gray-100 text-gray-600 border-gray-200',

                            'cancelada' =>
                                'bg-red-50 text-red-700 border-red-200',

                            default =>
                                'bg-amber-50 text-amber-700 border-amber-200',
                        };
                    @endphp

                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-sm font-semibold {{ $statusClasses }}">
                        <span class="w-2 h-2 rounded-full bg-current"></span>
                        {{ ucfirst($demanda->status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- INFORMAÇÕES --}}
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Empresa --}}
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[#236350]">
                            business
                        </span>
                        <span class="text-xs font-medium text-gray-500 uppercase">
                            Empresa
                        </span>
                    </div>
                    <p class="font-semibold text-gray-800">
                        {{ $demanda->cliente->nome ?? 'Não informado' }}
                    </p>
                </div>

                {{-- Quantidade --}}
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[#236350]">
                            inventory_2
                        </span>
                        <span class="text-xs font-medium text-gray-500 uppercase">
                            Quantidade
                        </span>
                    </div>
                    <p class="font-semibold text-gray-800">
                        {{ $demanda->quantidade }}
                        {{ $demanda->unidade }}
                    </p>
                </div>

                {{-- Valor máximo --}}
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[#236350]">
                            payments
                        </span>
                        <span class="text-xs font-medium text-gray-500 uppercase">
                            Valor máximo
                        </span>
                    </div>
                    <p class="font-semibold text-gray-800">
                        @if($demanda->valor_maximo)
                            R$ {{ number_format($demanda->valor_maximo, 2, ',', '.') }}
                        @else
                            Não informado
                        @endif
                    </p>

                </div>

                {{-- Localização --}}
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[#236350]">
                            location_on
                        </span>
                        <span class="text-xs font-medium text-gray-500 uppercase">
                            Localização
                        </span>
                    </div>
                    <p class="font-semibold text-gray-800">
                        {{ $demanda->localizacao ?? 'Não informada' }}
                    </p>
                </div>
            </div>

            {{-- Descrição --}}
            <div class="mt-6">
                <h2 class="text-lg font-bold text-gray-800 mb-2">
                    Descrição
                </h2>
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <p class="text-gray-600 leading-relaxed">
                        {{ $demanda->descricao ?? 'Nenhuma descrição foi informada.' }}
                    </p>
                </div>
            </div>

            {{-- Data limite --}}
            <div class="mt-5 flex items-center gap-2 text-sm text-gray-600">
                <span class="material-symbols-outlined text-[#236350]">
                    event
                </span>
                <span>
                    <strong class="text-gray-700">Data limite:</strong>
                    @if($demanda->data_limite)
                        {{ $demanda->data_limite->format('d/m/Y') }}
                    @else
                        Não informada
                    @endif
                </span>
            </div>
        </div>

        {{-- Ação --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            <a
                href="{{ route('ofertas-diretas.create', ['demanda_id' => $demanda->id]) }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5
                       btn-copav text-white rounded-md font-semibold text-sm
                       shadow-sm hover:opacity-90 transition-all">
                <span class="material-symbols-outlined text-lg">
                    add_circle
                </span>
                Fazer oferta para esta demanda
            </a>
        </div>
    </div>

    {{-- OFERTAS DOS FORNECEDORES --}}
    <div class="mt-8">

        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Ofertas dos fornecedores
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Confira as propostas recebidas para esta demanda.
                </p>
            </div>
            <span class="badge badge-ghost">
                {{ $demanda->ofertasDiretas->count() }}
                {{ $demanda->ofertasDiretas->count() === 1 ? 'oferta' : 'ofertas' }}
            </span>
        </div>

        @if($demanda->ofertasDiretas->count())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($demanda->ofertasDiretas as $oferta)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100hover:shadow-md transition-shadow overflow-hidden">

                        {{-- Cabeçalho da oferta --}}
                        <div class="p-5 border-b border-gray-100">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-50
                                                flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[#236350]">
                                            local_shipping
                                        </span>
                                    </div>

                                    <div>
                                        <p class="text-xs text-gray-500">
                                            Fornecedor
                                        </p>
                                        <h3 class="font-bold text-gray-800">
                                            {{ $oferta->fornecedor->nome ?? 'Fornecedor não informado' }}
                                        </h3>
                                    </div>
                                </div>

                                {{-- Status da oferta --}}
                                @php
                                    $statusOferta = match($oferta->status) {
                                        'aceita', 'aprovada' =>
                                            'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'recusada', 'cancelada' 
                                            =>'bg-red-50 text-red-700 border-red-200',

                                        default =>'bg-amber-50 text-amber-700 border-amber-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full border text-xs font-semibold {{ $statusOferta }}">
                                    {{ ucfirst($oferta->status) }}
                                </span>
                            </div>
                        </div>

                        {{-- Dados da oferta --}}
                        <div class="p-5 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">
                                        Quantidade
                                    </p>
                                    <p class="font-semibold text-gray-800">
                                        {{ $oferta->quantidade }}
                                        {{ $demanda->unidade }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500 mb-1">
                                        Valor
                                    </p>
                                    <p class="font-bold text-[#236350] text-lg">
                                        R$ {{ number_format($oferta->valor, 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Observação --}}
                            <div>
                                <p class="text-xs text-gray-500 mb-1">
                                    Observação
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ $oferta->observacao ?? 'Sem observação.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white border border-dashed border-gray-300 rounded-xlp-10 text-center">
                <span class="material-symbols-outlined text-4xl text-gray-400">
                    inbox
                </span>
                <h3 class="mt-3 font-semibold text-gray-700">
                    Nenhuma oferta ainda
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Ainda não há fornecedores que fizeram uma oferta para esta demanda.
                </p>
            </div>
        @endif
    </div>

    {{-- Voltar --}}
    <div class="mt-8">
        <a
            href="{{ route('demandas.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2
                   border border-gray-200 rounded-md
                   text-gray-600 font-medium text-sm
                   hover:bg-gray-50 transition-colors">
            <span class="material-symbols-outlined text-lg">
                arrow_back
            </span>
            Voltar para demandas
        </a>
    </div>
</div>

@endsection