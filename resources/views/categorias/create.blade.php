@extends('layouts.layout')

@section('title', 'Cadastrar Categoria')

@section('conteudo')
<div class="max-w-2xl mx-auto my-10 p-6 bg-white dark:bg-[#111827] rounded-xl shadow-md border border-gray-100 dark:border-[#273449]">

  {{-- Cabeçalho --}}
  <div class="flex items-center justify-between gap-4 pb-4 mb-6 border-b border-gray-100 dark:border-[#273449]">
    <div class="flex items-center gap-3">
      <div class="p-2 bg-emerald-100 dark:bg-emerald-950/60 text-[#1B4D3E] dark:text-emerald-400 rounded-lg flex items-center justify-center shrink-0">
        <span class="material-symbols-outlined text-2xl leading-none">category</span>
      </div>
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Cadastrar Categoria</h1>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Adicione uma nova categoria ao marketplace</p>
      </div>
    </div>

    <a
      href="{{ route('categorias.index') }}"
      class="inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors shrink-0"
    >
      <span class="material-symbols-outlined text-lg leading-none">arrow_back</span>
      <span>Voltar</span>
    </a>
  </div>

  {{-- Exibição de Erros de Validação --}}
  @if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/50 border-l-4 border-red-500 dark:border-red-400 rounded-r-lg text-red-700 dark:text-red-300 text-sm">
      <div class="flex items-center gap-2 font-semibold mb-1">
        <span class="material-symbols-outlined text-base leading-none">error</span>
        <span>Por favor, corrija os erros abaixo:</span>
      </div>
      <ul class="list-disc list-inside space-y-1">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Formulário --}}
  <form action="{{ route('categorias.store') }}" method="POST" class="space-y-5">
    @csrf

    {{-- Campo Nome --}}
    <div>
      <label for="nome" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
        Nome da Categoria <span class="text-red-500 dark:text-red-400">*</span>
      </label>
      <div class="relative flex items-center">
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 dark:text-gray-500">
          <span class="material-symbols-outlined text-xl leading-none">label</span>
        </span>
        <input
          type="text"
          id="nome"
          name="nome"
          value="{{ old('nome') }}"
          placeholder="Ex: Grãos e Cereais, Frutas e Hortaliças, Maquinários..."
          class="w-full pl-10 pr-3.5 py-2.5 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('nome') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#1B4D3E] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
          required
        >
      </div>
      @error('nome')
        <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
      @enderror
    </div>

    {{-- Campo Descrição --}}
    <div>
      <label for="descricao" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
        Descrição <span class="text-xs text-gray-400 dark:text-gray-500 font-normal">(Opcional)</span>
      </label>
      <textarea
        id="descricao"
        name="descricao"
        rows="4"
        placeholder="Breve descrição sobre os lotes e produtos pertencentes a esta categoria..."
        class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('descricao') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#1B4D3E] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
      >{{ old('descricao') }}</textarea>
      @error('descricao')
        <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
      @enderror
    </div>

    {{-- Botões de Ação --}}
    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-[#273449]">
      <a
        href="{{ route('categorias.index') }}"
        class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium border border-gray-300 dark:border-[#273449] text-gray-600 dark:text-gray-300 bg-transparent hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-800 dark:hover:text-white transition-colors"
      >
        Cancelar
      </a>

      <button
        type="submit"
        class="btn-copav inline-flex items-center justify-center gap-2 px-6 py-2 rounded-lg text-sm font-medium shadow-sm cursor-pointer transition-colors"
      >
        <span class="material-symbols-outlined text-lg leading-none">save</span>
        <span>Cadastrar Categoria</span>
      </button>
    </div>

  </form>
</div>
@endsection