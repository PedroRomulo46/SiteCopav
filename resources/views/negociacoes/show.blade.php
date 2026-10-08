@extends('layouts.layout')
@section('title', 'Detalhes da Negociação')

@section('conteudo')

    {{-- Botão Voltar --}}
    <div class="mb-4">
        <a
            href="{{ route('ofertas.show', $negociacao->oferta_id) }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#236350] transition-colors">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Voltar para o produto
        </a>
    </div>

    <div class="max-w-4xl mx-auto my-6 space-y-6">

        {{-- Mensagens --}}
        @if(session('sucesso'))

            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                <div class="flex items-center gap-2">

                    <span class="material-symbols-outlined text-green-600">
                        check_circle
                    </span>

                    <span>
                        {{ session('sucesso') }}
                    </span>

                </div>
            </div>

        @endif

        @if(session('erro'))

            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <div class="flex items-center gap-2">

                    <span class="material-symbols-outlined text-red-600">
                        error
                    </span>

                    <span>
                        {{ session('erro') }}
                    </span>

                </div>
            </div>
        @endif

        {{-- Card de Detalhes da Negociação --}}
        <div class="bg-white rounded-lg shadow-md p-6">

            <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-6">
                <div class="text-left">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 block">
                        NEGOCIAÇÃO
                    </span>

                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ $negociacao->oferta->produto->nome }}
                    </h1>
                </div>

                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800 capitalize">
                        {{ str_replace('_', ' ', $negociacao->status) }}
                    </span>
                </div>
            </div>

            {{-- Grid de Informações Principais --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                <div class="p-3 bg-gray-50 rounded-md border border-gray-100">
                    <span class="block text-xs font-medium text-gray-500 uppercase">
                        FORNECEDOR
                    </span>

                    <span class="text-base font-semibold text-gray-800">
                        {{ $negociacao->oferta->fornecedor->nome }}
                    </span>
                </div>

                <div class="p-3 bg-gray-50 rounded-md border border-gray-100">
                    <span class="block text-xs font-medium text-gray-500 uppercase">
                        CLIENTE
                    </span>

                    <span class="text-base font-semibold text-gray-800">
                        {{ $negociacao->cliente->nome }}
                    </span>
                </div>

                <div class="p-3 bg-gray-50 rounded-md border border-gray-100">
                    <span class="block text-xs font-medium text-gray-500 uppercase">
                        QUANTIDADE DA OFERTA
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
                        VALOR DA OFERTA
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

            {{-- Fazer proposta --}}

                @if(auth()->id() !== $negociacao->oferta->fornecedor->user_id)

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

                        {{-- Card de Proposta Individual --}}
                        <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-white shadow-sm">

                            {{-- Cabeçalho da Proposta (Autor à esquerda + Status à direita) --}}
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-3">
                                <div class="flex items-center gap-3">
                                    {{-- Identificação/Autor da proposta alinhado à esquerda --}}
                                    <div class="flex items-center gap-1.5 text-gray-700 font-semibold">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>

                                        <span>
                                            {{ $proposta->usuario->nome ?? 'Usuário' }}
                                        </span>
                                    </div>

                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full
                                                text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ $proposta->usuario->user_type === 'cliente' ? 'Cliente' : 'Fornecedor' }}
                                    </span>

                                    {{-- Badge de Status da Proposta ao lado do autor --}}
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        {{ $proposta->status ?? 'Pendente' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Informações de Valor e Quantidade --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-2 border-b border-gray-100">
                                <div>
                                    <span class="font-bold text-gray-800">Valor:</span>
                                    <span class="text-gray-700">R$ {{ number_format($proposta->valor ?? 12, 2, ',', '.') }}</span>
                                </div>

                                <div>
                                    <span class="font-bold text-gray-800">Quantidade:</span>
                                    <span class="text-gray-700">{{ $proposta->quantidade ?? 14 }} Sacas</span>
                                </div>
                            </div>

                            {{-- Observação --}}
                            <div class="mt-3">
                                <span class="block font-bold text-gray-800 mb-1">Observação:</span>
                                <div class="p-3 bg-gray-50 rounded-md border border-gray-100 text-gray-600 text-sm">
                                    {{ $proposta->observacao ?? 'Sem observação' }}
                                </div>
                            </div>

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

    {{-- POP-UP DE CONFIRMAÇÃO --}}

    <div
        id="modalConfirmarProposta"
        class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50 px-4">

        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">

            {{-- Cabeçalho --}}

            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-green-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-600">
                            handshake
                        </span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">
                            Confirmar proposta
                        </h2>
                        <p class="text-sm text-gray-500">
                            Confira os valores antes de aceitar.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Informações --}}
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-500">
                            Valor por unidade
                        </span>
                        <strong
                            id="modalValor"
                            class="text-gray-800">
                        </strong>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">

                        <span class="text-sm text-gray-500">
                            Quantidade
                        </span>

                        <strong
                            id="modalQuantidade"
                            class="text-gray-800">
                        </strong>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-green-50 border border-green-100 rounded-lg">
                        <span class="text-sm font-medium text-green-700">
                            Valor total
                        </span>
                        <strong
                            id="modalTotal"
                            class="text-lg text-[#236350]">
                        </strong>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-amber-50 border border-amber-100 rounded-lg">
                    <div class="flex gap-2">
                        <span class="material-symbols-outlined text-amber-600 text-lg">
                            warning
                        </span>
                        <p class="text-xs text-amber-800">
                            Ao aceitar, esta proposta será confirmada e a quantidade será contabilizada na oferta.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Botões --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-2">

                <button
                    type="button"
                    onclick="fecharModalProposta()"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition-colors">
                    Cancelar
                </button>
                <button
                    type="button"
                    onclick="confirmarAceite()"
                    class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-green-600 hover:bg-green-700 transition-colors">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
@endsection

<script>

    let formularioProposta = null;

    function confirmarProposta(valor, quantidade, unidade) {

        formularioProposta = event.currentTarget;

        const total = valor * quantidade;

        document.getElementById('modalValor').textContent =
            'R$ ' + valor.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        document.getElementById('modalQuantidade').textContent =
            quantidade.toLocaleString('pt-BR', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            }) + ' ' + unidade;

        document.getElementById('modalTotal').textContent =
            'R$ ' + total.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        document
            .getElementById('modalConfirmarProposta')
            .classList.remove('hidden');

        return false;
    }

    function fecharModalProposta() {
        document
            .getElementById('modalConfirmarProposta')
            .classList.add('hidden');

        formularioProposta = null;
    }

    function confirmarAceite() {
        if (formularioProposta) {

            formularioProposta.submit();

        }

    }

    // Fecha clicando fora do modal
    document
        .getElementById('modalConfirmarProposta')
        .addEventListener('click', function(event) {

            if (event.target === this) {

                fecharModalProposta();

            }
        });

</script>