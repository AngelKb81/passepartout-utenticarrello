<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Controller per le API amministrative.
 * Gestisce statistiche, dashboard e funzionalità admin.
 */
class AdminController extends Controller
{
    // Middleware applicato nelle route

    /**
     * Statistiche generali della dashboard
     */
    public function getDashboardStats(): JsonResponse
    {
        try {
            // Statistiche base
            $totalUsers = User::count();
            $totalProducts = Product::count();
            $totalCarts = Cart::count();
            $totalRevenue = CartItem::join('carts', 'cart_items.cart_id', '=', 'carts.id')
                ->where('carts.stato', 'completato')
                ->sum(DB::raw('cart_items.quantita * cart_items.prezzo_unitario'));

            // Utenti registrati negli ultimi 30 giorni
            $recentUsers = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();

            // Prodotti più venduti
            $topProducts = CartItem::select('product_id', DB::raw('SUM(quantita) as total_sold'))
                ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
                ->where('carts.stato', 'completato')
                ->groupBy('product_id')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->with('product:id,nome,prezzo')
                ->get();

            return response()->json([
                'message' => 'Statistiche dashboard recuperate con successo',
                'stats' => [
                    'total_users' => $totalUsers,
                    'total_products' => $totalProducts,
                    'total_carts' => $totalCarts,
                    'total_revenue' => number_format($totalRevenue, 2),
                    'recent_users' => $recentUsers,
                    'top_products' => $topProducts
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
     * Statistiche utenti per grafici
     */
    public function getUserStats(): JsonResponse
    {
        try {
            // Distribuzione per titolo di studio (usando il campo corretto)
            $educationStats = User::select('titolo_studi', DB::raw('count(*) as count'))
                ->whereNotNull('titolo_studi')
                ->groupBy('titolo_studi')
                ->get();

            // Distribuzione per città di nascita
            $regionStats = User::select('citta_nascita', DB::raw('count(*) as count'))
                ->whereNotNull('citta_nascita')
                ->groupBy('citta_nascita')
                ->orderByDesc('count')
                ->limit(10)
                ->get();

            // Statistiche demografiche di base (senza campo sesso che non esiste)
            $genderStats = collect([]); // Vuoto per ora

            // Registrazioni per mese (ultimi 12 mesi)
            $monthlyRegistrations = User::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as count')
            )
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            return response()->json([
                'message' => 'Statistiche utenti recuperate con successo',
                'user_stats' => [
                    'gender' => $genderStats,
                    'education' => $educationStats,
                    'regions' => $regionStats,
                    'monthly_registrations' => $monthlyRegistrations
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nel recupero delle statistiche utenti',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiche vendite per grafici
     */
    public function getSalesStats(): JsonResponse
    {
        try {
            // Vendite per mese (ultimi 12 mesi)
            $monthlySales = CartItem::select(
                DB::raw('YEAR(carts.created_at) as year'),
                DB::raw('MONTH(carts.created_at) as month'),
                DB::raw('SUM(cart_items.quantita * cart_items.prezzo_unitario) as revenue'),
                DB::raw('COUNT(DISTINCT carts.id) as orders_count')
            )
                ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
                ->where('carts.stato', 'completato')
                ->where('carts.created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            // Top prodotti per categoria
            $categoryStats = Product::select('categoria', DB::raw('count(*) as count'))
                ->whereNotNull('categoria')
                ->groupBy('categoria')
                ->get();

            return response()->json([
                'message' => 'Statistiche vendite recuperate con successo',
                'sales_stats' => [
                    'monthly_sales' => $monthlySales,
                    'categories' => $categoryStats
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nel recupero delle statistiche vendite',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista utenti per gestione admin
     */
    public function getUsers(): JsonResponse
    {
        try {
            $users = User::with('roles')
                ->orderByDesc('created_at')
                ->paginate(15);

            return response()->json([
                'message' => 'Lista utenti recuperata con successo',
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore nel recupero degli utenti',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
