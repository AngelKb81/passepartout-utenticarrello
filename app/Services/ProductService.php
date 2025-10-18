<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Service per la gestione dei prodotti del catalogo.
 * Gestisce operazioni CRUD, upload immagini e statistiche prodotti.
 */
class ProductService
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Crea un nuovo prodotto.
     */
    public function createProduct(array $productData, ?UploadedFile $image = null): Product
    {
        // Verifica che il codice prodotto sia unico
        if ($this->productRepository->findByCode($productData['codice'])) {
            throw new \Exception('Codice prodotto già esistente');
        }

        // Gestisce l'upload dell'immagine se presente
        if ($image) {
            $productData['immagine'] = $this->handleImageUpload($image);
        }

        return $this->productRepository->create($productData);
    }

    /**
     * Aggiorna un prodotto esistente.
     */
    public function updateProduct(int $productId, array $productData, ?UploadedFile $image = null): Product
    {
        $product = $this->productRepository->findByIdOrFail($productId);

        // Verifica unicità del codice se è stato modificato
        if (isset($productData['codice']) && $productData['codice'] !== $product->codice) {
            if ($this->productRepository->findByCode($productData['codice'])) {
                throw new \Exception('Codice prodotto già esistente');
            }
        }

        // Gestisce l'upload della nuova immagine
        if ($image) {
            // Rimuove la vecchia immagine se esiste
            $this->deleteProductImage($product);
            $productData['immagine'] = $this->handleImageUpload($image);
        }

        $this->productRepository->update($productId, $productData);
        return $this->productRepository->findByIdOrFail($productId);
    }

    /**
     * Elimina un prodotto.
     */
    public function deleteProduct(int $productId): bool
    {
        $product = $this->productRepository->findByIdOrFail($productId);

        // Rimuove l'immagine associata
        $this->deleteProductImage($product);

        return $this->productRepository->delete($productId);
    }

    /**
     * Cerca prodotti nel catalogo.
     */
    public function searchProducts(string $searchTerm, bool $activeOnly = true): array
    {
        $products = $this->productRepository->searchProducts($searchTerm, $activeOnly);

        return $products->map(function ($product) {
            return $this->formatProductForResponse($product);
        })->toArray();
    }

    /**
     * Ottiene i prodotti attivi con paginazione e filtri.
     */
    public function getActiveProducts(int $perPage = 15, array $filters = []): array
    {
        $products = $this->productRepository->getActiveProductsPaginated($perPage, $filters);

        return [
            'data' => $products->getCollection()->map(function ($product) {
                return $this->formatProductForResponse($product);
            }),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ];
    }

    /**
     * Ottiene tutti i prodotti (attivi e inattivi) per l'admin.
     */
    public function getAllProducts(int $perPage = 15, array $filters = []): array
    {
        $products = $this->productRepository->getAllProductsPaginated($perPage, $filters);

        return [
            'data' => $products->getCollection()->map(function ($product) {
                return $this->formatProductForResponse($product);
            }),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ];
    }

    /**
     * Ottiene i prodotti più venduti per la dashboard.
     */
    public function getBestSellingProducts(int $limit = 10): array
    {
        $products = $this->productRepository->getBestSellingProducts($limit);

        return $products->map(function ($product) {
            return [
                'id' => $product->id,
                'nome' => $product->nome,
                'codice' => $product->codice,
                'prezzo' => $product->prezzo,
                'vendite_simulate' => rand(50, 500), // Simulazione vendite per dashboard
            ];
        })->toArray();
    }

    /**
     * Ottiene statistiche sui prodotti per la dashboard admin.
     */
    public function getProductsStatistics(): array
    {
        $stats = $this->productRepository->getProductStats();
        $lowStockProducts = $this->productRepository->getLowStockProducts();

        return array_merge($stats, [
            'low_stock_products' => $lowStockProducts->count(),
            'low_stock_details' => $lowStockProducts->map(function ($product) {
                return [
                    'id' => $product->id,
                    'nome' => $product->nome,
                    'codice' => $product->codice,
                    'scorte' => $product->scorte,
                ];
            })->toArray(),
            'avg_price' => $this->productRepository->all()->avg('prezzo'),
        ]);
    }

    /**
     * Aggiorna le scorte di un prodotto.
     */
    public function updateProductStock(int $productId, int $newStock): Product
    {
        if ($newStock < 0) {
            throw new \Exception('Le scorte non possono essere negative');
        }

        $this->productRepository->updateStock($productId, $newStock);
        return $this->productRepository->findByIdOrFail($productId);
    }

    /**
     * Attiva o disattiva un prodotto.
     */
    public function toggleProductStatus(int $productId): Product
    {
        $product = $this->productRepository->findByIdOrFail($productId);

        $this->productRepository->update($productId, [
            'attivo' => !$product->attivo
        ]);

        return $this->productRepository->findByIdOrFail($productId);
    }

    /**
     * Gestisce l'upload dell'immagine prodotto.
     */
    private function handleImageUpload(UploadedFile $image): string
    {
        // Validazione dell'immagine
        if (!in_array($image->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'webp'])) {
            throw new \Exception('Formato immagine non supportato. Utilizzare: jpg, jpeg, png, webp');
        }

        if ($image->getSize() > 5 * 1024 * 1024) { // 5MB
            throw new \Exception('Immagine troppo grande. Dimensione massima: 5MB');
        }

        // Genera nome file unico
        $filename = 'product_' . Str::uuid() . '.' . $image->getClientOriginalExtension();

        // Salva l'immagine nella cartella public/images/products
        $image->move(public_path('images/products'), $filename);

        return 'products/' . $filename;
    }

    /**
     * Elimina l'immagine di un prodotto.
     */
    private function deleteProductImage(Product $product): void
    {
        if ($product->immagine && file_exists(public_path('images/' . $product->immagine))) {
            unlink(public_path('images/' . $product->immagine));
        }
    }

    /**
     * Formatta un prodotto per la risposta API.
     */
    private function formatProductForResponse(Product $product): array
    {
        return [
            'id' => $product->id,
            'nome' => $product->nome,
            'codice' => $product->codice,
            'descrizione' => $product->descrizione,
            'prezzo' => $product->prezzo,
            'immagine_url' => $product->immagine_url,
            'attivo' => $product->attivo,
            'scorte' => $product->scorte,
            'disponibile' => $product->isAvailable(),
            'created_at' => $product->created_at->format('d/m/Y H:i'),
            'updated_at' => $product->updated_at->format('d/m/Y H:i'),
        ];
    }
}
