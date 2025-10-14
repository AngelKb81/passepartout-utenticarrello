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
        Schema::table('users', function (Blueprint $table) {
            $table->string('cognome')->after('name');
            $table->string('titolo_studi')->nullable()->after('cognome');
            $table->date('data_nascita')->nullable()->after('titolo_studi');
            $table->string('citta_nascita')->nullable()->after('data_nascita');
            $table->timestamp('email_verified_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cognome', 'titolo_studi', 'data_nascita', 'citta_nascita']);
        });
    }
};
