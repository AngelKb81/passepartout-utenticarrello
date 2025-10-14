<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'cognome',
        'email',
        'password',
        'titolo_studi',
        'data_nascita',
        'citta_nascita',
        // 'ruoli', // Gestito tramite relazione many-to-many
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'data_nascita' => 'date',
        ];
    }

    /**
     * Relazione many-to-many con i ruoli.
     * Un utente può avere più ruoli.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * Relazione one-to-many con i carrelli.
     * Un utente può avere più carrelli (storico).
     */
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Ottiene il carrello attivo dell'utente.
     * Se non esiste lo crea automaticamente.
     */
    public function getActiveCart()
    {
        $cart = $this->carts()->where('stato', 'attivo')->first();

        if (!$cart) {
            $cart = $this->carts()->create(['stato' => 'attivo']);
        }

        return $cart;
    }

    /**
     * Verifica se l'utente ha un determinato ruolo.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Verifica se l'utente è un amministratore.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Ottiene il nome completo dell'utente.
     */
    public function getNomeCompletoAttribute(): string
    {
        return $this->name . ' ' . $this->cognome;
    }
}
