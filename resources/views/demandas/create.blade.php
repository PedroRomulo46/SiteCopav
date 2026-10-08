@extends('layouts.layout')
@section('title', 'Cadastrar Demanda')

@section('conteudo')

{{-- Botão Voltar --}}
<div class="mb-4">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#1B4D3E] transition-colors">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Voltar para a página inicial
    </a>
</div>

<div class="w-full max-w-7xl mx-auto px-2 sm:px-4 text-left">

    {{-- Cabeçalho da Página --}}
    <div class="flex flex-row items-center justify-between gap-4 mb-6 border-b border-gray-200 pb-4">
        <div class="text-left">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#1B4D3E] text-3xl">add_shopping_cart</span>
                Cadastrar Demanda
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Informe o produto ou insumo que sua empresa está procurando no mercado
            </p>
        </div>
    </div>

    {{-- Exibição de Erros de Validação --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-left shadow-sm">
            <div class="flex items-center gap-2 text-red-700 font-semibold mb-1">
                <span class="material-symbols-outlined text-xl">error</span>
                <span>Por favor, corrija os erros abaixo:</span>
            </div>
            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Cartão Principal do Formulário --}}
    <div class="max-w-6xl mx-auto bg-white rounded-xl border border-gray-100 shadow-sm p-6 sm:p-8">
        <form action="{{ route('demandas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Categoria --}}
                <div class="form-control w-full">
                    <label class="label font-semibold text-gray-700">
                        <span class="label-text flex items-center gap-1.5 font-medium">
                            <span class="material-symbols-outlined text-[#1B4D3E] text-lg">category</span>
                            Categoria <span class="text-red-500">*</span>
                        </span>
                    </label>
                    <select name="categoria_id" class="select select-bordered w-full focus:outline-none focus:border-[#1B4D3E] focus:ring-1 focus:ring-[#1B4D3E] bg-gray-50/50" required>
                        <option value="" disabled selected>Selecione uma categoria</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Produto que Procura --}}
                <div class="form-control w-full">
                    <label class="label font-semibold text-gray-700">
                        <span class="label-text flex items-center gap-1.5 font-medium">
                            <span class="material-symbols-outlined text-[#1B4D3E] text-lg">inventory_2</span>
                            Produto que procura <span class="text-red-500">*</span>
                        </span>
                    </label>
                    <input 
                        type="text" 
                        name="nome_produto" 
                        value="{{ old('nome_produto') }}" 
                        placeholder="Ex: Milho em grão" 
                        class="input input-bordered w-full focus:outline-none focus:border-[#1B4D3E] focus:ring-1 focus:ring-[#1B4D3E] bg-gray-50/50" 
                        required 
                    />
                </div>
            </div>

            {{-- Imagem da Demanda --}}
            <div class="form-control w-full">
                <label class="label font-semibold text-gray-700">
                    <span class="label-text flex items-center gap-1.5 font-medium">
                        <span class="material-symbols-outlined text-[#1B4D3E] text-lg">
                            image
                        </span>
                        Imagem da demanda
                    </span>
                </label>

                <input
                    type="file"
                    name="imagem"
                    accept="image/jpeg,image/png,image/jpg,image/webp"
                    class="file-input file-input-bordered w-full bg-gray-50/50"
                >

                <p class="text-xs text-gray-500 mt-1">
                    JPG, PNG ou WEBP. Máximo de 2 MB.
                </p>
            </div>

            {{-- Descrição --}}
            <div class="form-control w-full">
                <label class="label font-semibold text-gray-700">
                    <span class="label-text flex items-center gap-1.5 font-medium">
                        <span class="material-symbols-outlined text-[#1B4D3E] text-lg">description</span>
                        Descrição detalhada
                    </span>
                </label>
                <textarea 
                    name="descricao" 
                    rows="4" 
                    placeholder="Descreva especificações, padrão de qualidade ou exigências da sua empresa..." 
                    class="textarea textarea-bordered w-full focus:outline-none focus:border-[#1B4D3E] focus:ring-1 focus:ring-[#1B4D3E] bg-gray-50/50 resize-y"
                >{{ old('descricao') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Quantidade --}}
                <div class="form-control w-full">
                    <label class="label font-semibold text-gray-700">
                        <span class="label-text flex items-center gap-1.5 font-medium">
                            <span class="material-symbols-outlined text-[#1B4D3E] text-lg">format_list_numbered</span>
                            Quantidade necessária <span class="text-red-500">*</span>
                        </span>
                    </label>
                    <input 
                        type="number" 
                        name="quantidade" 
                        step="0.01" 
                        min="0" 
                        value="{{ old('quantidade') }}" 
                        placeholder="Ex: 500.00" 
                        class="input input-bordered w-full focus:outline-none focus:border-[#1B4D3E] focus:ring-1 focus:ring-[#1B4D3E] bg-gray-50/50" 
                        required 
                    />
                </div>

                {{-- Unidade --}}
                <div class="form-control w-full">
                    <label class="label font-semibold text-gray-700">
                        <span class="label-text flex items-center gap-1.5 font-medium">
                            <span class="material-symbols-outlined text-[#1B4D3E] text-lg">straighten</span>
                            Unidade de Medida <span class="text-red-500">*</span>
                        </span>
                    </label>
                    <input 
                        type="text" 
                        name="unidade" 
                        value="{{ old('unidade') }}" 
                        placeholder="Ex: kg, sacas, toneladas" 
                        class="input input-bordered w-full focus:outline-none focus:border-[#1B4D3E] focus:ring-1 focus:ring-[#1B4D3E] bg-gray-50/50" 
                        required 
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Valor Máximo --}}
                <div class="form-control w-full">
                    <label class="label font-semibold text-gray-700">
                        <span class="label-text flex items-center gap-1.5 font-medium">
                            <span class="material-symbols-outlined text-[#1B4D3E] text-lg">payments</span>
                            Valor máx. a pagar (R$)
                        </span>
                    </label>
                    <input 
                        type="number" 
                        name="valor_maximo" 
                        step="0.01" 
                        min="0" 
                        value="{{ old('valor_maximo') }}" 
                        placeholder="0,00" 
                        class="input input-bordered w-full focus:outline-none focus:border-[#1B4D3E] focus:ring-1 focus:ring-[#1B4D3E] bg-gray-50/50" 
                    />
                </div>

                {{-- Localização --}}
                <div class="form-control w-full">
                    <label class="label font-semibold text-gray-700">
                        <span class="label-text flex items-center gap-1.5 font-medium">
                            <span class="material-symbols-outlined text-[#1B4D3E] text-lg">location_on</span>
                            Localização / Entrega
                        </span>
                    </label>
                    <input 
                        type="text" 
                        name="localizacao" 
                        value="{{ old('localizacao', '') }}" 
                        placeholder="Ex: Icapuí - CE" 
                        class="input input-bordered w-full focus:outline-none focus:border-[#1B4D3E] focus:ring-1 focus:ring-[#1B4D3E] bg-gray-50/50" 
                    />
                </div>

                {{-- Data Limite --}}
                <div class="form-control w-full">
                    <label class="label font-semibold text-gray-700">
                        <span class="label-text flex items-center gap-1.5 font-medium">
                            <span class="material-symbols-outlined text-[#1B4D3E] text-lg">event</span>
                            Data Limite de Cotação
                        </span>
                    </label>
                    <input 
                        type="date" 
                        name="data_limite" 
                        value="{{ old('data_limite') }}" 
                        class="input input-bordered w-full focus:outline-none focus:border-[#1B4D3E] focus:ring-1 focus:ring-[#1B4D3E] bg-gray-50/50" 
                    />
                </div>
            </div>

            {{-- Rodapé / Botões de Ação --}}
            <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('demandas.index') }}" class="btn btn-ghost text-gray-600 hover:bg-gray-100">
                    Cancelar
                </a>
                <button type="submit" class="btn-copav p-2 rounded-md text-white border-none gap-2 shadow-sm font-medium">
                    <span class="material-symbols-outlined text-xl">check_circle</span>
                    Cadastrar Demanda
                </button>
            </div>
        </form>
    </div>

</div>

@endsection