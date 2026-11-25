@extends('layouts.layout')

@section('title', 'Acompanhar Encomenda')

@section('conteudo')

@php
    $usuarioId = auth()->id();
    $donoOfertaUserId = $oferta?->fornecedor?->user_id ?? $oferta?->user_id;
    $is_donodaoferta = $usuarioId !== null
        && (int) $usuarioId === (int) $donoOfertaUserId
        && (int) $usuarioId !== (int) $negociacao->cliente_id;

    $statusAtual = $negociacao->status_entrega ?? 'pagamento_aprovado';

    $etapas = [
        'pagamento_aprovado' => [
            'titulo' => 'Pagamento Aprovado',
            'descricao' => 'O pagamento foi processado e confirmado com sucesso.',
            'icone' => 'check',
            'cor' => 'bg-emerald-600',
            'botao' => 'Marcar: Pagamento Aprovado',
        ],
        'em_separacao' => [
            'titulo' => 'Em separação pelo Fornecedor',
            'descricao' => 'O fornecedor está preparando e embalando o seu lote.',
            'icone' => 'inventory_2',
            'cor' => 'bg-amber-500',
            'botao' => 'Marcar: Em separação',
        ],
        'saiu_para_entrega' => [
            'titulo' => 'Saiu para Entrega / Retirada',
            'descricao' => 'O pedido está a caminho ou disponível para retirada.',
            'icone' => 'local_shipping',
            'cor' => 'bg-sky-600',
            'botao' => 'Marcar: Saiu para entrega',
        ],
        'entregue' => [
            'titulo' => 'Entregue',
            'descricao' => 'A encomenda foi entregue ao comprador.',
            'icone' => 'verified',
            'cor' => 'bg-emerald-700',
            'botao' => 'Marcar: Entregue',
        ],
    ];

    $ordemEtapas = array_keys($etapas);
    $indiceAtual = array_search($statusAtual, $ordemEtapas);
    $indiceAtual = $indiceAtual === false ? 0 : $indiceAtual;
@endphp

{{-- Breadcrumb --}}
<nav class="flex flex-wrap items-center gap-2 text-sm mb-7" aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="text-gray-500 hover:text-[#236350]">
        Início
    </a>
    <span class="material-symbols-outlined text-gray-400 text-lg">
        chevron_right
    </span>
    <a
        href="{{ $is_donodaoferta ? route('negociacoes.vendas') : route('negociacoes.compras') }}"
        class="text-gray-500 hover:text-[#236350]"
    >
        {{ $is_donodaoferta ? 'Minhas Vendas' : 'Minhas Compras' }}
    </a>
    <span class="material-symbols-outlined text-gray-400 text-lg">
        chevron_right
    </span>
    <span class="font-semibold text-gray-800">
        {{ $is_donodaoferta ? 'Atualizar Entrega' : 'Acompanhar Encomenda' }}
    </span>
