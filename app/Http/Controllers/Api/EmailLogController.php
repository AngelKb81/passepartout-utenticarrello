<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserEmail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmailLogController extends Controller
{
    /**
     * Lista di tutte le email inviate
     */
    public function index(Request $request): JsonResponse
    {
        $query = UserEmail::with('user:id,nome,cognome,email')
            ->orderBy('created_at', 'desc');

        // Filtro per tipo
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filtro per status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filtro per user_id
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Ricerca per email
        if ($request->has('search') && $request->search) {
            $query->where('recipient_email', 'like', '%' . $request->search . '%');
        }

        $emails = $query->paginate($request->get('per_page', 20));

        // Trasforma i dati per compatibilità frontend
        $emails->getCollection()->transform(function ($email) {
            $email->status_type = $email->status;
            $email->template_type = $email->type;
            $email->body = $email->content;
            return $email;
        });

        return response()->json($emails);
    }

    /**
     * Dettaglio singola email
     */
    public function show(int $id): JsonResponse
    {
        $email = UserEmail::with('user:id,nome,cognome,email')
            ->findOrFail($id);

        // Trasforma i dati per compatibilità frontend
        $email->status_type = $email->status;
        $email->template_type = $email->type;
        $email->body = $email->content;

        return response()->json($email);
    }

    /**
     * Statistiche email
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => UserEmail::count(),
            'sent' => UserEmail::where('status', 'sent')->count(),
            'failed' => UserEmail::where('status', 'failed')->count(),
            'pending' => UserEmail::where('status', 'pending')->count(),
            'by_type' => UserEmail::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type'),
            'recent' => UserEmail::orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['id', 'recipient_email', 'type', 'status', 'created_at']),
        ];

        return response()->json($stats);
    }
}
