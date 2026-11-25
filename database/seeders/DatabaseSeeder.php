<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Fornecedor;
use App\Models\Oferta;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $senhaPadrao = Hash::make('12345678');

        // 1. Cria ou atualiza o Usuário Admin
        $admin = User::firstOrNew([
            'email' => 'admin@teste.com',
        ]);

        $admin->forceFill([
            'nome'              => 'Administrador',
            'password'          => $admin->exists && $admin->password ? $admin->password : $senhaPadrao,
            'user_type'         => 'admin',
            'is_admin'          => true,
            'email_verified_at' => $admin->email_verified_at ?? now(),
        ])->save();

        // 2. Cria ou atualiza o Usuário e o Fornecedor com nome "Empresa"
        $usuarioEmpresa = User::firstOrNew([
            'email' => 'empresa@teste.com',
        ]);

        $usuarioEmpresa->forceFill([
            'nome'              => 'Empresa',
            'password'          => $usuarioEmpresa->exists && $usuarioEmpresa->password ? $usuarioEmpresa->password : $senhaPadrao,
            'user_type'         => 'fornecedor',
            'is_admin'          => false,
            'email_verified_at' => $usuarioEmpresa->email_verified_at ?? now(),
        ])->save();

        $fornecedorEmpresa = Fornecedor::updateOrCreate(
            ['documento' => '12.345.678/0001-90'],
            [
                'nome'     => 'Empresa',
                'user_id'  => $usuarioEmpresa->id,
                'status'   => 'ativo',
                'telefone' => '(88) 99999-9999',
                'endereco' => 'Zona Rural, S/N',
                'cidade'   => 'Icapuí',
                'estado'   => 'CE',
            ]
        );

        // 3. Cria ou atualiza o Cliente comum
        $usuarioCliente = User::firstOrNew([
            'email' => 'cliente@teste.com',
        ]);

        $usuarioCliente->forceFill([
            'nome'              => 'Cliente Comum',
            'password'          => $usuarioCliente->exists && $usuarioCliente->password ? $usuarioCliente->password : $senhaPadrao,
            'user_type'         => 'cliente',
            'is_admin'          => false,
            'email_verified_at' => $usuarioCliente->email_verified_at ?? now(),
        ])->save();

        if (class_exists(Cliente::class) && Schema::hasTable('clientes')) {
            $clienteAttributes = ['nome' => 'Cliente Comum'];

            if (Schema::hasColumn('clientes', 'user_id')) {
                $clienteAttributes['user_id'] = $usuarioCliente->id;
            }
            if (Schema::hasColumn('clientes', 'email')) {
                $clienteAttributes['email'] = $usuarioCliente->email;
            }
            if (Schema::hasColumn('clientes', 'status')) {
                $clienteAttributes['status'] = 'ativo';
            }

            $lookupCliente = Schema::hasColumn('clientes', 'user_id')
                ? ['user_id' => $usuarioCliente->id]
                : (Schema::hasColumn('clientes', 'email')
                    ? ['email' => $usuarioCliente->email]
                    : ['nome' => 'Cliente Comum']);

            Cliente::updateOrCreate($lookupCliente, $clienteAttributes);
        }

        // 4. Cria Categorias
        $catGraos      = Categoria::firstOrCreate(['nome' => 'Grãos e Cereais']);
        $catFrutas     = Categoria::firstOrCreate(['nome' => 'Frutas e Hortaliças']);
        $catAdubos     = Categoria::firstOrCreate(['nome' => 'Adubos e Substratos']);
        $catMaquinario = Categoria::firstOrCreate(['nome' => 'Maquinários']);

        // 5. Cria Produtos vinculados ao fornecedor "Empresa"
        $p1 = Produto::firstOrCreate(
            ['nome' => 'Milho Verde', 'fornecedor_id' => $fornecedorEmpresa->id],
            [
                'descricao'    => 'Milho de alta qualidade para consumo e ração.',
                'unidade'      => 'Saca (60kg)',
                'categoria_id' => $catGraos->id,
                'imagem'       => 'assets/milho.png',
            ]
        );

        $p2 = Produto::firstOrCreate(
            ['nome' => 'Café Arábica', 'fornecedor_id' => $fornecedorEmpresa->id],
            [
                'descricao'    => 'Café especial ensacado e pronto para transporte.',
                'unidade'      => 'Saca (60kg)',
                'categoria_id' => $catGraos->id,
                'imagem'       => 'assets/cafe.png',
            ]
        );

        $p3 = Produto::firstOrCreate(
            ['nome' => 'Maçãs Verdes', 'fornecedor_id' => $fornecedorEmpresa->id],
            [
                'descricao'    => 'Maçãs suculentas da fazenda.',
                'unidade'      => 'Saca (60kg)',
                'categoria_id' => $catFrutas->id,
                'imagem'       => 'assets/maca.png',
            ]
        );

        $p4 = Produto::firstOrCreate(
            ['nome' => 'Nutriente de solo', 'fornecedor_id' => $fornecedorEmpresa->id],
            [
                'descricao'    => 'Nutriente especializado para produção de folhas e floração.',
                'unidade'      => 'Saca (60kg)',
                'categoria_id' => $catAdubos->id,
                'imagem'       => 'assets/nutriente.png',
            ]
        );

        $p5 = Produto::firstOrCreate(
            ['nome' => 'Trator John Deere Séries 3036EN', 'fornecedor_id' => $fornecedorEmpresa->id],
            [
                'descricao'    => 'Trator compacto destinado a cultura de café, horticultura e fruticultura (3 cilindros, 1.6 Diesel).',
                'unidade'      => '1200 (Kg)',
                'categoria_id' => $catMaquinario->id,
                'imagem'       => 'assets/trator.jpg',
            ]
        );

        $p6 = Produto::firstOrCreate(
            ['nome' => 'Tomates Frescos', 'fornecedor_id' => $fornecedorEmpresa->id],
            [
                'descricao'    => 'Tomates recem colhidos, muito frescos.',
                'unidade'      => '80 (Kg)',
                'categoria_id' => $catFrutas->id,
                'imagem'       => 'assets/tomate.png',
            ]
        );

        $p7 = Produto::firstOrCreate(
            ['nome' => 'Feijões da Fazenda Belém', 'fornecedor_id' => $fornecedorEmpresa->id],
            [
                'descricao'    => 'Feijões bonitos e plantados na lendária fazenda belém.',
                'unidade'      => '120 (Kg)',
                'categoria_id' => $catGraos->id,
                'imagem'       => 'assets/feijao.png',
            ]
        );

        $p8 = Produto::firstOrCreate(
            ['nome' => 'Drone Agricola', 'fornecedor_id' => $fornecedorEmpresa->id],
            [
                'descricao'    => 'Drone especializado para pulverização.',
                'unidade'      => '25 (Kg)',
                'categoria_id' => $catMaquinario->id,
                'imagem'       => 'assets/drone.png',
            ]
        );

        // 6. Cria Ofertas como se o fornecedor "Empresa" tivesse criado
        $ofertas = [
            ['produto' => $p1, 'quantidade' => 100, 'valor' => 85.50,     'unidade' => 'Sacas',     'dias' => 15],
            ['produto' => $p2, 'quantidade' => 50,  'valor' => 199.99,    'unidade' => 'Sacas',     'dias' => 30],
            ['produto' => $p3, 'quantidade' => 50,  'valor' => 45.70,     'unidade' => 'Sacas',     'dias' => 30],
            ['produto' => $p4, 'quantidade' => 50,  'valor' => 60.00,     'unidade' => 'Sacas',     'dias' => 30],
            ['produto' => $p5, 'quantidade' => 1,   'valor' => 145000.00, 'unidade' => 'S/Unidade', 'dias' => 30],
            ['produto' => $p6, 'quantidade' => 50,  'valor' => 800.00,    'unidade' => 'Sacas',     'dias' => 30],
            ['produto' => $p7, 'quantidade' => 120, 'valor' => 620.00,    'unidade' => 'Sacas',     'dias' => 30],
            ['produto' => $p8, 'quantidade' => 1,   'valor' => 120000.00, 'unidade' => 'Sacas',     'dias' => 30],
        ];

        $temUserIdEmOfertas = Schema::hasTable('ofertas') && Schema::hasColumn('ofertas', 'user_id');

        foreach ($ofertas as $item) {
            $dadosOferta = [
                'quantidade'    => $item['quantidade'],
                'valor'         => $item['valor'],
                'unidade'       => $item['unidade'],
                'data_validade' => now()->addDays($item['dias']),
            ];

            if ($temUserIdEmOfertas) {
                $dadosOferta['user_id'] = $usuarioEmpresa->id;
            }

            Oferta::firstOrCreate(
                [
                    'produto_id'    => $item['produto']->id,
                    'fornecedor_id' => $fornecedorEmpresa->id,
                    'status'        => 'publicada',
                ],
                $dadosOferta
            );
        }
    }
}