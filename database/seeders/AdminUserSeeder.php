<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Prima assicuriamoci che il ruolo admin esista
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        echo "✅ Ruolo admin creato/trovato: ID " . $adminRole->id . "\n";

        // Crea o aggiorna l'utente admin
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@passepartout-utenticarrello.test'],
            [
                'nome' => 'Mario',
                'cognome' => 'Rossi',
                'password' => Hash::make('admin123'),
                'titolo_studi' => 'Laurea in Informatica',
                'data_nascita' => '1980-01-01',
                'citta_nascita' => 'Roma',
                'email_verified_at' => now(),
            ]
        );

        echo "✅ Utente admin creato/aggiornato: " . $adminUser->email . " (ID: " . $adminUser->id . ")\n";

        // Associa il ruolo admin all'utente
        if (!$adminUser->roles()->where('role_id', $adminRole->id)->exists()) {
            $adminUser->roles()->attach($adminRole->id);
            echo "✅ Ruolo admin assegnato all'utente\n";
        } else {
            echo "✅ Utente ha già il ruolo admin\n";
        }

        // Verifica finale
        $adminUser->load('roles');
        echo "✅ Verifica: isAdmin() = " . ($adminUser->isAdmin() ? 'true' : 'false') . "\n";
        echo "✅ Ruoli utente: " . $adminUser->roles->pluck('name')->join(', ') . "\n";
    }
}
