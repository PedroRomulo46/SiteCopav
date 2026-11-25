@extends('layouts.layout')

@section('title', 'Detalhes da Negociação')

@section('conteudo')

@php
    $usuarioLogado = auth()->user();

    $is_Admin = $usuarioLogado && $usuarioLogado->isAdmin();

    $is_ClienteDaNegociacao = $usuarioLogado
        && (int) $negociacao->cliente_id === (int) $usuarioLogado->id;

    $fornecedorDoUsuario = $usuarioLogado?->fornecedor;

    $is_DonoDaOferta = $usuarioLogado
        && $fornecedorDoUsuario
        && (int) ($negociacao->oferta->fornecedor_id ?? 0)
            === (int) $fornecedorDoUsuario->id;

    $podeGerenciarPropostas = $is_DonoDaOferta
        || ($is_Admin && !$is_ClienteDaNegociacao);

    $podeCriarProposta = $is_ClienteDaNegociacao
        && !$is_DonoDaOferta;

    $urlAnterior = url()->previous();
    $subCompras = str_contains($urlAnterior, 'compras');
    $subVendas = str_contains($urlAnterior, 'vendas');
    $subOferta = str_contains($urlAnterior, 'ofertas');
@endphp

{{-- Breadcrumbs Dinâmicos --}}
<nav class="flex items-center text-sm mb-6 text-gray-600" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">
        {{-- Item 1: Início --}}
        <li>
            <a href="{{ route('home') }}" class="hover:text-[#236350]">
                Início
            </a>
        </li>

        {{-- Item Intermediário Dinâmico baseado no contexto ou origem --}}
        @if($subCompras)
            <li class="flex items-center space-x-2">
                <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
                <a href="{{ route('negociacoes.compras') }}" class="hover:text-[#236350] transition-colors">
                    Minhas Compras
                </a>
            </li>
        @elseif($subVendas)
            <li class="flex items-center space-x-2">
                <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
                <a href="{{ route('negociacoes.vendas') }}" class="hover:text-[#236350] transition-colors">
                    Minhas Vendas
                </a>
            </li>
        @elseif($subOferta)
            <li class="flex items-center space-x-2">
                <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
                <a href="{{ $urlAnterior }}" class="hover:text-[#236350] transition-colors">
                    {{ $negociacao->oferta->produto->nome ?? 'Oferta' }}
                </a>
            </li>
        @else
            <li class="flex items-center space-x-2">
                <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
                <a href="{{ $is_ClienteDaNegociacao ? route('negociacoes.compras') : route('negociacoes.vendas') }}" class="hover:text-[#236350] transition-colors">
                    {{ $is_ClienteDaNegociacao ? 'Minhas Compras' : 'Minhas Vendas' }}
                </a>
            </li>
        @endif

        {{-- Item Atual --}}
        <li class="flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <span class="font-semibold text-gray-800">
                Painel de Negociação
            </span>
        </li>
    </ol>
</nav>

<div
    id="negociacao-realtime"
    data-negociacao-id="{{ $negociacao->id }}"
    data-cliente-id="{{ $negociacao->cliente_id }}"
    data-pode-gerenciar="{{ $podeGerenciarPropostas ? '1' : '0' }}"
    data-unidade="{{ $negociacao->oferta->unidade ?? '' }}"
    data-csrf="{{ csrf_token() }}"
    data-url-aceitar="{{ route('propostas.aceitar', ['proposta' => '__ID__']) }}"
    data-url-recusar="{{ route('propostas.recusar', ['proposta' => '__ID__']) }}"
    class="max-w-4xl mx-auto my-6 space-y-6"
