
@extends('layouts.layout')

@section('chat_layout', true)

@section('conteudo')
@php
    $usuario = auth()->user();

    $participante = (int) $chat->cliente_id === (int) $usuario->id
        ? ($chat->fornecedor?->usuario?->nome ?? $chat->fornecedor?->nome ?? 'Fornecedor')
        : ($chat->cliente?->nome ?? 'Cliente');

    $tipoParticipante = (int) $chat->cliente_id === (int) $usuario->id
        ? 'Fornecedor'
        : 'Cliente';

    $inicialParticipante = mb_strtoupper(
        mb_substr(trim($participante), 0, 1)
    );

    $assuntoSolicitado = request('assunto_chat_id')
        ?? session('assunto_chat_id');

    $mostrarConversaMobile = request()->boolean('mostrar_conversa');

    $assuntoSelecionadoId = $assuntoSolicitado
        && $assuntos->contains('id', (int) $assuntoSolicitado)
            ? (int) $assuntoSolicitado
            : null;

    $mensagensIniciais = $chat->mensagens()
        ->with('usuario')
        ->when(
            $assuntoSelecionadoId,
            fn ($query) => $query->where('assunto_chat_id', $assuntoSelecionadoId),
            fn ($query) => $query->whereNull('assunto_chat_id')
        )
        ->latest()
        ->limit(50)
        ->get()
        ->reverse()
        ->values();

    $assuntoInicial = $assuntos->firstWhere('id', $assuntoSelecionadoId);

    $produtosIniciais = $assuntoInicial
        ? $assuntoInicial->negociacoes
            ->map(fn ($negociacao) => $negociacao->oferta?->produto)
            ->filter()
            ->unique('id')
            ->values()
        : collect();
@endphp

<div class="flex h-full min-h-0 w-full flex-col overflow-hidden">

    <div class="flex min-h-0 flex-1 overflow-hidden border-y border-gray-200 bg-white shadow-sm">

        {{-- PAINEL ESQUERDO --}}
        <aside
            id="painel-assuntos"
            
