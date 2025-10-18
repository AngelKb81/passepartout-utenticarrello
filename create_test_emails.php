<?php

require 'vendor/autoload.php';

use App\Models\User;
use App\Models\UserEmail;

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Prima prendimi un utente esistente
$user = User::first();
if (!$user) {
    echo "Nessun utente trovato, ne creo uno per i test...\n";
    $user = User::create([
        'nome' => 'Test',
        'cognome' => 'Admin',
        'email' => 'test@admin.com',
        'password' => bcrypt('password'),
        'ruolo' => 'admin'
    ]);
}

echo "User ID: {$user->id}\n";

// Creo alcuni email di test
UserEmail::create([
    'user_id' => $user->id,
    'recipient_email' => 'mario.rossi@email.com',
    'recipient_name' => 'Mario Rossi',
    'type' => 'welcome',
    'subject' => 'Benvenuto in Passepartout!',
    'content' => 'Ciao Mario, benvenuto nella nostra piattaforma...',
    'status' => 'sent',
    'sent_at' => now()
]);

UserEmail::create([
    'user_id' => $user->id,
    'recipient_email' => 'lucia.bianchi@email.com', 
    'recipient_name' => 'Lucia Bianchi',
    'type' => 'reset_password',
    'subject' => 'Reset della tua password',
    'content' => 'Hai richiesto di reimpostare la tua password...',
    'status' => 'sent',
    'sent_at' => now()->subHours(2)
]);

UserEmail::create([
    'user_id' => $user->id,
    'recipient_email' => 'giuseppe.verdi@email.com',
    'recipient_name' => 'Giuseppe Verdi', 
    'type' => 'order_confirmation',
    'subject' => 'Conferma del tuo ordine #12345',
    'content' => 'Il tuo ordine è stato confermato...',
    'status' => 'failed',
    'error_message' => 'Email address not valid',
    'sent_at' => now()->subHours(4)
]);

UserEmail::create([
    'user_id' => $user->id,
    'recipient_email' => 'anna.ferrari@email.com',
    'recipient_name' => 'Anna Ferrari',
    'type' => 'newsletter', 
    'subject' => 'Newsletter Gennaio 2025',
    'content' => 'Ecco le novità di questo mese...',
    'status' => 'pending',
    'sent_at' => null
]);

echo "Creato " . UserEmail::count() . " email di test\n";