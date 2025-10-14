# 🛒 Passepartout Utenti Carrello

Sistema full-stack per gestione utenti, carrello prodotti e dashboard amministrativa sviluppato con **Laravel 11** e **Vue 3**.

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
- 🔄 Statistiche utenti registrati (grafici mensili)
- 🔄 Top prodotti nei carrelli e bestseller
- 🔄 Fatturato simulato per mese
- 🔄 Distribuzione utenti per città/titolo studi
- 🔄 Grafici interattivi con Chart.js

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
users (id, name, cognome, email, titolo_studi, data_nascita, città_nascita)
roles (id, name, display_name, description)  
role_user (role_id, user_id) -- many-to-many
products (id, nome, codice, descrizione, prezzo, immagine, scorte)
carts (id, user_id, stato, ultimo_aggiornamento)
cart_items (id, cart_id, product_id, quantità, prezzo_unitario)
```

## 🚀 Setup Rapido (5 comandi)

```bash
# 1. Clone del repository
git clone https://github.com/AngelKb81/passepartout-utenticarrello.git
cd passepartout-utenticarrello

# 2. Installa dipendenze
composer install && npm install

# 3. Configura environment
cp .env.example .env
php artisan key:generate

# 4. Setup database (modifica DB_* in .env)
php artisan migrate --seed

# 5. Avvia il progetto
php artisan serve & npm run dev
```

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

## 👤 Credenziali Test

### Admin
- **Email:** admin@passepartout-utenticarrello.test  
- **Password:** password

### Utenti Standard
- **Email:** giulia.bianchi@email.test  
- **Email:** francesco.verde@email.test  
- **Email:** chiara.neri@email.test  
- **Password:** password (per tutti)

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

## 🧪 Testing

```bash
# Esegui test PHPUnit
php artisan test

# Test specifici
php artisan test --filter AuthTest
php artisan test --filter ProductTest
php artisan test --filter CartTest
```

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

## 🔮 Roadmap Future

- [ ] Sistema notifiche real-time
- [ ] Integrazione pagamenti Stripe
- [ ] Export/Import prodotti CSV
- [ ] Sistema recensioni prodotti
- [ ] Multi-language support
- [ ] Progressive Web App (PWA)
- [ ] Docker containerization

## 🤝 Contributi

Progetto sviluppato come **code challenge** per dimostrare competenze full-stack con Laravel e Vue.js.

### Scelte Architetturali

1. **Repository Pattern**: Astrazione database per testabilità
2. **Service Layer**: Business logic separata dai controller  
3. **Form Requests**: Validazione centralizzata e riutilizzabile
4. **Sanctum SPA**: Autenticazione stateless per Vue.js
5. **Storage Locale**: Semplice per demo, facilmente estendibile

## 📄 Licenza

Open source - MIT License

---

**Sviluppato con ❤️ da Angelo Corbelli**  
*Full-Stack Developer | Laravel & Vue.js Specialist*

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
