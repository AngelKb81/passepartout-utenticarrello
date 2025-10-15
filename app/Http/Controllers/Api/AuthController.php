<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            $user = $this->userService->register($request->validated());

            // Invia notifica di benvenuto
            $user->notify(new \App\Notifications\WelcomeNotification());

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

            // Crea il token di accesso
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login effettuato con successo!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'cognome' => $user->cognome,
                    'email' => $user->email,
                    'nome_completo' => $user->nome_completo,
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
                    'name' => $user->name,
                    'cognome' => $user->cognome,
                    'email' => $user->email,
                    'titolo_studi' => $user->titolo_studi,
                    'data_nascita' => $user->data_nascita ? $user->data_nascita->format('Y-m-d') : null,
                    'citta_nascita' => $user->citta_nascita,
                    'nome_completo' => $user->nome_completo,
                    'roles' => $user->roles->pluck('name'),
                    'is_admin' => $user->isAdmin(),
                    'email_verified_at' => $user->email_verified_at,
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
                'name' => ['required', 'string', 'max:255'],
                'cognome' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
                'titolo_studi' => ['nullable', 'string', 'max:255'],
                'data_nascita' => ['nullable', 'date', 'before:today'],
                'citta_nascita' => ['nullable', 'string', 'max:255'],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            ]);

            $user = $this->userService->updateUserProfile($request->user()->id, $validated);

            return response()->json([
                'message' => 'Profilo aggiornato con successo!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'cognome' => $user->cognome,
                    'email' => $user->email,
                    'titolo_studi' => $user->titolo_studi,
                    'data_nascita' => $user->data_nascita ? $user->data_nascita->format('Y-m-d') : null,
                    'citta_nascita' => $user->citta_nascita,
                    'nome_completo' => $user->nome_completo,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nell\'aggiornamento del profilo',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
