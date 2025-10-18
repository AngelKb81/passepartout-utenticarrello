<?php

namespace App\Services;

use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Service per la gestione del carrello utenti.
 * Gestisce aggiunta, rimozione e modifica prodotti nel carrello.
 */
class CartService
{
    protected CartRepository $cartRepository;
    protected ProductRepository $productRepository;

    public function __construct(
        CartRepository $cartRepository,
        ProductRepository $productRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Ottiene il carrello corrente dell'utente.
     */
    public function getUserCart(int $userId): array
    {
        $cart = $this->cartRepository->getActiveCartByUser($userId);

        if (!$cart) {
            return [
                'items' => [],
                'totale' => 0,
                'total_items' => 0,
                'stato' => 'vuoto'
            ];
        }

        return $this->formatCartForResponse($cart);
    }

    /**
     * Aggiunge un prodotto al carrello dell'utente.
     */
    public function addProductToCart(int $userId, int $productId, int $quantity = 1): array
    {
        if ($quantity <= 0) {
            throw new \Exception('La quantità deve essere maggiore di zero');
        }

        $product = $this->productRepository->findByIdOrFail($productId);

        if (!$product->isAvailable()) {
            throw new \Exception('Prodotto non disponibile: ' . $product->nome);
        }

        $cart = $this->cartRepository->addProductToCart($userId, $productId, $quantity);

        return $this->formatCartForResponse($cart);
    }

    /**
     * Rimuove un prodotto dal carrello.
     */
    public function removeProductFromCart(int $userId, int $productId): array
    {
        $this->cartRepository->removeProductFromCart($userId, $productId);

        return $this->getUserCart($userId);
    }

    /**
     * Aggiorna la quantità di un prodotto nel carrello.
     */
    public function updateCartItemQuantity(int $userId, int $productId, int $quantity): array
    {
        if ($quantity < 0) {
            throw new \Exception('La quantità non può essere negativa');
        }

        $cart = $this->cartRepository->updateCartItemQuantity($userId, $productId, $quantity);

        return $this->formatCartForResponse($cart);
    }

    /**
     * Svuota completamente il carrello dell'utente.
     */
    public function clearUserCart(int $userId): array
    {
        $this->cartRepository->clearCart($userId);

        return $this->getUserCart($userId);
    }

    /**
     * Aggiorna un articolo specifico del carrello tramite ID (per test).
     */
    public function updateCartItemById(int $userId, int $cartItemId, int $quantity): array
    {
        if ($quantity < 0) {
            throw new \Exception('La quantità non può essere negativa');
        }

        $cart = $this->cartRepository->updateCartItemById($userId, $cartItemId, $quantity);

        return $this->formatCartForResponse($cart);
    }

    /**
     * Rimuove un articolo specifico dal carrello tramite ID (per test).
     */
    public function removeCartItemById(int $userId, int $cartItemId): array
    {
        $this->cartRepository->removeCartItemById($userId, $cartItemId);

        return $this->getUserCart($userId);
    }

    /**
     * Ottiene il numero di articoli nel carrello (per badge UI).
     */
    public function getCartItemsCount(int $userId): int
    {
        return $this->cartRepository->getCartItemsCount($userId);
    }

    /**
     * Calcola il totale del carrello.
     */
    public function getCartTotal(int $userId): float
    {
        return $this->cartRepository->getCartTotal($userId);
    }

    /**
     * Simula il processo di checkout (per future implementazioni).
     */
    public function processCheckout(int $userId, array $shippingData = []): array
    {
        $cart = $this->cartRepository->getActiveCartByUser($userId);

        if (!$cart || $cart->items->isEmpty()) {
            throw new \Exception('Carrello vuoto, impossibile procedere con l\'ordine');
        }

        // Verifica disponibilità di tutti i prodotti
        foreach ($cart->items as $item) {
            if (!$item->product->hasStock($item->quantita)) {
                throw new \Exception("Scorte insufficienti per: {$item->product->nome}");
            }
        }

        // Simula il processo di ordine
        $orderId = 'ORD-' . time() . '-' . $userId;
        $totale = $cart->totale;

        // In un'implementazione reale qui:
        // - Si decrementerebbero le scorte
        // - Si salverebbe l'ordine
        // - Si processerebbero i pagamenti
        // - Si invierebbe conferma email

        // Per ora marca il carrello come ordinato
        $cart->update(['stato' => 'ordinato']);

        return [
            'ordine_id' => $orderId,
            'totale' => $totale,
            'stato' => 'confermato',
            'items' => $cart->items->count(),
            'data_ordine' => now()->format('d/m/Y H:i'),
        ];
    }

    /**
     * Ottiene statistiche sui carrelli per la dashboard admin.
     */
    public function getCartStatistics(): array
    {
        $stats = $this->cartRepository->getAbandonedCartsStats();
        $cartsWithTotals = $this->cartRepository->getCartsWithTotals();

        // Calcola fatturato simulato
        $totalRevenue = $cartsWithTotals
            ->where('stato', 'ordinato')
            ->sum('totale_calcolato');

        // Statistiche prodotti più aggiunti al carrello
        $popularProducts = $this->getPopularCartProducts();

        return array_merge($stats, [
            'total_revenue' => $totalRevenue,
            'average_cart_value' => $cartsWithTotals->where('totale_calcolato', '>', 0)->avg('totale_calcolato') ?? 0,
            'popular_products' => $popularProducts,
            'monthly_revenue' => $this->getMonthlyRevenue(),
        ]);
    }

    /**
     * Ottiene i prodotti più popolari nei carrelli.
     */
    private function getPopularCartProducts(int $limit = 10): array
    {
        // Query per ottenere i prodotti più aggiunti ai carrelli
        $popularProducts = DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->selectRaw('products.nome, products.codice, SUM(cart_items.quantita) as total_quantity')
            ->groupBy('products.id', 'products.nome', 'products.codice')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();

        return $popularProducts->toArray();
    }

    /**
     * Ottiene il fatturato mensile simulato.
     */
    private function getMonthlyRevenue(int $months = 12): array
    {
        // Simulazione fatturato mensile per la dashboard
        $monthlyData = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthKey = $month->format('Y-m');

            // In un'implementazione reale si baserebbe sui dati reali degli ordini
            $monthlyData[] = [
                'month' => $monthKey,
                'month_name' => $month->format('F Y'),
                'revenue' => rand(5000, 50000), // Simulazione per demo
                'orders' => rand(50, 500),
            ];
        }

        return $monthlyData;
    }

    /**
     * Formatta il carrello per la risposta API.
     */
    private function formatCartForResponse(Cart $cart): array
    {
        return [
            'id' => $cart->id,
            'stato' => $cart->stato,
            'items' => $cart->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product' => [
                        'id' => $item->product->id,
                        'nome' => $item->product->nome,
                        'codice' => $item->product->codice,
                        'descrizione' => $item->product->descrizione,
                        'prezzo' => $item->product->prezzo,  // Cambiato da prezzo_attuale
                        'immagine' => $item->product->immagine,  // Cambiato da immagine_url
                        'categoria' => $item->product->categoria,
                        'disponibile' => $item->product->isAvailable(),
                    ],
                    'quantita' => $item->quantita,
                    'quantity' => $item->quantita,  // Alias per compatibilità
                    'prezzo_unitario' => $item->prezzo_unitario,
                    'subtotale' => $item->subtotale,
                ];
            })->toArray(),
            'totale' => $cart->totale,
            'total_items' => $cart->total_items,
            'ultimo_aggiornamento' => $cart->ultimo_aggiornamento->format('d/m/Y H:i'),
        ];
    }
}
