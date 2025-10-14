<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository per la gestione degli utenti del sistema.
 * Fornisce metodi specifici per operazioni sugli utenti.
 */
class UserRepository extends BaseRepository
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * Trova un utente tramite email.
     */
    public function findByEmail(string $email): ?User
    {
        /** @var User */
        return $this->model->where('email', $email)->first();
    }

    /**
     * Ottiene utenti con un determinato ruolo.
     */
    public function getUsersByRole(string $roleName): Collection
    {
        /** @var Collection */
        return $this->model->whereHas('roles', function ($query) use ($roleName) {
            $query->where('name', $roleName);
        })->get();
    }

    /**
     * Ottiene le statistiche degli utenti per città di nascita.
     */
    public function getUsersByCityStats(): Collection
    {
        /** @var Collection */
        return $this->model
            ->selectRaw('citta_nascita, COUNT(*) as count')
            ->whereNotNull('citta_nascita')
            ->groupBy('citta_nascita')
            ->orderByDesc('count')
            ->get();
    }

    /**
     * Ottiene le statistiche degli utenti per titolo di studi.
     */
    public function getUsersByEducationStats(): Collection
    {
        /** @var Collection */
        return $this->model
            ->selectRaw('titolo_studi, COUNT(*) as count')
            ->whereNotNull('titolo_studi')
            ->groupBy('titolo_studi')
            ->orderByDesc('count')
            ->get();
    }

    /**
     * Ottiene il numero di utenti registrati per mese.
     */
    public function getUsersRegistrationByMonth(int $months = 12): Collection
    {
        /** @var Collection */
        return $this->model
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    /**
     * Verifica se un utente ha un ruolo specifico.
     */
    public function userHasRole(int $userId, string $roleName): bool
    {
        $user = $this->findById($userId);
        return $user ? $user->hasRole($roleName) : false;
    }

    /**
     * Assegna un ruolo a un utente.
     */
    public function assignRole(int $userId, string $roleName): bool
    {
        $user = $this->findByIdOrFail($userId);
        $role = \App\Models\Role::where('name', $roleName)->first();

        if (!$role) {
            throw new \Exception("Ruolo '{$roleName}' non trovato");
        }

        if (!$user->roles()->where('role_id', $role->id)->exists()) {
            $user->roles()->attach($role->id);
            return true;
        }

        return false; // Ruolo già assegnato
    }

    /**
     * Rimuove un ruolo da un utente.
     */
    public function removeRole(int $userId, string $roleName): bool
    {
        $user = $this->findByIdOrFail($userId);
        $role = \App\Models\Role::where('name', $roleName)->first();

        if (!$role) {
            return false;
        }

        return $user->roles()->detach($role->id) > 0;
    }
}
