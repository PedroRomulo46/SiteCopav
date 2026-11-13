<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Meu Marketplace')</title>

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&display=block" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Script para abrir o menu monile nas outras páginas --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        .btn-copav {
            background-color: #236350 !important;
            color: #ffffff !important;
            border: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .btn-copav:hover {
            background-color: #1a493b !important;
            color: #ffffff !important;
        }
        
        /* Esconde no mobile/tablet e exibe flex no computador */
        .menu-desktop {
          display: none;
        }
        @media (min-width: 1024px) {
          .menu-desktop {
            display: flex !important;
          }
        }

        .nav-link:hover {
          color: #fcd34d !important; /* Cor equivalente a amber-300 */
        }

    </style>

</head>

<script>
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>

<body class="bg-[#ebeae7] min-h-screen flex flex-col font-sans" x-data="{ menuMobileAberto: false }">

    {{-- Cabeçalho --}}
    <header class="bg-[#1B4D3E] text-white shadow-md w-full">
      <div class="w-full px-4 sm:px-6 lg:px-10 py-3 flex flex-col gap-3">
        
        {{-- LINHA SUPERIOR --}}
        <div class="flex items-center justify-between gap-2 md:gap-6 w-full">
          
          {{-- Logo --}}
          <a href="{{ route('home') }}" class="text-xl md:text-2xl font-bold tracking-wide shrink-0 whitespace-nowrap hover:opacity-90 transition-opacity">
            Site Copav
          </a>

          {{-- Busca --}}
          <div class="hidden sm:flex flex-1 max-w-2xl relative items-center">
            <input type="text" placeholder="Buscar lotes, produtos ou compradores..." class="input w-full bg-white text-gray-800 placeholder-gray-400 pl-4 pr-10 py-2 rounded-md focus:outline-none shadow-sm text-sm">
            <button class="absolute right-3 text-gray-500 hover:text-gray-700 flex items-center">
              <span class="material-symbols-outlined text-xl">search</span>
            </button>
          </div>

          {{-- Banner Oferta
          <div class="xl:flex hidden items-center gap-3 bg-slate-700 hover:bg-slate-600 border border-emerald-700 p-1.5 px-3 rounded-full shadow-md transition-all cursor-pointer group shrink-0">
            <span class="bg-emerald-800 group-hover:bg-amber-400 transition-colors text-white font-bold text-[10px] uppercase px-2 py-0.5 rounded-full shrink-0">
              Oferta
            </span>
            <div class="flex items-center gap-1.5 text-xs">
              <span class="font-bold text-white transition-colors">Café Arábica</span>
              <span class="text-white transition-colors">|</span>
              <span class="line-through text-[11px] text-white transition-colors">R$ 9.581,22</span>
              <span class="font-extrabold text-sm text-emerald-500 transition-colors">R$ 8.980,87</span>
            </div>
            <div class="ml-auto bg-emerald-600 group-hover:bg-emerald-500 text-white text-[11px] font-semibold px-2.5 py-1 rounded-full flex items-center gap-1 transition-colors shrink-0">
              <span>Ver Oferta</span>
              <span class="material-symbols-outlined text-xs">arrow_right_alt</span>
            </div>
          </div>
          --}}

          {{-- Botão Menu Hambúrguer --}}
          <button @click="menuMobileAberto = !menuMobileAberto" class="lg:hidden p-2 text-white hover:bg-[#236350] rounded-lg transition-colors focus:outline-none">
            <span class="material-symbols-outlined text-2xl" x-text="menuMobileAberto ? 'close' : 'menu'">menu</span>
          </button>
        </div>

        {{-- Busca Mobile --}}
        <div class="sm:hidden w-full relative flex items-center mt-1">
          <input type="text" placeholder="Buscar no marketplace..." class="input w-full bg-white text-gray-800 placeholder-gray-400 pl-3 pr-9 py-2 rounded-md text-xs">
          <button class="absolute right-2 text-gray-500 flex items-center">
            <span class="material-symbols-outlined text-lg">search</span>
          </button>
        </div>

        {{-- LINHA INFERIOR --}}
        <div class="menu-desktop items-center justify-between text-sm border-t border-[#236350] pt-2.5 text-gray-100 w-full">
          
          {{-- Links de Navegação Principal --}}
          <nav class="flex items-center gap-6 font-medium">
            <a href="{{ route('home') }}" class="nav-link transition-colors">Início</a>

            {{-- Exibe Lotes e Demandas apenas para Fornecedores ou Admins --}}
            @auth
              @if(auth()->user()->fornecedor || auth()->user()->is_admin || auth()->user()->user_type === 'admin' || auth()->user()->email === 'copavagricola@example.com')
                <a href="{{ route('ofertas.index') }}" class="nav-link transition-colors">Ofertas</a>
                <a href="{{ route('demandas.index') }}" class="nav-link transition-colors">Demandas</a>
              @endif
            @endauth
            
            {{-- Dropdown Categorias --}}
            <div class="dropdown relative">
              <div tabindex="0" role="button" class="flex items-center gap-1 nav-link transition-colors cursor-pointer py-1">
                <span>Categorias</span>
                <span class="material-symbols-outlined text-base">expand_more</span>
              </div>
              
              <ul tabindex="0" class="dropdown-content menu bg-white text-gray-800 rounded-box z-50 w-56 p-2 shadow-xl mt-1 border border-gray-100 max-h-60 overflow-y-auto">
                
                {{-- Condição de Acesso: Apenas Admin vê "Ver Todas" --}}
                @auth
                  @if(auth()->user()->is_admin || auth()->user()->user_type === 'admin' || auth()->user()->email === 'copavagricola@example.com')
                    <li>
                      <a href="{{ route('categorias.index') }}" class="font-bold text-[#1B4D3E] hover:bg-emerald-50">
                        <span class="material-symbols-outlined text-base">list</span>
                        Ver Todas
                      </a>
                    </li>
                  @else
                    <li>
                      <a href="{{ route('categorias.create') }}" class="font-bold text-amber-700 hover:bg-amber-50">
                        <span class="material-symbols-outlined text-base">add_circle</span>
                        Sugerir Categoria
                      </a>
                    </li>
                  @endif
                @else
                  <li>
                    <a href="{{ route('login') }}" class="font-bold text-amber-700 hover:bg-amber-50">
                      <span class="material-symbols-outlined text-base">add_circle</span>
                      Sugerir Categoria
                    </a>
                  </li>
                  @endauth
                  
                <div class="divider my-1"></div>

                {{-- Lista dinâmica de Categorias do Banco de Dados --}}
                @forelse($categorias ?? [] as $categoria)
                  <li>
                    <a href="{{ route('categorias.show', $categoria->id) }}" class="hover:bg-emerald-50 hover:text-[#1B4D3E]">
                      {{ $categoria->nome }}
                    </a>
                  </li>
                @empty
                  <li class="text-xs text-gray-400 p-2 text-center">Nenhuma categoria encontrada</li>
                @endforelse
              </ul>
            </div>
          </nav>

          {{-- Ações do Usuário / Autenticação --}}
          <div class="flex items-center gap-6">
            @auth

              {{-- Minhas compras: usuários autenticados --}}
              <a href="{{ route('negociacoes.compras') }}"
                class="flex items-center gap-1.5 nav-link transition-colors">
                  <span class="material-symbols-outlined text-lg">shopping_bag</span>
                  <span>Minhas compras</span>
              </a>

              {{-- Minhas vendas: fornecedor, empresa ou administrador --}}
              @if(
                  auth()->user()->user_type === 'admin' ||
                  auth()->user()->is_admin ||
                  auth()->user()->user_type === 'empresa' ||
                  auth()->user()->email === 'copavagricola@example.com' ||
                  auth()->user()->fornecedor
              )
                  <a href="{{ route('negociacoes.vendas') }}"
                    class="flex items-center gap-1.5 nav-link transition-colors">
                      <span class="material-symbols-outlined text-lg">storefront</span>
                      <span>Minhas vendas</span>
                  </a>
              @endif

              @if(!auth()->user()->fornecedor)
                <a href="{{ route('fornecedores.create') }}" class="flex items-center gap-1.5 nav-link transition-colors">
                  <span class="material-symbols-outlined text-lg">patient_list</span>
                  <span>Virar Fornecedor</span>
                </a>
              @else
                <a href="{{ route('fornecedores.show', auth()->user()->fornecedor) }}" class="flex items-center gap-1.5 nav-link transition-colors">
                  <span class="material-symbols-outlined text-lg">patient_list</span>
                  <span>Menu Fornecedor</span>
                </a>
              @endif

              @if(auth()->user()->is_admin || auth()->user()->user_type === 'admin' || auth()->user()->email === 'copavagricola@example.com')
                <a href="{{ route('demandas.create') }}" class="flex items-center gap-1.5 nav-link transition-colors">
                  <span class="material-symbols-outlined text-lg">add_circle</span>
                  <span>Criar Demanda</span>
                </a>
              @endif

              {{-- Menu do Usuário Logado --}}
              <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="flex items-center gap-2 cursor-pointer nav-link transition-colors">
                  <div class="w-7 h-7 rounded-full overflow-hidden bg-white text-gray-700 flex items-center justify-center shrink-0">
                    @if(auth()->user()->imagem)
                      <img src="{{ Storage::url(auth()->user()->imagem) }}" alt="Perfil" class="w-full h-full object-cover">
                    @else
                      <span class="material-symbols-outlined text-xl">account_circle</span>
                    @endif
                  </div>
                  <span class="font-semibold max-w-[120px] truncate">{{ auth()->user()->nome ?? auth()->user()->name }}</span>
                </div>

                <ul tabindex="0"
                class="dropdown-content menu bg-white text-gray-800 rounded-box z-50 w-48 p-2 shadow-lg mt-2">

                  {{-- Botão de configurações --}}
                  <li>
                      <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-2 hover:bg-gray-300">
                          <span class="material-symbols-outlined text-base">
                              settings
                          </span>
                          Configurações
                      </a>
                  </li>

                  {{-- Botão para sair da conta --}}
                  <li>
                      <form method="POST" action="{{ route('logout') }}">
                          @csrf

                          <button type="submit" class="w-full flex items-center gap-2 text-red-600 hover:bg-red-100 rounded-md text-left">
                              <span class="material-symbols-outlined text-base">
                                  logout
                              </span>
                              Sair
                          </button>
                      </form>
                  </li>
                </ul>
              </div>
            @endauth

            @guest
              <div class="flex items-center gap-3">
                <a href="{{ route('register') }}" class="nav-link transition-colors">Crie a sua conta</a>
                <span class="text-slate-300">|</span>
                <a href="{{ route('login') }}" class="nav-link transition-colors font-semibold">Entre</a>
              </div>
            @endguest
          </div>

        </div>

        {{-- Menu Desplegável Mobile --}}
        <div x-show="menuMobileAberto" x-cloak x-transition class="lg:hidden flex flex-col gap-3 border-t border-[#236350] pt-3 text-sm">
          <nav class="flex flex-col gap-1 font-medium">
            <a href="{{ route('home') }}" class="hover:bg-[#236350] p-2 rounded-md">Início</a>

            {{-- Exibe Lotes e Demandas no Mobile apenas para Fornecedores ou Admins --}}
            @auth
              @if(auth()->user()->fornecedor || auth()->user()->is_admin || auth()->user()->user_type === 'admin' || auth()->user()->email === 'copavagricola@example.com')
                <a href="{{ route('ofertas.index') }}" class="hover:bg-[#236350] p-2 rounded-md">Ofertas</a>
                <a href="{{ route('demandas.index') }}" class="hover:bg-[#236350] p-2 rounded-md">Demandas</a>
              @endif
            @endauth

            {{-- Dropdown Categorias Mobile --}}
            <div x-data="{ openCat: false }">
              <button @click="openCat = !openCat" class="flex items-center justify-between w-full p-2 hover:bg-[#236350] rounded-md text-left">
                <span>Categorias</span>
                <span class="material-symbols-outlined text-sm" x-text="openCat ? 'expand_less' : 'expand_more'">expand_more</span>
              </button>
              
              <div x-show="openCat" x-cloak class="pl-4 flex flex-col gap-1 mt-1">
                
                @auth
                  @if(auth()->user()->is_admin || auth()->user()->user_type === 'admin' || auth()->user()->email === 'copavagricola@example.com')
                    <a
                    href="{{ route('categorias.index') }}"
                    class="p-1.5 text-xs text-amber-200 hover:bg-[#236350] rounded font-semibold flex items-center gap-1">
                      - Ver Todas -
                    </a>
                  @else
                    <a
                    href="{{ route('categorias.create') }}"
                    class="p-1.5 text-xs hover:bg-[#236350] rounded font-semibold flex items-center gap-1">
                      <span class="material-symbols-outlined text-sm">add_circle</span>
                      Sugerir Categoria
                    </a>
                  @endif
                @else
                  <a
                  href="{{ route('categorias.create') }}"
                  class="p-1.5 text-xs hover:bg-[#236350] rounded font-semibold flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">add_circle</span>
                    Sugerir Categoria
                  </a>
                @endauth

                @foreach($categorias ?? [] as $categoria)
                  <a
                  href="{{ route('categorias.show', $categoria->id) }}"
                  class="p-1.5 text-xs hover:bg-[#236350] rounded">
                    {{ $categoria->nome }}
                  </a>
                @endforeach
              </div>
            </div>
          </nav>

          <div class="border-t border-[#236350] pt-2 flex flex-col gap-1">
            @auth

              {{-- Minhas compras --}}
              <a href="{{ route('negociacoes.compras') }}"
                class="flex items-center gap-2 p-2 hover:bg-[#236350] rounded-md">
                  <span class="material-symbols-outlined text-lg">shopping_bag</span>
                  <span>Minhas compras</span>
              </a>

              {{-- Minhas vendas: fornecedor, empresa ou administrador --}}
              @if(
                  auth()->user()->user_type === 'admin' ||
                  auth()->user()->is_admin ||
                  auth()->user()->user_type === 'empresa' ||
                  auth()->user()->email === 'copavagricola@example.com' ||
                  auth()->user()->fornecedor
              )
                  <a href="{{ route('negociacoes.vendas') }}"
                    class="flex items-center gap-2 p-2 hover:bg-[#236350] rounded-md">
                      <span class="material-symbols-outlined text-lg">storefront</span>
                      <span>Minhas vendas</span>
                  </a>
              @endif

              @if(!auth()->user()->fornecedor)
                <a href="{{ route('fornecedores.create') }}" class="flex items-center gap-2 p-2 hover:bg-[#236350] rounded-md">
                  <span class="material-symbols-outlined text-lg">patient_list</span>
                  <span>Virar Fornecedor</span>
                </a>
              @else
                <a href="{{ route('fornecedores.show', auth()->user()->fornecedor) }}" class="flex items-center gap-2 p-2 hover:bg-[#236350] rounded-md">
                  <span class="material-symbols-outlined text-lg">patient_list</span>
                  <span>Menu Fornecedor</span>
                </a>
              @endif

              @if(auth()->user()->is_admin || auth()->user()->user_type === 'admin' || auth()->user()->email === 'copavagricola@example.com')
                <a href="{{ route('demandas.create') }}" class="flex items-center gap-2 p-2 hover:bg-[#236350] rounded-md">
                  <span class="material-symbols-outlined text-lg">add_circle</span>
                  <span>Criar Demanda</span>
                </a>
              @endif

              {{-- Botão de configurações --}}
              <a href="{{ route('profile.edit') }}"
                class="flex w-full items-center gap-2 p-2 rounded-md
                        text-white hover:bg-[#236350]
                        transition-colors duration-200">
                  <span class="material-symbols-outlined text-lg">settings</span>
                  <span>Configurações</span>
              </a>

              {{-- Botão para sair da conta --}}
              <form method="POST" action="{{ route('logout') }}" class="w-full">
                  @csrf

                  <button type="submit"
                          class="flex w-full items-center gap-2 p-2 rounded-md
                                text-left text-white hover:bg-[#236350]
                                transition-colors duration-200">
                      <span class="material-symbols-outlined text-lg">logout</span>
                      <span>Sair</span>
                  </button>
              </form>
            @endauth

            @guest
              <div class="flex flex-col gap-2 p-2">
                <a href="{{ route('login') }}" class="btn bg-[#236350] hover:bg-[#123228] text-white btn-sm border-none w-full">Entre</a>
                <a href="{{ route('register') }}" class="btn bg-[#236350] hover:bg-[#123228] text-white btn-sm border-none w-full">Crie a sua conta</a>
              </div>
            @endguest
          </div>
        </div>

      </div>
    </header>

    <!-- Conteúdo Principal -->

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-10 py-6">
      @yield('conteudo')
    </main>

    {{-- Chat flutuante global do COPAV --}}
    @auth
        <div id="copav-chat-global"
          class="fixed bottom-5 right-5 z-[100]"
          x-data="{
              chatAberto: false,

              // Lista de conversas
              conversas: [],
              carregando: false,
              conversasCarregadas: false,
              erro: '',

              // Conversa selecionada
              conversaSelecionada: null,
              mensagens: [],
              carregandoMensagens: false,
              erroMensagens: '',

              // Usuário autenticado
              usuarioId: {{ auth()->id() }},

              async abrirChat() {
                  this.chatAberto = !this.chatAberto;

                  if (this.chatAberto && !this.conversasCarregadas) {
                      await this.carregarConversas();
                  }
              },

              async carregarConversas() {
                  this.carregando = true;
                  this.erro = '';

                  try {
                      const resposta = await fetch('{{ route('chat.conversas') }}', {
                          headers: {
                              'Accept': 'application/json',
                              'X-Requested-With': 'XMLHttpRequest'
                          }
                      });

                      if (!resposta.ok) {
                          throw new Error('Não foi possível carregar as conversas.');
                      }

                      this.conversas = await resposta.json();
                      this.conversasCarregadas = true;

                  } catch (erro) {
                      this.erro = erro.message;
                  } finally {
                      this.carregando = false;
                  }
              },

              async abrirConversa(conversa) {
                  this.conversaSelecionada = conversa;
                  this.mensagens = [];
                  this.carregandoMensagens = true;
                  this.erroMensagens = '';

                  try {
                      const resposta = await fetch(
                          `/chat/${conversa.negociacao_id}/mensagens`,
                          {
                              headers: {
                                  'Accept': 'application/json',
                                  'X-Requested-With': 'XMLHttpRequest'
                              }
                          }
                      );

                      if (!resposta.ok) {
                          throw new Error('Não foi possível carregar as mensagens.');
                      }

                      const dados = await resposta.json();
                      this.mensagens = dados.mensagens;

                      this.$nextTick(() => {
                          const lista = this.$refs.listaMensagens;

                          if (lista) {
                              lista.scrollTop = lista.scrollHeight;
                          }
                      });

                  } catch (erro) {
                      this.erroMensagens = erro.message;
                  } finally {
                      this.carregandoMensagens = false;
                  }
              },

              voltarConversas() {
                  this.conversaSelecionada = null;
                  this.mensagens = [];
                  this.erroMensagens = '';
              }
          }"
          >

           {{-- Botão flutuante --}}
            <button
                id="copav-chat-toggle"
                type="button"
                @click="abrirChat()"
                :aria-expanded="chatAberto.toString()"
                aria-label="Abrir mensagens"
                class="relative flex h-14 w-14 shrink-0 cursor-pointer select-none
                      items-center justify-center rounded-full
                      border-0 bg-[#236350] p-0 text-white shadow-xl
                      transition-all duration-200
                      hover:scale-105 hover:bg-[#1B4D3E]
                      focus:outline-none focus:ring-4 focus:ring-[#236350]/30"
            >
                <span
                    class="material-symbols-outlined pointer-events-none text-3xl"
                    x-text="chatAberto ? 'close' : 'chat'">
                    chat
                </span>

                <span
                    id="copav-chat-contador"
                    class="absolute -right-1 -top-1 hidden min-w-5 h-5
                          items-center justify-center rounded-full
                          border-2 border-white bg-red-500 px-1
                          text-[10px] font-bold leading-none text-white">
                    0
                </span>
            </button>

            {{-- Janela flutuante --}}
            <section
                x-cloak
                x-show="chatAberto"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-3 scale-95"
                class="absolute bottom-16 right-0 flex h-[min(520px,75vh)]
                      w-[min(360px,calc(100vw-2rem))] flex-col overflow-hidden
                      rounded-2xl border border-gray-200 bg-white shadow-2xl"
                aria-label="Janela de mensagens"
            >
                {{-- Cabeçalho --}}
                <div class="flex items-center justify-between
                            bg-[#1B4D3E] px-4 py-4 text-white">
                    <div>
                        <h2 class="font-semibold">Mensagens</h2>
                        <p class="text-xs text-white/75">
                            Suas conversas no COPAV
                        </p>
                    </div>

                    <a href="{{ route('chat') }}"
                      class="rounded-lg px-2 py-1 text-sm
                              text-white/90 transition hover:bg-white/10">
                        Ver todas
                    </a>
                </div>

                
                {{-- Conteúdo da lista de conversas --}}
                <div class="flex-1 overflow-y-auto bg-white">

                    {{-- Conversa selecionada --}}
                    <template x-if="conversaSelecionada">
                        <div class="flex h-full flex-col">

                            {{-- Cabeçalho da conversa --}}
                            <div class="flex items-center gap-3 border-b border-gray-200 bg-white px-4 py-3">

                                <button
                                    type="button"
                                    @click="voltarConversas()"
                                    class="rounded-full p-2 text-gray-600 transition hover:bg-gray-100"
                                    aria-label="Voltar às conversas">
                                    <span class="material-symbols-outlined">arrow_back</span>
                                </button>

                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#236350]/10 text-[#236350]">
                                    <span class="material-symbols-outlined">person</span>
                                </div>

                                <div class="min-w-0">
                                    <p
                                        class="truncate font-semibold text-gray-800"
                                        x-text="conversaSelecionada.participante">
                                    </p>
                                    <p class="text-xs text-gray-500">Conversa no COPAV</p>
                                </div>

                            </div>

                            {{-- Histórico de mensagens --}}
                            <div
                                x-ref="listaMensagens"
                                class="flex-1 space-y-3 overflow-y-auto bg-[#efeae2] px-3 py-4">

                                {{-- Carregando mensagens --}}
                                <template x-if="carregandoMensagens">
                                    <p class="py-6 text-center text-sm text-gray-500">
                                        Carregando mensagens...
                                    </p>
                                </template>

                                {{-- Erro --}}
                                <template x-if="erroMensagens">
                                    <p
                                        class="py-6 text-center text-sm text-red-600"
                                        x-text="erroMensagens">
                                    </p>
                                </template>

                                {{-- Nenhuma mensagem --}}
                                <template x-if="!carregandoMensagens && !erroMensagens && mensagens.length === 0">
                                    <p class="py-6 text-center text-sm text-gray-500">
                                        Nenhuma mensagem nesta conversa.
                                    </p>
                                </template>

                                {{-- Mensagens --}}
                                <template x-for="mensagem in mensagens" :key="mensagem.id">
                                    <div
                                        class="flex"
                                        :class="Number(mensagem.usuario_id) === Number(usuarioId) ? 'justify-end' : 'justify-start'">

                                        <div
                                            class="max-w-[85%] rounded-xl px-3 py-2 shadow-sm"
                                            :class="Number(mensagem.usuario_id) === Number(usuarioId)
                                                ? 'bg-[#d9fdd3] text-gray-800'
                                                : 'bg-white text-gray-800'">

                                            <p
                                                x-show="Number(mensagem.usuario_id) !== Number(usuarioId)"
                                                class="mb-1 text-xs font-semibold text-[#236350]"
                                                x-text="mensagem.nome">
                                            </p>

                                            <p
                                                class="whitespace-pre-wrap break-words text-sm"
                                                x-text="mensagem.conteudo">
                                            </p>

                                            <p
                                                class="mt-1 text-right text-[10px] text-gray-500"
                                                x-text="mensagem.created_at">
                                            </p>

                                        </div>
                                    </div>
                                </template>

                            </div>
                        </div>
                    </template>

                    {{-- Lista de conversas: aparece somente quando nenhuma está selecionada --}}
                    <template x-if="!conversaSelecionada">
                        <div class="flex h-full flex-col">

                            {{-- Carregando conversas --}}
                            <template x-if="carregando">
                                <div class="flex h-full flex-col items-center justify-center gap-3">
                                    <span class="loading loading-spinner loading-md text-[#236350]"></span>
                                    <p class="text-sm text-gray-500">Carregando conversas...</p>
                                </div>
                            </template>

                            {{-- Erro --}}
                            <template x-if="!carregando && erro">
                                <div class="p-6 text-center">
                                    <p class="text-sm text-red-500" x-text="erro"></p>

                                    <button
                                        @click="carregarConversas()"
                                        class="mt-3 text-sm font-medium text-[#236350] hover:underline">
                                        Tentar novamente
                                    </button>
                                </div>
                            </template>

                            {{-- Nenhuma conversa --}}
                            <template x-if="!carregando && !erro && conversas.length === 0 && conversasCarregadas">
                                <div class="flex h-full flex-col items-center justify-center px-6 text-center">
                                    <span class="material-symbols-outlined mb-3 text-5xl text-gray-300">
                                        forum
                                    </span>

                                    <p class="font-medium text-gray-700">
                                        Nenhuma conversa encontrada
                                    </p>

                                    <p class="mt-2 text-sm text-gray-500">
                                        Suas conversas aparecerão aqui quando houver uma proposta aceita.
                                    </p>
                                </div>
                            </template>

                            {{-- Conversas disponíveis --}}
                            <template x-if="!carregando && !erro && conversas.length > 0">
                                <div class="overflow-y-auto">

                                    <template x-for="conversa in conversas" :key="conversa.chat_id">
                                        <div
                                            @click="abrirConversa(conversa)"
                                            class="flex cursor-pointer items-center gap-3 border-b border-gray-100 px-4 py-4 transition hover:bg-gray-50">

                                            {{-- Avatar --}}
                                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#236350]/10 text-[#236350]">
                                                <span class="material-symbols-outlined text-2xl">
                                                    person
                                                </span>
                                            </div>

                                            {{-- Informações --}}
                                            <div class="min-w-0 flex-1">
                                                <p
                                                    class="truncate font-semibold text-gray-800"
                                                    x-text="conversa.participante">
                                                </p>

                                                <p
                                                    class="mt-1 truncate text-sm text-gray-500"
                                                    x-text="conversa.ultima_mensagem">
                                                </p>
                                            </div>

                                        </div>
                                    </template>

                                </div>
                            </template>

                        </div>
                    </template>

                </div>
            </section>
        </div>
    @endauth

    @stack('scripts')


  </body>
</html>