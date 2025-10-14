<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Popola la tabella roles con i ruoli base del sistema.
     * Crea i ruoli admin, user e business per garantire scalabilità futura.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Amministratore',
                'description' => 'Accesso completo al sistema, gestione utenti e prodotti, visualizzazione dashboard',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user',
                'display_name' => 'Utente',
                'description' => 'Utente standard, può visualizzare prodotti e gestire il proprio carrello',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'business',
                'display_name' => 'Business',
                'description' => 'Utente business con accesso a funzionalità avanzate (futuro sviluppo)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}
