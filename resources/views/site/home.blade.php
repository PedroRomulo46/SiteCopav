@extends('site.layout')
@section('title', 'home')
@section('conteudo')

<div class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 bg-gray-50 min-h-screen">

  <!-- Coluna esquerda: Lotes e Demandas -->
  <div class="lg:col-span-5 h-fit p-4 rounded-2xl flex flex-col gap-4" style="background-color: #DDD8CC">
    
    <!-- Botão de cadastrar lotes -->
    <button class="btn text-white w-full border-none text-lg py-8 rounded-xl shadow-inner" style="background-color: #79A961">
      Cadastrar novo lote +
    </button>

    <!-- Card unificado em fundo branco -->
    <div class="bg-white rounded-xl p-4 flex flex-col gap-3 shadow-md">
      
      <!-- Abas -->
      <div role="tablist" class="tabs tabs-border w-full flex justify-around border-b pb-2">
        <a role="tab" class="tab text-gray-500">Meus Lotes</a>
        <a role="tab" class="tab tab-active font-bold text-gray-800">Demandas da empresa</a>
      </div>

      <!-- Lista de Demandas/Lotes -->
      <div class="flex flex-col gap-3 mt-2">
        
        <!-- Item 1 -->
        <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-100 hover:shadow-[0_0_8px_3px_rgba(0,0,0,0.2)]">
          <img class="w-20 h-20 rounded-lg object-cover shrink-0" src="{{ asset('assets/milho.png') }}" alt="Milho" />
          <div class="flex flex-col justify-between grow self-stretch text-xs">
            <div>
              <h3 class="font-bold text-gray-800 text-sm">[Demanda #5]: 5 Ton. Milho</h3>
              <p class="text-gray-500">Expira em: 24/09/2026</p>
            </div>
            <div class="flex justify-end mt-2">
              <button class="btn border-2 hover:bg-[#79A961] hover:text-white p-2 btn-xs sm:btn-sm" style="border-color: #79A961">
                Ver detalhes
              </button>
            </div>
          </div>
        </div>

        <!-- Item 2 -->
        <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-100 hover:shadow-[0_0_8px_3px_rgba(0,0,0,0.2)]">
          <img class="w-20 h-20 rounded-lg object-cover shrink-0" src="{{ asset('assets/caju.png') }}" alt="Caju" />
          <div class="flex flex-col justify-between grow self-stretch text-xs">
            <div>
              <h3 class="font-bold text-gray-800 text-sm">[Demanda #6]: 3 Ton. Caju</h3>
              <p class="text-gray-500">Expira em: 30/05/2027</p>
            </div>
            <div class="flex justify-end mt-2">
              <button class="btn border-2 hover:bg-[#79A961] hover:text-white p-2 btn-xs sm:btn-sm" style="border-color: #79A961">
                Ver detalhes
              </button>
            </div>
          </div>
        </div>

        <!-- Item 3 -->
        <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-100 hover:shadow-[0_0_8px_3px_rgba(0,0,0,0.2)]">
          <img class="w-20 h-20 rounded-lg object-cover shrink-0" src="{{ asset('assets/feijao.png') }}" alt="Feijão" />
          <div class="flex flex-col justify-between grow self-stretch text-xs">
            <div>
              <h3 class="font-bold text-gray-800 text-sm">[Demanda #7]: 2 Ton. Feijão</h3>
              <p class="text-gray-500">Expira em: 07/12/2026</p>
            </div>
            <div class="flex justify-end mt-2">
              <button class="btn border-2 hover:bg-[#79A961] hover:text-white p-2 btn-xs sm:btn-sm" style="border-color: #79A961">
                Ver detalhes
              </button>
            </div>
          </div>
        </div>

        <!-- Item 4 -->
        <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-100 hover:shadow-[0_0_8px_3px_rgba(0,0,0,0.2)]">
          <img class="w-20 h-20 rounded-lg object-cover shrink-0" src="{{ asset('assets/tomate.png') }}" alt="Tomate" />
          <div class="flex flex-col justify-between grow self-stretch text-xs">
            <div>
              <h3 class="font-bold text-gray-800 text-sm">[Demanda #8]: 1 Ton. Tomate</h3>
              <p class="text-gray-500">Expira em: 12/02/2027</p>
            </div>
            <div class="flex justify-end mt-2">
              <button class="btn border-2 hover:bg-[#79A961] hover:text-white p-2 btn-xs sm:btn-sm" style="border-color: #79A961">
                Ver detalhes
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Coluna direita: Produtos Recomendados -->
  <div class="lg:col-span-7 flex flex-col gap-4 items-center">
    <h1 class="text-xl font-bold text-gray-800">Produtos que você pode se interessar...</h1>

    <!-- Grid para os Cards de Produtos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 w-full">
      
      <!-- Produto 1 -->
      <div class="bg-white hover:shadow-[0_0_20px_8px_rgba(0,0,0,0.1)] p-3 rounded-2xl flex flex-col justify-between">
        <img class="w-full h-64 object-cover rounded-xl mb-3" src="{{ asset('assets/sementesmilho.png') }}" alt="Sementes Milho" />
        <div>
          <h2 class="font-bold text-gray-800">Sementes Milho Híbrido</h2>
          <p class="text-xs text-gray-400">Alta produtividade - oferta especial</p>
          <p class="text-lg font-bold text-green-600 mt-1">R$ 45,00/Kg</p>
          <span class="text-xs text-gray-400">Hugo Jorge</span>
        </div>
      </div>

      <!-- Produto 2 -->
      <div class="bg-white hover:shadow-[0_0_20px_8px_rgba(0,0,0,0.1)] p-3 rounded-2xl flex flex-col justify-between">
        <img class="w-full h-64 object-cover rounded-xl mb-3" src="{{ asset('assets/drone.png') }}" alt="Drone" />
        <div>
          <h2 class="font-bold text-gray-800">Drone Pulverizador</h2>
          <p class="text-xs text-gray-400">Ideal para a pulverização de grandes campos</p>
          <p class="text-lg font-bold text-green-600 mt-1">R$ 60.000,00</p>
          <span class="text-xs text-gray-400">Hugo Jorge</span>
        </div>
      </div>

      <!-- Produto 3 -->
      <div class="bg-white hover:shadow-[0_0_20px_8px_rgba(0,0,0,0.1)] p-3 rounded-2xl flex flex-col justify-between">
        <img class="w-full h-64 object-cover rounded-xl mb-3" src="{{ asset('assets/nutriente.png') }}" alt="Nutriente de solo" />
        <div>
          <h2 class="font-bold text-gray-800">Nutriente de solo</h2>
          <p class="text-xs text-gray-400">Ideal para o crescimento e frutificação</p>
          <p class="text-lg font-bold text-green-600 mt-1">R$ 450,00/Kg</p>
          <span class="text-xs text-gray-400">Hugo Jorge</span>
        </div>
      </div>

      <!-- Produto 4 -->
      <div class="bg-white hover:shadow-[0_0_20px_8px_rgba(0,0,0,0.1)] p-3 rounded-2xl flex flex-col justify-between">
        <img class="w-full h-64 object-cover rounded-xl mb-3" src="{{ asset('assets/maca.png') }}" alt="Maçã Fuji" />
        <div>
          <h2 class="font-bold text-gray-800">Maçã Fuji</h2>
          <p class="text-xs text-gray-400">Alta produtividade - oferta especial</p>
          <p class="text-lg font-bold text-green-600 mt-1">R$ 12,00/Kg</p>
          <span class="text-xs text-gray-400">Hugo Jorge</span>
        </div>
      </div>

    </div>
  </div>

</div>

@endsection