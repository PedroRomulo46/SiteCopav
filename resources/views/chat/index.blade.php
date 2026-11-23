@extends('layouts.layout')

@section('chat_layout', true)

@section('conteudo')
<div class="flex h-full min-h-0 w-full flex-col overflow-hidden">
    <div
        x-data="chatIndex()"
        x-init="inicializar()"
        class="flex min-h-0 flex-1 overflow-hidden rounded-none border-y border-gray-200 bg-white shadow-sm"
    >
        {{-- PAINEL ESQUERDO --}}
        <aside
            class="flex min-h-0 w-full flex-col overflow-hidden border-r border-gray-200 bg-white md:w-[340px] md:shrink-0 lg:w-[390px]"
            :class="conversaSelecionada ? 'hidden md:flex' : 'flex'"
        >
            <div class="shrink-0 border-b border-gray-100 px-5 py-4">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        onclick="voltarParaOrigemChat()"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-100 hover:text-[#236350]"
                        title="Voltar"
                    >
                        <span class="material-symbols-outlined text-[23px]">arrow_back</span>
                    </button>

                    <div>
                        <h2 class="font-semibold text-gray-900">Suas conversas</h2>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ $chats->count() }}
                            {{ $chats->count() === 1 ? 'conversa' : 'conversas' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain">
                
            @forelse ($chats as $chat)
                @php
                    $ehCliente = (int) $chat->cliente_id === (int) auth()->id();

                    $nomeParticipante = $ehCliente
                        ? (
                            $chat->fornecedor?->usuario?->nome
                            ?? $chat->fornecedor?->nome
                            ?? 'Fornecedor'
                        )
                        : ($chat->cliente?->nome ?? 'Cliente');

                    $inicial = mb_strtoupper(mb_substr($nomeParticipante, 0, 1));
                @endphp

                {{-- IDENTIFICAÇÃO DO FORNECEDOR/CLIENTE --}}
                <div class="border-b border-gray-100 bg-gray-50 px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#E5EFEA] font-semibold text-[#1B4D3E]">
                            {{ $inicial }}
                        </div>

                        <div class="min-w-0">
                            <h3 class="truncate text-sm font-semibold text-gray-900">
                                {{ $nomeParticipante }}
                            </h3>

                            <p class="text-xs text-gray-500">
                                {{ $chat->assuntos->count() }}
                                {{ $chat->assuntos->count() === 1 ? 'assunto' : 'assuntos' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- MENSAGENS GERAIS --}}
                <button
                    type="button"
                    @click="abrirConversa({{ $chat->id }}, $event.currentTarget)"
                    data-chat-id="{{ $chat->id }}"
                    data-assunto-chat-id=""
                    data-contexto-chave="{{ $chat->id }}:geral"
                    data-contexto-titulo="Mensagens gerais"
                    data-participante="{{ $nomeParticipante }}"
                    class="flex w-full items-center gap-3 border-b border-gray-100 px-4 py-4 text-left transition hover:bg-gray-50"
                >
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#E5EFEA] text-[#236350]">
                        <span class="material-symbols-outlined">forum</span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h4 class="truncate text-sm font-medium text-gray-800">
                            Mensagens gerais
                        </h4>

                        <p class="mt-1 truncate text-xs text-gray-500">
                            Conversa geral com {{ $nomeParticipante }}
                        </p>
                    </div>

                    <span
                        x-show="naoLidas['{{ $chat->id }}:geral'] > 0"
                        x-cloak
                        class="flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full bg-[#236350] px-1 text-[10px] font-bold text-white"
                        x-text="naoLidas['{{ $chat->id }}:geral']"
                    ></span>
                </button>

                {{-- ASSUNTOS --}}
                @foreach ($chat->assuntos as $assunto)
                    @php
                        $primeiroProduto = $assunto->negociacoes
                            ->map(fn ($negociacao) => $negociacao->oferta?->produto)
                            ->filter()
                            ->unique('id')
                            ->first();
                    @endphp

                    <button
                        type="button"
                        @click="abrirConversa({{ $chat->id }}, $event.currentTarget)"
                        data-chat-id="{{ $chat->id }}"
                        data-assunto-chat-id="{{ $assunto->id }}"
                        data-contexto-chave="{{ $chat->id }}:{{ $assunto->id }}"
                        data-contexto-titulo="{{ $assunto->titulo }}"
                        data-participante="{{ $nomeParticipante }}"
                        class="flex w-full items-center gap-3 border-b border-gray-100 px-4 py-4 text-left transition hover:bg-gray-50"
                    >
                        @if ($primeiroProduto?->imagem)
                            <img
                                src="{{ asset('storage/' . ltrim($primeiroProduto->imagem, '/')) }}"
                                alt="{{ $primeiroProduto->nome }}"
                                class="h-10 w-10 shrink-0 rounded-xl object-cover"
                            >
                        @else
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#E5EFEA] text-[#236350]">
                                <span class="material-symbols-outlined">inventory_2</span>
                            </div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <h4 class="truncate text-sm font-medium text-gray-800">
                                {{ $primeiroProduto?->nome ?? $assunto->titulo }}
                            </h4>

                            <p class="mt-1 truncate text-xs text-gray-500">
                                {{ $assunto->titulo }}
                            </p>
                        </div>

                        <span
                            x-show="naoLidas['{{ $chat->id }}:{{ $assunto->id }}'] > 0"
                            x-cloak
                            class="flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full bg-[#236350] px-1 text-[10px] font-bold text-white"
                            x-text="naoLidas['{{ $chat->id }}:{{ $assunto->id }}']"
                        ></span>
                    </button>
                @endforeach

            @empty
                <div class="px-6 py-16 text-center">
                    <span class="material-symbols-outlined text-4xl text-gray-300">forum</span>

                    <h3 class="mt-3 font-semibold text-gray-800">
                        Nenhuma conversa por enquanto
                    </h3>

                    <p class="mt-2 text-sm text-gray-500">
                        Quando uma proposta for aceita, sua conversa ficará disponível aqui.
                    </p>

                    <a
                        href="{{ route('negociacoes.index') }}"
                        class="mt-5 inline-flex rounded-lg bg-[#236350] px-5 py-2.5 text-sm font-medium text-white transition hover:bg-[#1B4D3E]"
                    >
                        Ver negociações
                    </a>
                </div>
            @endforelse

            </div>
        </aside>

        {{-- PAINEL DIREITO --}}
        <section
            class="min-h-0 min-w-0 flex-1 flex-col overflow-hidden bg-[#F5F5F0]"
            :class="conversaSelecionada ? 'flex' : 'hidden md:flex'"
        >
            {{-- Estado inicial --}}
            <div
                x-show="!conversaSelecionada"
                class="flex flex-1 flex-col items-center justify-center px-6 text-center"
            >
                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-[#E5EFEA] text-[#236350]">
                    <span class="material-symbols-outlined text-5xl">forum</span>
                </div>

                <h2 class="mt-5 text-xl font-semibold text-gray-800">Suas mensagens</h2>

                <p class="mt-2 max-w-sm text-sm leading-relaxed text-gray-500">
                    Selecione uma conversa ao lado para visualizar as mensagens e continuar sua negociação.
                </p>

                <div class="mt-6 flex items-center gap-2 text-xs text-gray-400">
                    <span class="material-symbols-outlined text-base">lock</span>
                    Suas conversas são privadas.
                </div>
            </div>

            {{-- Conversa selecionada --}}
            <div
                x-show="conversaSelecionada"
                class="flex min-h-0 flex-1 flex-col"
                style="display: none;"
            >
                {{-- Cabeçalho --}}
                <header class="flex min-h-[72px] shrink-0 items-center gap-3 border-b border-gray-200 bg-white px-4 py-3 sm:px-6">
                    <button
                        type="button"
                        @click="voltarLista()"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-100 hover:text-[#236350] md:hidden"
                        title="Voltar às conversas"
                        aria-label="Voltar às conversas"
                    >
                        <span class="material-symbols-outlined">arrow_back</span>
                    </button>

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#E5EFEA] font-semibold text-[#1B4D3E]">
                        <span x-text="conversaSelecionada?.participante?.charAt(0).toUpperCase()"></span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h2
                            class="truncate font-semibold text-gray-900"
                            x-text="conversaSelecionada?.participante"
                        ></h2>

                        <p class="truncate text-xs text-gray-500">
                            Conversa privada
                        </p>
                    </div>

                    <a
                        :href="conversaSelecionada
                            ? '/chat/' + conversaSelecionada.chat_id
                                + (conversaSelecionada.assunto_chat_id
                                    ? '?assunto_chat_id=' + conversaSelecionada.assunto_chat_id
                                    : '')
                            : '#'"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-100 hover:text-[#236350]"
                        title="Abrir conversa em página completa"
                    >
                        <span class="material-symbols-outlined">open_in_new</span>
                    </a>
                </header>

                {{-- Histórico --}}
                <div
                    x-ref="listaMensagens"
                    class="min-h-0 flex-1 space-y-4 overflow-y-auto overscroll-contain px-4 py-5 sm:px-6"
                >
                    <div
                        x-show="carregandoMensagens"
                        class="py-8 text-center text-sm text-gray-500"
                    >
                        Carregando mensagens...
                    </div>

                    <div
                        x-show="erroMensagens"
                        class="mx-auto max-w-md rounded-lg bg-red-50 p-3 text-center text-sm text-red-600"
                        x-text="erroMensagens"
                    ></div>

                    <template x-for="mensagem in mensagens" :key="mensagem.id">
                        <div
                            class="flex"
                            :class="Number(mensagem.usuario_id) === usuarioId ? 'justify-end' : 'justify-start'"
                        >
                            <div
                                class="max-w-[88%] rounded-2xl px-4 py-3 shadow-sm sm:max-w-[75%]"
                                :class="Number(mensagem.usuario_id) === usuarioId
                                    ? 'rounded-br-sm bg-[#D9F2DF] text-gray-800'
                                    : 'rounded-bl-sm bg-white text-gray-800'"
                            >
                                <p
                                    class="whitespace-pre-wrap wrap-break-words text-sm leading-relaxed"
                                    x-text="mensagem.conteudo"
                                ></p>

                                <p
                                    class="mt-2 text-right text-[11px] text-gray-500"
                                    x-text="formatarDataHora(mensagem.created_at)"
                                ></p>
                            </div>
                        </div>
                    </template>

                    <div
                        x-show="!carregandoMensagens && mensagens.length === 0 && !erroMensagens"
                        class="py-12 text-center"
                    >
                        <p class="text-sm text-gray-500">Nenhuma mensagem ainda.</p>
                        <p class="mt-1 text-xs text-gray-400">
                            Envie uma mensagem para iniciar a conversa.
                        </p>
                    </div>
                </div>

                {{-- Erro de envio --}}
                <div
                    x-show="erroEnvio"
                    class="border-t border-red-100 bg-red-50 px-4 py-2 text-sm text-red-600"
                    x-text="erroEnvio"
                ></div>

                {{-- Formulário --}}
                <form
                    @submit.prevent="enviarMensagem()"
                    class="flex shrink-0 items-end gap-2 border-t border-gray-200 bg-white px-3 py-3 sm:gap-3 sm:px-5"
                >
                    <textarea
                        x-model="mensagemNova"
                        @keydown.enter="if ($event.ctrlKey || $event.metaKey) { $event.preventDefault(); enviarMensagem(); }"
                        rows="1"
                        maxlength="5000"
                        placeholder="Digite sua mensagem..."
                        class="max-h-32 min-h-[44px] min-w-0 flex-1 resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-[#236350] focus:ring-1 focus:ring-[#236350]"
                    ></textarea>

                    <div class="flex shrink-0 flex-col items-center gap-1">
                        <span class="hidden text-[11px] text-gray-400 sm:block">
                            Ctrl + Enter
                        </span>

                        <button
                            type="submit"
                            :disabled="!mensagemNova.trim() || enviandoMensagem"
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#236350] text-white transition hover:bg-[#1B4D3E] disabled:cursor-not-allowed disabled:opacity-40"
                            aria-label="Enviar mensagem"
                        >
                            <span x-show="!enviandoMensagem" class="material-symbols-outlined">send</span>
                            <span x-show="enviandoMensagem" class="material-symbols-outlined animate-spin">progress_activity</span>
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>

// Guarda a página de origem ao entrar no índice do chat.
(function () {
    const chave = 'sitecopav_chat_index_origem';
    const origem = document.referrer;

    if (!origem) return;

    try {
        const url = new URL(origem);

        // Só aceita páginas do próprio Site Copav.
        if (url.origin !== window.location.origin) return;

        // Não substitui a origem ao voltar do show para o index.
        if (url.pathname === '/chat' || url.pathname.startsWith('/chat/')) {
            return;
        }

        sessionStorage.setItem(chave, url.href);
    } catch (erro) {
        console.warn('Não foi possível registrar a página de origem.', erro);
    }
})();

// Retorna à página de origem, com fallback para a página inicial.
window.voltarParaOrigemChat = function () {
    const chave = 'sitecopav_chat_index_origem';

    try {
        const origem = sessionStorage.getItem(chave);

        if (origem) {
            const url = new URL(origem);

            if (url.origin === window.location.origin) {
                window.location.assign(url.href);
                return;
            }
        }
    } catch (erro) {
        console.warn('Não foi possível recuperar a página de origem.', erro);
    }

    window.location.assign('/');
};

window.chatIndex = function () {
    return {
        usuarioId: {{ (int) auth()->id() }},
        csrfToken: @json(csrf_token()),

        conversas: [],
        conversaSelecionada: null,
        assuntoSelecionadoId: '',
        contextoSelecionado: '',
        mensagens: [],
        mensagemNova: '',

        carregandoMensagens: false,
        enviandoMensagem: false,
        erroMensagens: '',
        erroEnvio: '',

        naoLidas: {},
        canaisInscritos: [],
        requisicaoMensagens: 0,

        inicializar() {
            this.conversas = Array.from(
                this.$el.querySelectorAll('[data-chat-id]')
            ).map(botao => {
                const chatId = Number(botao.dataset.chatId);
                const assuntoId = botao.dataset.assuntoChatId || null;

                return {
                    chat_id: chatId,
                    assunto_chat_id: assuntoId ? Number(assuntoId) : null,
                    contexto_chave: botao.dataset.contextoChave,
                    titulo: botao.dataset.contextoTitulo,
                    participante: botao.dataset.participante || 'Participante',
                    ultima_mensagem: 'Nenhuma mensagem ainda',
                    ultima_mensagem_usuario: null,
                    ultima_mensagem_data: null
                };
            });

            this.carregarContadores();
        },

        async carregarContadores() {
            try {
                const resposta = await fetch(@json(route('chat.conversas')), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!resposta.ok) {
                    throw new Error('Não foi possível carregar as conversas.');
                }

                const dados = await resposta.json();

                dados.forEach(item => {
                    // Mensagens gerais
                    const geral = this.conversas.find(
                        contexto =>
                            contexto.chat_id === Number(item.chat_id) &&
                            contexto.assunto_chat_id === null
                    );

                    if (geral && item.mensagens_gerais) {
                        geral.ultima_mensagem =
                            item.mensagens_gerais.ultima_mensagem;

                        geral.ultima_mensagem_usuario =
                            item.mensagens_gerais.ultima_mensagem_usuario;

                        geral.ultima_mensagem_data =
                            item.mensagens_gerais.ultima_mensagem_data;

                        this.naoLidas[geral.contexto_chave] =
                            Number(item.mensagens_gerais.mensagens_nao_lidas || 0);
                    }

                    // Assuntos
                    (item.assuntos || []).forEach(assunto => {
                        const contexto = this.conversas.find(
                            entrada =>
                                entrada.chat_id === Number(item.chat_id) &&
                                entrada.assunto_chat_id === Number(assunto.id)
                        );

                        if (!contexto) return;

                        contexto.ultima_mensagem = assunto.ultima_mensagem;
                        contexto.ultima_mensagem_usuario =
                            assunto.ultima_mensagem_usuario;
                        contexto.ultima_mensagem_data =
                            assunto.ultima_mensagem_data;

                        this.naoLidas[contexto.contexto_chave] =
                            Number(assunto.mensagens_nao_lidas || 0);
                    });
                });

            } catch (erro) {
                console.error('Erro ao carregar contadores:', erro);
            }

            this.conversas.forEach(contexto => {
                this.inscreverCanal(contexto.chat_id);
            });
        },

        textoPrevia(contexto) {
            if (!contexto) return '';

            const prefixo =
                Number(contexto.ultima_mensagem_usuario) === this.usuarioId
                    ? 'Você: '
                    : '';

            return prefixo + (
                contexto.ultima_mensagem || 'Nenhuma mensagem ainda'
            );
        },

        inscreverCanal(chatId) {
            if (!window.Echo || !chatId) return;

            chatId = Number(chatId);

            if (this.canaisInscritos.includes(chatId)) return;

            this.canaisInscritos.push(chatId);

            window.Echo.private(`chat.${chatId}`)
                .listen('.mensagem.enviada', mensagem => {
                    this.receberMensagem(mensagem);
                });
        },

        receberMensagem(mensagem) {
            const chave = mensagem.assunto_chat_id
                ? `${mensagem.chat_id}:${mensagem.assunto_chat_id}`
                : `${mensagem.chat_id}:geral`;

            const contexto = this.conversas.find(
                item => item.contexto_chave === chave
            );

            if (!contexto) return;

            contexto.ultima_mensagem = mensagem.conteudo;
            contexto.ultima_mensagem_usuario = mensagem.usuario_id;
            contexto.ultima_mensagem_data = mensagem.created_at;

            const mesmoContexto =
                this.contextoSelecionado === chave;

            if (
                mesmoContexto &&
                !this.carregandoMensagens
            ) {
                this.adicionarMensagem(mensagem);
                this.$nextTick(() => this.rolarHistorico());
            } else if (Number(mensagem.usuario_id) !== this.usuarioId) {
                this.naoLidas[chave] =
                    Number(this.naoLidas[chave] || 0) + 1;
            }
        },

        async abrirConversa(chatId, botao) {
            chatId = Number(chatId);

            if (!botao) {
                console.error('Botão do contexto não encontrado:', chatId);
                return;
            }

            const chave = botao.dataset.contextoChave;
            const assuntoId = botao.dataset.assuntoChatId || null;

            const contexto = this.conversas.find(
                item => item.contexto_chave === chave
            );

            if (!contexto) {
                console.error('Contexto não encontrado:', chave);
                return;
            }

            this.conversaSelecionada = contexto;
            this.contextoSelecionado = chave;
            this.assuntoSelecionadoId = assuntoId || '';

            this.mensagens = [];
            this.mensagemNova = '';
            this.erroMensagens = '';
            this.erroEnvio = '';

            this.inscreverCanal(chatId);

            await this.carregarMensagens();
        },

        async carregarMensagens() {
            if (!this.conversaSelecionada) return;

            const numeroRequisicao = ++this.requisicaoMensagens;

            this.carregandoMensagens = true;
            this.erroMensagens = '';
            this.mensagens = [];

            const chatId = this.conversaSelecionada.chat_id;
            const assuntoId = this.assuntoSelecionadoId;
            const chave = this.contextoSelecionado;

            try {
                let url = `/chat/${chatId}/mensagens`;

                if (assuntoId) {
                    url += `?assunto_chat_id=${encodeURIComponent(assuntoId)}`;
                }

                const resposta = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const dados = await resposta.json();

                if (!resposta.ok) {
                    throw new Error(
                        dados.message || 'Não foi possível carregar as mensagens.'
                    );
                }

                // Impede uma resposta antiga de substituir outro contexto.
                if (
                    numeroRequisicao === this.requisicaoMensagens &&
                    this.contextoSelecionado === chave
                ) {
                    this.mensagens = dados.mensagens || [];

                    // O controller já marcou somente este contexto como lido.
                    this.naoLidas[chave] = 0;
                }

            } catch (erro) {
                if (numeroRequisicao === this.requisicaoMensagens) {
                    this.erroMensagens = erro.message;
                }
            } finally {
                if (numeroRequisicao === this.requisicaoMensagens) {
                    this.carregandoMensagens = false;

                    this.$nextTick(() => {
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                const lista = this.$refs.listaMensagens;

                                if (lista) {
                                    lista.scrollTop = lista.scrollHeight;
                                }
                            });
                        });
                    });
                }
            }
        },

        adicionarMensagem(mensagem) {
            const existe = this.mensagens.some(
                item => Number(item.id) === Number(mensagem.id)
            );

            if (!existe) {
                this.mensagens.push(mensagem);
            }

            const chave = mensagem.assunto_chat_id
                ? `${mensagem.chat_id}:${mensagem.assunto_chat_id}`
                : `${mensagem.chat_id}:geral`;

            const contexto = this.conversas.find(
                item => item.contexto_chave === chave
            );

            if (contexto) {
                contexto.ultima_mensagem = mensagem.conteudo;
                contexto.ultima_mensagem_usuario = mensagem.usuario_id;
                contexto.ultima_mensagem_data = mensagem.created_at;
            }
        },

        async enviarMensagem() {
            const conteudo = this.mensagemNova.trim();

            if (
                !conteudo ||
                !this.conversaSelecionada ||
                this.enviandoMensagem
            ) return;

            this.enviandoMensagem = true;
            this.erroEnvio = '';

            try {
                const resposta = await fetch(
                    `/chat/${this.conversaSelecionada.chat_id}/mensagens`,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            conteudo,
                            assunto_chat_id: this.assuntoSelecionadoId || null
                        })
                    }
                );

                const dados = await resposta.json();

                if (!resposta.ok) {
                    throw new Error(
                        dados.message || 'Não foi possível enviar a mensagem.'
                    );
                }

                this.adicionarMensagem(dados.mensagem);
                this.mensagemNova = '';

                this.$nextTick(() => this.rolarHistorico());

            } catch (erro) {
                this.erroEnvio = erro.message;
            } finally {
                this.enviandoMensagem = false;
            }
        },

        voltarLista() {
            this.requisicaoMensagens++;

            this.conversaSelecionada = null;
            this.contextoSelecionado = '';
            this.assuntoSelecionadoId = '';
            this.mensagens = [];
            this.mensagemNova = '';
            this.erroMensagens = '';
            this.erroEnvio = '';
            this.carregandoMensagens = false;
        },

        rolarHistorico() {
            const lista = this.$refs.listaMensagens;

            if (!lista) return;

            const posicionar = () => {
                lista.scrollTo({
                    top: lista.scrollHeight,
                    behavior: 'instant'
                });
            };

            this.$nextTick(() => {
                requestAnimationFrame(() => {
                    posicionar();

                    requestAnimationFrame(posicionar);
                });

                setTimeout(posicionar, 100);
            });
        },

        formatarDataHora(data) {
            if (!data) return '';

            const dataMensagem = new Date(data);

            if (isNaN(dataMensagem.getTime())) return '';

            return dataMensagem.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }).replace(',', ' às');
        },

        formatarHoraLista(data) {
            if (!data) return '';

            const dataMensagem = new Date(data);

            if (isNaN(dataMensagem.getTime())) return '';

            return dataMensagem.toLocaleTimeString('pt-BR', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    };
};


</script>
@endpush
