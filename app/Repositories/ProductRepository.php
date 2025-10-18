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
     * Ottiene i prodotti attivi con paginazione e filtri.
     */
    public function getActiveProductsPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->active();

        // Filtro per categoria
        if (!empty($filters['categoria'])) {
            $query->where('categoria', $filters['categoria']);
        }

        // Filtro per prezzo minimo
        if (!empty($filters['min_price'])) {
            $query->where('prezzo_attuale', '>=', $filters['min_price']);
        }

        // Filtro per prezzo massimo
        if (!empty($filters['max_price'])) {
            $query->where('prezzo_attuale', '<=', $filters['max_price']);
        }

        // Filtro per ricerca testuale
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $search = $filters['search'];
                $q->where('nome', 'LIKE', "%{$search}%")
                    ->orWhere('codice', 'LIKE', "%{$search}%")
                    ->orWhere('descrizione', 'LIKE', "%{$search}%");
            });
        }

        // Ordinamento
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'nome_asc':
                    $query->orderBy('nome', 'asc');
                    break;
                case 'nome_desc':
                    $query->orderBy('nome', 'desc');
                    break;
                case 'prezzo_asc':
                    $query->orderBy('prezzo_attuale', 'asc');
                    break;
                case 'prezzo_desc':
                    $query->orderBy('prezzo_attuale', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        /** @var LengthAwarePaginator */
        return $query->paginate($perPage);
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
     * Ottiene tutti i prodotti (inclusi inattivi) con paginazione e filtri - Solo per Admin.
     */
    public function getAllProductsPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Filtro per categoria
        if (!empty($filters['categoria'])) {
            $query->where('categoria', $filters['categoria']);
        }

        // Filtro per prezzo minimo
        if (!empty($filters['min_price'])) {
            $query->where('prezzo_attuale', '>=', $filters['min_price']);
        }

        // Filtro per prezzo massimo
        if (!empty($filters['max_price'])) {
            $query->where('prezzo_attuale', '<=', $filters['max_price']);
        }

        // Filtro per ricerca testuale
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $search = $filters['search'];
                $q->where('nome', 'LIKE', "%{$search}%")
                    ->orWhere('codice', 'LIKE', "%{$search}%")
                    ->orWhere('descrizione', 'LIKE', "%{$search}%");
            });
        }

        // Filtro per stato attivo/inattivo
        if (isset($filters['attivo'])) {
            $query->where('attivo', $filters['attivo']);
        }

        // Ordinamento
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'nome_asc':
                    $query->orderBy('nome', 'asc');
                    break;
                case 'nome_desc':
                    $query->orderBy('nome', 'desc');
                    break;
                case 'prezzo_asc':
                    $query->orderBy('prezzo_attuale', 'asc');
                    break;
                case 'prezzo_desc':
                    $query->orderBy('prezzo_attuale', 'desc');
                    break;
                default:
                    $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        /** @var LengthAwarePaginator */
        return $query->paginate($perPage);
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
