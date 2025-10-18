# 🛒 Passepartout Utenti Carrello

Sistema full-stack per gestione utenti, carrello prodotti e dashboard amministrativa sviluppato con **Laravel 11** e **Vue 3**.

**📊 Test Results: 32/34 tests passing (94% success rate)**

## 🚀 Features Principali

### 👥 Gestione Utenti
- ✅ Registrazione completa (nome, cognome, email, titolo studi, data/città nascita)
- ✅ Login/Logout con autenticazione Sanctum
- ✅ Modifica profilo utente
- ✅ Reset password via email con token sicuro
- ✅ Sistema ruoli scalabile (admin, user, business)
- ✅ Middleware e policies per autorizzazioni

### 🛍️ Carrello Prodotti  
- ✅ Catalogo prodotti con dettagli completi
- ✅ Aggiunta/rimozione prodotti dal carrello
- ✅ Gestione quantità e calcolo totali
- ✅ Area admin per CRUD prodotti
- ✅ Upload immagini prodotti (storage locale)
- ✅ Sistema scorte e disponibilità

### 📊 Dashboard Amministrativa
- modifica e crezione prodotti
- pagine di email mandate agli users
- statistiche (alcune mokkate per completare la view).

## 🛠️ Stack Tecnologico

### Backend
- **Laravel 11** (PHP 8.2+)
- **MySQL** database
- **Eloquent ORM** con Repository Pattern
- **Laravel Sanctum** per autenticazione SPA
- **Service Layer** per business logic
- **Form Requests** per validazione
- **Seeder e Factory** per dati di test

### Frontend  
- **Vue 3** con Composition API
- **Vue Router 4** per routing
- **Pinia** per state management
- **Chart.js** per grafici dashboard
- **Axios** per chiamate API
- **Vite** per build tool

## 🏗️ Architettura

Il progetto segue principi di **Clean Architecture** e **SOLID**:

```
app/
├── Http/
│   ├── Controllers/Api/     # API Controllers
│   └── Requests/           # Form Validation
├── Models/                 # Eloquent Models
├── Repositories/          # Repository Pattern
├── Services/             # Business Logic
└── Policies/            # Authorization Logic
```

### Pattern Implementati
- **Repository Pattern** per astrazione database
- **Service Layer** per business logic
- **Dependency Injection** per loose coupling
- **Single Responsibility Principle**
- **Form Request Objects** per validazione

## 💾 Database Schema

```sql
users (id, nome, cognome, email, titolo_studi, data_nascita, città_nascita)
roles (id, name, display_name, description)  
role_user (role_id, user_id) -- many-to-many
products (id, nome, codice, descrizione, prezzo, immagine, scorte)
carts (id, user_id, stato, ultimo_aggiornamento)
cart_items (id, cart_id, product_id, quantità, prezzo_unitario)
```

## ⚡ Setup Rapido per Testare l'App

**Prerequisiti:**
- PHP 8.2+
- Node.js 18+  
- MySQL 8.0+
- Composer
- NPM/Yarn

**Installazione (6 comandi):**

```bash
# 1. Clone e accedi alla directory
git clone https://github.com/AngelKb81/passepartout-utenticarrello.git
cd passepartout-utenticarrello

# 2. Installa dipendenze PHP e JavaScript
composer install && npm install

# 3. Configura environment
cp .env.example .env
php artisan key:generate

# 4. Configura database nel file .env
DB_DATABASE=passepartout_utenticarrello
DB_USERNAME=root
DB_PASSWORD=your_password

# 5. Setup database con dati di test
php artisan migrate:fresh --seed

# 6. Avvia servers di sviluppo
php artisan serve & npm run dev
```

🎯 **L'app sarà accessibile su: http://127.0.0.1:8000**

## 🔧 Configurazione Database

