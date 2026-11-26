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
    <a href="{{ route('home') }}" class="inline-flex items-center text-gray-500 dark:text-gray-400 hover:text-[#236350] dark:hover:text-emerald-400 transition-colors">
        Início
    </a>
    <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-lg leading-none">
        chevron_right
    </span>
    <a
        href="{{ $is_donodaoferta ? route('negociacoes.vendas') : route('negociacoes.compras') }}"
        class="inline-flex items-center text-gray-500 dark:text-gray-400 hover:text-[#236350] dark:hover:text-emerald-400 transition-colors"
    >
        {{ $is_donodaoferta ? 'Minhas Vendas' : 'Minhas Compras' }}
    </a>
    <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-lg leading-none">
        chevron_right
    </span>
    <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
        {{ $is_donodaoferta ? 'Atualizar Entrega' : 'Acompanhar Encomenda' }}
    </span>
</nav>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Detalhes da Encomenda</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">
            Acompanhe o status de separação, envio e entrega do seu pedido (#{{ $negociacao->id }}).
        </p>
    </div>

    {{-- Alerta de feedback --}}
    <div id="alerta-status-entrega" class="hidden mb-5 rounded-lg border border-emerald-200 dark:border-emerald-800/60 bg-emerald-50 dark:bg-emerald-950/50 px-4 py-3 text-sm text-emerald-800 dark:text-emerald-200 items-center gap-2">
        <span id="icone-alerta-entrega" class="material-symbols-outlined text-lg leading-none">check_circle</span>
        <span id="texto-alerta-entrega">Status da entrega atualizado.</span>
    </div>

    {{-- Card do Produto / Encomenda --}}
    <div class="bg-white dark:bg-[#111827] rounded-2xl border border-gray-200 dark:border-[#273449] shadow-sm p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 justify-between border-b border-gray-100 dark:border-[#273449] pb-6 mb-6">
            <div>
                <span class="inline-block px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-transparent dark:border-emerald-800/50 text-xs font-bold tracking-wide uppercase mb-2">
                    Compra Aprovada
                </span>
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $produto->nome ?? 'Produto' }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Fornecedor: <strong class="text-gray-700 dark:text-gray-200">{{ $oferta->fornecedor->nome ?? 'Copav' }}</strong>
                </p>
            </div>
            <div class="text-left sm:text-right">
                <span class="text-xs text-gray-400 dark:text-gray-500 block">Negociação</span>
                <span class="text-lg font-bold text-gray-800 dark:text-gray-100">#{{ $negociacao->id }}</span>
            </div>
        </div>

        {{-- Controles do Dono da Oferta --}}
        @if($is_donodaoferta)
            <div class="mb-6 rounded-xl border border-sky-200 dark:border-sky-800/60 bg-sky-50/60 dark:bg-sky-950/30 p-4">
                <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
                    <div>
                        <h3 class="text-sm font-bold text-sky-950 dark:text-sky-200 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-sky-700 dark:text-sky-400 leading-none">local_shipping</span>
                            <span>Atualizar Status da Entrega (Fornecedor)</span>
                        </h3>
                        <p class="text-xs text-sky-800/80 dark:text-sky-300/80 mt-0.5">
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
                            class="px-3.5 py-2 rounded-lg text-xs font-semibold border transition-all cursor-pointer inline-flex items-center gap-1.5"
                        >
                            <span class="material-symbols-outlined text-sm leading-none">{{ $etapa['icone'] }}</span>
                            <span>{{ $etapa['botao'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Timeline Interativa --}}
        <div class="space-y-6" id="timeline-entrega">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider mb-4">Status do Pedido</h3>

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
                        class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 transition-colors {{ $concluidoOuAtual ? ($etapa['cor'] . ' text-white') : 'bg-gray-200 dark:bg-slate-800 text-gray-600 dark:text-gray-400' }}"
                    >
                        <span class="material-symbols-outlined text-sm leading-none">{{ $etapa['icone'] }}</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $etapa['titulo'] }}</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $etapa['descricao'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Botões de Ação --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <a
            href="{{ route('chat.show', $negociacao) }}"
            class="flex-1 flex items-center justify-center gap-2 rounded-xl border border-gray-300 dark:border-[#273449] bg-white dark:bg-[#111827] px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors"
        >
            <span class="material-symbols-outlined text-lg leading-none">chat</span>
            <span>{{ $is_donodaoferta ? 'Falar com o Comprador' : 'Falar com o Vendedor' }}</span>
        </a>
        <a
            href="{{ $is_donodaoferta ? route('negociacoes.vendas') : route('negociacoes.compras') }}"
            class="btn-copav flex-1 flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-white transition-colors"
        >
            <span class="material-symbols-outlined text-lg leading-none">{{ $is_donodaoferta ? 'storefront' : 'shopping_bag' }}</span>
            <span>{{ $is_donodaoferta ? 'Minhas Vendas' : 'Minhas Compras' }}</span>
        </a>
    </div>
</div>

@if($is_donodaoferta)
<script>
    const etapasConfig = @json($etapas);
    const ordemEtapas = Object.keys(etapasConfig);
    let statusAtualFront = @json($statusAtual);

    function temaEscuroAtivo() {
        const html = document.documentElement;
        return html.classList.contains('tema-escuro') || html.getAttribute('data-theme') === 'dark';
    }

    function exibirAlertaEntrega(mensagem, tipo = 'sucesso') {
        const alerta = document.getElementById('alerta-status-entrega');
        const icone = document.getElementById('icone-alerta-entrega');
        const texto = document.getElementById('texto-alerta-entrega');

        if (!alerta || !texto) return;

        alerta.classList.remove(
            'hidden',
            'border-emerald-200', 'dark:border-emerald-800/60', 'bg-emerald-50', 'dark:bg-emerald-950/50', 'text-emerald-800', 'dark:text-emerald-200',
            'border-red-200', 'dark:border-red-800/60', 'bg-red-50', 'dark:bg-red-950/50', 'text-red-800', 'dark:text-red-200'
        );
        alerta.classList.add('flex');

        if (tipo === 'erro') {
            alerta.classList.add('border-red-200', 'dark:border-red-800/60', 'bg-red-50', 'dark:bg-red-950/50', 'text-red-800', 'dark:text-red-200');
            if (icone) icone.textContent = 'error';
        } else {
            alerta.classList.add('border-emerald-200', 'dark:border-emerald-800/60', 'bg-emerald-50', 'dark:bg-emerald-950/50', 'text-emerald-800', 'dark:text-emerald-200');
            if (icone) icone.textContent = 'check_circle';
        }

        texto.textContent = mensagem;
    }

    function renderizarTimeline(novoStatus) {
        const indiceAtivo = ordemEtapas.indexOf(novoStatus);
        const alvo = indiceAtivo === -1 ? 0 : indiceAtivo;
        const escuro = temaEscuroAtivo();

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

                circulo.className = escuro
                    ? 'w-8 h-8 rounded-full bg-slate-800 text-gray-400 flex items-center justify-center shrink-0 transition-colors'
                    : 'w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center shrink-0 transition-colors';
            }
        });

        document.querySelectorAll('[data-status-btn]').forEach((btn) => {
            const btnStatus = btn.getAttribute('data-status-btn');
            if (btnStatus === novoStatus) {
                btn.className = 'px-3.5 py-2 rounded-lg text-xs font-semibold border border-[#236350] bg-[#236350] text-white transition-all cursor-pointer inline-flex items-center gap-1.5 shadow-sm';
            } else {
                btn.className = escuro
                    ? 'px-3.5 py-2 rounded-lg text-xs font-semibold border border-[#273449] bg-[#161f30] text-gray-200 hover:bg-slate-800 transition-all cursor-pointer inline-flex items-center gap-1.5'
                    : 'px-3.5 py-2 rounded-lg text-xs font-semibold border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition-all cursor-pointer inline-flex items-center gap-1.5';
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