#!/bin/bash

# 🚀 Script Setup Automatico - Passepartout Utenti Carrello
# Installa e configura l'intero progetto in un comando

echo "🚀 Setup Passepartout Utenti Carrello..."
echo "========================================="

# Colori per output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Funzione per stampare step
print_step() {
    echo -e "${BLUE}📦 $1${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Check requisiti
print_step "Controllo requisiti sistema..."

if ! command -v php &> /dev/null; then
    print_error "PHP non trovato. Installa PHP 8.2+ per continuare."
    exit 1
fi

if ! command -v composer &> /dev/null; then
    print_error "Composer non trovato. Installa Composer per continuare."
    exit 1
fi

if ! command -v node &> /dev/null; then
    print_error "Node.js non trovato. Installa Node.js 18+ per continuare."
    exit 1
fi

if ! command -v npm &> /dev/null; then
    print_error "NPM non trovato. Installa NPM per continuare."
    exit 1
fi

print_success "Tutti i requisiti soddisfatti!"

# 1. Installa dipendenze
print_step "Installazione dipendenze PHP..."
composer install --no-dev --optimize-autoloader

print_step "Installazione dipendenze Node.js..."
npm install

# 2. Configura environment
print_step "Configurazione environment..."
if [ ! -f .env ]; then
    cp .env.example .env
    print_success "File .env creato da .env.example"
else
    print_warning "File .env già esistente, saltato."
fi

# Genera chiave applicazione
php artisan key:generate --force

# 3. Configura database
print_step "Configurazione database..."

# Chiedi credenziali database all'utente
echo ""
echo "🔧 Configurazione Database MySQL"
echo "================================"
read -p "Nome database (default: passepartout_utenticarrello): " DB_NAME
DB_NAME=${DB_NAME:-passepartout_utenticarrello}

read -p "Host database (default: 127.0.0.1): " DB_HOST  
DB_HOST=${DB_HOST:-127.0.0.1}

read -p "Username database (default: root): " DB_USER
DB_USER=${DB_USER:-root}

read -s -p "Password database (lascia vuoto se nessuna): " DB_PASS
echo ""

# Aggiorna .env con credenziali database
sed -i "" "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "" "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env  
sed -i "" "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "" "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env

print_success "Credenziali database configurate in .env"

# 4. Setup database
print_step "Creazione tabelle e seeding dati..."
php artisan migrate:fresh --seed --force

print_success "Database configurato con successo!"

# 5. Setup storage
print_step "Configurazione storage..."
php artisan storage:link

# 6. Ottimizzazioni
print_step "Ottimizzazione applicazione..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

print_success "Ottimizzazioni completate!"

# Finale
echo ""
echo "🎉 Setup completato con successo!"
echo "=================================="
echo ""
echo "📋 Credenziali di test:"
echo "  Admin: admin@passepartout-utenticarrello.test / password"
echo "  User:  giulia.bianchi@email.test / password"
echo ""
echo "🚀 Per avviare l'applicazione:"
echo "  ./start.sh"
echo ""
echo "🌐 URL applicazione:"
echo "  http://127.0.0.1:8000"
echo ""
echo "🧪 Per eseguire i test:"
echo "  php artisan test"
echo ""
print_success "Buon sviluppo! 🚀"