@extends('site.layout')
@section('title', 'Detalhes da Oferta')

@section('conteudo')

<div class="p-6 bg-gray-50 min-h-screen flex justify-center items-center">
    <div class="bg-white shadow-md rounded-2xl p-6 max-w-2xl w-full flex flex-col md:flex-row gap-6">
        
        <img class="w-full md:w-1/2 h-64 object-cover rounded-xl" src="{{ asset('assets/milho.png') }}" alt="{{ $produto->nome }}" />

        <div class="flex flex-col justify-between grow">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $produto->nome }}</h1>
                <p class="text-sm text-gray-500 mt-2">{{ $produto->descricao }}</p>
                <p class="text-sm font-bold text-[#79A961] mt-3">Vendida em: {{ $produto->unidade }}</p>
            </div>
        </div>
    </div>

        <div class="mt-6 flex flex-col gap-2">
            <a href="{{ route('home') }}" class="btn text-white bg-[#79A961] hover:bg-[#709b58] border-none rounded-xl px-4 py-2">
                Comprar Agora
            </a>
            <a href="{{ route('home') }}" class="btn text-white bg-[#79A961] hover:bg-[#709b58] border-none rounded-xl px-4 py-2">
                Adicionar ao Carrinho
            </a>
        </div>
</div>

@endsection