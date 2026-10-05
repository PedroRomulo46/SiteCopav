<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('negociacoes', function (Blueprint $table) {
            $table->timestamp('fornecedor_visualizada_em')
                ->nullable()
                ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('negociacoes', function (Blueprint $table) {
            $table->dropColumn('fornecedor_visualizada_em');
        });
    }
};