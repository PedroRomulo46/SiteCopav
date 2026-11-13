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

<body class="bg-[#ebeae7] min-h-screen flex flex-col font-sans
    @if(View::hasSection('chat_layout')) !h-screen !overflow-hidden @endif">

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

    <main class="flex-1 min-h-0 w-full
    @if(View::hasSection('chat_layout'))
        overflow-hidden !p-0
    @else
        px-4 sm:px-6 lg:px-10 py-6
    @endif">
    @yield('conteudo')
</main>
    {{-- Chat flutuante global do COPAV --}}
    
    @auth
      <div
          id="copav-chat-global"
          class="fixed bottom-5 right-5 z-[100]"
          x-data="{
              chatAberto: false,
              conversas: [],
              carregando: false,
              conversasCarregadas: false,
              erro: '',
              conversaSelecionada: null,
              mensagens: [],
              carregandoMensagens: false,
              erroMensagens: '',
              mensagemNova: '',
              enviandoMensagem: false,
              usuarioId: {{ (int) auth()->id() }},
              csrfToken: '{{ csrf_token() }}',
              urlConversas: '{{ route('chat.conversas') }}',

              chatIdAtivo: null,
              canaisInscritos: [],

              abrirChat() {
                  this.chatAberto = !this.chatAberto;

                  if (this.chatAberto && !this.conversasCarregadas) {
                      this.carregarConversas();
                  }
              },

              async carregarConversas() {
                  this.carregando = true;
                  this.erro = '';

                  try {
                      const resposta = await fetch(this.urlConversas, {
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

                      this.conversas.forEach(conversa => {
                          this.inscreverCanal(conversa.chat_id);
                      });

                  } catch (erro) {
                      this.erro = erro.message;
                  } finally {
                      this.carregando = false;
                  }
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
                  const conversa = this.conversas.find(
                      item => Number(item.chat_id) === Number(mensagem.chat_id)
                  );

                  if (conversa) {
                      conversa.ultima_mensagem = mensagem.conteudo;

                      if (
                          Number(this.chatIdAtivo) === Number(mensagem.chat_id)
                      ) {
                          this.adicionarMensagem(mensagem);
                          this.rolarHistorico();
                      } else if (
                          Number(mensagem.usuario_id) !== this.usuarioId
                      ) {
                          conversa.mensagens_nao_lidas =
                              Number(conversa.mensagens_nao_lidas || 0) + 1;
                      }
                  }
              },

              async abrirConversa(conversa) {
                  this.conversaSelecionada = conversa;
                  this.mensagens = [];
                  this.erroMensagens = '';
                  this.carregandoMensagens = true;
                  this.chatIdAtivo = conversa.chat_id;

                  conversa.mensagens_nao_lidas = 0;

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

                      this.chatIdAtivo = dados.chat_id;
                      this.inscreverCanal(dados.chat_id);
                      this.mensagens = dados.mensagens || [];

                      this.$nextTick(() => this.rolarHistorico());

                  } catch (erro) {
                      this.erroMensagens = erro.message;
                  } finally {
                      this.carregandoMensagens = false;
                  }
              },

              adicionarMensagem(mensagem) {
                  if (this.mensagens.some(
                      item => Number(item.id) === Number(mensagem.id)
                  )) {
                      return;
                  }

                  this.mensagens.push(mensagem);

                  const conversa = this.conversas.find(
                      item => Number(item.chat_id) === Number(mensagem.chat_id)
                  );

                  if (conversa) {
                      conversa.ultima_mensagem = mensagem.conteudo;
                  }
              },

              async enviarMensagem() {
                  const conteudo = this.mensagemNova.trim();

                  if (
                      !conteudo ||
                      !this.conversaSelecionada ||
                      this.enviandoMensagem
                  ) {
                      return;
                  }

                  this.enviandoMensagem = true;
                  this.erroMensagens = '';

                  try {
                      const resposta = await fetch(
                          `/chat/${this.conversaSelecionada.negociacao_id}/mensagens`,
                          {
                              method: 'POST',
                              headers: {
                                  'Content-Type': 'application/json',
                                  'Accept': 'application/json',
                                  'X-CSRF-TOKEN': this.csrfToken,
                                  'X-Requested-With': 'XMLHttpRequest'
                              },
                              body: JSON.stringify({
                                  conteudo: conteudo
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
                      this.erroMensagens = erro.message;
                  } finally {
                      this.enviandoMensagem = false;
                  }
              },

              voltarConversas() {
                  this.conversaSelecionada = null;
                  this.chatIdAtivo = null;
                  this.mensagens = [];
                  this.erroMensagens = '';
              },

              rolarHistorico() {
                  const lista = this.$refs.listaMensagens;

                  if (lista) {
                      lista.scrollTop = lista.scrollHeight;
                  }
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
            }

          }"
          x-cloak
      >
          <!-- Botão flutuante -->
          <button
              type="button"
              @click="abrirChat()"
              class="relative flex h-14 w-14 items-center justify-center rounded-full bg-[#1B4D3E] text-white shadow-xl transition hover:bg-[#236350] hover:scale-105"
              aria-label="Abrir mensagens"
          >
              <span class="material-symbols-outlined text-3xl">chat</span>

              <span
                  x-show="conversas.some(c => Number(c.mensagens_nao_lidas || 0) > 0)"
                  class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-xs font-bold text-white"
                  x-text="conversas.reduce((total, c) => total + Number(c.mensagens_nao_lidas || 0), 0)"
              ></span>
          </button>

          <!-- Janela do chat -->
          <section
              x-show="chatAberto"
              x-transition
              class="absolute bottom-16 right-0 flex h-[min(560px,75vh)] w-[min(370px,calc(100vw-2rem))] flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl"
              style="display: none;"
          >
              
          <!-- Cabeçalho -->
          <div class="flex min-h-[68px] items-center justify-between bg-[#1B4D3E] px-4 py-3 text-white">

              <!-- Identificação da conversa -->
              <div class="flex min-w-0 flex-1 items-center gap-3">

                  <button
                      x-show="conversaSelecionada"
                      @click="voltarConversas()"
                      type="button"
                      class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-0 bg-transparent p-0 text-white transition-colors hover:bg-white/10"
                      aria-label="Voltar às conversas"
                  >
                      <span class="material-symbols-outlined text-[23px] leading-none">
                          arrow_back
                      </span>
                  </button>

                  <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15">
                      <span class="material-symbols-outlined text-[23px] leading-none">
                          forum
                      </span>
                  </div>

                  <div class="min-w-0">
                      <h3
                          class="truncate font-semibold"
                          x-text="conversaSelecionada ? conversaSelecionada.participante : 'Mensagens'"
                      ></h3>

                      <p
                          class="text-xs text-white/70"
                          x-text="conversaSelecionada ? 'Conversa' : 'Suas conversas'"
                      ></p>
                  </div>
              </div>

              <!-- Ações do cabeçalho -->
              <div class="ml-3 flex shrink-0 items-center gap-1">

                  <a
                      href="{{ route('chat') }}"
                      class="flex h-9 w-9 items-center justify-center rounded-full border-0 bg-transparent text-white no-underline transition-colors hover:bg-white/10"
                      title="Ver todas as conversas"
                      aria-label="Ver todas as conversas"
                  >
                      <span class="material-symbols-outlined text-[22px] leading-none">
                          open_in_new
                      </span>
                  </a>

                  <button
                      type="button"
                      @click="chatAberto = false"
                      class="flex h-9 w-9 items-center justify-center rounded-full border-0 bg-transparent p-0 text-white transition-colors hover:bg-white/10"
                      aria-label="Fechar chat"
                  >
                      <span class="material-symbols-outlined text-[23px] leading-none">
                          close
                      </span>
                  </button>

              </div>
          </div>


              <!-- Lista de conversas -->
              <div
                  x-show="!conversaSelecionada"
                  class="flex-1 overflow-y-auto bg-gray-50"
              >
                  <div
                      x-show="carregando"
                      class="p-6 text-center text-sm text-gray-500"
                  >
                      Carregando conversas...
                  </div>

                  <div
                      x-show="erro"
                      class="p-5 text-center text-sm text-red-600"
                      x-text="erro"
                  ></div>

                  <template x-for="conversa in conversas" :key="conversa.chat_id">
                      <button
                          type="button"
                          @click="abrirConversa(conversa)"
                          class="flex w-full items-center gap-3 border-b border-gray-100 px-4 py-4 text-left transition hover:bg-white"
                      >
                          <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#1B4D3E]/10 text-[#1B4D3E]">
                              <span class="material-symbols-outlined">person</span>
                          </div>

                          <div class="min-w-0 flex-1">
                              <div class="flex items-center justify-between gap-2">
                                  <span
                                      class="truncate font-semibold text-gray-800"
                                      x-text="conversa.participante"
                                  ></span>

                                  <span
                                      x-show="Number(conversa.mensagens_nao_lidas || 0) > 0"
                                      class="flex h-5 min-w-5 items-center justify-center rounded-full bg-[#236350] px-1 text-xs font-bold text-white"
                                      x-text="conversa.mensagens_nao_lidas"
                                  ></span>
                              </div>

                              <p
                                  class="mt-1 truncate text-sm text-gray-500"
                                  x-text="conversa.ultima_mensagem"
                              ></p>
                          </div>
                      </button>
                  </template>

                  <div
                      x-show="!carregando && conversas.length === 0 && !erro"
                      class="px-5 py-12 text-center"
                  >
                      <span class="material-symbols-outlined text-4xl text-gray-300">forum</span>
                      <p class="mt-2 text-sm text-gray-500">
                          Você ainda não possui conversas.
                      </p>
                  </div>
              </div>

              <!-- Conversa selecionada -->
              <div
                  x-show="conversaSelecionada"
                  class="flex min-h-0 flex-1 flex-col bg-[#f5f5f0]"
                  style="display: none;"
              >
                  <!-- Histórico -->
                  <div
                      x-ref="listaMensagens"
                      class="flex-1 space-y-3 overflow-y-auto p-3"
                  >
                      <div
                          x-show="carregandoMensagens"
                          class="py-8 text-center text-sm text-gray-500"
                      >
                          Carregando mensagens...
                      </div>

                      <div
                          x-show="erroMensagens"
                          class="rounded-lg bg-red-50 p-3 text-center text-sm text-red-600"
                          x-text="erroMensagens"
                      ></div>

                      <template x-for="mensagem in mensagens" :key="mensagem.id">
                          <div
                              class="flex"
                              :class="Number(mensagem.usuario_id) === usuarioId ? 'justify-end' : 'justify-start'"
                          >
                              <div
                                  class="max-w-[85%] rounded-2xl px-3 py-2 shadow-sm"
                                  :class="Number(mensagem.usuario_id) === usuarioId
                                      ? 'rounded-br-sm bg-[#d9f2df] text-gray-800'
                                      : 'rounded-bl-sm bg-white text-gray-800'"
                              >
                                  <p
                                      class="whitespace-pre-wrap break-words text-sm"
                                      x-text="mensagem.conteudo"
                                  ></p>

                                  <p
                                      class="mt-1 text-right text-[10px] font-medium text-gray-500"
                                      x-text="formatarDataHora(mensagem.created_at)"
                                  ></p>
                              </div>
                          </div>
                      </template>

                      <div
                          x-show="!carregandoMensagens && mensagens.length === 0 && !erroMensagens"
                          class="py-8 text-center text-sm text-gray-400"
                      >
                          Nenhuma mensagem ainda. Inicie a conversa!
                      </div>
                  </div>

                  <!-- Formulário de envio -->
                  <form
                      @submit.prevent="enviarMensagem()"
                      class="flex shrink-0 items-end gap-2 border-t border-gray-200 bg-white p-3"
                  >
                      <textarea
                          x-model="mensagemNova"
                          @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); enviarMensagem(); }"
                          rows="1"
                          maxlength="5000"
                          placeholder="Digite sua mensagem..."
                          class="max-h-24 min-h-[42px] flex-1 resize-none rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm outline-none transition focus:border-[#236350] focus:ring-1 focus:ring-[#236350]"
                      ></textarea>

                      <button
                          type="submit"
                          :disabled="!mensagemNova.trim() || enviandoMensagem"
                          class="flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-xl bg-[#236350] text-white transition hover:bg-[#1B4D3E] disabled:cursor-not-allowed disabled:opacity-40"
                          aria-label="Enviar mensagem"
                      >
                          <span
                              x-show="!enviandoMensagem"
                              class="material-symbols-outlined"
                          >send</span>

                          <span
                              x-show="enviandoMensagem"
                              class="material-symbols-outlined animate-spin"
                          >progress_activity</span>
                      </button>
                  </form>
              </div>
          </section>
      </div>
    @endauth


    @stack('scripts')


  </body>
</html>