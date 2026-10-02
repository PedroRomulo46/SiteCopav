@extends('layouts.layout')
@section('title', 'Detalhes da Negociação')

@section('conteudo')

{{-- Botão Voltar --}}
<div class="mb-4">
    <a
        href="{{ route('ofertas.show', $negociacao->oferta_id) }}"
        class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#236350] transition-colors"
    >
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Voltar para o produto
    </a>
</div>

<div class="max-w-4xl mx-auto my-6 space-y-6">

    {{-- Card de Detalhes da Negociação --}}
    <div class="bg-white rounded-lg shadow-md p-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 pb-4 mb-6 gap-2">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                    Negociação
                </span>

                <h1 class="text-2xl font-bold text-gray-800">
                    {{ $negociacao->oferta->produto->nome }}
                </h1>
            </div>
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800 capitalize">
                    {{ $negociacao->status }}
                </span>
            </div>
        </div>

        {{-- Grid de Informações Principais --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

            <div class="p-3 bg-gray-50 rounded-md border border-gray-100">
                <span class="block text-xs font-medium text-gray-500 uppercase">
                    Fornecedor
                </span>

                <span class="text-base font-semibold text-gray-800">
                    {{ $negociacao->oferta->fornecedor->nome }}
                </span>
            </div>

            <div class="p-3 bg-gray-50 rounded-md border border-gray-100">
                <span class="block text-xs font-medium text-gray-500 uppercase">
                    Cliente
                </span>

                <span class="text-base font-semibold text-gray-800">
                    {{ $negociacao->cliente->nome }}
                </span>
            </div>

            <div class="p-3 bg-gray-50 rounded-md border border-gray-100">
                <span class="block text-xs font-medium text-gray-500 uppercase">
                    Quantidade da Oferta
                </span>
                <span class="text-base font-semibold text-gray-800">
                    @if(strtolower($negociacao->oferta->unidade) === 'saca')
                        {{ number_format($negociacao->oferta->quantidade, 0, ',', '.') }}
                    @else
                        {{ fmod($negociacao->oferta->quantidade, 1) == 0
                            ? number_format($negociacao->oferta->quantidade, 0, ',', '.')
                            : number_format($negociacao->oferta->quantidade, 2, ',', '.') }}
                    @endif
                    {{ $negociacao->oferta->unidade }}
                </span>
            </div>
            <div class="p-3 bg-gray-50 rounded-md border border-gray-100">
                <span class="block text-xs font-medium text-gray-500 uppercase">
                    Valor da Oferta
                </span>
                <span class="text-base font-semibold text-gray-800">
                    R$ {{ number_format($negociacao->oferta->valor, 2, ',', '.') }}
                </span>
            </div>
        </div>
    </div>


    {{-- Seção de Propostas --}}
    <div class="bg-white rounded-lg shadow-md p-6">

        <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-6">

            <h2 class="text-xl font-bold text-gray-800">
                Propostas
            </h2>

            {{-- Apenas quem participa da negociação pode fazer proposta --}}
            @if(
                auth()->id() === $negociacao->cliente_id ||
                auth()->id() === $negociacao->oferta->fornecedor->user_id
            )

                <a
                    href="{{ route('propostas.create', $negociacao) }}"
                    class="inline-flex items-center gap-1 bg-[#236350] hover:bg-[#1B4D3E] text-white text-sm px-4 py-2 rounded-md transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-base">
                        add
                    </span>
                    Fazer proposta
                </a>

            @endif

        </div>


        @if($negociacao->propostas->count())

            <div class="space-y-4">

                @foreach($negociacao->propostas as $proposta)

                    <div class="p-4 border border-gray-200 rounded-lg hover:border-gray-300 transition-colors bg-white">

                        {{-- Cabeçalho da proposta --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-100 pb-3 mb-3 gap-2">

                            <div class="flex items-center gap-2">

                                <span class="material-symbols-outlined text-gray-400">
                                    person
                                </span>

                                <span class="font-semibold text-gray-800">
                                    {{ $proposta->usuario->nome }}
                                </span>

                            </div>

                            <span class="self-start sm:self-auto text-xs px-2.5 py-1 rounded-full font-medium bg-gray-100 text-gray-700 capitalize">
                                {{ $proposta->status }}
                            </span>

                        </div>


                        {{-- Informações da proposta --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">

                            <div>

                                <strong class="text-gray-600">
                                    Valor:
                                </strong>

                                <span class="text-gray-900 font-medium">
                                    R$ {{ number_format($proposta->valor, 2, ',', '.') }}
                                </span>

                            </div>


                            <div>

                                <strong class="text-gray-600">
                                    Quantidade:
                                </strong>

                                <span class="text-gray-900">

                                    @if(isset($proposta->quantidade))
                                        @if(strtolower($negociacao->oferta->unidade) === 'saca')
                                            {{ number_format($proposta->quantidade, 0, ',', '.') }}
                                        @else
                                            {{ fmod($proposta->quantidade, 1) == 0
                                                ? number_format($proposta->quantidade, 0, ',', '.')
                                                : number_format($proposta->quantidade, 2, ',', '.') }}
                                        @endif
                                        {{ $negociacao->oferta->unidade }}

                                    @else
                                        Não informada
                                    @endif
                                </span>
                            </div>
                        </div>

                        {{-- Observação --}}
                        <div class="mt-3 pt-3 border-t border-gray-100 text-sm">
                            <strong class="text-gray-600 block mb-1">
                                Observação:
                            </strong>
                            <p class="text-gray-700 bg-gray-50 p-2.5 rounded border border-gray-100 text-xs sm:text-sm">
                                {{ $proposta->observacao ?? 'Sem observação' }}
                            </p>

                        </div>

                        {{-- BOTÕES DE ACEITAR / RECUSAR                       --}}
                        @if(
                            auth()->id() === $negociacao->oferta->fornecedor->user_id &&
                            $proposta->status === 'pendente'
                        )

                            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-100">

                                {{-- Aceitar --}}
                                <form
                                    action="{{ route('propostas.aceitar', $proposta) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                        Aceitar proposta
                                    </button>
                                </form>

                                {{-- Recusar --}}
                                <form
                                    action="{{ route('propostas.recusar', $proposta) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                        Recusar proposta
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                <span class="material-symbols-outlined text-gray-400 text-4xl mb-2">
                    inbox
                </span>
                <p class="text-gray-500 font-medium">
                    Nenhuma proposta foi realizada até o momento.
                </p>
            </div>
        @endif

        {{-- Botões de Rodapé --}}
        <div class="flex items-center justify-start mt-6 pt-4 border-t border-gray-200">
            <a
                href="{{ route('negociacoes.index') }}"
                class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                Voltar para as negociações
            </a>
        </div>
    </div>
</div>

@endsection