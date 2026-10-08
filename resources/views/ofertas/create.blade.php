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

                <select
                    name="produto_id"
                    id="produto_id"
                    required
                    onchange="verificarCriarProduto(this)"
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]">
                    <option value="">
                        Selecione o produto
                    </option>

                    @forelse($produtos as $produto)
                        <option
                            value="{{ $produto->id }}"
                            {{ old('produto_id') == $produto->id ? 'selected' : '' }}>
                            {{ $produto->nome }}
                            - {{ $produto->categoria->nome ?? 'Sem Categoria' }}
                        </option>
                    @empty
                        <option value="__criar_produto__">
                            + Criar produto
                        </option>
                    @endforelse
                </select>
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

                <select
                    name="unidade"
                    required
                    class="w-full p-3 border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]">
                    <option value="">
                        Selecione a unidade
                    </option>

                    <option
                        value="kg"
                        {{ old('unidade') == 'kg' ? 'selected' : '' }}>
                        Kg
                    </option>

                    <option
                        value="saca"
                        {{ old('unidade') == 'saca' ? 'selected' : '' }}>
                        Saca
                    </option>
                </select>
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

            <select
                name="status"
                id="status"
                required
                class="w-full p-3 border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]">
                <option
                    value="rascunho"
                    {{ old('status') == 'rascunho' ? 'selected' : '' }}>
                    Rascunho
                </option>

                <option
                    value="publicada"
                    {{ old('status', 'publicada') == 'publicada' ? 'selected' : '' }}>
                    Publicada
                </option>
            </select>
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

    function verificarCriarProduto(select) {
        if (select.value === '__criar_produto__') {
            window.location.href = "{{ route('produtos.create') }}";
        }
    }

    status.addEventListener('change', atualizarDataInicio);

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