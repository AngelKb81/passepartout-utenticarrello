#!/bin/bash

# Script per avviare entrambi i server di sviluppo
# Laravel (backend) e Vite (frontend)

echo "🚀 Avvio Passepartout Utenti Carrello..."

# Libera le porte se occupate
echo ""
echo "✅ Porte liberate"

# Avvia Laravel in background
echo ""
echo "📦 Avvio server Laravel su http://127.0.0.1:8000"
php artisan serve --host=127.0.0.1 --port=8000 &
LARAVEL_PID=$!

# Aspetta che Laravel si avvii
sleep 2

# Avvia Vite in background  
echo "⚡ Avvio server Vite su http://localhost:5173"
npm run dev &
VITE_PID=$!

# Messaggio di successo
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🌐 Apri il browser su: http://127.0.0.1:8000"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "Premi CTRL+C per fermare entrambi i server"
echo ""

# Funzione per cleanup
cleanup() {
    echo ""
    echo "🛑 Fermando i server..."
    kill $LARAVEL_PID 2>/dev/null
    kill $VITE_PID 2>/dev/null
    echo "👋 Server fermati. Arrivederci!"
    exit 0
}

# Intercetta CTRL+C
trap cleanup SIGINT

# Mantieni lo script in esecuzione
wait