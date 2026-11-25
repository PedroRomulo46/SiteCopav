@extends('layouts.layout')

@section('title', 'Sugerir Categoria')

@section('conteudo')

<div>
    {{-- breadcrumb --}}
    <nav class="flex items-center text-sm mb-6 text-gray-600" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li>
                <a href="{{ route('home') }}" class="hover:text-[#1B4D3E]">
                    Início
                </a>
            </li>

            @if(auth()->user()?->isAdmin())
                <li class="flex items-center space-x-2">
                    <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
                    <a href="{{ route('categorias.index') }}" class="hover:text-[#1B4D3E]">
                        Categorias
                    </a>
                </li>
            @endif

            <li class="flex items-center space-x-2">
                <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
                <span class="font-semibold text-gray-800">
                    @yield('title')
                </span>
            </li>
        </ol>
    </nav>

    <div class="w-full max-w-3xl mx-auto px-2 sm:px-4 text-left">
        {{-- alerta de sucesso --}}
        @if(session('sucesso'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-600 text-emerald-800 rounded-r-md flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl">check_circle</span>
                    <span>{{ session('sucesso') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="btn btn-xs btn-circle btn-ghost text-emerald-800">✕</button>
            </div>
        @endif

        {{-- cabeçalho da página --}}
        <div class="mb-6 border-b border-gray-200 pb-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#1B4D3E] text-3xl">lightbulb</span>
                Sugerir Nova Categoria
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Não encontrou a categoria ideal para seu produto ou demanda? Envie uma sugestão para nossa equipe avaliar.
            </p>
        </div>

        {{-- formulário de sugestão --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 sm:p-8">
            <form action="{{ route('categorias.sugerir.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- nome da categoria sugerida --}}
                <div class="form-control">
                    <label for="nome" class="label py-1">
                        <span class="label-text font-medium text-gray-700">
                            Nome da categoria <span class="text-red-500">*</span>
                        </span>
                    </label>
                    <input
                        id="nome"
                        name="nome"
                        type="text"
                        value="{{ old('nome') }}"
                        placeholder="Ex: Sementes Orgânicas, Irrigação, Embalagens..."
                        required
                        autofocus
                        class="input input-bordered w-full focus:border-[#1B4D3E] focus:outline-none @error('nome') border-red-500 @enderror"
                    >
                    @error('nome')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- descrição / justificativa --}}
                <div class="form-control">
                    <label for="descricao" class="label py-1">
                        <span class="label-text font-medium text-gray-700">
                            Descrição ou exemplos de produtos
                        </span>
                    </label>
                    <textarea
                        id="descricao"
                        name="descricao"
                        rows="4"
                        placeholder="Descreva brevemente quais tipos de produtos ou lotes entrariam nesta categoria..."
                        class="textarea textarea-bordered w-full focus:border-[#1B4D3E] focus:outline-none @error('descricao') border-red-500 @enderror"
                    >{{ old('descricao') }}</textarea>
                    @error('descricao')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- botões de ação --}}
                <div class="pt-3 border-t border-gray-100 flex flex-wrap items-center justify-end gap-3">
                    <a href="{{ url()->previous() }}" class="btn btn-ghost text-gray-600 hover:bg-gray-100">
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-copav text-white border-none gap-2 px-6 shadow-sm"
                    >
                        <span class="material-symbols-outlined text-lg">send</span>
                        Enviar Sugestão
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection