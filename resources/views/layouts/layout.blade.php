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

        /* Fundo padrão (modo claro) */
        html:not(.tema-escuro) body {
            background-color: #ebeae7;
            color: #111827;
        }

        /* Modo escuro explícito */
        html.tema-escuro body {
            background-color: #090d16;
            color: #f3f4f6;
        }

        /* Títulos e textos gerais no modo escuro */
        html.tema-escuro main h1,
        html.tema-escuro main h2,
        html.tema-escuro main h3,
        html.tema-escuro main h4,
        html.tema-escuro nav[aria-label="Breadcrumb"] {
            color: #f9fafb;
        }

        html.tema-escuro main p {
            color: #9ca3af;
        }

        /* Cartões e blocos com bg-white no modo escuro (excluindo dropdowns e busca) */
        html.tema-escuro .card,
        html.tema-escuro main .bg-white:not([x-show]) {
            background-color: #111827 !important;
            color: #f3f4f6 !important;
            border-color: #1f2937 !important;
        }

        /* Blocos internos claros (como "SUA ÚLTIMA PROPOSTA") no modo escuro */
        html.tema-escuro main .bg-gray-50 {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }

        html.tema-escuro main .bg-gray-50 p,
        html.tema-escuro main .bg-gray-50 strong {
            color: #e5e7eb !important;
        }

        /* Inputs dentro de formulários no modo escuro (sem quebrar a barra de busca do topo) */
        html.tema-escuro main input,
        html.tema-escuro main textarea,
        html.tema-escuro main select {
            background-color: #1f2937 !important;
            color: #f9fafb !important;
            border-color: #374151 !important;
        }

        /* Verde do valor mais claro e legível no modo escuro */
        html.tema-escuro .card-vitrine .text-\[\#1B4D3E\],
        html.tema-escuro .card-vitrine .text-\[\#236350\] {
            color: #34d399 !important;
        }

        /* Barras divisórias internas do card no modo escuro com tom cinza da descrição */
        html.tema-escuro .card-vitrine .border-gray-100,
        html.tema-escuro .card-vitrine .border-t {
            border-color: #9ca3af !important;
        }

        /* Dropdowns / menus flutuantes brancos mantêm texto escuro e legível */
        [x-show].bg-white,
        [x-show] .bg-white {
            background-color: #ffffff !important;
            color: #1f2937 !important;
        }

        [x-show].bg-white a,
        [x-show].bg-white button,
        [x-show] .bg-white a,
        [x-show] .bg-white button {
            color: #1f2937 !important;
        }

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
          color: #fcd34d !important;
        }

        html:not(.tema-escuro) .container-home {
            background-color: #ebeae7;
        }

        html.tema-escuro .container-home {
            background-color: transparent;
        }
    </style>

    <script>
        (function () {
            const escuro = localStorage.getItem('tema_escuro') === '1';
            const html = document.documentElement;
            if (escuro) {
                html.classList.add('tema-escuro');
                html.setAttribute('data-theme', 'dark');
            } else {
                html.classList.remove('tema-escuro');
                html.setAttribute('data-theme', 'light');
            }
        })();
    </script>

</head>

