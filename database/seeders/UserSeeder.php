<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Crea utenti di test con dati realistici in italiano.
     * Include un admin e alcuni utenti standard per testare il sistema.
     */
    public function run(): void
    {
        // Utente admin
        $adminUserId = DB::table('users')->insertGetId([
            'name' => 'Mario',
            'cognome' => 'Rossi',
            'email' => 'admin@passepartout-utenticarrello.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'titolo_studi' => 'Laurea',
            'data_nascita' => '1985-03-15',
            'citta_nascita' => 'Milano',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Utenti standard di test
        $users = [
            [
                'name' => 'Giulia',
                'cognome' => 'Bianchi',
                'email' => 'giulia.bianchi@email.test',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'titolo_studi' => 'Diploma Superiore',
                'data_nascita' => '1992-07-22',
                'citta_nascita' => 'Roma',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Francesco',
                'cognome' => 'Verde',
                'email' => 'francesco.verde@email.test',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'titolo_studi' => 'Laurea Magistrale',
                'data_nascita' => '1988-11-03',
                'citta_nascita' => 'Napoli',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chiara',
                'cognome' => 'Neri',
                'email' => 'chiara.neri@email.test',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'titolo_studi' => 'Master',
                'data_nascita' => '1995-01-18',
                'citta_nascita' => 'Torino',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        $standardUserIds = [];
        foreach ($users as $user) {
            $standardUserIds[] = DB::table('users')->insertGetId($user);
        }

        // Assegna ruoli agli utenti
        $adminRoleId = DB::table('roles')->where('name', 'admin')->first()->id;
        $userRoleId = DB::table('roles')->where('name', 'user')->first()->id;

        // Admin ha sia ruolo admin che user
        DB::table('role_user')->insert([
            ['role_id' => $adminRoleId, 'user_id' => $adminUserId, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => $userRoleId, 'user_id' => $adminUserId, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Utenti standard hanno solo ruolo user
        foreach ($standardUserIds as $userId) {
            DB::table('role_user')->insert([
                'role_id' => $userRoleId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
