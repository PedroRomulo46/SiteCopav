<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Meu Marketplace')</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&icon_names=search,arrow_right_alt,account_circle,forum,home,arrow_back,palette,settings,logout&display=block" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  </head>

  <body>
    <div class="drawer lg:drawer-open">
      <input id="my-drawer-4" type="checkbox" class="drawer-toggle inline"/>
      
      <div class="drawer-content min-h-screen flex flex-col">
        <!-- Navbar -->
        <nav class="navbar w-full h-20 bg-[#1B4D3E] text-white flex justify-between items-center px-4 shadow-xl">
          <label for="my-drawer-4" class="btn btn-square btn-ghost lg:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-6 h-6 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
          </label>

          <div class="text-2xl font-semibold">
            Site Copav
          </div>

          <div class="w-1/2 relative flex items-center">
            <input type="text" placeholder="Buscar lotes, produtos ou compradores..." class="input w-full bg-white text-gray-500 font-bold pl-10 pr-4">
            <span class="material-symbols-outlined absolute left-3 text-gray-500 pointer-events-none">search</span>
          </div>

          <!-- Banner de Oferta -->
          <div class="hidden md:flex items-center gap-3 bg-amber-100 hover:bg-slate-700 border border-emerald-600 text-white p-2.5 py-2 px-4 rounded-lg shadow-md transition-all cursor-pointer group">
            
            <!-- Tag Oferta -->
            <span class="bg-emerald-800 group-hover:bg-amber-400 transition-colors text-white font-bold text-xs font uppercase px-2.5 py-1 rounded-full shadow-sm shrink-0">
              Oferta
            </span>

            <!-- Informações do Produto e Preços -->
            <div class="flex items-center gap-2 text-xs">
              <span class="font-bold text-sm text-black group-hover:text-white transition-colors">Café Arábica</span>
              <span class="text-black group-hover:text-white transition-colors">|</span>
              <span class="line-through text-xs text-black group-hover:text-white transition-colors">6 Sacas de R$ 9.581,22</span>
              <span class="text-xs text-black group-hover:text-white transition-colors font-bold ml-1">Por apenas</span>
              <span class="font-extrabold text-base text-emerald-800 group-hover:text-emerald-500 transition-colors">R$ 8.980,87</span>
            </div>

            <!-- Botão Ação -->
            <div class="ml-auto bg-emerald-600 group-hover:bg-emerald-500 text-white text-xs font-semibold px-3 py-1.5 rounded-md flex items-center gap-1 transition-colors shrink-0">
              <span>Ver oferta</span>
              <span class="material-symbols-outlined text-sm transition-transform group-hover:translate-x-0.5">arrow_right_alt</span>
            </div>

          </div>
        </nav>

        <!-- Conteúdo -->
        <main class="flex-1">
          @yield('conteudo')
        </main>
      </div>

      <!-- Sidebar -->
      <div class="drawer-side is-drawer-close:overflow-visible min-h-screen">
        <label for="my-drawer-4" aria-label="close sidebar" class="drawer-overlay"></label>
        <div class="bg-[#123228] flex min-h-full flex-col items-center is-drawer-close:w-14 is-drawer-open:w-64 transition-all">

          <!-- Foto de perfil -->
          <div class="avatar p-2 py-5 flex flex-col">
            <div class="w-16 h-16 rounded-full border-2 border-[#1B4D3E] flex items-center justify-center bg-white text-gray-600 overflow-hidden">
              @if(auth()->check() && auth()->user()->imagem)
                <img src="{{ Storage::url(auth()->user()->imagem) }}" alt="Foto de perfil" class="w-full h-full object-cover">
              @else
                <span class="material-symbols-outlined text-4xl leading-none flex items-center justify-center">account_circle</span>
              @endif
            </div>
            <!-- Nome do usuário -->
            <span class="mt-2 text-sm font-semibold text-white text-center is-drawer-close:hidden truncate max-w-[180px]">
              {{ auth()->check() ? auth()->user()->nome : 'Visitante' }}
            </span>
          </div>

          <ul class="menu w-full grow px-2 gap-1">
            <li>
              <a href="{{ route('home') }}" class="is-drawer-close:tooltip is-drawer-close:tooltip-right text-white hover:bg-[#1B4D3E]" data-tip="Home">
                <span class="material-symbols-outlined">home</span>
                <span class="is-drawer-close:hidden">Home</span>
              </a>
            </li>

            @auth
            <li>
              <a href="{{ route('chat') }}" class="is-drawer-close:tooltip is-drawer-close:tooltip-right text-white hover:bg-[#1B4D3E]" data-tip="Mensagens">
                <span class="material-symbols-outlined">forum</span>
                <span class="is-drawer-close:hidden">Mensagens</span>
              </a>
            </li>
            <li>
              <a href="{{ route('profile.edit') }}" class="is-drawer-close:tooltip is-drawer-close:tooltip-right text-white hover:bg-[#1B4D3E]" data-tip="Configurações">
                <span class="material-symbols-outlined">settings</span>
                <span class="is-drawer-close:hidden">Configurações</span>
              </a>
            </li>
            <li class="mt-auto hover:bg-[#1B4D3E] rounded-sm">
              <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit"
                class="w-full text-red-600 is-drawer-close:tooltip is-drawer-close:tooltip-right inline-flex items-center"
                data-tip="Sair">
                  <span class="material-symbols-outlined">logout</span>
                  <span class="is-drawer-close:hidden font-semibold mx-2">Sair</span>
                </button>
              </form>
            </li>
            @endauth

            @guest
            <li>
              <a href="{{ route('login') }}" class="text-white is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Entrar">
                <span class="material-symbols-outlined">account_circle</span>
                <span class="is-drawer-close:hidden">Entrar / Cadastrar</span>
              </a>
            </li>
            @endguest
          </ul>
        </div>
      </div>
    </div>
  </body>
</html>