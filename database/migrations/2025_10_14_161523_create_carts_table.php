<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('stato', ['attivo', 'ordinato', 'abbandonato'])->default('attivo');
            $table->timestamp('ultimo_aggiornamento')->useCurrent();
            $table->timestamps();

            // Un utente può avere solo un carrello attivo alla volta
            $table->unique(['user_id', 'stato']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
