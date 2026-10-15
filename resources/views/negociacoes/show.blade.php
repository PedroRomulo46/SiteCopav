@extends('layouts.layout')
@section('title', 'Detalhes da Negociação')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">
        {{-- Item 1: Início --}}
        <li>
            <a href="{{ route('home') }}" class="hover:text-[#236350] transition-colors">
                Início
            </a>
        </li>

        @php
            $usuario = auth()->user();
            $is_Cliente = $usuario && $negociacao->cliente_id === $usuario->id;
        @endphp

        {{-- Item 2: Compras ou Vendas --}}
        <li class="flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            @if($is_Cliente)
                <a href="{{ route('negociacoes.compras') }}" class="hover:text-[#236350] transition-colors">
                    Minhas Compras
                </a>
            @else
                <a href="{{ route('negociacoes.vendas') }}" class="hover:text-[#236350] transition-colors">
                    Minhas Vendas
                </a>
            @endif
        </li>

        {{-- Item 3: Página Atual --}}
        <li class="flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <span class="font-semibold text-gray-800">
                Negociação #{{ $negociacao->id }}
            </span>
        </li>
    </ol>
</nav>

    <div
        id="negociacao-realtime"
        data-negociacao-id="{{ $negociacao->id }}"
        data-pode-gerenciar="{{ auth()->user()->user_type === 'admin' || (auth()->user()->fornecedor && $negociacao->oferta->fornecedor && auth()->user()->fornecedor->id === $negociacao->oferta->fornecedor->id) ? '1' : '0' }}"
        data-unidade="{{ $negociacao->oferta->unidade }}"
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

                <div id="lista-propostas" class="space-y-4">
                    @forelse($negociacao->propostas as $proposta)
                        {{-- Card de Proposta Individual --}}
                        <div
                            id="card-proposta-{{ $proposta->id }}"
                            class="border border-gray-200 rounded-lg p-4 mb-4 bg-white shadow-sm"
                        >

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

                                    <span
                                        id="status-proposta-{{ $proposta->id }}"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200 capitalize"
                                    >
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
                                $ehAutorDaProposta = (int) auth()->id() === (int) $proposta->usuario_id;
                            @endphp

                           @if($statusPendente)
                                <div
                                    id="acoes-proposta-{{ $proposta->id }}"
                                    class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-end gap-2"
                                >

                                    {{-- Se for o cliente que fez a proposta: Mostra 'Cancelar Proposta' --}}
                                    @if($ehAutorDaProposta && !$ehGerenciadorDoProduto)

                                        {{-- Se for o cliente: Cancelar proposta --}}
                                        <form action="{{ route('propostas.cancelar', $proposta->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="p-2 rounded-md text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 border border-gray-300 transition-colors">
                                                Cancelar Proposta
                                            </button>
                                        </form>

                                        {{-- Se for o fornecedor: Recusar e aceitar --}}
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
                                            <form action="{{ route('propostas.aceitar', $proposta->id) }}" method="POST"
                                                onsubmit="return confirmarProposta(event, {{ $proposta->valor }}, {{ $proposta->quantidade }}, '{{ $negociacao->oferta->unidade }}')">
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
                    @empty
                        <div id="lista-propostas-vazia"
                            class="text-center py-6 text-gray-500">
                            Nenhuma proposta foi realizada ainda.
                        </div>
                    @endforelse
                </div>

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

@push('scripts')

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


<script>
document.addEventListener('DOMContentLoaded', function () {
    const raiz = document.getElementById('negociacao-realtime');

    if (!raiz) return;

    if (!window.Echo) {
        console.error('Echo não foi carregado na tela da negociação.');
        return;
    }

    const negociacaoId = Number(raiz.dataset.negociacaoId);
    const lista = document.getElementById('lista-propostas');
    const podeGerenciar = raiz.dataset.podeGerenciar === '1';
    const unidade = raiz.dataset.unidade || '';
    const csrf = raiz.dataset.csrf;

    // Cancelar proposta sem recarregar a página
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

                // O evento PropostaAtualizada atualizará o cartão via Echo.
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
        const card = document.getElementById(
            `card-proposta-${proposta.id}`
        );

        const status = document.getElementById(
            `status-proposta-${proposta.id}`
        );

        if (status) {
            const nomes = {
                pendente: 'Pendente',
                aceita: 'Aceita',
                recusada: 'Recusada',
                cancelada: 'Cancelada'
            };

            status.textContent =
                nomes[proposta.status] || proposta.status;

            status.className =
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border capitalize';

            if (proposta.status === 'aceita') {
                status.classList.add(
                    'bg-green-100', 'text-green-800', 'border-green-200'
                );
            } else if (proposta.status === 'recusada') {
                status.classList.add(
                    'bg-red-100', 'text-red-800', 'border-red-200'
                );
            } else if (proposta.status === 'cancelada') {
                status.classList.add(
                    'bg-gray-100', 'text-gray-600', 'border-gray-200'
                );
            } else {
                status.classList.add(
                    'bg-gray-100', 'text-gray-800', 'border-gray-200'
                );
            }
        }

        if (['aceita', 'recusada', 'cancelada'].includes(proposta.status)) {
            document.getElementById(
                `acoes-proposta-${proposta.id}`
            )?.remove();
        }

        if (card) {
            card.dataset.status = proposta.status;
        }
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
            ? 'btn-copav p-2 rounded-md text-xs font-semibold text-white transition-colors'
            : 'p-2 rounded-md text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors';

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

        // Evita duplicar uma proposta já presente na página.
        if (document.getElementById(`card-proposta-${proposta.id}`)) {
            return;
        }

        document.getElementById('lista-propostas-vazia')?.remove();

        const card = document.createElement('article');
        card.id = `card-proposta-${proposta.id}`;
        card.className =
            'border border-gray-200 rounded-lg p-4 mb-4 bg-white shadow-sm';

        const cabecalho = document.createElement('div');
        cabecalho.className =
            'flex flex-wrap items-center gap-2 border-b border-gray-100 pb-3 mb-3';

        const autor = document.createElement('strong');
        autor.className = 'text-gray-700';
        autor.textContent = proposta.usuario_nome || 'Usuário';
        cabecalho.appendChild(autor);

        const tipo = document.createElement('span');
        tipo.className =
            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100';
        tipo.textContent = proposta.usuario_tipo === 'cliente'
            ? 'Cliente'
            : 'Fornecedor';
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
            `Quantidade: ${formatarQuantidade(proposta.quantidade)} ${unidade}`;

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

        lista.prepend(card);
        atualizarStatus(proposta);
    }

    window.Echo.private(`negociacao.${negociacaoId}`)
        .listen('.proposta.criada', function (evento) {
            const proposta = evento.proposta;

            if (
                !proposta ||
                Number(proposta.negociacao_id) !== negociacaoId
            ) {
                return;
            }

            adicionarProposta(proposta);
        })
        .listen('.proposta.atualizada', function (evento) {
            const proposta = evento.proposta;

            if (
                !proposta ||
                Number(proposta.negociacao_id) !== negociacaoId
            ) {
                return;
            }

            // Se a proposta ainda não existir na tela, cria o card.
            adicionarProposta(proposta);
            atualizarStatus(proposta);
        });
});
</script>
@endpush