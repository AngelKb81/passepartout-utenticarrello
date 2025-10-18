<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository per la gestione del carrello utenti.
 * Gestisce operazioni sui carrelli e i loro elementi.
 */
class CartRepository extends BaseRepository
{
    public function __construct(Cart $cart)
    {
        parent::__construct($cart);
    }

    /**
     * Ottiene il carrello attivo di un utente.
     */
    public function getActiveCartByUser(int $userId): ?Cart
    {
        /** @var Cart */
        return $this->model
            ->with(['items.product'])
            ->where('user_id', $userId)
            ->where('stato', 'attivo')
            ->first();
    }

    /**
     * Crea un nuovo carrello attivo per un utente.
     */
    public function createActiveCart(int $userId): Cart
    {
        /** @var Cart */
        return $this->model->create([
            'user_id' => $userId,
            'stato' => 'attivo',
            'ultimo_aggiornamento' => now(),
        ]);
    }

    /**
     * Ottiene o crea il carrello attivo per un utente.
     */
    public function getOrCreateActiveCart(int $userId): Cart
    {
        $cart = $this->getActiveCartByUser($userId);

        if (!$cart) {
            $cart = $this->createActiveCart($userId);
        }

        return $cart;
    }

    /**
     * Aggiunge un prodotto al carrello.
     */
    public function addProductToCart(int $userId, int $productId, int $quantity = 1): Cart
    {
        $cart = $this->getOrCreateActiveCart($userId);
        $product = Product::findOrFail($productId);

        if (!$product->isAvailable()) {
            throw new \Exception('Prodotto non disponibile: ' . $product->nome);
        }

        if (!$product->hasStock($quantity)) {
            throw new \Exception('Scorte insufficienti per il prodotto: ' . $product->nome);
        }

        $cart->addProduct($product, $quantity);

        return $cart->load(['items.product']);
    }

    /**
     * Rimuove un prodotto dal carrello.
     */
    public function removeProductFromCart(int $userId, int $productId): bool
    {
        $cart = $this->getActiveCartByUser($userId);

        if (!$cart) {
            return false;
        }

        $product = Product::findOrFail($productId);
        return $cart->removeProduct($product);
    }

    /**
     * Aggiorna la quantità di un prodotto nel carrello.
     */
    public function updateCartItemQuantity(int $userId, int $productId, int $quantity): Cart
    {
        $cart = $this->getActiveCartByUser($userId);

        if (!$cart) {
            throw new \Exception('Carrello non trovato');
        }

        $cartItem = $cart->items()->where('product_id', $productId)->first();

        if (!$cartItem) {
            throw new \Exception('Prodotto non trovato nel carrello');
        }

        if ($quantity <= 0) {
            $cartItem->delete();
        } else {
            // Verifica disponibilità scorte
            if (!$cartItem->product->hasStock($quantity)) {
                throw new \Exception('Scorte insufficienti per il prodotto: ' . $cartItem->product->nome);
            }

            $cartItem->update(['quantita' => $quantity]);
        }

        $cart->touch('ultimo_aggiornamento');

        return $cart->load(['items.product']);
    }

    /**
     * Svuota il carrello di un utente.
     */
    public function clearCart(int $userId): bool
    {
        $cart = $this->getActiveCartByUser($userId);

        if (!$cart) {
            return false;
        }

        return $cart->clear();
    }

    /**
     * Aggiorna un articolo specifico del carrello tramite ID (per test).
     */
    public function updateCartItemById(int $userId, int $cartItemId, int $quantity): Cart
    {
        $cart = $this->getActiveCartByUser($userId);

        if (!$cart) {
            throw new \Exception('Carrello non trovato');
        }

        $cartItem = $cart->items()->where('id', $cartItemId)->first();

        if (!$cartItem) {
            throw new \Exception('Articolo non trovato nel carrello');
        }

        if ($quantity <= 0) {
            $cartItem->delete();
        } else {
            // Verifica disponibilità scorte
            if (!$cartItem->product->hasStock($quantity)) {
                throw new \Exception('Scorte insufficienti per il prodotto: ' . $cartItem->product->nome);
            }

            $cartItem->update(['quantita' => $quantity]);
        }

        $cart->touch('ultimo_aggiornamento');

        return $cart->load(['items.product']);
    }

    /**
     * Rimuove un articolo specifico dal carrello tramite ID (per test).
     */
    public function removeCartItemById(int $userId, int $cartItemId): bool
    {
        $cart = $this->getActiveCartByUser($userId);

        if (!$cart) {
            throw new \Exception('Carrello non trovato');
        }

        // Prima verifica se l'item esiste (per qualsiasi utente)
        $itemExists = \App\Models\CartItem::where('id', $cartItemId)->exists();
        
        if ($itemExists) {
            // L'item esiste ma cerchiamo nel carrello dell'utente corrente
            $cartItem = $cart->items()->where('id', $cartItemId)->first();
            
            if (!$cartItem) {
                throw new \InvalidArgumentException('AUTHORIZATION_ERROR:Non autorizzato a modificare questo carrello');
            }
        } else {
            throw new \Exception('Articolo non trovato');
        }

        $cartItem->delete();
        $cart->touch('ultimo_aggiornamento');

        return true;
    }

    /**
     * Calcola il totale del carrello.
     */
    public function getCartTotal(int $userId): float
    {
        $cart = $this->getActiveCartByUser($userId);

        return $cart ? $cart->totale : 0.0;
    }

    /**
     * Ottiene il numero di articoli nel carrello.
     */
    public function getCartItemsCount(int $userId): int
    {
        $cart = $this->getActiveCartByUser($userId);

        return $cart ? $cart->total_items : 0;
    }

    /**
     * Ottiene tutti i carrelli con i loro totali (per statistiche admin).
     */
    public function getCartsWithTotals(): Collection
    {
        /** @var Collection */
        return $this->model
            ->with(['user', 'items.product'])
            ->get()
            ->map(function ($cart) {
                $cart->totale_calcolato = $cart->totale;
                return $cart;
            });
    }

    /**
     * Ottiene le statistiche sui carrelli abbandonati.
     */
    public function getAbandonedCartsStats(): array
    {
        $totalCarts = $this->model->count();
        $abandonedCarts = $this->model->where('stato', 'abbandonato')->count();
        $activeCarts = $this->model->where('stato', 'attivo')->count();

        return [
            'total_carts' => $totalCarts,
            'abandoned_carts' => $abandonedCarts,
            'active_carts' => $activeCarts,
            'abandonment_rate' => $totalCarts > 0 ? ($abandonedCarts / $totalCarts) * 100 : 0,
        ];
    }
}
