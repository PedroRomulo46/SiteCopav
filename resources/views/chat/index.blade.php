@extends('layouts.layout')

@section('conteudo')
<div class="max-w-6xl mx-auto px-4 py-8">

    {{-- Cabeçalho --}}
    <div class="mb-8">
        <p class="text-sm font-medium text-[#236350] mb-2">
            COPAV · COMUNICAÇÃO
        </p>

        <h1 class="text-3xl font-bold text-gray-900">
            Mensagens
        </h1>

        <p class="text-gray-500 mt-2">
            Converse com clientes e fornecedores das suas compras e negociações.
        </p>
    </div>

    {{-- Lista de conversas --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">
                Suas conversas
            </h2>

            <span class="text-sm text-gray-500">
                {{ $chats->count() }} {{ $chats->count() === 1 ? 'conversa' : 'conversas' }}
            </span>
        </div>

        @forelse ($chats as $chat)
            @php
                $negociacao = $chat->negociacao;
                $ehCliente = (int) $negociacao->cliente_id === (int) auth()->id();

                $outroParticipante = $ehCliente
                    ? $negociacao->oferta->fornecedor
                    : $negociacao->cliente;

                $ultimaMensagem = $chat->mensagens->first();

                $nomeParticipante = $ehCliente
                    ? ($outroParticipante->nome ?? 'Fornecedor')
                    : ($outroParticipante->nome ?? 'Cliente');

                $nomeProduto = $negociacao->oferta->produto->nome
                    ?? 'Produto da negociação';
            @endphp

            <a
                href="{{ route('chat.show', $negociacao) }}"
                class="flex items-center gap-4 p-5 sm:px-6 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors"
            >
                {{-- Avatar --}}
                <div class="w-12 h-12 rounded-full bg-[#E5EFEA] text-[#1B4D3E] flex items-center justify-center shrink-0 font-bold text-lg">
                    {{ mb_strtoupper(mb_substr($nomeParticipante, 0, 1)) }}
                </div>

                {{-- Informações --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="font-semibold text-gray-900 truncate">
                            {{ $nomeParticipante }}
                        </h3>

                        @if ($ultimaMensagem)
                            <span class="text-xs text-gray-400">
                                {{ $ultimaMensagem->created_at->format('d/m H:i') }}
                            </span>
                        @endif
                    </div>

                    <p class="text-sm text-[#236350] mt-1 truncate">
                        {{ $nomeProduto }}
                    </p>

                    <p class="text-sm text-gray-500 mt-1 truncate">
                        @if ($ultimaMensagem)
                            {{ $ultimaMensagem->usuario_id == auth()->id() ? 'Você: ' : '' }}{{ $ultimaMensagem->conteudo }}
                        @else
                            Nenhuma mensagem ainda. Inicie a conversa.
                        @endif
                    </p>
                </div>

                <span class="text-gray-400 text-xl shrink-0" aria-hidden="true">
                    ›
                </span>
            </a>
        @empty
            <div class="px-6 py-16 text-center">
                <div class="w-16 h-16 rounded-full bg-[#E5EFEA] text-[#236350] flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl">forum</span>
                </div>

                <h3 class="text-lg font-semibold text-gray-800">
                    Nenhuma conversa por enquanto
                </h3>

                <p class="text-sm text-gray-500 max-w-md mx-auto mt-2">
                    Quando uma proposta for aceita, a conversa ficará disponível aqui para você falar com o outro participante.
                </p>

                <a
                    href="{{ route('negociacoes.index') }}"
                    class="inline-flex items-center justify-center mt-6 px-5 py-2.5 rounded-lg bg-[#236350] text-white font-medium hover:bg-[#1B4D3E] transition-colors"
                >
                    Ver negociações
                </a>
            </div>
        @endforelse

    </div>
</div>
@endsection