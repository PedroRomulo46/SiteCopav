<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&icon_names=search,arrow_right_alt,palette,settings&display=block" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  </head>

  <body>
    <div class="drawer lg:drawer-open">
      <!-- Controller do drawer -->
      <input id="my-drawer-4" type="checkbox" class="drawer-toggle inline" />
      
      <!-- Conteúdo principal -->
      <div class="drawer-content flex flex-col">

        <!-- Navbar -->
        <nav class="navbar w-full h-20 text-white flex justify-between items-center px-4 shadow-xl"  style="background-color: #79A961">
          <!-- Botão para abrir menu no mobile -->
          <label for="my-drawer-4" class="btn btn-square btn-ghost lg:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-6 h-6 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
          </label>

          <!-- Saudação -->
          <div class="text-2xl">Olá, Carlos!</div>

          <!-- Campo de busca -->
          <div class="w-1/3 relative flex items-center">
            <input type="text" placeholder="Buscar lotes, produtos ou compradores..." class="input w-full bg-white text-gray-500 font-bold pl-10 pr-4">
            <span class="material-symbols-outlined absolute left-3 text-gray-500 pointer-events-none">
              search
            </span>
          </div>

          <!-- Banner promocional -->
          <div class="hidden md:flex items-center gap-3 bg-emerald-800/60 backdrop-blur-md text-white p-1.5 pr-4 rounded-full border border-emerald-500/30 shadow-sm hover:bg-emerald-800/80 transition-all cursor-pointer">
            <span class="bg-amber-400 text-emerald-950 text-[10px] font-black uppercase px-2 py-1 rounded-full shadow-xs">
              Oferta
            </span>
            <div class="flex items-center gap-2 text-xs">
              <span class="font-semibold text-emerald-100 text-[15px]">Café Arabica</span>
              <span class="text-emerald-300">|</span>
              <span class="text-gray-400 line-through text-[15px]">6 Sacas de R$ 9.581,22</span>
              <span class="font-bold text-amber-300 text-lg">Por apenas R$ 8.980,87</span>
            </div>

            <!-- ìcone de seta -->
            <span class="material-symbols-outlined">arrow_right_alt</span>
          </div>
        </nav>

        <!-- Conteudo da home -->
        @yield('conteudo')

      </div>

      <!-- Sidebar -->
      <div class="drawer-side is-drawer-close:overflow-visible">
        <label for="my-drawer-4" aria-label="close sidebar" class="drawer-overlay"></label>
        <div class="flex min-h-full flex-col items-start is-drawer-close:w-14 is-drawer-open:w-64" style="background-color:#DDD8CC">

          <!-- Foto de perfil -->
          <div class="avatar p-2 py-5">
            <div class="w-16 rounded-full">
              <img src="https://media.istockphoto.com/id/2185723286/pt/foto/portrait-of-senior-farmer-in-corn-field-looking-at-camera-holding-crop-in-hands-at-sunset.jpg?s=2048x2048&w=is&k=20&c=a8WmLGrSWVQ0dBexy2unxfRUZMPijulF6DUc4mf5hUQ=" alt="Foto perfil agricultor">
            </div>
          </div>

          <!-- Itens da sidebar -->
          <ul class="menu w-full grow">
            <li>
              <button class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Homepage">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round" stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor" class="my-1.5 inline-block size-4"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                <span class="is-drawer-close:hidden">Home</span>
              </button>
            </li>
            <li>
              <button class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Settings">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round" stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor" class="my-1.5 inline-block size-4"><path d="M20 7h-9"></path><path d="M14 17H5"></path><circle cx="17" cy="17" r="3"></circle><circle cx="7" cy="7" r="3"></circle></svg>
                <span class="is-drawer-close:hidden">Configurações</span>
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </body>
</html>