<body class="min-h-screen flex flex-col font-sans transition-colors duration-200
    @if(View::hasSection('chat_layout')) h-screen overflow-hidden @endif">

    {{-- Cabeçalho --}}
    <header
    x-data="{
        menuMobileAberto: false,
        usuarioMenuAberto: false,
        categoriasMenuAberto: false
    }"
    class="bg-[#1B4D3E] text-white shadow-md w-full"
    >
      <div class="w-full px-4 sm:px-6 lg:px-10 py-3 flex flex-col gap-3">
        
        {{-- LINHA SUPERIOR --}}
        <div class="flex items-center justify-between gap-2 md:gap-6 w-full">
          
          {{-- Logo --}}
          <a href="{{ route('home') }}" class="text-xl md:text-2xl font-bold tracking-wide shrink-0 whitespace-nowrap hover:opacity-90 transition-opacity">
            Site Copav
          </a>

        {{-- Busca Desktop Inteligente (Live Search) --}}
        <div 
            x-data="{
                termo: '{{ request('q') }}',
                resultados: [],
                aberto: false,
                carregando: false,
                async pesquisar() {
                    if (this.termo.trim().length < 2) {
                        this.resultados = [];
                        this.aberto = false;
                        return;
                    }
                    this.carregando = true;
                    this.aberto = true;
                    try {
                        const res = await fetch(`{{ route('buscar.sugestoes') }}?q=${encodeURIComponent(this.termo)}`);
                        this.resultados = await res.json();
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.carregando = false;
                    }
                }
            }"
            @click.outside="aberto = false"
            @keydown.escape.window="aberto = false"
            class="hidden sm:flex flex-1 max-w-2xl relative items-center"
        >
            <form action="{{ route('buscar') }}" method="GET" class="w-full relative flex items-center">
                <input 
                    type="text" 
                    name="q" 
                    x-model="termo"
                    @input.debounce.300ms="pesquisar()"
                    @focus="if (resultados.length > 0) aberto = true"
                    autocomplete="off"
                    placeholder="Buscar lotes, produtos ou compradores..." 
                    class="input w-full bg-white text-gray-800 placeholder-gray-400 pl-4 pr-10 py-2 rounded-md focus:outline-none shadow-sm text-sm"
                >
                <button type="submit" class="absolute right-3 text-gray-500 hover:text-gray-700 flex items-center cursor-pointer">
                    <span class="material-symbols-outlined text-xl" x-show="!carregando">search</span>
                    <span class="material-symbols-outlined text-xl animate-spin text-[#236350]" x-show="carregando" x-cloak>progress_activity</span>
                </button>
            </form>

            {{-- Dropdown Flutuante de Resultados --}}
            <div 
                x-show="aberto" 
                x-cloak
                x-transition.opacity.duration.150ms
                class="absolute left-0 right-0 top-full mt-2 bg-white text-gray-800 rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-50"
            >
                {{-- Lista de Produtos Encontrados --}}
                <template x-if="resultados.length > 0">
                    <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                        <template x-for="item in resultados" :key="item.id">
                            <a :href="item.url" class="flex items-center gap-3 p-3 hover:bg-emerald-50/70 transition-colors">
                                <img :src="item.imagem" :alt="item.nome" class="w-12 h-12 rounded-lg object-cover bg-gray-100 shrink-0 border border-gray-200">
                                <div class="flex-1 min-w-0 text-left">
                                    <p class="text-sm font-bold text-gray-900 truncate" x-text="item.nome"></p>
                                    <p class="text-xs text-gray-500 truncate">Fornecedor: <span x-text="item.fornecedor"></span></p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-sm font-extrabold text-[#236350] block" x-text="'R$ ' + item.valor"></span>
                                    <span class="text-[11px] text-gray-400" x-text="'/ ' + item.unidade"></span>
                                </div>
                            </a>
                        </template>
                    </div>
                </template>

                {{-- Estado Vazio --}}
                <template x-if="!carregando && resultados.length === 0 && termo.trim().length >= 2">
                    <div class="p-4 text-center text-sm text-gray-500">
                        Nenhum lote encontrado para "<span class="font-semibold text-gray-700" x-text="termo"></span>".
                    </div>
                </template>

                {{-- Rodapé para ver todos os resultados --}}
                <template x-if="resultados.length > 0">
                    <a :href="`{{ route('buscar') }}?q=${encodeURIComponent(termo)}`" class="block bg-gray-50 hover:bg-gray-100 text-center py-2.5 text-xs font-bold text-[#236350] border-t border-gray-200 transition-colors">
                        Ver todos os resultados para "<span x-text="termo"></span>"
                    </a>
                </template>
            </div>
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

        {{-- Busca Mobile Inteligente (Live Search) --}}
        <div 
            x-data="{
                termo: '{{ request('q') }}',
                resultados: [],
                aberto: false,
                carregando: false,
                async pesquisar() {
                    if (this.termo.trim().length < 2) {
                        this.resultados = [];
                        this.aberto = false;
                        return;
                    }
                    this.carregando = true;
                    this.aberto = true;
                    try {
                        const res = await fetch(`{{ route('buscar.sugestoes') }}?q=${encodeURIComponent(this.termo)}`);
                        this.resultados = await res.json();
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.carregando = false;
                    }
                }
            }"
            @click.outside="aberto = false"
            @keydown.escape.window="aberto = false"
            class="sm:hidden w-full relative mt-1"
        >
            <form action="{{ route('buscar') }}" method="GET" class="w-full relative flex items-center">

                {{-- text-sm ou text-[16px] evita o zoom automático do Safari no iPhone ao tocar no input --}}
                <input 
                    type="text" 
                    name="q" 
                    x-model="termo"
                    @input.debounce.300ms="pesquisar()"
                    @focus="if (resultados.length > 0) aberto = true"
                    autocomplete="off"
                    placeholder="Buscar no marketplace..." 
                    class="input w-full bg-white text-gray-800 placeholder-gray-400 pl-3 pr-9 py-2 rounded-md text-sm focus:outline-none shadow-sm"
                >
                <button type="submit" class="absolute right-2.5 text-gray-500 flex items-center cursor-pointer">
                    <span class="material-symbols-outlined text-lg" x-show="!carregando">search</span>
                    <span class="material-symbols-outlined text-lg animate-spin text-[#236350]" x-show="carregando" x-cloak>progress_activity</span>
                </button>
            </form>

            {{-- Dropdown Flutuante de Resultados Mobile --}}
            <div 
                x-show="aberto" 
                x-cloak
                x-transition.opacity.duration.150ms
                class="absolute left-0 right-0 top-full mt-1.5 bg-white text-gray-800 rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-50"
            >
                {{-- Lista de Produtos Encontrados --}}
                <template x-if="resultados.length > 0">
                    {{-- max-h-[60vh] garante que a lista caiba na tela mesmo com o teclado do celular aberto --}}
                    <div class="divide-y divide-gray-100 max-h-[60vh] overflow-y-auto overscroll-contain">
                        <template x-for="item in resultados" :key="item.id">
                            <a :href="item.url" class="flex items-center gap-2.5 p-2.5 active:bg-emerald-50 transition-colors">
                                <img :src="item.imagem" :alt="item.nome" class="w-10 h-10 rounded-lg object-cover bg-gray-100 shrink-0 border border-gray-200">
                                <div class="flex-1 min-w-0 text-left">
                                    <p class="text-xs font-bold text-gray-900 truncate" x-text="item.nome"></p>
                                    <p class="text-[11px] text-gray-500 truncate">Forn.: <span x-text="item.fornecedor"></span></p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-xs font-extrabold text-[#236350] block" x-text="'R$ ' + item.valor"></span>
                                    <span class="text-[10px] text-gray-400" x-text="'/ ' + item.unidade"></span>
                                </div>
                            </a>
                        </template>
                    </div>
                </template>

                {{-- Estado Vazio --}}
                <template x-if="!carregando && resultados.length === 0 && termo.trim().length >= 2">
                    <div class="p-3.5 text-center text-xs text-gray-500">
                        Nenhum lote encontrado para "<span class="font-semibold text-gray-700" x-text="termo"></span>".
                    </div>
                </template>

                {{-- Rodapé para ver todos os resultados --}}
                <template x-if="resultados.length > 0">
                    <a :href="`{{ route('buscar') }}?q=${encodeURIComponent(termo)}`" class="block bg-gray-50 active:bg-gray-100 text-center py-2.5 text-xs font-bold text-[#236350] border-t border-gray-200 transition-colors">
                        Ver todos os resultados (<span x-text="resultados.length"></span>)
                    </a>
                </template>
            </div>
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
            <div
                class="relative"
                @click.outside="categoriasMenuAberto = false"
                @keydown.escape.window="categoriasMenuAberto = false"
            >

                {{-- Botão Categorias --}}
                <button
                    type="button"
                    @click="categoriasMenuAberto = !categoriasMenuAberto"
                    :aria-expanded="categoriasMenuAberto"
                    class="flex items-center gap-1 nav-link transition-colors cursor-pointer py-1 focus:outline-none"
                >
                    <span>Categorias</span>

                    <span
                        class="material-symbols-outlined text-base transition-transform duration-200"
                        :class="categoriasMenuAberto ? 'rotate-180' : ''"
                    >
                        expand_more
                    </span>
                </button>

                {{-- desktop: conteúdo do dropdown de categorias --}}
                <div
                    x-show="categoriasMenuAberto"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                    class="absolute left-0 top-full mt-2 w-56 bg-white text-gray-800 rounded-xl shadow-xl border border-gray-200 p-2 z-100 max-h-60 overflow-y-auto origin-top-left"
                >
                    @auth
                        @if(auth()->user()->is_admin ||
                            auth()->user()->user_type === 'admin' ||
                            auth()->user()->email === 'copavagricola@example.com')

                            <a
                                href="{{ route('categorias.index') }}"
                                @click="categoriasMenuAberto = false"
                                class="flex items-center gap-2 px-3 py-2 rounded-md font-bold text-[#1B4D3E] hover:bg-emerald-50 transition-colors"
                            >
                                <span class="material-symbols-outlined text-base">list</span>
                                Ver Todas
                            </a>

                        @else

                            <a
                                href="{{ route('categorias.sugerir') }}"
                                @click="categoriasMenuAberto = false"
                                class="flex items-center gap-2 px-3 py-2 rounded-md font-bold text-amber-700 hover:bg-amber-50 transition-colors"
                            >
                                <span class="material-symbols-outlined text-base">add</span>
                                Sugerir Categoria
                            </a>

                        @endif
                    @else

                        <a
                            href="{{ route('login') }}"
                            @click="categoriasMenuAberto = false"
                            class="flex items-center gap-2 px-3 py-2 rounded-md font-bold text-amber-700 hover:bg-amber-50 transition-colors"
                        >
                            <span class="material-symbols-outlined text-base">add</span>
                            Sugerir Categoria
                        </a>

                    @endauth

                    <div class="border-t border-gray-100 my-1"></div>

                    {{-- categorias cadastradas (visível para todos, inclusive visitantes) --}}
                    @forelse($categorias ?? [] as $categoria)
                        <a
                            href="{{ route('categorias.show', $categoria->id) }}"
                            @click="categoriasMenuAberto = false"
                            class="block px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-emerald-50 hover:text-[#1B4D3E] transition-colors"
                        >
                            {{ $categoria->nome }}
                        </a>
                    @empty
                        <div class="text-xs text-gray-400 p-2 text-center">
                            Nenhuma categoria encontrada
                        </div>
                    @endforelse
                </div>
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
                      <span>Minhas Vendas</span>
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
                <div
                    class="relative"
                    @click.outside="usuarioMenuAberto = false"
                    @keydown.escape.window="usuarioMenuAberto = false"
                >

                    {{-- Botão do usuário --}}
                    <button
                        type="button"
                        @click="usuarioMenuAberto = !usuarioMenuAberto"
                        :aria-expanded="usuarioMenuAberto"
                        class="flex items-center gap-2 cursor-pointer nav-link transition-colors focus:outline-none"
                    >

                        <div class="w-7 h-7 rounded-full overflow-hidden bg-white text-gray-700 flex items-center justify-center shrink-0">

                            @if(auth()->user()->imagem)
                                <img
                                    src="{{ Storage::url(auth()->user()->imagem) }}"
                                    alt="Perfil"
                                    class="w-full h-full object-cover"
                                >
                            @else
                                <span class="material-symbols-outlined text-xl">
                                    account_circle
                                </span>
                            @endif

                        </div>

                        <span class="font-semibold max-w-120px truncate">
                            {{ auth()->user()->nome ?? auth()->user()->name }}
                        </span>

                        <span
                            class="material-symbols-outlined text-base transition-transform duration-200"
                            :class="usuarioMenuAberto ? 'rotate-180' : ''"
                        >
                            expand_more
                        </span>

                    </button>

                    {{-- Dropdown --}}
                    <div
                        x-show="usuarioMenuAberto"
                        x-cloak
                        x-transition.origin.top.right
                        class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-200 p-2 z-100"
                    >

                        {{-- Perfil --}}
                        <a
                            href="{{ route('profile.show') }}"
                            class="flex items-center gap-2 w-full px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100 hover:text-[#1B4D3E] transition-colors duration-150"
                        >
                            <span class="material-symbols-outlined text-lg">
                                person
                            </span>

                            <span>Meu Perfil</span>
                        </a>

                        {{-- Separador --}}
                        <div class="border-t border-gray-100 my-1"></div>

                        {{-- Configurações --}}
                        <a
                            href="{{ route('profile.edit') }}"
                            class="flex items-center gap-2 w-full px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100 hover:text-[#1B4D3E] transition-colors duration-150"
                        >
                            <span class="material-symbols-outlined text-lg">
                                settings
                            </span>

                            <span>Configurações</span>
                        </a>

                        {{-- Separador --}}
                        <div class="border-t border-gray-100 my-1"></div>

                        {{-- Sair --}}
                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                            class="w-full"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="flex items-center gap-2 w-full px-3 py-2 rounded-md text-left text-error hover:bg-red-50 transition-colors duration-150 cursor-pointer"
                            >
                                <span class="material-symbols-outlined text-lg">
                                    logout
                                </span>

                                <span>Sair</span>
                            </button>

                        </form>

                    </div>

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

            {{-- mobile: dropdown de categorias --}}
            <div x-data="{ openCat: false }">
                <button
                    type="button"
                    @click="openCat = !openCat"
                    :aria-expanded="openCat"
                    class="flex items-center justify-between w-full p-2 hover:bg-[#236350] rounded-md text-left"
                >
                    <span>Categorias</span>

                    <span
                        class="material-symbols-outlined text-sm transition-transform duration-200"
                        :class="openCat ? 'rotate-180' : ''"
                    >
                        expand_more
                    </span>
                </button>

                <div
                    x-show="openCat"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="pl-4 flex flex-col gap-1 mt-1"
                >
                    @auth
                        @if(auth()->user()->is_admin || auth()->user()->user_type === 'admin' || auth()->user()->email === 'copavagricola@example.com')
                            <a
                                href="{{ route('categorias.index') }}"
                                class="p-1.5 text-md text-amber-200 hover:bg-[#236350] rounded font-semibold flex items-center gap-1"
                            >
                                - Ver Todas -
                            </a>
                        @else
                            <a
                                href="{{ route('categorias.sugerir') }}"
                                class="p-1.5 text-md hover:bg-[#236350] rounded font-semibold flex items-center gap-1"
                            >
                                <span class="material-symbols-outlined text-sm">add</span>
                                Sugerir Categoria
                            </a>
                        @endif
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="p-1.5 text-md hover:bg-[#236350] rounded font-semibold flex items-center gap-1"
                        >
                            <span class="material-symbols-outlined text-sm">add</span>
                            Sugerir Categoria
                        </a>
                    @endauth

                    @foreach($categorias ?? [] as $categoria)
                        <a
                            href="{{ route('categorias.show', $categoria->id) }}"
                            class="p-1.5 text-md hover:bg-[#236350] rounded"
                        >
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
                  <span>Minhas Compras</span>
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
                      <span>Minhas Vendas</span>
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

              {{-- Botão de Perfil --}}
              <a href="{{ route('profile.show') }}"
                class="flex w-full items-center gap-2 p-2 rounded-md
                        text-white hover:bg-[#236350]
                        transition-colors duration-200">
                  <span class="material-symbols-outlined text-lg">person</span>
                  <span>Perfil</span>
              </a>

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
        overflow-hidden p-0
    @else
        px-4 sm:px-6 lg:px-10 py-6
    @endif">
    @yield('conteudo')
</main>

    {{-- Chat flutuante global do COPAV --}}
    @unless(View::hasSection('chat_layout'))
    @auth
      <div
          id="copav-chat-global"
          class="fixed bottom-5 right-5 z-100"
          x-init="inicializarChat()"
          x-data="{
              chatAberto: false,
              conversas: [],
              carregando: false,
              conversasCarregadas: false,
              erro: '',
              conversaSelecionada: null,
              assuntoSelecionadoId: null,
              cardNegociacaoAberto: false,
              mensagens: [],
              carregandoMensagens: false,
              erroMensagens: '',
              mensagemNova: '',
              enviandoMensagem: false,
              usuarioId: {{ (int) auth()->id() }},
              csrfToken: '{{ csrf_token() }}',
              urlConversas: '{{ route('chat.conversas') }}',
              totalNaoLidas: 0,
              chatIdAtivo: null,
              canaisInscritos: [],
              chaveEstadoChat: 'sitecopav_chat_estado_{{ auth()->id() }}',
  
              salvarEstadoChat() {
                  sessionStorage.setItem(this.chaveEstadoChat, JSON.stringify({
                      chatId: this.chatIdAtivo,
                      assuntoId: this.assuntoSelecionadoId,
                      chatAberto: this.chatAberto
                  }));
              },

              abrirChat() {
                  this.chatAberto = !this.chatAberto;
                  this.salvarEstadoChat();

                  if (this.chatAberto && !this.conversasCarregadas) {
                      this.carregarConversas();
                  }
              },

              async carregarConversas() {
                  if (this.carregando) return;

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
                      this.conversas.forEach(conversa => {
                      this.inscreverCanalGlobal(conversa.chat_id);
                  });

                    this.atualizarTotalNaoLidas();
                    this.conversasCarregadas = true;

                  } catch (erro) {
                      this.erro = erro.message;
                  } finally {
                      this.carregando = false;
                  }
              },

              async inicializarChat() {
                await this.carregarConversas();

                const estadoSalvo = sessionStorage.getItem(this.chaveEstadoChat);

                if (!estadoSalvo) return;

                try {
                    const estado = JSON.parse(estadoSalvo);

                    // Restaura primeiro o estado de abertura.
                    this.chatAberto = Boolean(estado.chatAberto);

                    // É válido ter o painel aberto sem uma conversa selecionada.
                    if (estado.chatId === null || estado.chatId === undefined) {
                        return;
                    }

                    const conversa = this.conversas.find(
                        item => Number(item.chat_id) === Number(estado.chatId)
                    );

                    if (!conversa) {
                        sessionStorage.removeItem(this.chaveEstadoChat);
                        return;
                    }

                    const assuntoExiste = estado.assuntoId === null ||
                        conversa.assuntos?.some(
                            item => Number(item.id) === Number(estado.assuntoId)
                        );

                    if (!assuntoExiste) {
                        sessionStorage.removeItem(this.chaveEstadoChat);
                        return;
                    }

                    await this.abrirConversa(conversa, estado.assuntoId);

                    // Garante que o estado original seja mantido.
                    this.chatAberto = Boolean(estado.chatAberto);

                } catch (erro) {
                    console.error('Erro ao restaurar o chat:', erro);
                    sessionStorage.removeItem(this.chaveEstadoChat);
                }
            },

              atualizarTotalNaoLidas() {
                this.totalNaoLidas = this.conversas.reduce(
                    (total, conversa) => {
                        const naoLidasGerais = Number(
                            conversa.mensagens_gerais?.mensagens_nao_lidas || 0
                        );

                        const naoLidasAssuntos = (conversa.assuntos || []).reduce(
                            (subtotal, assunto) =>
                                subtotal + Number(assunto.mensagens_nao_lidas || 0),
                            0
                        );

                        return total + naoLidasGerais + naoLidasAssuntos;
                    },
                    0
                );
            },

              
              inscreverCanalGlobal(chatId) {
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

                if (!conversa) return;

                const assuntoId = mensagem.assunto_chat_id
                    ? Number(mensagem.assunto_chat_id)
                    : null;

                // Atualiza a última mensagem geral ou do assunto correspondente.
                let contexto;

                if (assuntoId === null) {
                    contexto = conversa.mensagens_gerais;
                } else {
                    contexto = conversa.assuntos?.find(
                        item => Number(item.id) === assuntoId
                    );
                }

                if (!contexto) return;

                contexto.ultima_mensagem = mensagem.conteudo;
                contexto.ultima_mensagem_usuario = mensagem.usuario_id;
                contexto.ultima_mensagem_data = mensagem.created_at;

                // Atualiza também a última mensagem geral da conversa.
                conversa.ultima_mensagem = mensagem.conteudo;
                conversa.ultima_mensagem_usuario = mensagem.usuario_id;
                conversa.ultima_mensagem_data = mensagem.created_at;
                conversa.ultima_mensagem_assunto_chat_id = assuntoId;

                // Mensagens enviadas pelo próprio usuário não geram notificações.
                if (Number(mensagem.usuario_id) === Number(this.usuarioId)) {
                    return;
                }

                // Verifica se o usuário está exatamente no contexto que recebeu a mensagem.
                const mesmoContexto =
                    this.chatAberto &&
                    Number(this.chatIdAtivo) === Number(mensagem.chat_id) &&
                    (
                        (this.assuntoSelecionadoId === null && assuntoId === null) ||
                        Number(this.assuntoSelecionadoId) === Number(assuntoId)
                    );

                if (mesmoContexto) {
                    this.adicionarMensagem(mensagem);
                    this.rolarHistorico();
                } else {
                    contexto.mensagens_nao_lidas =
                        Number(contexto.mensagens_nao_lidas || 0) + 1;
                }

                this.atualizarTotalNaoLidas();
            },


              
            async abrirConversa(conversa, assuntoId = null) {
                this.conversaSelecionada = conversa;
                this.assuntoSelecionadoId =
                    assuntoId === null ? null : Number(assuntoId);
                this.cardNegociacaoAberto = false;
                this.mensagens = [];
                this.erroMensagens = '';
                this.carregandoMensagens = true;
                this.chatIdAtivo = Number(conversa.chat_id);
                this.salvarEstadoChat();

                const lista = this.$refs.listaMensagens;

                if (lista) {
                    lista.style.visibility = 'hidden';
                    lista.scrollTop = 0;
                }

                try {
                    const url = new URL(
                        `/chat/${conversa.chat_id}/mensagens`,
                        window.location.origin
                    );

                    if (this.assuntoSelecionadoId !== null) {
                        url.searchParams.set(
                            'assunto_chat_id',
                            this.assuntoSelecionadoId
                        );
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

                    this.inscreverCanalGlobal(dados.chat_id);
                    this.mensagens = dados.mensagens || [];

                    if (this.assuntoSelecionadoId === null) {
                    if (conversa.mensagens_gerais) {
                        conversa.mensagens_gerais.mensagens_nao_lidas = 0;
                    }
                } else {
                    const assunto = conversa.assuntos?.find(
                        item => Number(item.id) === this.assuntoSelecionadoId
                    );

                    if (assunto) {
                        assunto.mensagens_nao_lidas = 0;
                    }
                }

                this.atualizarTotalNaoLidas();

                    this.atualizarTotalNaoLidas();

                    this.$nextTick(() => this.rolarHistorico());

                } catch (erro) {
                    console.error('Erro ao carregar mensagens:', erro);
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

                if (!conversa) return;

                const assuntoId = mensagem.assunto_chat_id
                    ? Number(mensagem.assunto_chat_id)
                    : null;

                const contexto = assuntoId === null
                    ? conversa.mensagens_gerais
                    : conversa.assuntos?.find(
                        item => Number(item.id) === assuntoId
                    );

                if (contexto) {
                    contexto.ultima_mensagem = mensagem.conteudo;
                    contexto.ultima_mensagem_usuario = mensagem.usuario_id;
                    contexto.ultima_mensagem_data = mensagem.created_at;
                }

                conversa.ultima_mensagem = mensagem.conteudo;
                conversa.ultima_mensagem_usuario = mensagem.usuario_id;
                conversa.ultima_mensagem_data = mensagem.created_at;
                conversa.ultima_mensagem_assunto_chat_id = assuntoId;
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
                                conteudo: conteudo,
                                assunto_chat_id: this.assuntoSelecionadoId
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
                    console.error('Erro ao enviar mensagem:', erro);
                    this.erroMensagens = erro.message;
                } finally {
                    this.enviandoMensagem = false;
                }
              },

              voltarConversas() {
                this.conversaSelecionada = null;
                this.chatIdAtivo = null;
                this.assuntoSelecionadoId = null;
                this.mensagens = [];
                this.erroMensagens = '';
                this.cardNegociacaoAberto = false;

                this.salvarEstadoChat();
            },

              rolarHistorico() {
                  const lista = this.$refs.listaMensagens;

                  if (lista) {
                      lista.scrollTop = lista.scrollHeight;
                      lista.style.visibility = 'visible';
                  }
              },

              textoPrevia(contexto) {
                if (!contexto) return 'Nenhuma mensagem ainda';

                const mensagem = contexto.ultima_mensagem
                    || 'Nenhuma mensagem ainda';

                return Number(contexto.ultima_mensagem_usuario) === Number(this.usuarioId)
                    ? `Você: ${mensagem}`
                    : mensagem;
            },

            formatarHoraLista(data) {
                if (!data) return '';

                const dataMensagem = new Date(data);

                if (isNaN(dataMensagem.getTime())) return '';

                const agora = new Date();

                const mesmaData =
                    dataMensagem.toDateString() === agora.toDateString();

                if (mesmaData) {
                    return dataMensagem.toLocaleTimeString('pt-BR', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }

                const ontem = new Date();
                ontem.setDate(ontem.getDate() - 1);

                if (dataMensagem.toDateString() === ontem.toDateString()) {
                    return 'Ontem';
                }

                return dataMensagem.toLocaleDateString('pt-BR', {
                    day: '2-digit',
                    month: '2-digit'
                });
            },
              
            itensCaixaEntrada() {
                const itens = [];

                this.conversas.forEach(conversa => {
                    const geral = conversa.mensagens_gerais || {};

                    // Item das mensagens gerais
                    itens.push({
                        chave: `${conversa.chat_id}-geral`,
                        conversa: conversa,
                        assuntoId: null,
                        participante: conversa.participante,
                        titulo: 'Mensagens gerais',
                        imagem: null,
                        contexto: geral,
                        data: geral.ultima_mensagem_data,
                        naoLidas: Number(geral.mensagens_nao_lidas || 0),
                    });

                    // Um item independente para cada assunto
                    (conversa.assuntos || []).forEach(assunto => {
                        itens.push({
                            chave: `${conversa.chat_id}-assunto-${assunto.id}`,
                            conversa: conversa,
                            assuntoId: Number(assunto.id),
                            participante: conversa.participante,
                            titulo: assunto.produtos?.[0]?.nome || assunto.titulo,
                            imagem: assunto.produtos?.[0]?.imagem || null,
                            contexto: assunto,
                            data: assunto.ultima_mensagem_data,
                            naoLidas: Number(assunto.mensagens_nao_lidas || 0),
                        });
                    });
                });

                // Mais recentes primeiro
                return itens.sort((a, b) => {
                    const dataA = a.data ? new Date(a.data).getTime() : 0;
                    const dataB = b.data ? new Date(b.data).getTime() : 0;

                    return dataB - dataA;
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
                id="copav-chat-contador"
                x-show="totalNaoLidas > 0"
                x-text="totalNaoLidas > 99 ? '99+' : totalNaoLidas"
                x-cloak
                class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white"
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
          <div class="flex min-h-68px items-center justify-between bg-[#1B4D3E] px-4 py-3 text-white">

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

                <div class="min-w-0 flex-1">
                    <div class="flex min-w-0 items-center gap-1.5">
                        <h3
                            class="truncate font-semibold"
                            x-text="conversaSelecionada ? conversaSelecionada.participante : 'Mensagens'"
                        ></h3>

                    </div>

                    <p
                        class="truncate text-xs text-white/70"
                        x-text="conversaSelecionada ? 'Conversa' : 'Suas conversas'"
                    ></p>
                </div>
            </div>


              <!-- Ações do cabeçalho -->
              <div class="ml-3 flex shrink-0 items-center gap-1">

                  <a
                    :href="conversaSelecionada
                        ? `/chat?chat_id=${conversaSelecionada.chat_id}${
                            assuntoSelecionadoId !== null
                                ? `&assunto_chat_id=${assuntoSelecionadoId}`
                                : ''
                        }`
                        : '{{ route('chat') }}'"
                    class="flex h-9 w-9 items-center justify-center rounded-full border-0 bg-transparent text-white no-underline transition-colors hover:bg-white/10"
                    title="Abrir no chat completo"
                    aria-label="Abrir no chat completo"
                >
                    <span class="material-symbols-outlined text-[22px] leading-none">
                        open_in_new
                    </span>
                </a>

                  <button
                      type="button"
                      @click="chatAberto = false; salvarEstadoChat()"
                      class="flex h-9 w-9 items-center justify-center rounded-full border-0 bg-transparent p-0 text-white transition-colors hover:bg-white/10"
                      aria-label="Fechar chat"
                  >
                      <span class="material-symbols-outlined text-[23px] leading-none">
                          close
                      </span>
                  </button>

              </div>
          </div>

          <!-- Acesso às negociações do assunto -->
        <div
            x-show="
                conversaSelecionada &&
                assuntoSelecionadoId !== null &&
                (
                    conversaSelecionada?.assuntos?.find(
                        item => Number(item.id) === Number(assuntoSelecionadoId)
                    )?.links_negociacoes || []
                ).length > 0
            "
            x-cloak
            class="shrink-0 border-b border-gray-200 bg-white px-4 py-2"
        >
            <button
                type="button"
                @click="cardNegociacaoAberto = !cardNegociacaoAberto"
                class="flex w-full items-center justify-between rounded-lg px-2 py-2 text-sm font-medium text-[#236350] transition hover:bg-[#E5EFEA]/60"
            >
                <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[19px]">
                        handshake
                    </span>
                    Negociações vinculadas
                </span>

                <span
                    class="material-symbols-outlined text-[20px] transition-transform"
                    :class="cardNegociacaoAberto ? 'rotate-180' : ''"
                >
                    expand_more
                </span>
            </button>

            <div
                x-show="cardNegociacaoAberto"
                x-transition
                x-cloak
                class="mt-1 space-y-2 pb-2"
            >
                <template
                    x-for="negociacao in (
                        conversaSelecionada?.assuntos?.find(
                            item => Number(item.id) === Number(assuntoSelecionadoId)
                        )?.links_negociacoes || []
                    )"
                    :key="negociacao.id"
                >
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                        <div class="flex items-start gap-2">
                            <span class="material-symbols-outlined mt-0.5 text-[20px] text-[#236350]">
                                handshake
                            </span>

                            <div class="min-w-0 flex-1">
                                <p
                                    class="text-sm font-semibold text-gray-800"
                                    x-text="negociacao.titulo"
                                ></p>

                                <p
                                    class="mt-1 truncate text-xs text-gray-500"
                                    x-text="negociacao.produto"
                                ></p>
                            </div>
                        </div>

                        <a
                            :href="negociacao.url"
                            class="mt-3 flex w-full items-center justify-center gap-2 rounded-lg bg-[#236350] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#1B4D3E]"
                        >
                            <span class="material-symbols-outlined text-[17px]">
                                open_in_new
                            </span>
                            Acessar negociação
                        </a>
                    </div>
                </template>
            </div>
        </div>              
        
        <!-- Lista de conversas -->
        <div
            x-show="!conversaSelecionada"
            class="min-h-0 flex-1 overflow-y-auto bg-white"
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

            <template
                x-for="item in itensCaixaEntrada()"
                :key="item.chave"
            >
                <button
                    type="button"
                    @click="abrirConversa(item.conversa, item.assuntoId)"
                    class="flex w-full items-center gap-3 border-b border-gray-100 px-4 py-3 text-left transition hover:bg-gray-50"
                >
                    <!-- Imagem ou ícone -->
                    <template x-if="item.imagem">
                        <img
                            :src="item.imagem"
                            :alt="item.titulo"
                            class="h-10 w-10 shrink-0 rounded-full object-cover"
                        >
                    </template>

                    <template x-if="!item.imagem">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#E5EFEA] text-[#236350]">
                            <span
                                class="material-symbols-outlined text-[21px]"
                                x-text="item.assuntoId === null ? 'forum' : 'inventory_2'"
                            ></span>
                        </div>
                    </template>

                    <!-- Informações -->
                    <div class="min-w-0 flex-1">
                        <div class="flex min-w-0 items-center justify-between gap-2">
                            <p
                                class="truncate text-sm font-semibold text-gray-800"
                                x-text="item.participante"
                            ></p>

                            <span
                                class="shrink-0 text-[10px] text-gray-400"
                                x-text="formatarHoraLista(item.data)"
                            ></span>
                        </div>

                        <p
                            class="mt-0.5 truncate text-xs font-medium text-[#236350]"
                            x-text="item.titulo"
                        ></p>

                        <div class="mt-0.5 flex items-center justify-between gap-2">
                            <p
                                class="truncate text-xs text-gray-500"
                                x-text="textoPrevia(item.contexto)"
                            ></p>

                            <span
                                x-show="item.naoLidas > 0"
                                class="flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full bg-[#236350] px-1 text-[10px] font-bold text-white"
                                x-text="item.naoLidas > 99 ? '99+' : item.naoLidas"
                            ></span>
                        </div>
                    </div>
                </button>
            </template>

            <div
                x-show="!carregando && conversas.length === 0 && !erro"
                class="px-5 py-12 text-center"
            >
                <span class="material-symbols-outlined text-4xl text-gray-300">
                    forum
                </span>

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
                    class="min-h-0 flex-1 space-y-4 overflow-y-auto overscroll-contain px-4 py-5 sm:px-6"
                    style="visibility: hidden;"
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
                                      class="whitespace-pre-wrap wrap-break-words text-sm"
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
                          class="h-11 min-h-11 flex-1 resize-none rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm outline-none transition focus:border-[#236350] focus:ring-1 focus:ring-[#236350]"
                      ></textarea>

                      <button
                          type="submit"
                          :disabled="!mensagemNova.trim() || enviandoMensagem"
                          class="flex h-11 w-11 shrink-0 items-center justify-center self-end rounded-xl bg-[#236350] text-white transition hover:bg-[#1B4D3E] disabled:cursor-not-allowed disabled:opacity-40"
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
    @endunless

    @stack('scripts')


  </body>
</html>