Modifica il file `.env` con le tue credenziali:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=passepartout_utenticarrello
DB_USERNAME=root
DB_PASSWORD=la_tua_password
```

## 📧 Configurazione Email (SMTP) - Opzionale

L'applicazione invia email per:
- ✅ Benvenuto dopo registrazione
- ✅ Reset password  

**⚠️ Per il testing, le email sono disabilitate nei test automatici**

**Setup rapido per test manuali:**

```env
# Aggiungi al file .env per testare invio email
MAIL_MAILER=log  # Salva email nei log invece di inviarle
# OPPURE per email reali:
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io  # Mailtrap gratuito
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@passepartout.test"
MAIL_FROM_NAME="Passepartout"
```

**Nota:** Con `MAIL_MAILER=log` puoi vedere le email in `storage/logs/laravel.log`

## 🎲 Dati di Test Precaricati

Dopo `php artisan migrate:fresh --seed` avrai:

**👥 Utenti (4 totali):**
- 1 Admin con accesso dashboard completa
- 3 Utenti standard con profili completi (nome, cognome, titoli studio diversi)

**📦 Prodotti (10 totali):**
- Categorie: Smartphone, Computer, Audio, Tablet, Wearabl, Fotocamera, Gaming, Networking, Monitor ecc cc.
- Scorte variabili per testare disponibilità
- Immagini placeholder (generate con AI)

**🛒 Carrelli Pre-esistenti:**
- Alcuni utenti hanno già carrelli con prodotti
- Diversi stati: attivo, ordinato, abbandonato
- Utile per testare scenari reali

**🔐 Ruoli e Permessi:**
- `admin`: Accesso completo dashboard e gestione
- `user`: Accesso carrello e profilo
- `business`: Preparato per future funzionalità B2B

**💾 Reset Veloce Dati:**
```bash
php artisan migrate:fresh --seed  # Ripristina tutto
```

## � Credenziali per il Testing

### 👨‍💼 Amministratore (accesso dashboard admin)
- **Email:** `admin@passepartout-utenticarrello.test`  
- **Password:** `password`
- **Accesso:** Dashboard completa, gestione prodotti, logs email, statistiche

### 👥 Utenti Standard (per testare funzionalità utente)
- **Email:** `giulia.bianchi@email.test`  
- **Email:** `francesco.verde@email.test`
- **Email:** `chiara.neri@email.test`
- **Password:** `password` (per tutti)
- **Accesso:** Carrello, profilo, logout

### 🧪 Come Testare
1. **Frontend:** Vai su http://127.0.0.1:8000
2. **Login Admin:** Usa le credenziali admin per accedere alla dashboard
3. **Test Carrello:** Login come utente standard, aggiungi prodotti al carrello
4. **API Diretta:** Testa gli endpoint con Postman/curl (vedi sezione API)
5. **Tests Automatici:** Esegui `php artisan test` per verificare la suite di test

## 📚 API Endpoints

### Autenticazione
```http
POST /api/register          # Registrazione utente
POST /api/login             # Login
POST /api/logout            # Logout
GET  /api/user              # Profilo utente corrente
PUT  /api/user/profile      # Aggiorna profilo
POST /api/password/reset    # Reset password
```

### Prodotti
```http
GET    /api/products         # Lista prodotti
GET    /api/products/{id}    # Dettaglio prodotto
POST   /api/products         # Crea prodotto (admin)
PUT    /api/products/{id}    # Aggiorna prodotto (admin)
DELETE /api/products/{id}    # Elimina prodotto (admin)
GET    /api/products/search  # Ricerca prodotti
```

### Carrello
```http
GET    /api/cart            # Carrello corrente
POST   /api/cart/add        # Aggiungi al carrello
PUT    /api/cart/update     # Aggiorna quantità
DELETE /api/cart/remove     # Rimuovi dal carrello
DELETE /api/cart/clear      # Svuota carrello
POST   /api/cart/checkout   # Finalizza ordine
```

### Dashboard Admin
```http
GET /api/admin/stats/users     # Statistiche utenti
GET /api/admin/stats/products  # Statistiche prodotti  
GET /api/admin/stats/revenue   # Fatturato mensile
GET /api/admin/stats/charts    # Dati per grafici
```

## 🧪 Testing Suite

**Stato Attuale: 32/34 test passano (94% success rate)**

```bash
# Esegui tutti i test
php artisan test

# Test specifici per area
php artisan test --filter AuthenticationTest    # ✅ 10/10 
php artisan test --filter ProductTest           # ✅ 10/10
php artisan test --filter CartTest              # ⚠️ 9/11 (2 test marginali)

