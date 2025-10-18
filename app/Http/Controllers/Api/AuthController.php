<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

/**
 * Controller per la gestione dell'autenticazione utenti.
 * Gestisce registrazione, login, logout e operazioni di autenticazione.
 */
class AuthController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Registra un nuovo utente nel sistema.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            Log::info('🔹 Registrazione utente - dati ricevuti', $request->all());

            $user = $this->userService->registerUser($request->validated());
            Log::info('✅ Utente creato con successo', ['user_id' => $user->id]);

            // Invia notifica di benvenuto
            try {
                $user->notify(new \App\Notifications\WelcomeNotification());
                Log::info('📧 Email di benvenuto inviata', ['user_id' => $user->id]);
            } catch (\Exception $e) {
                Log::error('❌ Errore invio email benvenuto', ['error' => $e->getMessage()]);
                // Non bloccare la registrazione se l'email fallisce
            }

            return response()->json([
                'message' => 'Registrazione completata con successo',
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'cognome' => $user->cognome,
                    'email' => $user->email,
                    'telefono' => $user->telefono,
                    'data_nascita' => $user->data_nascita,
                    'citta_residenza' => $user->citta_residenza,
                    'titolo_studio' => $user->titolo_studio,
                    'professione' => $user->professione,
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('❌ Errore registrazione:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Errore durante la registrazione',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Autentica un utente esistente.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->authenticateUser(
                $request->email,
                $request->password
            );

            if (!$user) {
                return response()->json([
                    'message' => 'Credenziali non valide',
                ], 401);
            }

            // Carica i ruoli
            $user->load('roles');

            // Crea il token di accesso
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login effettuato con successo!',
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'cognome' => $user->cognome,
                    'email' => $user->email,
                    'titolo_studi' => $user->titolo_studi,
                    'data_nascita' => $user->data_nascita ? $user->data_nascita->format('Y-m-d') : null,
                    'citta_nascita' => $user->citta_nascita,
                    'roles' => $user->roles->pluck('name'),
                    'is_admin' => $user->isAdmin(),
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore durante il login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout dell'utente corrente.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revoca tutti i token dell'utente corrente
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logout effettuato con successo!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore durante il logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ottiene le informazioni dell'utente autenticato.
     */
    public function user(Request $request): JsonResponse
    {
        try {
            $user = $request->user()->load('roles');

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'cognome' => $user->cognome,
                    'email' => $user->email,
                    'titolo_studi' => $user->titolo_studi,
                    'data_nascita' => $user->data_nascita ? $user->data_nascita->format('Y-m-d') : null,
                    'citta_nascita' => $user->citta_nascita,
                    'roles' => $user->roles->pluck('name'),
                    'is_admin' => $user->isAdmin(),
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nel recupero dati utente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aggiorna il profilo dell'utente autenticato.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            // Validazione dei dati del profilo
            $validated = $request->validate([
                'nome' => ['required', 'string', 'max:255'],
                'cognome' => ['required', 'string', 'max:255'],
                'titolo_studi' => ['nullable', 'string', 'max:255'],
                'data_nascita' => ['nullable', 'date', 'before:today'],
                'citta_nascita' => ['nullable', 'string', 'max:255'],
            ]);

            $user = $request->user();
            $user->update($validated);

            return response()->json([
                'message' => 'Profilo aggiornato con successo!',
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'cognome' => $user->cognome,
                    'email' => $user->email,
                    'titolo_studi' => $user->titolo_studi,
                    'data_nascita' => $user->data_nascita ? $user->data_nascita->format('Y-m-d') : null,
                    'citta_nascita' => $user->citta_nascita,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nell\'aggiornamento del profilo',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Cambia la password dell'utente autenticato.
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'current_password' => ['required', 'string'],
                'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $user = $request->user();

            // Verifica password attuale
            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'message' => 'Password attuale non corretta'
                ], 422);
            }

            // Aggiorna password
            $user->update([
                'password' => Hash::make($validated['new_password'])
            ]);

            Log::info('Password cambiata con successo', ['user_id' => $user->id]);

            return response()->json([
                'message' => 'Password cambiata con successo!'
            ]);
        } catch (\Exception $e) {
            Log::error('Errore cambio password:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Errore durante il cambio password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambia l'email dell'utente autenticato.
     */
    public function changeEmail(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'new_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string'],
            ]);

            $user = $request->user();

            // Verifica password
            if (!Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'message' => 'Password non corretta'
                ], 422);
            }

            // Aggiorna email
            $user->update([
                'email' => $validated['new_email'],
                'email_verified_at' => null // Resetta verifica email
            ]);

            // TODO: Invia email di verifica al nuovo indirizzo
            Log::info('Email cambiata con successo', [
                'user_id' => $user->id,
                'old_email' => $user->getOriginal('email'),
                'new_email' => $validated['new_email']
            ]);

            return response()->json([
                'message' => 'Email cambiata con successo! Effettua nuovamente il login.'
            ]);
        } catch (\Exception $e) {
            Log::error('Errore cambio email:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Errore durante il cambio email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Invia email di reset password.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'email'],
            ]);

            $user = \App\Models\User::where('email', $validated['email'])->first();

            if (!$user) {
                // Non rivelare se l'email esiste o no (security best practice)
                return response()->json([
                    'message' => 'Se l\'email esiste nel sistema, riceverai le istruzioni per il reset della password.'
                ]);
            }

            // Genera URL per reset password (per ora segnaposto)
            // In produzione dovresti generare un token sicuro e salvarlo nel DB
            $resetUrl = config('app.url', 'http://127.0.0.1:8000') . '/reset-password?email=' . urlencode($user->email);

            // Invia email di reset password
            $user->notify(new \App\Notifications\PasswordResetNotification($resetUrl, $user->nome, $user->id));

            Log::info('📧 Email reset password inviata', ['email' => $validated['email']]);

            return response()->json([
                'message' => 'Se l\'email esiste nel sistema, riceverai le istruzioni per il reset della password.'
            ]);
        } catch (\Exception $e) {
            Log::error('Errore reset password:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Errore durante l\'invio dell\'email di reset',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset password (senza token per ora - versione semplificata).
     */
    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'email', 'exists:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $user = \App\Models\User::where('email', $validated['email'])->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Utente non trovato'
                ], 404);
            }

            // Aggiorna la password
            $user->password = Hash::make($validated['password']);
            $user->save();

            Log::info('✅ Password reimpostata con successo', ['email' => $validated['email']]);

            return response()->json([
                'message' => 'Password reimpostata con successo!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Errore di validazione',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('❌ Errore reset password:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Errore durante il reset della password',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
