@extends('layouts.layout')
@section('title', 'Criar novo lote')

@section('conteudo')

<div class="mb-4">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#236350] transition-colors">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Voltar para a página inicial
    </a>
</div>

<div class="max-w-4xl mx-auto my-6 p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">
        Cadastrar Nova Oferta
    </h1>

    {{-- Bloco para exibição de erros de validação --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
            <p class="font-bold">
                Atenção! Corrija os erros abaixo:
            </p>

            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('ofertas.store') }}" method="POST" class="space-y-4">
        @csrf

        {{-- Campos principais --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Produto --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Produto:
            </label>

            <div class="flex gap-2">
                <div class="dropdown dropdown-bottom w-full">
                    <div
                        tabindex="0"
                        role="button"
                        class="flex items-center justify-between w-full p-3
                            bg-white border border-gray-200 rounded-box
                            shadow-sm text-gray-700
                            hover:border-gray-300
                            cursor-pointer transition-colors">

                        <span id="produtoSelecionado">
                            {{ old('produto_id') ? ($produtos->firstWhere('id', old('produto_id'))->nome ?? 'Selecione o produto') : 'Selecione o produto' }}
                        </span>

                        <span class="material-symbols-outlined text-gray-500">
                            expand_more
                        </span>
                    </div>

                    <ul
                        tabindex="0"
                        class="dropdown-content menu bg-white text-gray-800 rounded-box
                            z-50 w-full p-2 shadow-xl mt-1
                            border border-gray-100 max-h-60 overflow-y-auto">

                        @foreach($produtos as $produto)
                            <li>
                                <a
                                    href="#"
                                    onclick="selecionarProduto(event, {{ $produto->id }}, '{{ addslashes($produto->nome) }}')"
                                    class="hover:bg-emerald-50">

                                    {{ $produto->nome }}
                                    - {{ $produto->categoria->nome ?? 'Sem Categoria' }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <input type="hidden" name="produto_id" id="produto_id" value="{{ old('produto_id') }}" required>
                </div>

                {{-- Criar novo produto --}}
                <a
                    href="{{ route('produtos.create') }}"
                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-md text-amber-800 border border-amber-200 hover:bg-amber-50 transition-colors font-semibold text-sm whitespace-nowrap">
                    <span class="material-symbols-outlined text-lg">
                        add_circle
                    </span>
                    Novo produto
                </a>
            </div>
        </div>

            {{-- Quantidade --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Quantidade:
                </label>

                <input
                    type="number"
                    name="quantidade"
                    step="1"
                    min="0"
                    value="{{ old('quantidade') }}"
                    required
                    class="w-full p-3 border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]">
            </div>

            {{-- Valor --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Valor (R$):
                </label>

                <input
                    type="number"
                    name="valor"
                    step="0.01"
                    min="0"
                    value="{{ old('valor') }}"
                    required
                    class="w-full p-3 border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]">
            </div>

            {{-- Unidade --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Unidade:
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

                        <span id="unidadeSelecionada">
                            {{ old('unidade') ? ucfirst(old('unidade')) : 'Selecione a unidade' }}
                        </span>

                        <span class="material-symbols-outlined text-gray-500">
                            expand_more
                        </span>
                    </div>

                    <ul
                        tabindex="0"
                        class="dropdown-content menu bg-white text-gray-800 rounded-box z-50 w-full p-2 shadow-xl mt-1 border border-gray-100">

                        <li>
                            <a
                                href="#"
                                onclick="selecionarUnidade(event, 'kg', 'Kg')"
                                class="hover:bg-emerald-50">
                                Kg
                            </a>
                        </li>

                        <li>
                            <a
                                href="#"
                                onclick="selecionarUnidade(event, 'saca', 'Saca')"
                                class="hover:bg-emerald-50">
                                Saca
                            </a>
                        </li>
                    </ul>

                    <input type="hidden" name="unidade" id="unidade" value="{{ old('unidade') }}" required>
                </div>
            </div>

            {{-- Localização --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Localização:
                </label>

                <input
                    type="text"
                    name="localizacao"
                    value="{{ old('localizacao') }}"
                    placeholder="Ex: Icapuí - CE"
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]">
            </div>

            {{-- Data de início --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Data de início:
                </label>

                <input
                    type="date"
                    name="data_inicio"
                    id="data_inicio"
                    value="{{ old('data_inicio', now()->format('Y-m-d')) }}"
                    min="{{ now()->format('Y-m-d') }}"
                    class="w-full p-3 border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]">
            </div>

            {{-- Data de término --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Data de término:
                </label>

                <input
                    type="date"
                    name="data_validade"
                    id="data_validade"
                    value="{{ old('data_validade') }}"
                    min="{{ old('data_inicio', now()->format('Y-m-d')) }}"
                    class="w-full p-3 border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]">
            </div>
        </div>

        {{-- Status --}}
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Status:
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

                    <span id="statusSelecionado">
                        {{ old('status') === 'rascunho' ? 'Rascunho' : 'Publicada' }}
                    </span>

                    <span class="material-symbols-outlined text-gray-500">
                        expand_more
                    </span>
                </div>

                <ul
                    tabindex="0"
                    class="dropdown-content menu bg-white text-gray-800 rounded-box
                        z-50 w-full p-2 shadow-xl mt-1 border border-gray-100">

                    <li>
                        <a
                            href="#"
                            onclick="selecionarStatus(event, 'rascunho', 'Rascunho')"
                            class="hover:bg-emerald-50">
                            Rascunho
                        </a>
                    </li>

                    <li>
                        <a
                            href="#"
                            onclick="selecionarStatus(event, 'publicada', 'Publicada')"
                            class="hover:bg-emerald-50">
                            Publicada
                        </a>
                    </li>
                </ul>

                <input
                    type="hidden"
                    name="status"
                    id="status"
                    value="{{ old('status', 'publicada') }}"
                    required>
            </div>
        </div>


        {{-- Botões --}}
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200 w-full">

            <a href="{{ route('ofertas.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                Cancelar
            </a>

            <button type="submit"
                    class="p-2 btn-copav text-white rounded-md text-sm shadow-md transition-all cursor-pointer border-0 hover:opacity-90">
                Cadastrar oferta
            </button>

        </div>
    </form>
</div>


<script>
    const status = document.getElementById('status');
    const dataInicio = document.getElementById('data_inicio');
    const dataValidade = document.getElementById('data_validade');

    const hoje = "{{ now()->format('Y-m-d') }}";

    function atualizarDataInicio() {
        if (status.value === 'publicada') {
            dataInicio.value = hoje;
            dataInicio.min = hoje;
            dataValidade.min = hoje;
        }
    }

    function selecionarProduto(event, id, nome) {
        event.preventDefault();

        document.getElementById('produto_id').value = id;
        document.getElementById('produtoSelecionado').textContent = nome;

        fecharDropdown(event);
    }

    function selecionarUnidade(event, valor, nome) {
        event.preventDefault();

        document.getElementById('unidade').value = valor;
        document.getElementById('unidadeSelecionada').textContent = nome;

        fecharDropdown(event);
    }

    function selecionarStatus(event, valor, nome) {
        event.preventDefault();

        document.getElementById('status').value = valor;
        document.getElementById('statusSelecionado').textContent = nome;

        // Atualiza as datas imediatamente
        atualizarDataInicio();

        fecharDropdown(event);
    }

    function fecharDropdown(event) {
        const dropdown = event.target.closest('.dropdown');

        if (dropdown) {
            const botao = dropdown.querySelector('[role="button"]');

            if (botao) {
                botao.blur();
            }
        }
    }

    dataInicio.addEventListener('change', function () {
        dataValidade.min = dataInicio.value;

        if (
            dataValidade.value &&
            dataValidade.value < dataInicio.value
        ) {
            dataValidade.value = '';
        }
    });

    atualizarDataInicio();
</script>

@endsection