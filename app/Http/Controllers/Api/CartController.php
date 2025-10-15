<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller per la gestione del carrello utenti.
 * Fornisce endpoint API per operazioni sul carrello.
 */
class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Ottiene il carrello dell'utente autenticato.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $cart = $this->cartService->getUserCart($request->user()->id);

            return response()->json([
                'message' => 'Carrello recuperato con successo',
                'cart' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nel recupero del carrello',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aggiunge un prodotto al carrello.
     */
    public function addToCart(AddToCartRequest $request): JsonResponse
    {
        try {
            $cart = $this->cartService->addProductToCart(
                $request->user()->id,
                $request->product_id,
                $request->quantita
            );

            return response()->json([
                'message' => 'Prodotto aggiunto al carrello con successo!',
                'cart' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nell\'aggiunta al carrello',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Aggiorna la quantità di un prodotto nel carrello.
     */
    public function updateQuantity(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'product_id' => ['required', 'integer', 'exists:products,id'],
                'quantita' => ['required', 'integer', 'min:0', 'max:99'],
            ]);

            $cart = $this->cartService->updateCartItemQuantity(
                $request->user()->id,
                $request->product_id,
                $request->quantita
            );

            return response()->json([
                'message' => 'Quantità aggiornata con successo!',
                'cart' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nell\'aggiornamento della quantità',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Rimuove un prodotto dal carrello.
     */
    public function removeFromCart(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'product_id' => ['required', 'integer', 'exists:products,id'],
            ]);

            $cart = $this->cartService->removeProductFromCart(
                $request->user()->id,
                $request->product_id
            );

            return response()->json([
                'message' => 'Prodotto rimosso dal carrello!',
                'cart' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nella rimozione dal carrello',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Svuota completamente il carrello.
     */
    public function clearCart(Request $request): JsonResponse
    {
        try {
            $cart = $this->cartService->clearUserCart($request->user()->id);

            return response()->json([
                'message' => 'Carrello svuotato con successo!',
                'cart' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nello svuotamento del carrello',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ottiene il numero di articoli nel carrello (per badge UI).
     */
    public function getItemsCount(Request $request): JsonResponse
    {
        try {
            $count = $this->cartService->getCartItemsCount($request->user()->id);

            return response()->json([
                'items_count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nel conteggio articoli',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcola il totale del carrello.
     */
    public function getTotal(Request $request): JsonResponse
    {
        try {
            $total = $this->cartService->getCartTotal($request->user()->id);

            return response()->json([
                'total' => $total,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nel calcolo del totale',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simula il processo di checkout.
     */
    public function checkout(Request $request): JsonResponse
    {
        try {
            $shippingData = $request->validate([
                'indirizzo' => ['nullable', 'string', 'max:255'],
                'citta' => ['nullable', 'string', 'max:100'],
                'cap' => ['nullable', 'string', 'max:10'],
                'note' => ['nullable', 'string', 'max:500'],
            ]);

            $user = $request->user();
            $cart = $this->cartService->getUserCart($user->id);
            $totalAmount = $cart ? $cart->totale() : 0;

            $order = $this->cartService->processCheckout($user->id, $shippingData);

            // Invia notifica di checkout completato
            if ($cart) {
                $user->notify(new \App\Notifications\CartCheckedOut($cart, $totalAmount));
            }

            return response()->json([
                'message' => 'Ordine completato con successo!',
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nel completamento dell\'ordine',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
