<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'stato',
        'ultimo_aggiornamento',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ultimo_aggiornamento' => 'datetime',
        ];
    }

    /**
     * Relazione many-to-one con l'utente.
     * Un carrello appartiene a un utente.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relazione one-to-many con gli elementi del carrello.
     * Un carrello può contenere più elementi.
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Scope per ottenere solo i carrelli attivi.
     */
    public function scopeActive($query)
    {
        return $query->where('stato', 'attivo');
    }

    /**
     * Calcola il totale del carrello.
     */
    public function getTotaleAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->prezzo_unitario * $item->quantita;
        });
    }

    /**
     * Ottiene il numero totale di articoli nel carrello.
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantita');
    }

    /**
     * Aggiunge un prodotto al carrello o aggiorna la quantità se già presente.
     */
    public function addProduct(Product $product, int $quantity = 1): CartItem
    {
        $cartItem = $this->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->update([
                'quantita' => $cartItem->quantita + $quantity,
                'prezzo_unitario' => $product->prezzo, // Aggiorna il prezzo se cambiato
            ]);
        } else {
            $cartItem = $this->items()->create([
                'product_id' => $product->id,
                'quantita' => $quantity,
                'prezzo_unitario' => $product->prezzo,
            ]);
        }

        $this->touch('ultimo_aggiornamento');

        return $cartItem;
    }

    /**
     * Rimuove un prodotto dal carrello.
     */
    public function removeProduct(Product $product): bool
    {
        $removed = $this->items()->where('product_id', $product->id)->delete();

        if ($removed) {
            $this->touch('ultimo_aggiornamento');
        }

        return $removed > 0;
    }

    /**
     * Svuota completamente il carrello.
     */
    public function clear(): bool
    {
        $cleared = $this->items()->delete();

        if ($cleared) {
            $this->touch('ultimo_aggiornamento');
        }

        return $cleared > 0;
    }
}
