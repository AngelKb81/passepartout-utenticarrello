<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\UserEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller per le statistiche della dashboard amministrativa.
 */
class AdminDashboardController extends Controller
{
    /**
     * Ottiene le statistiche generali per la dashboard admin.
     */
    public function getStats()
    {
        try {
            // Statistiche base
            $totalUsers = User::count();
            $totalProducts = Product::count();
            $totalEmails = UserEmail::count();
            $activeProducts = Product::where('attivo', true)->count();

            // Prodotti per categoria
            $productsByCategory = Product::select('categoria', DB::raw('count(*) as count'))
                ->groupBy('categoria')
                ->orderBy('count', 'desc')
                ->get();

            // Registrazioni utenti per mese (ultimi 6 mesi)
            $userRegistrations = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return response()->json([
                'message' => 'Statistiche recuperate con successo',
                'stats' => [
                    'total_users' => $totalUsers,
                    'total_products' => $totalProducts,
                    'total_emails' => $totalEmails,
                    'active_products' => $activeProducts,
                    'products_by_category' => $productsByCategory,
                    'user_registrations' => $userRegistrations,
                    // Rimuoviamo i dati sui carrelli che non servono
                    'total_carts' => 0
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nel recupero delle statistiche',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ottiene statistiche sui prodotti.
     */
    public function getProductStats()
    {
        try {
            $stats = [
                'total' => Product::count(),
                'active' => Product::where('attivo', true)->count(),
                'inactive' => Product::where('attivo', false)->count(),
                'low_stock' => Product::where('scorte', '<', 10)->count(),
                'out_of_stock' => Product::where('scorte', 0)->count(),
                'by_category' => Product::select('categoria', DB::raw('count(*) as count'))
                    ->groupBy('categoria')
                    ->orderBy('count', 'desc')
                    ->get()
            ];

            return response()->json([
                'message' => 'Statistiche prodotti recuperate',
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nel recupero statistiche prodotti',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