</nav>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Detalhes da Encomenda</h1>
        <p class="text-gray-500 mt-1">Acompanhe o status de separação, envio e entrega do seu pedido (#{{ $negociacao->id }}).</p>
    </div>

    {{-- Alerta de feedback --}}
    <div id="alerta-status-entrega" class="hidden mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 items-center gap-2">
        <span id="icone-alerta-entrega" class="material-symbols-outlined text-lg">check_circle</span>
        <span id="texto-alerta-entrega">Status da entrega atualizado.</span>
    </div>

    {{-- Card do Produto / Encomenda --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 justify-between border-b border-gray-100 pb-6 mb-6">
            <div>
                <span class="inline-block px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-xs font-bold tracking-wide uppercase mb-2">
                    Compra Aprovada
                </span>
                <h2 class="text-xl font-bold text-gray-900">{{ $produto->nome ?? 'Produto' }}</h2>
                <p class="text-sm text-gray-500 mt-1">Fornecedor: <strong class="text-gray-700">{{ $oferta->fornecedor->nome ?? 'Copav' }}</strong></p>
            </div>
            <div class="text-left sm:text-right">
                <span class="text-xs text-gray-400 block">Negociação</span>
                <span class="text-lg font-bold text-gray-800">#{{ $negociacao->id }}</span>
            </div>
        </div>

        {{-- Controles do Dono da Oferta --}}
        @if($is_donodaoferta)
            <div class="mb-6 rounded-xl border border-sky-200 bg-sky-50/60 p-4">
                <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
                    <div>
                        <h3 class="text-sm font-bold text-sky-950 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-sky-700">local_shipping</span>
                            Atualizar Status da Entrega (Fornecedor)
                        </h3>
                        <p class="text-xs text-sky-800/80 mt-0.5">
                            Selecione a etapa atual para o comprador acompanhar o andamento.
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach($etapas as $chave => $etapa)
                        <button
                            type="button"
                            data-status-btn="{{ $chave }}"
                            onclick="atualizarStatusEntrega('{{ $chave }}')"
                            class="px-3.5 py-2 rounded-lg text-xs font-semibold border transition-all cursor-pointer flex items-center gap-1.5"
                        >
                            <span class="material-symbols-outlined text-sm">{{ $etapa['icone'] }}</span>
                            {{ $etapa['botao'] }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Timeline Interativa --}}
        <div class="space-y-6" id="timeline-entrega">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4">Status do Pedido</h3>

            @foreach($etapas as $chave => $etapa)
                @php
                    $passoIndice = $loop->index;
                    $concluidoOuAtual = $passoIndice <= $indiceAtual;
                @endphp
                <div
                    id="etapa-{{ $chave }}"
                    data-passo="{{ $passoIndice }}"
                    class="flex items-start gap-4 transition-opacity duration-200 {{ $concluidoOuAtual ? 'opacity-100' : 'opacity-50' }}"
                >
                    <div
                        data-icone-etapa
                        class="w-8 h-8 rounded-full text-white flex items-center justify-center shrink-0 transition-colors {{ $concluidoOuAtual ? $etapa['cor'] : 'bg-gray-200 text-gray-600' }}"
                    >
                        <span class="material-symbols-outlined text-sm">{{ $etapa['icone'] }}</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">{{ $etapa['titulo'] }}</h4>
                        <p class="text-xs text-gray-500">{{ $etapa['descricao'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Botões de Ação --}}
    <div class="flex gap-3">
        <a href="{{ route('chat.show', $negociacao) }}" class="flex-1 flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            <span class="material-symbols-outlined text-lg">chat</span>
            {{ $is_donodaoferta ? 'Falar com o Comprador' : 'Falar com o Vendedor' }}
        </a>
        <a
            href="{{ $is_donodaoferta ? route('negociacoes.vendas') : route('negociacoes.compras') }}"
            class="btn-copav flex-1 flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-white transition-colors"
        >
            <span class="material-symbols-outlined text-lg">{{ $is_donodaoferta ? 'storefront' : 'shopping_bag' }}</span>
            {{ $is_donodaoferta ? 'Minhas Vendas' : 'Minhas Compras' }}
        </a>
    </div>
</div>

@if($is_donodaoferta)
<script>
    const etapasConfig = @json($etapas);
    const ordemEtapas = Object.keys(etapasConfig);
    let statusAtualFront = @json($statusAtual);

    function exibirAlertaEntrega(mensagem, tipo = 'sucesso') {
        const alerta = document.getElementById('alerta-status-entrega');
        const icone = document.getElementById('icone-alerta-entrega');
        const texto = document.getElementById('texto-alerta-entrega');

        if (!alerta || !texto) return;

        alerta.classList.remove(
            'hidden',
            'border-emerald-200', 'bg-emerald-50', 'text-emerald-800',
            'border-red-200', 'bg-red-50', 'text-red-800'
        );
        alerta.classList.add('flex');

        if (tipo === 'erro') {
            alerta.classList.add('border-red-200', 'bg-red-50', 'text-red-800');
            if (icone) icone.textContent = 'error';
        } else {
            alerta.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-800');
            if (icone) icone.textContent = 'check_circle';
        }

        texto.textContent = mensagem;
    }

    function renderizarTimeline(novoStatus) {
        const indiceAtivo = ordemEtapas.indexOf(novoStatus);
        const alvo = indiceAtivo === -1 ? 0 : indiceAtivo;

        ordemEtapas.forEach((chave, idx) => {
            const linha = document.getElementById(`etapa-${chave}`);
            const circulo = linha?.querySelector('[data-icone-etapa]');
            const corAtiva = etapasConfig[chave].cor;

            if (!linha || !circulo) return;

            if (idx <= alvo) {
                linha.classList.remove('opacity-50');
                linha.classList.add('opacity-100');

                circulo.className = `w-8 h-8 rounded-full text-white flex items-center justify-center shrink-0 transition-colors ${corAtiva}`;
            } else {
                linha.classList.remove('opacity-100');
                linha.classList.add('opacity-50');

                circulo.className = 'w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center shrink-0 transition-colors';
            }
        });

        document.querySelectorAll('[data-status-btn]').forEach((btn) => {
            const btnStatus = btn.getAttribute('data-status-btn');
            if (btnStatus === novoStatus) {
                btn.className = 'px-3.5 py-2 rounded-lg text-xs font-semibold border border-[#236350] bg-[#236350] text-white transition-all cursor-pointer flex items-center gap-1.5 shadow-sm';
            } else {
                btn.className = 'px-3.5 py-2 rounded-lg text-xs font-semibold border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition-all cursor-pointer flex items-center gap-1.5';
            }
        });
    }

    async function atualizarStatusEntrega(novoStatus) {
        const statusAnterior = statusAtualFront;
        statusAtualFront = novoStatus;
        renderizarTimeline(novoStatus);

        @if(\Illuminate\Support\Facades\Route::has('compras.encomenda.status'))
        try {
            const response = await fetch("{{ route('compras.encomenda.status', $negociacao) }}", {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status_entrega: novoStatus })
            });

            if (!response.ok) {
                const erroData = await response.json().catch(() => ({}));
                throw new Error(erroData.message || 'Não foi possível atualizar o status da entrega.');
            }

            exibirAlertaEntrega(`Status atualizado para: ${etapasConfig[novoStatus].titulo}.`, 'sucesso');
        } catch (e) {
            console.error('Erro ao salvar status de entrega:', e);
            statusAtualFront = statusAnterior;
            renderizarTimeline(statusAnterior);
            exibirAlertaEntrega(e.message || 'Erro ao salvar status de entrega. Tente novamente.', 'erro');
        }
        @else
        exibirAlertaEntrega(`Status selecionado: ${etapasConfig[novoStatus].titulo} (adicione a rota compras.encomenda.status para salvar no banco).`, 'sucesso');
        @endif
    }

    renderizarTimeline(statusAtualFront);
</script>
@endif

@endsection