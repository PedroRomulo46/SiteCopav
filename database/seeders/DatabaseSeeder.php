<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Categoria;
use App\Models\Fornecedor;
use App\Models\Produto;
use App\Models\Oferta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cria Usuário principal da Empresa (Admin / Cooperativa)
        $user = User::factory()->create([
            'nome'      => 'Copav Admin',
            'email'     => 'empresa@teste.com',
            'password'  => Hash::make('12345678'), // Senha definida para Login
            'user_type' => 'admin',
            'is_admin'  => true,
        ]);

        // 1.1. Cria Perfil de Fornecedor vinculado ao Admin
        $fornecedorCopav = Fornecedor::create([
            'nome'      => 'Cooperativa Agrícola COPAV',
            'user_id'   => $user->id,
            'status'    => 'ativo',
            'documento' => '12.345.678/0001-90',
            'telefone'  => '(88) 99999-9999',
            'endereco'  => 'Zona Rural, S/N',
            'cidade'    => 'Icapuí',
            'estado'    => 'CE',
        ]);

        // 2. Cria Categorias individuais
        $catGraos      = Categoria::create(['nome' => 'Grãos e Cereais']);
        $catFrutas     = Categoria::create(['nome' => 'Frutas e Hortaliças']);
        $catAdubos     = Categoria::create(['nome' => 'Adubos e Substratos']);
        $catMaquinario = Categoria::create(['nome' => 'Maquinários']);

        // 3. Cria Produtos vinculados às suas respectivas categorias
        $p1 = Produto::create([
            'nome'          => 'Milho Verde',
            'descricao'     => 'Milho de alta qualidade para consumo e ração.',
            'unidade'       => 'Saca (60kg)',
            'categoria_id'  => $catGraos->id,
            'fornecedor_id' => $fornecedorCopav->id,
            'imagem'        => 'assets/milho.png',
        ]);

        $p2 = Produto::create([
            'nome'          => 'Café Arábica',
            'descricao'     => 'Café especial ensacado e pronto para transporte.',
            'unidade'       => 'Saca (60kg)',
            'categoria_id'  => $catGraos->id,
            'fornecedor_id' => $fornecedorCopav->id,
            'imagem'        => 'assets/cafe.png',
        ]);

        $p3 = Produto::create([
            'nome'          => 'Maçãs Verdes',
            'descricao'     => 'Maçãs suculentas da fazenda.',
            'unidade'       => 'Saca (60kg)',
            'categoria_id'  => $catFrutas->id,
            'fornecedor_id' => $fornecedorCopav->id,
            'imagem'        => 'assets/maca.png',
        ]);

        $p4 = Produto::create([
            'nome'          => 'Nutriente de solo',
            'descricao'     => 'Nutriente especializado para produção de folhas e floração.',
            'unidade'       => 'Saca (60kg)',
            'categoria_id'  => $catAdubos->id,
            'fornecedor_id' => $fornecedorCopav->id,
            'imagem'        => 'assets/nutriente.png',
        ]);
        
        // 4. Cria Ofertas públicas
        Oferta::create([
            'produto_id'    => $p1->id,
            'fornecedor_id' => $fornecedorCopav->id,
            'quantidade'    => 100,
            'valor'         => 85.50,
            'unidade'       => 'Sacas',
            'status'        => 'publicada',
            'data_validade' => now()->addDays(15),
        ]);

        Oferta::create([
            'produto_id'    => $p2->id,
            'fornecedor_id' => $fornecedorCopav->id,
            'quantidade'    => 50,
            'valor'         => 240.00,
            'unidade'       => 'Sacas',
            'status'        => 'publicada',
            'data_validade' => now()->addDays(30),
        ]);

        Oferta::create([
            'produto_id'    => $p3->id,
            'fornecedor_id' => $fornecedorCopav->id,
            'quantidade'    => 50,
            'valor'         => 240.00,
            'unidade'       => 'Sacas',
            'status'        => 'publicada',
            'data_validade' => now()->addDays(30),
        ]);

        Oferta::create([
            'produto_id'    => $p4->id,
            'fornecedor_id' => $fornecedorCopav->id,
            'quantidade'    => 50,
            'valor'         => 240.00,
            'unidade'       => 'Sacas',
            'status'        => 'publicada',
            'data_validade' => now()->addDays(30),
        ]);
    }
}