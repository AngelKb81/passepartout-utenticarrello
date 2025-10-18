<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Aggiungiamo prima la colonna 'nome' se non esiste
            if (!Schema::hasColumn('users', 'nome')) {
                $table->string('nome')->nullable()->after('id');
            }
        });

        // Ora copiamo i dati da 'name' a 'nome'
        DB::statement('UPDATE users SET nome = name WHERE nome IS NULL OR nome = ""');

        Schema::table('users', function (Blueprint $table) {
            // Eliminiamo la colonna 'name' ora inutile
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ripristiniamo la colonna 'name'
            $table->string('name')->nullable()->after('id');

            // Copiamo i dati da 'nome' a 'name'
            DB::statement('UPDATE users SET name = nome WHERE name IS NULL');
        });
    }
};
