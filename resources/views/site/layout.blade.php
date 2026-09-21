<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Meu Marketplace')</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&icon_names=search,arrow_right_alt,account_circle,forum,home,arrow_back,palette,settings,logout&display=block" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  </head>

  <body>
    <div class="drawer lg:drawer-open">
      <input id="my-drawer-4" type="checkbox" class="drawer-toggle inline" />
      
      <div class="drawer-content min-h-screen flex flex-col">
        <!-- Navbar -->
        <nav class="navbar w-full h-20 bg-[#79A961] text-white flex justify-between items-center px-4 shadow-xl">
          <label for="my-drawer-4" class="btn btn-square btn-ghost lg:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-6 h-6 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
          </label>

          <div class="text-2xl font-semibold">
            Olá, {{ auth()->check() ? auth()->user()->name : 'Visitante' }}!
          </div>

          <div class="w-1/3 relative flex items-center">
            <input type="text" placeholder="Buscar lotes, produtos ou compradores..." class="input w-full bg-white text-gray-500 font-bold pl-10 pr-4">
            <span class="material-symbols-outlined absolute left-3 text-gray-500 pointer-events-none">search</span>
          </div>

          <div class="hidden md:flex items-center gap-3 bg-emerald-800/60 backdrop-blur-md text-white p-1.5 pr-4 rounded-full border border-emerald-500/30 shadow-sm hover:bg-emerald-800/80 transition-all cursor-pointer">
            <span class="bg-amber-400 text-emerald-950 text-[10px] font-black uppercase px-2 py-1 rounded-full shadow-xs">Oferta</span>
            <div class="flex items-center gap-2 text-xs">
              <span class="font-semibold text-emerald-100 text-[15px]">Café Arabica</span>
              <span class="text-white">|</span>
              <span class="text-gray-400 line-through text-[15px]">6 Sacas de R$ 9.581,22</span>
              <span class="font-bold text-amber-300 text-lg">Por apenas R$ 8.980,87</span>
            </div>
            <span class="material-symbols-outlined">arrow_right_alt</span>
          </div>
        </nav>

        <!-- AQUI ENTRA O CONTEÚDO DAS OUTRAS PÁGINAS -->
        <main class="flex-1">
          @yield('conteudo')
        </main>
      </div>

      <!-- Sidebar -->
      <div class="drawer-side is-drawer-close:overflow-visible min-h-screen">
        <label for="my-drawer-4" aria-label="close sidebar" class="drawer-overlay"></label>
        <div class="bg-[#DDD8CC] flex min-h-full flex-col items-center is-drawer-close:w-14 is-drawer-open:w-64 transition-all">

          <div class="avatar p-2 py-5">
            <div class="w-14 h-14 rounded-full border-2 border-[#79A961] flex items-center justify-center bg-white text-gray-600 overflow-hidden">
              @if(auth()->check() && auth()->user()->imagem)
                <img src="{{ asset('storage/' . auth()->user()->imagem) }}" alt="Foto de perfil" class="w-full h-full object-cover">
              @else
                <span class="material-symbols-outlined text-4xl leading-none flex items-center justify-center">account_circle</span>
              @endif
            </div>
          </div>

          <ul class="menu w-full grow px-2 gap-1">
            <li>
              <a href="{{ route('home') }}" class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Home">
                <span class="material-symbols-outlined">home</span>
                <span class="is-drawer-close:hidden">Home</span>
              </a>
            </li>

            @auth
            <li>
              <a href="{{ route('chat') }}" class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Mensagens">
                <span class="material-symbols-outlined">forum</span>
                <span class="is-drawer-close:hidden">Mensagens</span>
              </a>
            </li>
            <li>
              <a href="{{ route('profile.edit') }}" class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Configurações">
                <span class="material-symbols-outlined">settings</span>
                <span class="is-drawer-close:hidden">Configurações</span>
              </a>
            </li>
            <li class="mt-auto">
              <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full text-red-600 is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Sair">
                  <span class="material-symbols-outlined">logout</span>
                  <span class="is-drawer-close:hidden font-semibold">Sair</span>
                </button>
              </form>
            </li>
            @endauth

            @guest
            <li>
              <a href="{{ route('login') }}" class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Entrar">
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