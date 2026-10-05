@extends('layouts.layout')
@section('title', 'Detalhes do Fornecedor')

@section('conteudo')

{{-- Botão de Voltar --}}
<div class="mb-4 text-left">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#236350] transition-colors">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Voltar para a página inicial
    </a>
</div>

<div class="w-full max-w-4xl mx-auto my-6 px-4">

    {{-- Card Principal --}}
    <div class="w-full bg-white rounded-xl shadow-md p-6 sm:p-8 overflow-hidden">
        
        {{-- Cabeçalho Centralizado --}}
        <div class="flex flex-col items-center justify-center text-center gap-4 mb-6 pb-6 border-b border-gray-200 w-full">
            
            {{-- Título --}}
            <div class="w-full text-center">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800 leading-tight">{{ $fornecedor->nome }}</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Detalhes completos do fornecedor cadastrado</p>
            </div>

            {{-- Botões no Meio --}}
            @if(
                auth()->id() === $fornecedor->user_id ||
                auth()->user()->user_type === 'admin')
                <div class="flex items-center justify-center gap-3 shrink-0">
                    <a href="{{ route('fornecedores.edit', $fornecedor) }}" class="inline-flex items-center gap-1.5 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-base">edit</span>
                        Editar
                    </a>

                    <form action="{{ route('fornecedores.destroy', $fornecedor) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este fornecedor?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-base">delete</span>
                            Excluir
                        </button>
                    </form>
                </div>
            @endif

        </div>

        {{-- Grid de Informações --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
            <div>
                <span class="block text-xs font-medium uppercase tracking-wider text-gray-400">Documento</span>
                <p class="mt-1 text-sm sm:text-base text-gray-900 font-semibold">{{ $fornecedor->documento }}</p>
            </div>

            <div>
                <span class="block text-xs font-medium uppercase tracking-wider text-gray-400">Telefone</span>
                <p class="mt-1 text-sm sm:text-base text-gray-900 font-semibold">{{ $fornecedor->telefone }}</p>
            </div>

            <div class="md:col-span-2">
                <span class="block text-xs font-medium uppercase tracking-wider text-gray-400">Descrição</span>
                <p class="mt-1 text-sm sm:text-base text-gray-800">{{ $fornecedor->descricao ?? 'Não informada' }}</p>
            </div>

            <div>
                <span class="block text-xs font-medium uppercase tracking-wider text-gray-400">Endereço</span>
                <p class="mt-1 text-sm sm:text-base text-gray-800">{{ $fornecedor->endereco }}</p>
            </div>

            <div>
                <span class="block text-xs font-medium uppercase tracking-wider text-gray-400">Cidade / Estado</span>
                <p class="mt-1 text-sm sm:text-base text-gray-800">{{ $fornecedor->cidade }} - {{ $fornecedor->estado }}</p>
            </div>

            <div>
                <span class="block text-xs font-medium uppercase tracking-wider text-gray-400">Status</span>
                <p class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full {{ $fornecedor->status === 'ativo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($fornecedor->status) }}
                    </span>
                </p>
            </div>
        </div>

    </div>

</div>

@endsection