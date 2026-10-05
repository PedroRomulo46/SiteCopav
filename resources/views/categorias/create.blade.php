@extends('layouts.layout')

@section('conteudo')
<div class="max-w-2xl mx-auto my-10 p-6 bg-white rounded-xl shadow-md border border-gray-100">

  {{-- Cabeçalho --}}
  <div class="flex items-center justify-between pb-4 mb-6 border-b border-gray-100">
    <div class="flex items-center gap-3">
      <div class="p-2 bg-emerald-100 text-[#1B4D3E] rounded-lg">
        <span class="material-symbols-outlined text-2xl">category</span>
      </div>
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Cadastrar Categoria</h1>
        <p class="text-xs text-gray-500">Adicione uma nova categoria de livros ao sistema</p>
      </div>
    </div>

    <a href="{{ route('categorias.index') }}" class="btn btn-ghost btn-sm text-gray-600 hover:text-[#1B4D3E] flex items-center gap-1">
      <span class="material-symbols-outlined text-lg">arrow_back</span>
      Voltar
    </a>
  </div>

  {{-- Exibição de Erros de Validação --}}
  @if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-red-700 text-sm">
      <div class="flex items-center gap-2 font-semibold mb-1">
        <span class="material-symbols-outlined text-base">error</span>
        Por favor, corrija os erros abaixo:
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
    <div class="form-control">
      <label for="nome" class="label text-sm font-medium text-gray-700">
        <span class="label-text font-semibold">Nome da Categoria <span class="text-red-500">*</span></span>
      </label>
      <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
          <span class="material-symbols-outlined text-xl">label</span>
        </span>
        <input
          type="text"
          id="nome"
          name="nome"
          value="{{ old('nome') }}"
          placeholder="Ex: Ficção Científica, Romance, Biografia..."
          class="input input-bordered w-full pl-10 focus:border-[#1B4D3E] focus:ring-1 focus:ring-[#1B4D3E] @error('nome') input-error @enderror"
          required
        >
      </div>
      @error('nome')
        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
      @enderror
    </div>

    {{-- Campo Descrição --}}
    <div class="form-control">
      <label for="descricao" class="label text-sm font-medium text-gray-700">
        <span class="label-text font-semibold">Descrição <span class="text-xs text-gray-400 font-normal">(Opcional)</span></span>
      </label>
      <textarea
        id="descricao"
        name="descricao"
        rows="4"
        placeholder="Breve descrição sobre os livros pertencentes a esta categoria..."
        class="textarea textarea-bordered w-full focus:border-[#1B4D3E] focus:ring-1 focus:ring-[#1B4D3E] @error('descricao') textarea-error @enderror"
      >{{ old('descricao') }}</textarea>
      @error('descricao')
        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
      @enderror
    </div>

    {{-- Botões de Ação --}}
    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
      <a href="{{ route('categorias.index') }}" class="btn btn-outline border-gray-300 text-gray-600 hover:bg-gray-100 hover:text-gray-800">
        Cancelar
      </a>
      
      <button type="submit" class="btn btn-copav text-white border-none gap-2 px-6">
        <span class="material-symbols-outlined text-lg">save</span>
        Cadastrar Categoria
      </button>
    </div>

  </form>
</div>
@endsection