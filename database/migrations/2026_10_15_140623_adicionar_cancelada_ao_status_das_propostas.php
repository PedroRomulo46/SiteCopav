
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE propostas
            MODIFY COLUMN status
            ENUM('pendente', 'aceita', 'recusada', 'cancelada')
            NOT NULL DEFAULT 'pendente'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE propostas
            MODIFY COLUMN status
            ENUM('pendente', 'aceita', 'recusada')
            NOT NULL DEFAULT 'pendente'
        ");
    }
};
