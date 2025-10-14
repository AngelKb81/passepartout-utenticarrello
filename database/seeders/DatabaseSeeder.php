<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Popola il database con i dati di base del sistema.
     * Esegue i seeder nell'ordine corretto per rispettare le dipendenze.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,      // Prima i ruoli
            UserSeeder::class,      // Poi gli utenti (che dipendono dai ruoli)
            ProductSeeder::class,   // Infine i prodotti
        ]);
    }
}