class="{{ $mostrarConversaMobile ? 'hidden' : 'flex' }} min-h-0 w-full shrink-0 flex-col overflow-hidden border-r border-gray-200 bg-white md:flex md:w-[340px] lg:w-[390px]"
        >

            {{-- CABEÇALHO DA LISTA --}}
            <div class="shrink-0 border-b border-gray-200 bg-white px-4 py-4">

                <a href="{{ route('chat') }}"
                   class="mb-4 inline-flex items-center gap-2 text-sm text-gray-500 transition hover:text-[#236350]">
                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                    Voltar às conversas
                </a>

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#E5EFEA] font-semibold text-[#1B4D3E]">
                            {{ $inicialParticipante }}
                        </div>

                        <div class="min-w-0">
                            <h1 class="truncate text-base font-bold text-gray-900">
                                {{ $participante }}
                            </h1>

                            <p class="mt-0.5 text-xs text-gray-500">
                                {{ $tipoParticipante }} · Conversa privada
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LISTA DE ASSUNTOS --}}
            <div class="min-h-0 flex-1 overflow-y-auto p-3">

                <p class="mb-3 px-2 text-xs font-semibold uppercase tracking-wider text-gray-400">
                    Histórico
                </p>

                {{-- MENSAGENS GERAIS --}}
                <button
                    type="button"
                    data-selecionar-assunto=""
                    data-titulo="Mensagens gerais"
                    data-produto=""
                    class="item-assunto mb-2 flex w-full items-center gap-3 rounded-xl border p-3 text-left transition
                    {{ !$assuntoSelecionadoId
                        ? 'border-[#236350]/30 bg-[#EAF2ED]'
                        : 'border-transparent bg-white hover:border-gray-200 hover:bg-gray-50' }}"
                >
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl
                        {{ !$assuntoSelecionadoId
                            ? 'bg-[#236350] text-white'
                            : 'bg-gray-100 text-gray-500' }}">
                        <span class="material-symbols-outlined text-xl">chat</span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-800">
                            Mensagens gerais
                        </p>
                        <p class="mt-1 truncate text-xs text-gray-500">
                            Conversa sem vínculo com negociação
                        </p>
                    </div>

                    <span class="material-symbols-outlined text-lg text-gray-400">
                        chevron_right
                    </span>
                </button>

                {{-- ASSUNTOS DAS NEGOCIAÇÕES --}}
                @forelse ($assuntos as $assunto)
                    @php
                        $selecionado = $assuntoSelecionadoId === (int) $assunto->id;

                        $produtos = $assunto->negociacoes
                            ->map(fn ($negociacao) => $negociacao->oferta?->produto)
                            ->filter()
                            ->unique('id')
                            ->values();

                        $quantidadeNegociacoes = $assunto->negociacoes->count();
                        $produtoPrincipal = $produtos->first();
                        $quantidadeAdicional = max(0, $produtos->count() - 1);
                    @endphp

                    <button
                        type="button"
                        data-selecionar-assunto="{{ $assunto->id }}"
                        data-titulo="{{ $assunto->titulo }}"
                        data-produto="{{ $produtoPrincipal?->nome ?? '' }}"
                        class="item-assunto mb-2 flex w-full items-center gap-3 rounded-xl border p-3 text-left transition
                        {{ $selecionado
                            ? 'border-[#236350]/30 bg-[#EAF2ED]'
                            : 'border-transparent bg-white hover:border-gray-200 hover:bg-gray-50' }}"
                    >

                        {{-- FOTO DO PRODUTO --}}
                        @if ($produtoPrincipal?->imagem)
                            <img
                                src="{{ asset('storage/' . ltrim($produtoPrincipal->imagem, '/')) }}"
                                alt="{{ $produtoPrincipal->nome }}"
                                class="h-12 w-12 shrink-0 rounded-xl border border-gray-200 bg-white object-cover"
                                loading="lazy"
                                onerror="this.onerror=null;this.src='{{ asset('images/produto-sem-imagem.png') }}';"
                            >
                        @else
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-400">
                                <span class="material-symbols-outlined text-2xl">inventory_2</span>
                            </div>
                        @endif

                        {{-- INFORMAÇÕES DO ASSUNTO --}}
                        <div class="min-w-0 flex-1">

                            <p class="truncate text-sm font-semibold text-gray-800">
                                {{ $produtoPrincipal?->nome ?? $assunto->titulo }}
                            </p>

                            <p class="mt-1 truncate text-xs text-gray-500">
                                {{ $assunto->titulo }}
                            </p>

                            <p class="mt-1 text-[11px] font-medium text-[#236350]">
                                {{ $assunto->negociacoes->count() }}
                                {{ $assunto->negociacoes->count() === 1 ? 'negociação' : 'negociações' }}
                                ·
                                {{ $produtos->count() }}
                                {{ $produtos->count() === 1 ? 'produto' : 'produtos' }}
                            </p>

                        </div>

                        <span class="material-symbols-outlined shrink-0 text-lg text-gray-400">
                            chevron_right
                        </span>
                    </button>

                @empty
                    <div class="px-4 py-10 text-center">
                        <span class="material-symbols-outlined text-3xl text-gray-300">
                            forum
                        </span>
                        <p class="mt-2 text-sm text-gray-500">
                            Nenhum assunto de negociação disponível.
                        </p>
                    </div>
                @endforelse

            </div>
        </aside>

        {{-- PAINEL DIREITO --}}
        <main
            id="painel-conversa"
            class="{{ $mostrarConversaMobile ? 'flex' : 'hidden' }} min-h-0 min-w-0 flex-1 flex-col overflow-hidden md:flex"
        >

            {{-- CABEÇALHO DO CHAT --}}
            <header class="flex min-h-[72px] shrink-0 items-center gap-3 border-b border-gray-200 bg-white px-4 py-3 sm:px-6">   
                <button
                    id="voltar-lista-assuntos"
                    type="button"
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-100 hover:text-[#236350] md:hidden"
                    title="Voltar"
                    aria-label="Voltar"
                >
                    <span class="material-symbols-outlined text-[23px]">arrow_back</span>
                </button>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#E5EFEA] font-semibold text-[#1B4D3E]">
                    {{ $inicialParticipante }}
                </div>

                <div class="min-w-0 flex-1">
                    <h2 id="titulo-assunto" class="truncate text-sm font-semibold text-gray-900">
                        {{ $assuntoInicial?->titulo ?? 'Mensagens gerais' }}
                    </h2>

                    <p id="descricao-assunto" class="mt-0.5 truncate text-xs text-gray-500">
                        @if ($produtosIniciais->isNotEmpty())
                            {{ $produtosIniciais->first()->nome }} ·
                        @endif
                        Conversa com {{ $participante }}
                    </p>
                </div>

                <div class="hidden items-center gap-2 rounded-full bg-[#EAF2ED] px-3 py-1.5 text-xs font-medium text-[#236350] sm:flex">
                    <span class="h-2 w-2 rounded-full bg-green-600"></span>
                    Chat privado
                </div>
            </header>

            {{-- HISTÓRICO DE MENSAGENS --}}
            <div
                id="lista-mensagens"
                data-chat-id="{{ $chat->id }}"
                data-assunto-id="{{ $assuntoSelecionadoId ?? '' }}"
                class="min-h-0 flex-1 space-y-4 overflow-y-auto overscroll-contain bg-[#F5F5F0] px-4 py-5 sm:px-6"
                style="visibility: hidden;"
            >

                @forelse ($mensagensIniciais as $mensagem)
                    @php
                        $minhaMensagem = (int) $mensagem->usuario_id === (int) $usuario->id;
                    @endphp

                    <div
                        class="flex {{ $minhaMensagem ? 'justify-end' : 'justify-start' }}"
                        data-message-id="{{ $mensagem->id }}"
                    >
                        <div class="max-w-[88%] rounded-2xl px-4 py-3 shadow-sm sm:max-w-[75%]
                            {{ $minhaMensagem
                                ? 'rounded-br-md bg-[#D9F2DF] text-gray-800'
                                : 'rounded-bl-sm bg-white text-gray-800' }}">

                            <p class="whitespace-pre-wrap break-words text-sm leading-relaxed">{{ trim($mensagem->conteudo) }}</p>

                            <p class="mt-2 text-right text-[11px]
                                {{ $minhaMensagem ? 'text-gray-500' : 'text-gray-400' }}">
                                {{ $mensagem->created_at->format('d/m/Y H:i') }}
                            </p>

                        </div>
                    </div>
                @empty
                    <div id="mensagens-vazias" class="flex h-full min-h-[220px] flex-col items-center justify-center text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-[#E5EFEA] text-[#236350]">
                            <span class="material-symbols-outlined text-3xl">chat_bubble_outline</span>
                        </div>

                        <h3 class="mt-4 font-semibold text-gray-800">
                            Nenhuma mensagem por aqui
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Envie uma mensagem para iniciar esta conversa.
                        </p>
                    </div>
                @endforelse

            </div>

            {{-- INDICADOR DE CARREGAMENTO --}}
            <div id="indicador-carregamento"
                 class="pointer-events-none absolute hidden items-center justify-center">
            </div>

            {{-- FORMULÁRIO DE ENVIO --}}
            <form id="form-mensagem"
                class="shrink-0 border-t border-gray-200 bg-white px-3 py-3 sm:px-5">

                @csrf

                <div class="flex items-end gap-2 sm:gap-3">

                    <textarea
                        id="conteudo-mensagem"
                        name="conteudo"
                        rows="1"
                        maxlength="5000"
                        placeholder="Digite sua mensagem..."
                        class="max-h-32 min-h-[44px] min-w-0 flex-1 resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-[#236350] focus:ring-1 focus:ring-[#236350]"
                        required
                    ></textarea>

                    {{-- BOTÃO E ATALHO --}}
                    <div class="flex shrink-0 flex-col items-center gap-1">

                        <p class="hidden text-[11px] text-gray-400 sm:block">
                            Ctrl + Enter
                        </p>

                        <button
                            type="submit"
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#236350] text-white transition hover:bg-[#1B4D3E] disabled:cursor-not-allowed disabled:opacity-40"
                            aria-label="Enviar mensagem"
                        >
                            <span id="texto-botao" class="hidden">Enviar</span>
                            <span class="material-symbols-outlined text-xl">send</span>
                        </button>

                    </div>

                </div>

            </form> 

        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.getElementById('form-mensagem');
    const campo = document.getElementById('conteudo-mensagem');
    const lista = document.getElementById('lista-mensagens');

    if (!formulario || !campo || !lista) return;

        // Alternância entre a conversa e o menu de assuntos no celular.
    const painelAssuntos = document.getElementById('painel-assuntos');
    const painelConversa = document.getElementById('painel-conversa');
    const botaoVoltar = document.getElementById('voltar-lista-assuntos');

    botaoVoltar?.addEventListener('click', () => {
        painelConversa?.classList.add('hidden');
        painelConversa?.classList.remove('flex');

        painelAssuntos?.classList.remove('hidden');
        painelAssuntos?.classList.add('flex');
    });

    // Seleciona um assunto e carrega sua conversa.
    document.querySelectorAll('[data-selecionar-assunto]').forEach(item => {
        item.addEventListener('click', () => {
            const assuntoId = item.dataset.selecionarAssunto;

            const url = new URL(window.location.href);

            if (assuntoId) {
                url.searchParams.set('assunto_chat_id', assuntoId);
            } else {
                url.searchParams.delete('assunto_chat_id');
            }

            url.searchParams.set('mostrar_conversa', '1');

            window.location.assign(url.toString());
        });
    });

    const botao = formulario.querySelector('button[type="submit"]');
    const textoBotao = document.getElementById('texto-botao');
    const estado = document.getElementById('estado-chat');

    function atualizarEstado(mensagem) {
        if (estado) {
            estado.textContent = mensagem;
        }
    }

    const chatId = lista.dataset.chatId;
    const usuarioId = {{ (int) auth()->id() }};
    const participante = @json($participante);

    const urlEnvio = @json(route('chat.mensagens.store', $chat));
    const urlMensagens = @json(route('chat.mensagens', $chat));
    const token = formulario.querySelector('input[name="_token"]').value;

    let assuntoAtual = lista.dataset.assuntoId || '';

    function rolarParaBaixo(revelar = false) {
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                lista.scrollTop = lista.scrollHeight;

                if (revelar) {
                    lista.style.visibility = 'visible';
                }
            });
        });
    }


    function adicionarMensagem(mensagem) {
        if (!mensagem || !mensagem.id) return;
        if (Number(mensagem.chat_id) !== Number(chatId)) return;

        const assuntoMensagem = mensagem.assunto_chat_id
            ? String(mensagem.assunto_chat_id)
            : '';

        if (assuntoMensagem !== String(assuntoAtual)) return;

        if (lista.querySelector(`[data-message-id="${mensagem.id}"]`)) return;

        document.getElementById('mensagens-vazias')?.remove();

        const minhaMensagem = Number(mensagem.usuario_id) === usuarioId;

        const linha = document.createElement('div');
        linha.dataset.messageId = mensagem.id;
        linha.className = `flex ${minhaMensagem ? 'justify-end' : 'justify-start'}`;

        const balao = document.createElement('div');

        balao.className = 'max-w-[88%] rounded-2xl px-4 py-3 shadow-sm sm:max-w-[75%] ' +
        (minhaMensagem
            ? 'rounded-br-md bg-[#D9F2DF] text-gray-800'
            : 'rounded-bl-sm bg-white text-gray-800');

        const texto = document.createElement('p');
        texto.className = 'whitespace-pre-wrap break-words text-sm leading-relaxed';
        texto.textContent = (mensagem.conteudo || '').trim();

        const horario = document.createElement('p');
        horario.className = 'mt-2 text-right text-[11px] ' +
            (minhaMensagem ? 'text-gray-500' : 'text-gray-400');

        const data = mensagem.created_at
            ? new Date(mensagem.created_at)
            : new Date();

        horario.textContent = Number.isNaN(data.getTime())
            ? mensagem.created_at
            : data.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

        balao.append(texto, horario);
        linha.appendChild(balao);
        lista.appendChild(linha);

        rolarParaBaixo();
    }

    // Recebe mensagens em tempo real.
    if (window.Echo) {
        window.Echo.private(`chat.${chatId}`)
            .listen('.mensagem.enviada', mensagem => {
                adicionarMensagem(mensagem);
            });
    } else {
        console.error('Laravel Echo não está disponível nesta página.');
        atualizarEstado('Conexão em tempo real indisponível.');
    }

    // Envia mensagens.
    formulario.addEventListener('submit', async evento => {
        evento.preventDefault();

        const conteudo = campo.value.trim();

        if (!conteudo) {
            campo.focus();
            return;
        }

        botao.disabled = true;
        textoBotao.textContent = 'Enviando...';
        atualizarEstado('Enviando sua mensagem...');

        try {
            const resposta = await fetch(urlEnvio, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    conteudo,
                    assunto_chat_id: assuntoAtual || null
                })
            });

            const dados = await resposta.json();

            if (!resposta.ok) {
                throw new Error(dados.message || 'Não foi possível enviar a mensagem.');
            }

            adicionarMensagem(dados.mensagem);

            campo.value = '';
            atualizarEstado('Mensagem enviada.');
            campo.focus();

        } catch (erro) {
            console.error('Erro ao enviar mensagem:', erro);
            atualizarEstado(erro.message || 'Erro ao enviar mensagem.');
        } finally {
            botao.disabled = false;
            textoBotao.textContent = 'Enviar';
            rolarParaBaixo();
        }
    });

    // Ctrl + Enter envia; Enter sozinho cria uma nova linha.
    campo.addEventListener('keydown', evento => {
        if (evento.key === 'Enter' && (evento.ctrlKey || evento.metaKey)) {
            evento.preventDefault();
            formulario.requestSubmit();
        }
    });

    rolarParaBaixo(true);
});
</script>
@endpush
