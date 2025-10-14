<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service per la gestione degli utenti del sistema.
 * Contiene la business logic per registrazione, autenticazione e gestione profilo.
 */
class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Registra un nuovo utente nel sistema.
     */
    public function registerUser(array $userData): User
    {
        // Verifica che l'email non sia già in uso
        if ($this->userRepository->findByEmail($userData['email'])) {
            throw new \Exception('Email già registrata nel sistema');
        }

        // Hash della password
        $userData['password'] = Hash::make($userData['password']);

        DB::beginTransaction();
        try {
            // Crea l'utente
            $user = $this->userRepository->create($userData);

            // Assegna il ruolo "user" di default
            $this->userRepository->assignRole($user->id, 'user');

            // Invia email di benvenuto (in futuro)
            $this->sendWelcomeEmail($user);

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Autentica un utente con email e password.
     */
    public function authenticateUser(string $email, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user && Hash::check($password, $user->password)) {
            return $user;
        }

        return null;
    }

    /**
     * Aggiorna il profilo utente.
     */
    public function updateUserProfile(int $userId, array $profileData): User
    {
        // Rimuovi la password dai dati se è vuota
        if (isset($profileData['password']) && empty($profileData['password'])) {
            unset($profileData['password']);
        } else if (isset($profileData['password'])) {
            $profileData['password'] = Hash::make($profileData['password']);
        }

        // Verifica che l'email non sia già in uso da altri utenti
        if (isset($profileData['email'])) {
            $existingUser = $this->userRepository->findByEmail($profileData['email']);
            if ($existingUser && $existingUser->id !== $userId) {
                throw new \Exception('Email già utilizzata da un altro utente');
            }
        }

        $this->userRepository->update($userId, $profileData);
        return $this->userRepository->findByIdOrFail($userId);
    }

    /**
     * Genera e invia un token per il reset della password.
     */
    public function generatePasswordResetToken(string $email): string
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new \Exception('Utente non trovato');
        }

        $token = Str::random(60);

        // Salva il token nella tabella password_resets (da implementare migration)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Invia email con il token (implementazione futura)
        $this->sendPasswordResetEmail($user, $token);

        return $token;
    }

    /**
     * Resetta la password utilizzando il token.
     */
    public function resetPassword(string $email, string $token, string $newPassword): bool
    {
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord || !Hash::check($token, $resetRecord->token)) {
            throw new \Exception('Token di reset non valido');
        }

        // Verifica che il token non sia scaduto (1 ora)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            throw new \Exception('Token di reset scaduto');
        }

        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            throw new \Exception('Utente non trovato');
        }

        // Aggiorna la password
        $this->userRepository->update($user->id, [
            'password' => Hash::make($newPassword)
        ]);

        // Rimuove il token utilizzato
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return true;
    }

    /**
     * Ottiene le statistiche degli utenti per la dashboard admin.
     */
    public function getUsersStatistics(): array
    {
        return [
            'total_users' => $this->userRepository->count(),
            'users_by_city' => $this->userRepository->getUsersByCityStats(),
            'users_by_education' => $this->userRepository->getUsersByEducationStats(),
            'registrations_by_month' => $this->userRepository->getUsersRegistrationByMonth(),
            'admin_users' => $this->userRepository->getUsersByRole('admin')->count(),
            'standard_users' => $this->userRepository->getUsersByRole('user')->count(),
        ];
    }

    /**
     * Cambia l'email di un utente con processo di verifica.
     */
    public function changeUserEmail(int $userId, string $newEmail): bool
    {
        // Verifica che la nuova email non sia già in uso
        if ($this->userRepository->findByEmail($newEmail)) {
            throw new \Exception('La nuova email è già in uso');
        }

        $user = $this->userRepository->findByIdOrFail($userId);

        // Aggiorna l'email e rimuovi la verifica
        $this->userRepository->update($userId, [
            'email' => $newEmail,
            'email_verified_at' => null
        ]);

        // Invia email di conferma (implementazione futura)
        $this->sendEmailVerification($user);

        return true;
    }

    /**
     * Verifica l'email di un utente.
     */
    public function verifyUserEmail(int $userId): bool
    {
        return $this->userRepository->update($userId, [
            'email_verified_at' => now()
        ]);
    }

    /**
     * Invia email di benvenuto (placeholder per implementazione futura).
     */
    private function sendWelcomeEmail(User $user): void
    {
        // Implementazione futura con Mail facade
        // Per ora logga l'azione
        Log::info("Email di benvenuto inviata a: {$user->email}");
    }

    /**
     * Invia email per reset password (placeholder per implementazione futura).
     */
    private function sendPasswordResetEmail(User $user, string $token): void
    {
        // Implementazione futura con Mail facade
        Log::info("Email reset password inviata a: {$user->email} con token: {$token}");
    }

    /**
     * Invia email di verifica (placeholder per implementazione futura).
     */
    private function sendEmailVerification(User $user): void
    {
        // Implementazione futura con Mail facade
        Log::info("Email di verifica inviata a: {$user->email}");
    }
}
