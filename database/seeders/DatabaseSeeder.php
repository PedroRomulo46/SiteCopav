<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Categoria;
use App\Models\Fornecedor;
use App\Models\Produto;
use App\Models\Oferta;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cria Usuário principal
        $user = User::factory()->create([
            'nome' => 'Copav',
            'email' => 'copavagricola@example.com',
            'user_type' => 'fornecedor',
        ]);

        // 1.1. Cria Fornecedor
        $fornecedorCopav = Fornecedor::create([
            'nome' => 'Cooperativa Agrícola',
            'user_id' => $user->id,
            'status' => 'ativo',
            'documento' => '12.345.678/0001-90',
            'telefone' => '(88) 99999-9999',
            'endereco' => 'Zona Rural, S/N',
            'cidade' => 'Icapuí',
            'estado' => 'CE',
        ]);

        // 2. Criando um segundo usuário do tipo cliente
        $cliente = User::factory()->create([
            'nome' => 'João Silva',
            'email' => 'joaosilva@example.com',
            'user_type' => 'fornecedor',
        ]);

        // 2.1. Cria Fornecedor
        $fornecedorJoao = Fornecedor::create([
            'nome' => 'João Silva Produções Rurais',
            'user_id' => $cliente->id,
            'status' => 'ativo',
            'documento' => '18.394.578/8911-99',
            'telefone' => '(88) 95989-9979',
            'endereco' => 'Zona Rural, S/N',
            'cidade' => 'Juazeiro do Norte',
            'estado' => 'CE',
        ]);

        // 3. Cria Categoria
        $categoria = Categoria::create(['nome' => 'Grãos e Cereais']);

        // 4. Cria Produtos
        $p1 = Produto::create([
            'nome' => 'Milho Verde',
            'descricao' => 'Milho de alta qualidade para consumo e ração.',
            'unidade' => 'Saca (60kg)',
            'categoria_id' => $categoria->id,
            'fornecedor_id' => $fornecedorJoao->id,
        ]);

        $p2 = Produto::create([
            'nome' => 'Café Arábica',
            'descricao' => 'Café especial ensacado e pronto para transporte.',
            'unidade' => 'Saca (60kg)',
            'categoria_id' => $categoria->id,
            'fornecedor_id' => $fornecedorCopav->id,
        ]);

        $p3 = Produto::create([
            'nome' => 'Maçãs Verdes',
            'descricao' => 'Maçãs suculentas da fazenda.',
            'unidade' => 'Saca (60kg)',
            'categoria_id' => $categoria->id,
            'fornecedor_id' => $fornecedorJoao->id,
        ]);

        $p4 = Produto::create([
            'nome' => 'Romãs',
            'descricao' => 'Romãs saborosas direto do Ceará.',
            'unidade' => 'Saca (60kg)',
            'categoria_id' => $categoria->id,
            'fornecedor_id' => $fornecedorCopav->id,
        ]);
        
        // 5. Cria Ofertas com valor e status compatível com o ENUM
        Oferta::create([
            'produto_id' => $p1->id,
            'fornecedor_id' => $fornecedorCopav->id,
            'quantidade' => 100,
            'valor' => 85.50,
            'unidade' => 'Sacas',
            'status' => 'publicada',
            'data_validade' => now()->addDays(15),
        ]);

        Oferta::create([
            'produto_id' => $p2->id,
            'fornecedor_id' => $fornecedorJoao->id,
            'quantidade' => 50,
            'valor' => 240.00,
            'unidade' => 'Sacas',
            'status' => 'publicada',
            'data_validade' => now()->addDays(30),
        ]);
    }
}