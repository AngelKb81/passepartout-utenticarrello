<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;
use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller per la gestione dei prodotti del catalogo.
 * Fornisce endpoint API per CRUD prodotti e ricerca.
 */
class ProductController extends Controller
{
    protected ProductService $productService;
    protected ProductRepository $productRepository;

    public function __construct(ProductService $productService, ProductRepository $productRepository)
    {
        $this->productService = $productService;
        $this->productRepository = $productRepository;
    }

    /**
     * Lista tutti i prodotti con paginazione e filtri.
     * Per admin: mostra tutti i prodotti (attivi e inattivi)
     * Per utenti: mostra solo prodotti attivi
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $isAdmin = $request->is('api/admin/*');

            // Raccogli i filtri dalla richiesta
            $filters = [
                'categoria' => $request->get('categoria'),
                'min_price' => $request->get('min_price'),
                'max_price' => $request->get('max_price'),
                'search' => $request->get('search'),
                'sort' => $request->get('sort')
            ];

            // Rimuovi i filtri vuoti
            $filters = array_filter($filters, function ($value) {
                return !is_null($value) && $value !== '';
            });

            // Per admin mostra tutti i prodotti, altrimenti solo attivi
            if ($isAdmin) {
                $products = $this->productService->getAllProducts($perPage, $filters);
            } else {
                $products = $this->productService->getActiveProducts($perPage, $filters);
            }

            return response()->json([
                'message' => 'Prodotti recuperati con successo',
                'products' => $products['data'],
                'pagination' => $products['pagination'],
                'filters' => $filters
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nel recupero dei prodotti',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostra i dettagli di un prodotto specifico.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productRepository->findById($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Prodotto non trovato'
                ], 404);
            }

            return response()->json([
                'message' => 'Prodotto trovato',
                'product' => $product,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nel recupero del prodotto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crea un nuovo prodotto (solo admin).
     */
    public function store(CreateProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->createProduct(
                $request->validated(),
                $request->file('immagine')
            );

            return response()->json([
                'message' => 'Prodotto creato con successo!',
                'product' => $product,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nella creazione del prodotto',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Aggiorna un prodotto esistente (solo admin).
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->updateProduct(
                $id,
                $request->validated(),
                $request->file('immagine')
            );

            return response()->json([
                'message' => 'Prodotto aggiornato con successo!',
                'product' => $product,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nell\'aggiornamento del prodotto',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Elimina un prodotto (solo admin).
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->productService->deleteProduct($id);

            if (!$deleted) {
                return response()->json([
                    'message' => 'Prodotto non trovato'
                ], 404);
            }

            return response()->json([
                'message' => 'Prodotto eliminato con successo!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nell\'eliminazione del prodotto',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Cerca prodotti per nome, codice o descrizione.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $searchTerm = $request->get('q', '');

            if (empty($searchTerm)) {
                return response()->json([
                    'message' => 'Termine di ricerca obbligatorio',
                ], 422);
            }

            $products = $this->productService->searchProducts($searchTerm);

            return response()->json([
                'message' => 'Ricerca completata',
                'products' => $products,
                'search_term' => $searchTerm,
                'results_count' => count($products),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nella ricerca',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Attiva/disattiva un prodotto (solo admin).
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $product = $this->productService->toggleProductStatus($id);

            return response()->json([
                'message' => 'Stato prodotto aggiornato con successo!',
                'product' => $product,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nell\'aggiornamento dello stato',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Aggiorna le scorte di un prodotto (solo admin).
     */
    public function updateStock(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'scorte' => ['required', 'integer', 'min:0']
            ]);

            $product = $this->productService->updateProductStock($id, $request->scorte);

            return response()->json([
                'message' => 'Scorte aggiornate con successo!',
                'product' => $product,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nell\'aggiornamento delle scorte',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
