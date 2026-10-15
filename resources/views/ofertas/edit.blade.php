@extends('layouts.layout')

@section('title', 'Editar Oferta')

@section('conteudo')

<div class="max-w-4xl mx-auto">

    {{-- Botão voltar --}}
    <div class="mb-6">
        <a
            href="{{ route('ofertas.index') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#236350] transition-colors">
            <span class="material-symbols-outlined text-lg">
                arrow_back
            </span>
            Voltar para ofertas
        </a>
    </div>

    {{-- Cabeçalho --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Editar oferta
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Atualize as informações da oferta.
        </p>
    </div>

    {{-- Mensagens de erro --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-md">
            <p class="font-semibold mb-2">
                Corrija os seguintes erros:
            </p>
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulário --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
        <form
            id="form-editar-oferta"
            action="{{ route('ofertas.update', $oferta) }}"
            method="POST"
            class="space-y-6">

            @csrf
            @method('PUT')

            {{-- Produto --}}
            <div>
                <label
                    for="produto_id"
                    class="block text-sm font-medium text-gray-700 mb-2">
                    Produto
                </label>

                <select
                    name="produto_id"
                    id="produto_id"
                    required
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#236350] focus:border-[#236350]">
                    <option value="">
                        Selecione um produto
                    </option>

                    @foreach($produtos as $produto)
                        <option
                            value="{{ $produto->id }}"
                            {{ old('produto_id', $oferta->produto_id) == $produto->id ? 'selected' : '' }}>
                            {{ $produto->nome }}

                            @if($produto->categoria)
                                — {{ $produto->categoria->nome }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Quantidade e unidade --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label
                        for="quantidade"
                        class="block text-sm font-medium text-gray-700 mb-2">
                        Quantidade
                    </label>

                    <input
                        type="number"
                        name="quantidade"
                        id="quantidade"
                        value="{{ old('quantidade', $oferta->quantidade) }}"
                        min="0"
                        step="0.01"
                        required
                        class="w-full border border-gray-300 rounded-md px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#236350] focus:border-[#236350]">
                </div>

                <div>
                    <label
                        for="unidade"
                        class="block text-sm font-medium text-gray-700 mb-2">
                        Unidade
                    </label>

                    <input
                        type="text"
                        name="unidade"
                        id="unidade"
                        value="{{ old('unidade', $oferta->unidade) }}"
                        maxlength="50"
                        required
                        placeholder="Ex.: kg, saca, tonelada"
                        class="w-full border border-gray-300 rounded-md px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#236350] focus:border-[#236350]">
                </div>
            </div>

            {{-- Valor --}}
            <div>
                <label
                    for="valor"
                    class="block text-sm font-medium text-gray-700 mb-2">
                    Valor
                </label>

                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500">
                        R$
                    </span>

                    <input
                        type="number"
                        name="valor"
                        id="valor"
                        value="{{ old('valor', $oferta->valor) }}"
                        min="0"
                        step="0.01"
                        required
                        class="w-full border border-gray-300 rounded-md pl-10 pr-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#236350] focus:border-[#236350]">
                </div>
            </div>

            {{-- Localização --}}
            <div>
                <label
                    for="localizacao"
                    class="block text-sm font-medium text-gray-700 mb-2">
                    Localização
                </label>

                <input
                    type="text"
                    name="localizacao"
                    id="localizacao"
                    value="{{ old('localizacao', $oferta->localizacao) }}"
                    maxlength="255"
                    placeholder="Ex.: Icapuí - CE"
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#236350] focus:border-[#236350]">
            </div>

            {{-- Datas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label
                        for="data_inicio"
                        class="block text-sm font-medium text-gray-700 mb-2">
                        Data de início
                    </label>

                    <input
                        type="date"
                        name="data_inicio"
                        id="data_inicio"
                        value="{{ old('data_inicio', $oferta->data_inicio ? \Carbon\Carbon::parse($oferta->data_inicio)->format('Y-m-d') : '') }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#236350] focus:border-[#236350]">
                </div>

                <div>
                    <label
                        for="data_validade"
                        class="block text-sm font-medium text-gray-700 mb-2">
                        Data de validade
                    </label>

                    <input
                        type="date"
                        name="data_validade"
                        id="data_validade"
                        value="{{ old('data_validade', $oferta->data_validade ? \Carbon\Carbon::parse($oferta->data_validade)->format('Y-m-d') : '') }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#236350] focus:border-[#236350]">
                </div>
            </div>

            {{-- Status --}}
                <label
                    for="status"
                    class="block text-sm font-medium text-gray-700 mb-2">
                    Status
                </label>

                <select
                    name="status"
                    id="status"
                    required
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#236350] focus:border-[#236350]">
                    <option
                        value="rascunho"
                        {{ old('status', $oferta->status) === 'rascunho' ? 'selected' : '' }}>
                        Rascunho
                    </option>

                    <option
                        value="publicada"
                        {{ old('status', $oferta->status) === 'publicada' ? 'selected' : '' }}>
                        Publicada
                    </option>

                    <option
                        value="encerrada"
                        {{ old('status', $oferta->status) === 'encerrada' ? 'selected' : '' }}>
                        Encerrada
                    </option>

                    <option
                        value="cancelada"
                        {{ old('status', $oferta->status) === 'cancelada' ? 'selected' : '' }}>
                        Cancelada
                    </option>
                </select>
            </div>

        </form>

             {{-- Botões --}}
        <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-100">

            {{-- Excluir à esquerda --}}
            <form
                action="{{ route('ofertas.destroy', $oferta) }}"
                method="POST"
                onsubmit="return confirm('Tem certeza que deseja excluir esta oferta?');"
            >
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 p-2 bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-md transition-colors">
                    <span class="material-symbols-outlined">
                        delete
                    </span>
                    Excluir oferta
                </button>
            </form>

            {{-- Cancelar e salvar à direita --}}
            <div class="flex gap-3">

                <a
                    href="{{ route('ofertas.index') }}"
                    class="inline-flex items-center justify-center px-5 py-2.5 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    form="form-editar-oferta"
                    class="btn-copav inline-flex items-center justify-center gap-2 text-white px-5 py-2.5 rounded-md transition-colors"
                >
                    <span class="material-symbols-outlined">
                        save
                    </span>

                    Salvar alterações
                </button>

            </div>

        </div>

    </form>

</div>

@endsection