#!/bin/bash

# Script per avviare Laravel + Vite in un unico comando
# Uso: ./start.sh

echo "🚀 Avvio Passepartout Utenti Carrello..."
echo ""

# Controlla se la porta 8000 è già occupata
if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null ; then
    echo "⚠️  Porta 8000 già in uso. Fermo il processo esistente..."
    lsof -ti:8000 | xargs kill -9 2>/dev/null
    sleep 1
fi

# Controlla se la porta 5173 è già occupata
if lsof -Pi :5173 -sTCP:LISTEN -t >/dev/null ; then
    echo "⚠️  Porta 5173 già in uso. Fermo il processo esistente..."
    lsof -ti:5173 | xargs kill -9 2>/dev/null
    sleep 1
fi

echo "✅ Porte liberate"
echo ""
echo "📦 Avvio server Laravel su http://127.0.0.1:8000"
echo "⚡ Avvio server Vite su http://localhost:5173"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🌐 Apri il browser su: http://127.0.0.1:8000"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "Premi CTRL+C per fermare entrambi i server"
echo ""

# Avvia Laravel in background
php artisan serve --host=127.0.0.1 --port=8000 &
LARAVEL_PID=$!

# Aspetta che Laravel sia pronto
sleep 2

# Avvia Vite (in foreground per vedere i log)
npm run dev

# Quando Vite viene fermato (CTRL+C), ferma anche Laravel
kill $LARAVEL_PID 2>/dev/null
echo ""
echo "👋 Server fermati. Arrivederci!"
