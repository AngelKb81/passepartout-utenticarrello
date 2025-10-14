<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantita',
        'prezzo_unitario',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'prezzo_unitario' => 'decimal:2',
            'quantita' => 'integer',
        ];
    }

    /**
     * Relazione many-to-one con il carrello.
     * Un elemento appartiene a un carrello.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Relazione many-to-one con il prodotto.
     * Un elemento si riferisce a un prodotto.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calcola il subtotale per questo elemento (prezzo x quantità).
     */
    public function getSubtotaleAttribute(): float
    {
        return $this->prezzo_unitario * $this->quantita;
    }

    /**
     * Aggiorna la quantità dell'elemento nel carrello.
     */
    public function updateQuantity(int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->delete();
        }

        return $this->update(['quantita' => $quantity]);
    }
}
