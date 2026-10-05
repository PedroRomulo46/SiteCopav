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

          {{-- Banner Oferta --}}
          <div class="xl:flex hidden items-center gap-3 bg-amber-100 hover:bg-slate-700 border border-emerald-700 p-1.5 px-3 rounded-full shadow-md transition-all cursor-pointer group shrink-0">
            <span class="bg-emerald-800 group-hover:bg-amber-400 transition-colors text-white font-bold text-[10px] uppercase px-2 py-0.5 rounded-full shrink-0">
              Oferta
            </span>
            <div class="flex items-center gap-1.5 text-xs">
              <span class="font-bold text-black group-hover:text-white transition-colors">Café Arábica</span>
              <span class="text-black group-hover:text-white transition-colors">|</span>
              <span class="line-through text-[11px] text-black group-hover:text-white transition-colors">R$ 9.581,22</span>
              <span class="font-extrabold text-sm text-emerald-800 group-hover:text-emerald-500 transition-colors">R$ 8.980,87</span>
            </div>
            <div class="ml-auto bg-emerald-600 group-hover:bg-emerald-500 text-white text-[11px] font-semibold px-2.5 py-1 rounded-full flex items-center gap-1 transition-colors shrink-0">
              <span>Ver Oferta</span>
              <span class="material-symbols-outlined text-xs">arrow_right_alt</span>
            </div>
          </div>

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
                <a href="{{ route('ofertas.index') }}" class="nav-link transition-colors">Lotes</a>
                <a href="{{ route('demandas.index') }}" class="nav-link transition-colors">Demandas</a>
              @endif
            @endauth
            
            {{-- Dropdown Categorias --}}
            <div class="dropdown relative">
              <div tabindex="0" role="button" class="flex items-center gap-1 hover:text-amber-300 transition-colors cursor-pointer py-1">
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
                    <a href="{{ route('categorias.create') }}" class="font-bold text-amber-700 hover:bg-amber-50">
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

              <a href="{{ route('negociacoes.index') }}" class="flex items-center gap-1.5 hover:text-amber-300 transition-colors">
                <span class="material-symbols-outlined text-lg">forum</span>
                <span>Minhas negociações</span>
              </a>

              @if(!auth()->user()->fornecedor)
                <a href="{{ route('fornecedores.create') }}" class="flex items-center gap-1.5 hover:text-amber-300 transition-colors">
                  <span class="material-symbols-outlined text-lg">patient_list</span>
                  <span>Virar Fornecedor</span>
                </a>
              @else
                <a href="{{ route('fornecedores.show', auth()->user()->fornecedor) }}" class="flex items-center gap-1.5 hover:text-amber-300 transition-colors">
                  <span class="material-symbols-outlined text-lg">patient_list</span>
                  <span>Meu cadastro</span>
                </a>
              @endif

              @if(auth()->user()->is_admin || auth()->user()->user_type === 'admin' || auth()->user()->email === 'copavagricola@example.com')
                <a href="{{ route('demandas.create') }}" class="flex items-center gap-1.5 hover:text-amber-300 transition-colors">
                  <span class="material-symbols-outlined text-lg">add_circle</span>
                  <span>Criar Demanda</span>
                </a>
              @endif

              {{-- Menu do Usuário Logado --}}
              <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="flex items-center gap-2 cursor-pointer hover:text-amber-300 transition-colors">
                  <div class="w-7 h-7 rounded-full overflow-hidden bg-white text-gray-700 flex items-center justify-center shrink-0">
                    @if(auth()->user()->imagem)
                      <img src="{{ Storage::url(auth()->user()->imagem) }}" alt="Perfil" class="w-full h-full object-cover">
                    @else
                      <span class="material-symbols-outlined text-xl">account_circle</span>
                    @endif
                  </div>
                  <span class="font-semibold max-w-[120px] truncate">{{ auth()->user()->nome ?? auth()->user()->name }}</span>
                </div>
                
                <ul tabindex="0" class="dropdown-content menu bg-white text-gray-800 rounded-box z-50 w-48 p-2 shadow-lg mt-2">
                  <li>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2">
                      <span class="material-symbols-outlined text-base">settings</span>
                      Configurações
                    </a>
                  </li>
                  <li>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                      @csrf
                      <button type="submit" class="w-full flex items-center gap-2 text-red-600 rounded-md text-left">
                        <span class="material-symbols-outlined text-base">logout</span>
                        Sair
                      </button>
                    </form>
                  </li>
                </ul>
              </div>
            @endauth

            @guest
              <div class="flex items-center gap-3">
                <a href="{{ route('register') }}" class="hover:text-amber-300 transition-colors">Crie a sua conta</a>
                <span class="text-slate-300">|</span>
                <a href="{{ route('login') }}" class="hover:text-amber-300 transition-colors font-semibold">Entre</a>
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
                <a href="{{ route('ofertas.index') }}" class="hover:bg-[#236350] p-2 rounded-md">Lotes</a>
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
                    <a href="{{ route('categorias.index') }}" class="p-1.5 text-xs text-amber-300 hover:bg-[#236350] rounded font-semibold flex items-center gap-1">
                      <span class="material-symbols-outlined text-sm">list</span>
                      Ver Todas
                    </a>
                  @else
                    <a href="{{ route('categorias.create') }}" class="p-1.5 text-xs text-amber-300 hover:bg-[#236350] rounded font-semibold flex items-center gap-1">
                      <span class="material-symbols-outlined text-sm">add_circle</span>
                      Sugerir Categoria
                    </a>
                  @endif
                @else
                  <a href="{{ route('categorias.create') }}" class="p-1.5 text-xs text-amber-300 hover:bg-[#236350] rounded font-semibold flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">add_circle</span>
                    Sugerir Categoria
                  </a>
                @endauth

                @foreach($categorias ?? [] as $categoria)
                  <a href="{{ route('categorias.show', $categoria->id) }}" class="p-1.5 text-xs hover:bg-[#236350] rounded">
                    {{ $categoria->nome }}
                  </a>
                @endforeach
              </div>
            </div>
          </nav>

          <div class="border-t border-[#236350] pt-2 flex flex-col gap-1">
            @auth

              <a href="{{ route('negociacoes.index') }}" class="flex items-center gap-2 p-2 hover:bg-[#236350] rounded-md">
                <span class="material-symbols-outlined text-lg">forum</span>
                <span>Minhas negociações</span>
              </a>

              @if(!auth()->user()->fornecedor)
                <a href="{{ route('fornecedores.create') }}" class="flex items-center gap-2 p-2 hover:bg-[#236350] rounded-md">
                  <span class="material-symbols-outlined text-lg">patient_list</span>
                  <span>Virar Fornecedor</span>
                </a>
              @else
                <a href="{{ route('fornecedores.show', auth()->user()->fornecedor) }}" class="flex items-center gap-2 p-2 hover:bg-[#236350] rounded-md">
                  <span class="material-symbols-outlined text-lg">patient_list</span>
                  <span>Meu Fornecedor</span>
                </a>
              @endif

              @if(auth()->user()->is_admin || auth()->user()->user_type === 'admin' || auth()->user()->email === 'copavagricola@example.com')
                <a href="{{ route('demandas.create') }}" class="flex items-center gap-2 p-2 hover:bg-[#236350] rounded-md">
                  <span class="material-symbols-outlined text-lg">add_circle</span>
                  <span>Criar Demanda</span>
                </a>
              @endif

              <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 p-2 hover:bg-[#236350] rounded-md">
                <span class="material-symbols-outlined text-lg">settings</span>
                <span>Configurações</span>
              </a>

              <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 p-2 hover:bg-[#236350] rounded-md text-left text-red-300 hover:text-red-100">
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

    @stack('scripts')

  </body>
</html>