@extends('layouts.layout')
@section('title', 'Detalhes do Fornecedor')

@section('conteudo')

<div class="text-gray-600 mx-1 mt-1">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1 hover:text-gray-700">
        <span class="material-symbols-outlined">arrow_back</span>
        Voltar
    </a>
</div>

<div class="max-w-4xl mx-auto my-6 p-6 bg-white rounded-lg shadow-md">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $fornecedor->nome }}</h1>
            <p class="text-sm text-gray-500 mt-1">Detalhes completos do fornecedor cadastrado</p>
        </div>

        @if(
            auth()->id() === $fornecedor->user_id ||
            auth()->user()->user_type === 'admin'
        )
            <div class="flex items-center gap-3 mt-4 md:mt-0">
                <a href="{{ route('fornecedores.edit', $fornecedor) }}" class="inline-flex items-center gap-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    <span class="material-symbols-outlined text-base">edit</span>
                    Editar
                </a>

                <form action="{{ route('fornecedores.destroy', $fornecedor) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este fornecedor?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        <span class="material-symbols-outlined text-base">delete</span>
                        Excluir
                    </button>
                </form>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <span class="block text-sm font-medium text-gray-500">Documento</span>
            <p class="mt-1 text-base text-gray-900 font-semibold">{{ $fornecedor->documento }}</p>
        </div>

        <div>
            <span class="block text-sm font-medium text-gray-500">Telefone</span>
            <p class="mt-1 text-base text-gray-900 font-semibold">{{ $fornecedor->telefone }}</p>
        </div>

        <div class="md:col-span-2">
            <span class="block text-sm font-medium text-gray-500">Descrição</span>
            <p class="mt-1 text-base text-gray-900">{{ $fornecedor->descricao ?? 'Não informada' }}</p>
        </div>

        <div>
            <span class="block text-sm font-medium text-gray-500">Endereço</span>
            <p class="mt-1 text-base text-gray-900">{{ $fornecedor->endereco }}</p>
        </div>

        <div>
            <span class="block text-sm font-medium text-gray-500">Cidade / Estado</span>
            <p class="mt-1 text-base text-gray-900">{{ $fornecedor->cidade }} - {{ $fornecedor->estado }}</p>
        </div>

        <div>
            <span class="block text-sm font-medium text-gray-500">Status</span>
            <p class="mt-1">
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $fornecedor->status === 'ativo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($fornecedor->status) }}
                </span>
            </p>
        </div>
    </div>

</div>

@endsection
