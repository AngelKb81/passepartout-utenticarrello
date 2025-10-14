<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nome',
        'codice',
        'descrizione',
        'prezzo',
        'immagine',
        'attivo',
        'scorte',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'prezzo' => 'decimal:2',
            'attivo' => 'boolean',
            'scorte' => 'integer',
        ];
    }

    /**
     * Relazione one-to-many con gli elementi del carrello.
     * Un prodotto può essere in più carrelli.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Scope per ottenere solo i prodotti attivi.
     */
    public function scopeActive($query)
    {
        return $query->where('attivo', true);
    }

    /**
     * Scope per ottenere i prodotti disponibili (scorte > 0).
     */
    public function scopeAvailable($query)
    {
        return $query->where('scorte', '>', 0);
    }

    /**
     * Ottiene l'URL completo dell'immagine del prodotto.
     */
    public function getImmagineUrlAttribute(): string
    {
        if ($this->immagine) {
            return asset('images/' . $this->immagine);
        }

        return asset('images/products/placeholder.jpg');
    }

    /**
     * Verifica se il prodotto è disponibile per l'acquisto.
     */
    public function isAvailable(): bool
    {
        return $this->attivo && $this->scorte > 0;
    }

    /**
     * Verifica se ci sono scorte sufficienti per una quantità richiesta.
     */
    public function hasStock(int $quantity = 1): bool
    {
        return $this->scorte >= $quantity;
    }
}
