@extends('layouts.layout')

@section('title', 'Fazer Oferta Direta')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">
        <li class="inline-flex items-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors">
                Início
            </a>
        </li>

        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <a href="{{ route('demandas.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors">
                Demandas
            </a>
        </li>

        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="max-w-4xl mx-auto px-4 py-6 text-left">

    {{-- Card --}}
    <div class="bg-white dark:bg-[#111827] rounded-xl shadow-md border border-gray-200 dark:border-[#273449] overflow-hidden">

        {{-- Cabeçalho --}}
        <div class="p-6 border-b border-gray-100 dark:border-[#273449]">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-emerald-50 dark:bg-emerald-950/60 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400 leading-none">
                        local_offer
                    </span>
                </div>

                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                        Fazer Oferta Direta
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Selecione uma demanda e informe sua oferta.
                    </p>
                </div>
            </div>
        </div>

        {{-- Erros --}}
        @if ($errors->any())
            <div class="mx-6 mt-6 p-4 bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800/60 rounded-lg text-red-700 dark:text-red-300">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined leading-none">
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Demanda:
                </label>
                <div class="dropdown dropdown-bottom w-full">
                    <div
                        tabindex="0"
                        role="button"
                        class="flex items-center justify-between w-full p-3
                               bg-white dark:bg-[#161f30] border {{ $errors->has('demanda_id') ? 'border-red-500 dark:border-red-400' : 'border-gray-200 dark:border-[#273449]' }} rounded-box
                               shadow-sm text-gray-700 dark:text-gray-200
                               hover:border-gray-300 dark:hover:border-slate-600
                               cursor-pointer transition-colors">
                        <span id="demandaSelecionada" class="truncate {{ $demandaSelecionada ? 'text-gray-700 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500' }}">
                            {{ $demandaSelecionada?->nome_produto ?? 'Selecione uma demanda' }}
                        </span>
                        <span class="material-symbols-outlined text-gray-500 dark:text-gray-400 leading-none shrink-0">
                            expand_more
                        </span>
                    </div>

                    <ul
                        tabindex="0"
                        class="dropdown-content menu bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 rounded-box
                               z-50 w-full p-2 shadow-xl mt-1
                               border border-gray-100 dark:border-[#273449] max-h-64 overflow-y-auto">

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
                                    class="hover:bg-emerald-50 dark:hover:bg-emerald-950/50 rounded-md">
                                    <div class="flex flex-col text-left">
                                        <span class="font-medium text-gray-800 dark:text-gray-100">
                                            {{ $demanda->nome_produto }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
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
                       p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-100 dark:border-emerald-800/60 rounded-lg">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400 leading-none shrink-0 mt-0.5">
                        info
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                            Demanda selecionada
                        </p>
                        <p id="detalhesDemanda" class="text-sm text-gray-600 dark:text-gray-300 mt-1">
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
                    <label for="quantidade" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
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
                            class="w-full p-3 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('quantidade') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }}
                                   shadow-sm outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400">
                    </div>
                </div>

                {{-- Valor --}}
                <div>
                    <label for="valor" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
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
                            class="w-full p-3 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('valor') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }}
                                   shadow-sm outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400">
                    </div>
                </div>
            </div>

            {{-- Observação --}}
            <div>
                <label for="observacao" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Observação:
                </label>
                <textarea
                    name="observacao"
                    id="observacao"
                    rows="4"
                    placeholder="Informe detalhes sobre sua oferta..."
                    class="w-full p-3 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('observacao') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }}
                           shadow-sm resize-none outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400">{{ old('observacao') }}</textarea>
            </div>

            {{-- Botões --}}
            <div class="flex items-center justify-between pt-5 border-t border-gray-200 dark:border-[#273449]">
                <a
                    href="{{ route('demandas.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white text-sm font-medium transition-colors">
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5
                           btn-copav text-white rounded-lg text-sm font-semibold
                           shadow-md transition-all hover:opacity-95 cursor-pointer">
                    <span class="material-symbols-outlined text-lg leading-none">
                        send
                    </span>
                    <span>Enviar oferta</span>
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
        selecionada.classList.remove('text-gray-400', 'dark:text-gray-500');
        selecionada.classList.add('text-gray-700', 'dark:text-gray-200');

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