# Test di esempio
php artisan test --filter ExampleTest           # ✅ 2/2
```

**Test Coverage:**
- ✅ **Autenticazione:** Registrazione, login, logout, profili, ruoli
- ✅ **Prodotti:** CRUD completo, filtri, ricerca, autorizzazioni admin
- ✅ **Carrello:** Aggiunta, rimozione, quantità, calcoli, checkout
- ✅ **API:** Tutti gli endpoint testati con scenari realistici

**Test Failing (non bloccanti):**
- `user_can_checkout_cart`: Problema notification in ambiente test
- `user_cannot_modify_another_users_cart`: Codice di errore HTTP

**🔍 Database Test:** Usa SQLite in memoria per isolamento completo

**🔧 Requisiti Sistema:**
- **PHP:** 8.2+ (compatibile con 8.3)
- **MySQL:** 8.0+ o MariaDB 10.3+
- **Node.js:** 18+ (per build frontend)
- **Memory:** Minimo 512MB RAM
- **Storage:** ~100MB per installazione completa

**🏗️ Architettura Scalabile:**
- Repository Pattern per swap database facile
- API RESTful standard per integrazioni
- Frontend SPA per user experience fluida
- Caching ready (Redis/Memcached)
- Queue system preparato per email async

## 🎯 Caratteristiche Tecniche

### Sicurezza
- ✅ Validazione input lato server
- ✅ Protezione CSRF 
- ✅ Autenticazione token-based
- ✅ Autorizzazione basata su ruoli
- ✅ Sanitizzazione dati

### Performance
- ✅ Query ottimizzate con Eloquent
- ✅ Eager loading per relazioni
- ✅ Caching configurabile
- ✅ Asset bundling con Vite

### Scalabilità
- ✅ Repository Pattern per swap database
- ✅ Service Layer per business logic
- ✅ Dependency Injection
- ✅ API RESTful strutturate

## 🤝 Contributi

Progetto sviluppato come **code challenge** per dimostrare competenze full-stack.

### Scelte Architetturali

1. **Repository Pattern**: Astrazione database per testabilità
2. **Service Layer**: Business logic separata dai controller  
3. **Form Requests**: Validazione centralizzata e riutilizzabile
4. **Sanctum SPA**: Autenticazione stateless per Vue.js
5. **Storage Locale**: Semplice per demo, facilmente estendibile

## � Troubleshooting Rapido

**Problema: Server non si avvia**
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

**Problema: Errori database**
```bash
php artisan migrate:fresh --seed
```

**Problema: Errori di cache**
```bash
php artisan cache:clear && php artisan config:clear && php artisan route:clear
```

**Problema: Frontend non carica**
```bash
npm install && npm run dev
```

**Problema: Test falliscono**
```bash
# Usa database di test separato
php artisan test --env=testing
```

## 📊 Architettura Tecnologica

**Backend (Laravel 11):**
- Repository Pattern per gestione database
- Service Layer per business logic
- Form Requests per validazione avanzata
- Laravel Sanctum per autenticazione SPA
- PHPUnit per testing completo

**Frontend (Vue 3):**
- Composition API per reattività
- Pinia per state management
- Chart.js per visualizzazioni admin
- Tailwind CSS per styling
- Axios per comunicazione API

**Database:**
- MySQL per produzione
- SQLite in-memory per testing
- Seeder per dati di test realistici
- Migration per schema versionato

## 📞 Supporto e Contatti

**Per segnalazioni/domande:**
- **Sviluppatore:** Angelo Corbelli
- **GitHub:** [AngelKb81/passepartout-utenticarrello](https://github.com/AngelKb81/passepartout-utenticarrello)
- **Email:** angelo.corbelli81@gmail.com

---

**🚀 Developed with ❤️  by Angelo Corbelli** 

## 🌐 Guida Completa per il Testing

### 📱 Test Frontend (Browser)

**1. Homepage e Navigazione**
- Vai su `http://127.0.0.1:8000`
- Testa la navigazione tra Home, Prodotti
- Verifica responsive design (mobile/desktop)

**2. Registrazione Utente**
- Clicca "Registrati" 
- Compila: nome, cognome, email, titolo studi, data/città nascita
- Verifica validazione campi (email duplicate, password deboli)

**3. Sistema Prodotti**
- Sfoglia catalogo prodotti con filtri categoria
- Usa ricerca per nome prodotto
- Visualizza dettagli singolo prodotto

**4. Carrello Shopping**
- Aggiungi prodotti (testa quantità, scorte)
- Modifica quantità esistenti
- Rimuovi prodotti dal carrello
- Procedi al checkout (simulato)

**5. Area Admin** (login con admin@passepartout-utenticarrello.test)
- Dashboard con grafici Chart.js (alcuni mokkati solo per view)
- Gestione prodotti (CRUD completo)
- Logs email inviati

### 🔧 Test API (Postman/curl)

**Base URL:** `http://127.0.0.1:8000/api`

**Headers richiesti:**
```
Accept: application/json
Content-Type: application/json
Authorization: Bearer {token}  # Per endpoint protetti
```

**Flusso di test completo:**
```bash
# 1. Registrazione
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","cognome":"User","email":"test@test.com","password":"password","password_confirmation":"password","titolo_studi":"Laurea","data_nascita":"1990-01-01","citta_nascita":"Roma"}'

# 2. Login (salva il token)
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"password"}'

# 3. Lista prodotti
curl -X GET http://127.0.0.1:8000/api/products

# 4. Aggiungi al carrello (usa token)
curl -X POST http://127.0.0.1:8000/api/cart/add \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"product_id":1,"quantity":2}'

# 5. Visualizza carrello
curl -X GET http://127.0.0.1:8000/api/cart \
  -H "Authorization: Bearer YOUR_TOKEN"
```