>

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
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ $negociacao->oferta->produto->nome ?? 'Produto' }}
                    </h1>

                    @if($negociacao->oferta)
                        <a
                            href="{{ route('ofertas.show', $negociacao->oferta) }}"
                            title="Visitar loja do produto"
                            aria-label="Visitar loja do produto"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg
                                text-[#236350] bg-green-50 border border-green-100
                                hover:bg-green-100 hover:border-green-200
                                transition-colors"
                        >
                            <span class="material-symbols-outlined text-xl">
                                storefront
                            </span>
                        </a>
                    @endif
                </div>
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
                    {{ $negociacao->oferta->fornecedor->nome ?? 'Fornecedor não informado' }}
                </span>
            </div>

            <div class="p-3 bg-gray-50 rounded-md border border-gray-100">
                <span class="block text-xs font-medium text-gray-500 uppercase">CLIENTE</span>
                <span class="text-base font-semibold text-gray-800">
                    {{ $negociacao->cliente->nome ?? $negociacao->cliente->name ?? 'Cliente não informado' }}
                </span>
            </div>

            <div class="p-3 bg-gray-50 rounded-md border border-gray-100">
                <span class="block text-xs font-medium text-gray-500 uppercase">QUANTIDADE DA OFERTA</span>
                <span class="text-base font-semibold text-gray-800">
                    @php
                        $qtdOferta = $negociacao->oferta->quantidade ?? 0;
                        $unidadeOferta = $negociacao->oferta->unidade ?? '';
                    @endphp
                    @if(strtolower($unidadeOferta) === 'saca')
                        {{ number_format($qtdOferta, 0, ',', '.') }}
                    @else
                        {{ fmod($qtdOferta, 1) == 0
                            ? number_format($qtdOferta, 0, ',', '.')
                            : number_format($qtdOferta, 2, ',', '.') }}
                    @endif
                    {{ $unidadeOferta }}
                </span>
            </div>

            <div class="p-3 bg-gray-50 rounded-md border border-gray-100">
                <span class="block text-xs font-medium text-gray-500 uppercase">VALOR DA OFERTA</span>
                <span class="text-base font-semibold text-gray-800">
                    R$ {{ number_format($negociacao->oferta->valor ?? 0, 2, ',', '.') }}
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

            @auth
                @if($podeGerenciarPropostas)
                    <div class="text-xs text-center text-gray-500 py-2 px-4 bg-gray-100 rounded-lg">
                        @if($is_DonoDaOferta)
                            Esta oferta pertence a você.
                        @else
                            Acesso Administrativo.
                        @endif
                    </div>
                @elseif($podeCriarProposta)
                    <a
                        href="{{ route('propostas.create', $negociacao) }}"
                        class="inline-flex items-center gap-1 btn-copav text-white text-sm px-4 py-2 rounded-md transition-colors shadow-sm"
                    >
                        <span class="material-symbols-outlined text-base">add</span>
                        Fazer proposta
                    </a>
                @endif
            @endauth
        </div>

        <div id="lista-propostas" class="space-y-6">
            {{-- Propostas aceitas --}}
            <section>
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-green-600">check_circle</span>
                    <h3 class="font-bold text-gray-800">Propostas aceitas</h3>
                    <span id="contador-aceitas" class="text-xs text-gray-500"></span>
                </div>

                <div id="propostas-aceitas" class="space-y-4">
                    <p id="vazio-aceitas" class="text-sm text-gray-500 bg-gray-50 border border-gray-100 rounded-lg p-4">
                        Nenhuma proposta aceita ainda.
                    </p>
                </div>
            </section>

            {{-- Demais propostas --}}
            <section>
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-amber-600">pending_actions</span>
                    <h3 class="font-bold text-gray-800">Outras propostas</h3>
                    <span id="contador-outras" class="text-xs text-gray-500"></span>
                </div>

                <div id="propostas-outras" class="space-y-4">
                    <p id="vazio-outras" class="text-sm text-gray-500 bg-gray-50 border border-gray-100 rounded-lg p-4">
                        Nenhuma outra proposta no momento.
                    </p>
                </div>
            </section>

            {{-- Área temporária dos cards renderizados pelo Blade --}}
            <div id="propostas-iniciais" class="hidden">
                @forelse($negociacao->propostas as $proposta)
                    @php
                        $statusAtual = strtolower($proposta->status ?? 'pendente');

                        $coresCard = [
                            'aceita' => 'border-l-4 border-l-green-500 border-gray-200',
                            'recusada' => 'border-l-4 border-l-red-500 border-gray-200',
                            'cancelada' => 'border-l-4 border-l-gray-400 border-gray-200',
                            'pendente' => 'border-l-4 border-l-amber-400 border-gray-200',
                        ];

                        $ehAutorCliente = (int) ($proposta->usuario_id ?? 0) === (int) $negociacao->cliente_id;
                        $statusPendente = $statusAtual === 'pendente';
                        $is_AutorDaProposta = $usuarioLogado && (int) $usuarioLogado->id === (int) $proposta->usuario_id;
                        $is_GerenciadorDoProduto = $podeGerenciarPropostas;
                    @endphp

                    <div
                        id="card-proposta-{{ $proposta->id }}"
                        data-status="{{ $statusAtual }}"
                        class="rounded-lg border {{ $coresCard[$statusAtual] ?? $coresCard['pendente'] }} p-4 mb-4 bg-white shadow-sm"
                    >
                        {{-- Cabeçalho da Proposta --}}
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-3">
                            <div class="flex items-center gap-3 flex-wrap">
                                <div class="flex items-center gap-1.5 text-gray-700 font-semibold">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>{{ $proposta->usuario->nome ?? $proposta->usuario->name ?? 'Usuário' }}</span>
                                </div>

                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $ehAutorCliente ? 'Cliente' : 'Fornecedor' }}
                                </span>

                                <span
                                    id="status-proposta-{{ $proposta->id }}"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border capitalize
                                        {{ $statusAtual === 'aceita' ? 'bg-green-100 text-green-800 border-green-200' : '' }}
                                        {{ $statusAtual === 'recusada' ? 'bg-red-100 text-red-800 border-red-200' : '' }}
                                        {{ $statusAtual === 'cancelada' ? 'bg-gray-100 text-gray-600 border-gray-200' : '' }}
                                        {{ $statusAtual === 'pendente' ? 'bg-amber-100 text-amber-800 border-amber-200' : '' }}"
                                >
                                    {{ ucfirst($statusAtual) }}
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
                                <span class="text-gray-700">{{ $proposta->quantidade }} {{ $unidadeOferta }}</span>
                            </div>
                        </div>

                        {{-- Observação --}}
                        <div class="mt-3">
                            <span class="block font-bold text-gray-800 mb-1">Observação:</span>
                            <div class="p-3 bg-gray-50 rounded-md border border-gray-100 text-gray-600 text-sm">
                                {{ $proposta->observacao ?? 'Sem observação' }}
                            </div>
                        </div>

                        @if($statusPendente)
                            <div
                                id="acoes-proposta-{{ $proposta->id }}"
                                class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-end gap-2"
                            >
                                @if($is_AutorDaProposta && !$is_GerenciadorDoProduto)
                                    <form action="{{ route('propostas.cancelar', $proposta->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="p-2 rounded-md text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 border border-gray-300 transition-colors cursor-pointer">
                                            Cancelar Proposta
                                        </button>
                                    </form>
                                @elseif($is_GerenciadorDoProduto)
                                    <form action="{{ route('propostas.recusar', $proposta->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="p-2 rounded-md text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors cursor-pointer">
                                            Recusar Proposta
                                        </button>
                                    </form>

                                    <form action="{{ route('propostas.aceitar', $proposta->id) }}" method="POST"
                                        onsubmit="return confirmarProposta(event, {{ $proposta->valor }}, {{ $proposta->quantidade }}, '{{ $unidadeOferta }}')">
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="btn-copav p-2 rounded-md text-xs font-semibold text-white transition-colors cursor-pointer">
                                            Aceitar Proposta
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div id="lista-propostas-vazia"
                        class="text-center py-6 text-gray-500">
                        Nenhuma proposta foi realizada ainda.
                    </div>
                @endforelse
            </div>
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
                class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition-colors cursor-pointer">
                Cancelar
            </button>
            <button
                type="button"
                onclick="confirmarAceite()"
                class="btn-copav px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors cursor-pointer">
                Confirmar
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let formularioProposta = null;

    function confirmarProposta(event, valor, quantidade, unidade) {
        event.preventDefault();
        formularioProposta = event.target;

        const total = valor * quantidade;

        document.getElementById('modalValor').textContent =
            'R$ ' + Number(valor).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        document.getElementById('modalQuantidade').textContent =
            Number(quantidade).toLocaleString('pt-BR', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            }) + (unidade ? ' ' + unidade : '');

        document.getElementById('modalTotal').textContent =
            'R$ ' + Number(total).toLocaleString('pt-BR', {
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

    const modalConfirmarProposta = document.getElementById('modalConfirmarProposta');

    if (modalConfirmarProposta) {
        modalConfirmarProposta.addEventListener('click', function(event) {
            if (event.target === this) {
                fecharModalProposta();
            }
        });
    }
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const raiz = document.getElementById('negociacao-realtime');

    if (!raiz) return;

    const negociacaoId = Number(raiz.dataset.negociacaoId);
    const clienteId = Number(raiz.dataset.clienteId);
    const lista = document.getElementById('lista-propostas');
    const podeGerenciar = raiz.dataset.podeGerenciar === '1';
    const unidade = raiz.dataset.unidade || '';
    const csrf = raiz.dataset.csrf;
    const propostasAceitas = document.getElementById('propostas-aceitas');
    const propostasOutras = document.getElementById('propostas-outras');
    const propostasIniciais = document.getElementById('propostas-iniciais');

    function atualizarSecoes() {
        const quantidadeAceitas = propostasAceitas.querySelectorAll('[id^="card-proposta-"]').length;
        const quantidadeOutras = propostasOutras.querySelectorAll('[id^="card-proposta-"]').length;

        document.getElementById('vazio-aceitas').classList.toggle('hidden', quantidadeAceitas > 0);
        document.getElementById('vazio-outras').classList.toggle('hidden', quantidadeOutras > 0);

        document.getElementById('contador-aceitas').textContent = `(${quantidadeAceitas})`;
        document.getElementById('contador-outras').textContent = `(${quantidadeOutras})`;
    }

    function distribuirCard(card, status) {
        const destino = status === 'aceita'
            ? propostasAceitas
            : propostasOutras;

        destino.appendChild(card);
        atualizarSecoes();
    }

    propostasIniciais.querySelectorAll('[id^="card-proposta-"]').forEach(card => {
        distribuirCard(card, card.dataset.status || 'pendente');
    });

    document.querySelectorAll(
        'form[action*="/propostas/"][action*="/cancelar"]'
    ).forEach(function (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const botao = form.querySelector('button[type="submit"]');
            if (botao) {
                botao.disabled = true;
                botao.textContent = 'Cancelando...';
            }

            try {
                const resposta = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: new URLSearchParams(new FormData(form))
                });

                if (!resposta.ok) {
                    throw new Error('Não foi possível cancelar a proposta.');
                }
            } catch (erro) {
                console.error(erro);
                alert('Não foi possível cancelar a proposta. Tente novamente.');

                if (botao) {
                    botao.disabled = false;
                    botao.textContent = 'Cancelar Proposta';
                }
            }
        });
    });

    function formatarValor(valor) {
        return Number(valor).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function formatarQuantidade(valor) {
        return Number(valor).toLocaleString('pt-BR', {
            maximumFractionDigits: 2
        });
    }

    function atualizarStatus(proposta) {
        const card = document.getElementById(`card-proposta-${proposta.id}`);
        const status = document.getElementById(`status-proposta-${proposta.id}`);

        const nomes = {
            pendente: 'Pendente',
            aceita: 'Aceita',
            recusada: 'Recusada',
            cancelada: 'Cancelada'
        };

        const cores = {
            aceita: ['bg-green-100', 'text-green-800', 'border-green-200'],
            recusada: ['bg-red-100', 'text-red-800', 'border-red-200'],
            cancelada: ['bg-gray-100', 'text-gray-600', 'border-gray-200'],
            pendente: ['bg-amber-100', 'text-amber-800', 'border-amber-200']
        };

        const statusAtual = proposta.status || 'pendente';

        if (status) {
            status.textContent = nomes[statusAtual] || statusAtual;
            status.className =
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border capitalize';
            status.classList.add(...(cores[statusAtual] || cores.pendente));
        }

        if (card) {
            card.dataset.status = statusAtual;
            card.className =
                'rounded-lg border border-gray-200 border-l-4 p-4 mb-4 bg-white shadow-sm';

            const bordas = {
                aceita: 'border-l-green-500',
                recusada: 'border-l-red-500',
                cancelada: 'border-l-gray-400',
                pendente: 'border-l-amber-400'
            };

            card.classList.add(bordas[statusAtual] || bordas.pendente);
            distribuirCard(card, statusAtual);
        }

        if (['aceita', 'recusada', 'cancelada'].includes(statusAtual)) {
            document.getElementById(`acoes-proposta-${proposta.id}`)?.remove();
        }

        atualizarSecoes();
    }

    function criarFormulario(proposta, tipo) {
        const formulario = document.createElement('form');
        const modeloUrl = tipo === 'aceitar'
            ? raiz.dataset.urlAceitar
            : raiz.dataset.urlRecusar;

        formulario.action = modeloUrl.replace(
            '__ID__',
            encodeURIComponent(proposta.id)
        );
        formulario.method = 'POST';
        formulario.className = 'inline-flex';

        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = csrf;
        formulario.appendChild(token);

        const metodo = document.createElement('input');
        metodo.type = 'hidden';
        metodo.name = '_method';
        metodo.value = 'PATCH';
        formulario.appendChild(metodo);

        const botao = document.createElement('button');
        botao.type = 'submit';
        botao.className = tipo === 'aceitar'
            ? 'btn-copav p-2 rounded-md text-xs font-semibold text-white transition-colors cursor-pointer'
            : 'p-2 rounded-md text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors cursor-pointer';

        botao.textContent = tipo === 'aceitar'
            ? 'Aceitar Proposta'
            : 'Recusar Proposta';

        formulario.appendChild(botao);

        if (tipo === 'aceitar') {
            formulario.addEventListener('submit', function (event) {
                confirmarProposta(
                    event,
                    Number(proposta.valor),
                    Number(proposta.quantidade),
                    unidade
                );
            });
        }

        return formulario;
    }

    function adicionarProposta(proposta) {
        if (!lista || !proposta || !proposta.id) return;

        if (document.getElementById(`card-proposta-${proposta.id}`)) {
            return;
        }

        document.getElementById('lista-propostas-vazia')?.remove();

        const card = document.createElement('article');
        card.id = `card-proposta-${proposta.id}`;
        card.className =
            'rounded-lg border border-gray-200 border-l-4 p-4 mb-4 bg-white shadow-sm';

        const cabecalho = document.createElement('div');
        cabecalho.className =
            'flex flex-wrap items-center gap-2 border-b border-gray-100 pb-3 mb-3';

        const autor = document.createElement('strong');
        autor.className = 'text-gray-700';
        autor.textContent = proposta.usuario_nome || 'Usuário';
        cabecalho.appendChild(autor);

        const ehAutorCliente = Number(proposta.usuario_id) === clienteId;
        const tipo = document.createElement('span');
        tipo.className =
            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100';
        tipo.textContent = ehAutorCliente ? 'Cliente' : 'Fornecedor';
        cabecalho.appendChild(tipo);

        const status = document.createElement('span');
        status.id = `status-proposta-${proposta.id}`;
        status.className =
            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200 capitalize';
        cabecalho.appendChild(status);

        card.appendChild(cabecalho);

        const detalhes = document.createElement('div');
        detalhes.className =
            'grid grid-cols-1 sm:grid-cols-2 gap-4 py-2 border-b border-gray-100';

        const valor = document.createElement('p');
        valor.className = 'text-gray-700';
        valor.textContent = `Valor Unitário: R$ ${formatarValor(proposta.valor)}`;

        const quantidade = document.createElement('p');
        quantidade.className = 'text-gray-700';
        quantidade.textContent =
            `Quantidade: ${formatarQuantidade(proposta.quantidade)}${unidade ? ' ' + unidade : ''}`;

        detalhes.append(valor, quantidade);
        card.appendChild(detalhes);

        const observacaoTitulo = document.createElement('strong');
        observacaoTitulo.className = 'block text-gray-800 mt-3 mb-1';
        observacaoTitulo.textContent = 'Observação:';
        card.appendChild(observacaoTitulo);

        const observacao = document.createElement('div');
        observacao.className =
            'p-3 bg-gray-50 rounded-md border border-gray-100 text-gray-600 text-sm';
        observacao.textContent = proposta.observacao || 'Sem observação';
        card.appendChild(observacao);

        if (podeGerenciar && proposta.status === 'pendente') {
            const acoes = document.createElement('div');
            acoes.id = `acoes-proposta-${proposta.id}`;
            acoes.className =
                'mt-4 pt-3 border-t border-gray-100 flex items-center justify-end gap-2';

            acoes.appendChild(criarFormulario(proposta, 'recusar'));
            acoes.appendChild(criarFormulario(proposta, 'aceitar'));
            card.appendChild(acoes);
        }

        propostasIniciais.appendChild(card);
        atualizarStatus(proposta);
    }

    if (!window.Echo) {
        console.error('Echo não foi carregado na tela da negociação.');
        return;
    }

    window.Echo.private(`negociacao.${negociacaoId}`)
        .listen('.proposta.criada', function (evento) {
            const proposta = evento.proposta;

            if (!proposta || Number(proposta.negociacao_id) !== negociacaoId) {
                return;
            }

            adicionarProposta(proposta);
        })
        .listen('.proposta.atualizada', function (evento) {
            const proposta = evento.proposta;

            if (!proposta || Number(proposta.negociacao_id) !== negociacaoId) {
                return;
            }

            adicionarProposta(proposta);
            atualizarStatus(proposta);
        });
});
</script>
@endpush