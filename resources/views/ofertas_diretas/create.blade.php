@extends('layouts.layout')

@section('title', 'Fazer Oferta Direta')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">
        <li>
            <a href="{{ route('home') }}" class="hover:text-[#1B4D3E]">
                Início
            </a>
        </li>

        <li class="flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <a href="{{ route('demandas.index') }}" class="hover:text-[#1B4D3E]">
                Demandas
            </a>
        </li>

        <li class="flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <span class="font-semibold text-gray-800">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="max-w-4xl mx-auto px-4 py-6">

    {{-- Card --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">

        {{-- Cabeçalho --}}
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-emerald-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#236350]">
                        local_offer
                    </span>
                </div>

                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Fazer Oferta Direta
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Selecione uma demanda e informe sua oferta.
                    </p>
                </div>
            </div>
        </div>

        {{-- Erros --}}
        @if ($errors->any())
            <div class="mx-6 mt-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined">
                        error
                    </span>
                    <p class="font-bold">
                        Atenção! Corrija os erros abaixo:
                    </p>
                </div>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $demandaSelecionada = old('demanda_id')
                ? $demandas->firstWhere('id', old('demanda_id'))
                : null;
            $nomeEmpresaSelecionada = $demandaSelecionada?->cliente->nome
                ?? $demandaSelecionada?->cliente->name
                ?? 'empresa';
        @endphp

        {{-- Formulário --}}
        <form action="{{ route('ofertas-diretas.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            {{-- Demanda --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Demanda:
                </label>
                <div class="dropdown dropdown-bottom w-full">
                    <div
                        tabindex="0"
                        role="button"
                        class="flex items-center justify-between w-full p-3
                               bg-white border border-gray-200 rounded-box
                               shadow-sm text-gray-700
                               hover:border-gray-300
                               cursor-pointer transition-colors">
                        <span id="demandaSelecionada" class="{{ $demandaSelecionada ? 'text-gray-700' : 'text-gray-400' }}">
                            {{ $demandaSelecionada?->nome_produto ?? 'Selecione uma demanda' }}
                        </span>
                        <span class="material-symbols-outlined text-gray-500">
                            expand_more
                        </span>
                    </div>

                    <ul
                        tabindex="0"
                        class="dropdown-content menu bg-white text-gray-800 rounded-box
                               z-50 w-full p-2 shadow-xl mt-1
                               border border-gray-100 max-h-64 overflow-y-auto">

                        @foreach($demandas as $demanda)
                            @php
                                $nomeEmpresa = $demanda->cliente->nome
                                    ?? $demanda->cliente->name
                                    ?? 'Empresa não informada';
                            @endphp
                            <li>
                                <a
                                    href="#"
                                    data-demanda-id="{{ $demanda->id }}"
                                    data-demanda-nome="{{ $demanda->nome_produto }}"
                                    data-demanda-empresa="{{ $nomeEmpresa }}"
                                    data-demanda-quantidade="{{ $demanda->quantidade }}"
                                    data-demanda-unidade="{{ $demanda->unidade ?? '' }}"
                                    onclick="selecionarDemanda(event, this)"
                                    class="hover:bg-emerald-50">
                                    <div class="flex flex-col">
                                        <span class="font-medium">
                                            {{ $demanda->nome_produto }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ $nomeEmpresa }}
                                            ·
                                            {{ $demanda->quantidade }}
                                            {{ $demanda->unidade ?? '' }}
                                        </span>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <input
                        type="hidden"
                        name="demanda_id"
                        id="demanda_id"
                        value="{{ old('demanda_id') }}"
                        required>
                </div>
            </div>

            {{-- Informações da demanda selecionada --}}
            <div
                id="infoDemanda"
                class="{{ $demandaSelecionada ? '' : 'hidden' }}
                       p-4 bg-emerald-50 border border-emerald-100 rounded-lg">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[#236350]">
                        info
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">
                            Demanda selecionada
                        </p>
                        <p id="detalhesDemanda" class="text-sm text-gray-600 mt-1">
                            @if($demandaSelecionada)
                                {{ $demandaSelecionada->quantidade }}
                                {{ $demandaSelecionada->unidade ?? '' }}
                                solicitados por
                                {{ $nomeEmpresaSelecionada }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Quantidade e valor --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Quantidade --}}
                <div>
                    <label for="quantidade" class="block text-sm font-medium text-gray-700 mb-1">
                        Quantidade que consegue fornecer:
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            name="quantidade"
                            id="quantidade"
                            step="0.01"
                            min="0"
                            value="{{ old('quantidade') }}"
                            required
                            placeholder="Ex: 100"
                            class="w-full p-3 border border-gray-300 rounded-md
                                   shadow-sm focus:ring-[#236350] focus:border-[#236350]">
                    </div>
                </div>

                {{-- Valor --}}
                <div>
                    <label for="valor" class="block text-sm font-medium text-gray-700 mb-1">
                        Valor da oferta (R$):
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            name="valor"
                            id="valor"
                            step="0.01"
                            min="0"
                            value="{{ old('valor') }}"
                            placeholder="0,00"
                            required
                            class="w-full p-3 border border-gray-300 rounded-md
                                   shadow-sm focus:ring-[#236350] focus:border-[#236350]">
                    </div>
                </div>
            </div>

            {{-- Observação --}}
            <div>
                <label for="observacao" class="block text-sm font-medium text-gray-700 mb-1">
                    Observação:
                </label>
                <textarea
                    name="observacao"
                    id="observacao"
                    rows="4"
                    placeholder="Informe detalhes sobre sua oferta..."
                    class="w-full p-3 border border-gray-300 rounded-md
                           shadow-sm resize-none
                           focus:ring-[#236350] focus:border-[#236350]">{{ old('observacao') }}</textarea>
            </div>

            {{-- Botões --}}
            <div class="flex items-center justify-between pt-5 border-t border-gray-200">
                <a
                    href="{{ route('demandas.index') }}"
                    class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5
                           btn-copav text-white rounded-md text-sm font-semibold
                           shadow-md transition-all hover:opacity-90 cursor-pointer">
                    <span class="material-symbols-outlined text-lg">
                        send
                    </span>
                    Enviar oferta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function selecionarDemanda(event, element) {
        event.preventDefault();

        const id = element.dataset.demandaId || '';
        const nome = element.dataset.demandaNome || 'Selecione uma demanda';
        const empresa = element.dataset.demandaEmpresa || 'empresa';
        const quantidade = element.dataset.demandaQuantidade || '';
        const unidade = element.dataset.demandaUnidade || '';

        document.getElementById('demanda_id').value = id;

        const selecionada = document.getElementById('demandaSelecionada');
        selecionada.textContent = nome;
        selecionada.classList.remove('text-gray-400');
        selecionada.classList.add('text-gray-700');

        const sufixoUnidade = unidade ? ' ' + unidade : '';
        document.getElementById('detalhesDemanda').textContent =
            quantidade + sufixoUnidade + ' solicitados por ' + empresa;

        document.getElementById('infoDemanda').classList.remove('hidden');

        const dropdown = event.target.closest('.dropdown');
        if (dropdown) {
            const botao = dropdown.querySelector('[role="button"]');
            if (botao) {
                botao.blur();
            }
        }
    }
</script>

@endsection