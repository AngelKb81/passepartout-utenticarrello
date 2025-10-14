<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Repository per la gestione dei prodotti.
 * Gestisce tutte le operazioni CRUD sui prodotti del catalogo.
 */
class ProductRepository extends BaseRepository
{
    public function __construct(Product $product)
    {
        parent::__construct($product);
    }

    /**
     * Ottiene tutti i prodotti attivi.
     */
    public function getActiveProducts(): Collection
    {
        /** @var Collection */
        return $this->model->active()->get();
    }

    /**
     * Ottiene i prodotti attivi con paginazione.
     */
    public function getActiveProductsPaginated(int $perPage = 15): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator */
        return $this->model->active()->paginate($perPage);
    }

    /**
     * Cerca prodotti per nome o codice.
     */
    public function searchProducts(string $search, bool $activeOnly = true): Collection
    {
        $query = $this->model->where(function ($q) use ($search) {
            $q->where('nome', 'LIKE', "%{$search}%")
                ->orWhere('codice', 'LIKE', "%{$search}%")
                ->orWhere('descrizione', 'LIKE', "%{$search}%");
        });

        if ($activeOnly) {
            $query->active();
        }

        /** @var Collection */
        return $query->get();
    }

    /**
     * Trova un prodotto per codice.
     */
    public function findByCode(string $code): ?Product
    {
        /** @var Product */
        return $this->model->where('codice', $code)->first();
    }

    /**
     * Ottiene i prodotti più venduti (simulato con dati casuali per ora).
     */
    public function getBestSellingProducts(int $limit = 10): Collection
    {
        /** @var Collection */
        return $this->model
            ->active()
            ->inRandomOrder() // Temporaneo: in futuro si baserebbe sui dati reali di vendita
            ->take($limit)
            ->get();
    }

    /**
     * Ottiene i prodotti con scorte basse.
     */
    public function getLowStockProducts(int $threshold = 10): Collection
    {
        /** @var Collection */
        return $this->model
            ->where('scorte', '<=', $threshold)
            ->where('attivo', true)
            ->orderBy('scorte')
            ->get();
    }

    /**
     * Aggiorna le scorte di un prodotto.
     */
    public function updateStock(int $productId, int $quantity): bool
    {
        $product = $this->findByIdOrFail($productId);
        return $product->update(['scorte' => $quantity]);
    }

    /**
     * Decrementa le scorte di un prodotto.
     */
    public function decrementStock(int $productId, int $quantity): bool
    {
        $product = $this->findByIdOrFail($productId);

        if ($product->scorte < $quantity) {
            throw new \Exception('Scorte insufficienti per il prodotto: ' . $product->nome);
        }

        return $product->update(['scorte' => $product->scorte - $quantity]);
    }

    /**
     * Incrementa le scorte di un prodotto.
     */
    public function incrementStock(int $productId, int $quantity): bool
    {
        $product = $this->findByIdOrFail($productId);
        return $product->update(['scorte' => $product->scorte + $quantity]);
    }

    /**
     * Ottiene statistiche sui prodotti per categoria (future implementazioni).
     */
    public function getProductStats(): array
    {
        return [
            'total_products' => $this->count(),
            'active_products' => $this->model->active()->count(),
            'out_of_stock' => $this->model->where('scorte', 0)->count(),
            'low_stock' => $this->model->where('scorte', '>', 0)->where('scorte', '<=', 10)->count(),
        ];
    }

    /**
     * Ottiene i prodotti ordinati per prezzo.
     */
    public function getProductsByPrice(string $order = 'asc'): Collection
    {
        /** @var Collection */
        return $this->model
            ->active()
            ->orderBy('prezzo', $order)
            ->get();
    }
}
