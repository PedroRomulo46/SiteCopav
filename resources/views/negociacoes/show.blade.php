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

        {{-- Mensagens de Feedback --}}
        @if(session('sucesso'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-green-600">check_circle</span>
                    <span>{{ session('sucesso') }}</span>
                </div>
            </div>
        @endif

        @if(session('erro'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-red-600">error</span>
                    <span>{{ session('erro') }}</span>
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
                    <span class="block text-xs font-medium text-gray-500 uppercase">FORNECEDOR</span>
                    <span class="text-base font-semibold text-gray-800">
                        {{ $negociacao->oferta->fornecedor->nome }}
                    </span>
                </div>

                <div class="p-3 bg-gray-50 rounded-md border border-gray-100">
                    <span class="block text-xs font-medium text-gray-500 uppercase">CLIENTE</span>
                    <span class="text-base font-semibold text-gray-800">
                        {{ $negociacao->cliente->nome }}
                    </span>
                </div>

                <div class="p-3 bg-gray-50 rounded-md border border-gray-100">
                    <span class="block text-xs font-medium text-gray-500 uppercase">QUANTIDADE DA OFERTA</span>
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
                    <span class="block text-xs font-medium text-gray-500 uppercase">VALOR DA OFERTA</span>
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

                {{-- Exibe o botão 'Fazer proposta' APENAS para quem NÃO é o Dono do Produto / Admin / Fornecedor --}}
                @php
                    $usuarioLogado = auth()->user();
                    $ehDonoDaOferta = $usuarioLogado->id === ($negociacao->oferta->fornecedor->user_id ?? null);
                    $ehAdminOuEmpresa = in_array($usuarioLogado->user_type ?? '', ['admin', 'empresa', 'fornecedor']) || ($usuarioLogado->fornecedor ?? false);
                @endphp

                @if(!$ehDonoDaOferta && !$ehAdminOuEmpresa)
                    <a
                        href="{{ route('propostas.create', $negociacao) }}"
                        class="inline-flex items-center gap-1 bg-[#236350] hover:bg-[#1B4D3E] text-white text-sm px-4 py-2 rounded-md transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-base">add</span>
                        Fazer proposta
                    </a>
                @endif
            </div>

            @if($negociacao->propostas->count())
                <div class="space-y-4">
                    @foreach($negociacao->propostas as $proposta)
                        {{-- Card de Proposta Individual --}}
                        <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-white shadow-sm">

                            {{-- Cabeçalho da Proposta --}}
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-1.5 text-gray-700 font-semibold">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>{{ $proposta->usuario->nome ?? 'Usuário' }}</span>
                                    </div>

                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ ($proposta->usuario->user_type ?? '') === 'cliente' ? 'Cliente' : 'Fornecedor' }}
                                    </span>

                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200 capitalize">
                                        {{ $proposta->status ?? 'Pendente' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Informações de Valor e Quantidade --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-2 border-b border-gray-100">
                                <div>
                                    <span class="font-bold text-gray-800">Valor Unitário:</span>
                                    <span class="text-gray-700">R$ {{ number_format($proposta->valor, 2, ',', '.') }}</span>
                                </div>

                                <div>
                                    <span class="font-bold text-gray-800">Quantidade:</span>
                                    <span class="text-gray-700">{{ $proposta->quantidade }} {{ $negociacao->oferta->unidade }}</span>
                                </div>
                            </div>

                            {{-- Observação --}}
                            <div class="mt-3">
                                <span class="block font-bold text-gray-800 mb-1">Observação:</span>
                                <div class="p-3 bg-gray-50 rounded-md border border-gray-100 text-gray-600 text-sm">
                                    {{ $proposta->observacao ?? 'Sem observação' }}
                                </div>
                            </div>

                            {{-- Definição das ações de acordo com o perfil do usuário --}}
                            @php
                                $statusPendente = strtolower($proposta->status ?? 'pendente') === 'pendente';

                                // 1. Verifica se quem está logado é o FORNECEDOR dono da oferta ou ADMIN
                                $ehGerenciadorDoProduto = (auth()->id() === ($negociacao->oferta->fornecedor->user_id ?? null)) ||
                                                        in_array(auth()->user()->user_type ?? '', ['admin', 'empresa']);

                                // 2. Verifica se o usuário logado é o CLIENTE que fez esta proposta
                                $ehAutorDaProposta = (auth()->id() === ($proposta->user_id ?? null)) ||
                                                    ((auth()->user()->cliente->id ?? null) === ($proposta->cliente_id ?? null));
                            @endphp

                            @if($statusPendente)
                                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-end gap-2">

                                    {{-- Se for o cliente que fez a proposta: Mostra 'Cancelar Proposta' --}}
                                    @if($ehAutorDaProposta && !$ehGerenciadorDoProduto)
                                        <form action="{{ route('propostas.recusar', $proposta->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button 
                                                type="submit"
                                                class="p-2 rounded-md text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 border border-gray-300 transition-colors">
                                                Cancelar Proposta
                                            </button>
                                        </form>

                                    {{-- Se for o fornecedor: Mostra 'Recusar' e 'Aceitar' --}}
                                    @elseif($ehGerenciadorDoProduto)

                                        {{-- Formulário Recusar --}}
                                        <form action="{{ route('propostas.recusar', $proposta->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button 
                                                type="submit"
                                                class="p-2 rounded-md text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors">
                                                Recusar Proposta
                                            </button>
                                        </form>

                                        {{-- Formulário Aceitar --}}
                                        <form action="{{ route('propostas.aceitar', $proposta->id) }}" method="POST" onsubmit="return confirmarProposta(event, {{ $proposta->valor }}, {{ $proposta->quantidade }}, '{{ $negociacao->oferta->unidade }}')">
                                            @csrf
                                            @method('PATCH')
                                            <button 
                                                type="submit"
                                                class="btn-copav p-2 rounded-md text-xs font-semibold text-white transition-colors">
                                                Aceitar Proposta
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    <span class="material-symbols-outlined text-gray-400 text-4xl mb-2">inbox</span>
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

    {{-- Pop-up de confirmação --}}
    <div
        id="modalConfirmarProposta"
        class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50 px-4">

        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-green-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-600">handshake</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Confirmar proposta</h2>
                        <p class="text-sm text-gray-500">Confira os valores antes de aceitar.</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-500">Valor por unidade</span>
                        <strong id="modalValor" class="text-gray-800"></strong>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-500">Quantidade</span>
                        <strong id="modalQuantidade" class="text-gray-800"></strong>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-green-50 border border-green-100 rounded-lg">
                        <span class="text-sm font-medium text-green-700">Valor total</span>
                        <strong id="modalTotal" class="text-lg text-[#236350]"></strong>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-amber-50 border border-amber-100 rounded-lg">
                    <div class="flex gap-2">
                        <span class="material-symbols-outlined text-amber-600 text-lg">warning</span>
                        <p class="text-xs text-amber-800">
                            Ao aceitar, esta proposta será confirmada e a quantidade será contabilizada na oferta.
                        </p>
                    </div>
                </div>
            </div>

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
                    class="btn-copav px-4 py-2 rounded-lg text-sm font-semibold text-whitetransition-colors">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
@endsection

<script>
    let formularioProposta = null;

    function confirmarProposta(event, valor, quantidade, unidade) {
        event.preventDefault();
        formularioProposta = event.target;

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

        document.getElementById('modalConfirmarProposta').classList.remove('hidden');
        document.getElementById('modalConfirmarProposta').classList.add('flex');

        return false;
    }

    function fecharModalProposta() {
        document.getElementById('modalConfirmarProposta').classList.add('hidden');
        document.getElementById('modalConfirmarProposta').classList.remove('flex');
        formularioProposta = null;
    }

    function confirmarAceite() {
        if (formularioProposta) {
            formularioProposta.submit();
        }
    }


    // Fecha clicando fora do modal
    const modalConfirmarProposta = document.getElementById('modalConfirmarProposta');

    if (modalConfirmarProposta) {
        modalConfirmarProposta.addEventListener('click', function(event) {
            if (event.target === this) {
                fecharModalProposta();
            }
        });
    }
</script>