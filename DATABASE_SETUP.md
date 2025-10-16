# 📊 Database Setup - Guida Completa

## ✅ Database Popolato con Successo

Il database è stato popolato con **dati reali** usando i seeder Laravel.

### 🗄️ Dati Inseriti

#### Prodotti (10 totali)
1. **Smartphone Samsung Galaxy S24** - €999.99 - 25 scorte
2. **Laptop Dell XPS 13** - €1299.99 - 15 scorte
3. **Cuffie Sony WH-1000XM5** - €349.99 - 40 scorte
4. **Tablet Apple iPad Pro 12.9"** - €1199.99 - 12 scorte
5. **Smartwatch Garmin Fenix 7** - €599.99 - 18 scorte
6. **Fotocamera Canon EOS R6** - €2199.99 - 8 scorte
7. **Console PlayStation 5** - €549.99 - 6 scorte
8. **Router WiFi 6 ASUS AX6000** - €299.99 - 22 scorte
9. **Monitor Gaming LG UltraGear 27"** - €399.99 - 14 scorte
10. **Altoparlante Smart Amazon Echo Studio** - €199.99 - 30 scorte

#### Immagini Prodotto
Tutte le immagini sono disponibili in: `/public/images/products/`
- smartphone-samsung-s24.jpg
- laptop-dell-xps13.jpg
- cuffie-sony-wh1000xm5.jpg
- tablet-ipad-pro.jpg
- smartwatch-garmin-fenix7.jpg
- console-ps5.jpg
- fotocamera-canon-eosr6.jpg
- router-asus-ax6000.jpg
- monitor-lg-ultragear.jpg
- speaker-echo-studio.jpg
- placeholder.svg (fallback)

#### Utenti e Ruoli
- Ruoli creati dal RoleSeeder
- Utenti creati dal UserSeeder

---

## 🔄 Come Rieseguire i Seeder

Se devi ripopolare il database da zero:

```bash
# Reset completo database + seeder
php artisan migrate:fresh --seed

# Oppure solo i seeder (senza reset)
php artisan db:seed
```

---

## 🛠️ Struttura Tabella Products

| Campo | Tipo | Descrizione |
|-------|------|-------------|
| `id` | bigint | ID univoco |
| `nome` | string | Nome prodotto |
| `codice` | string | Codice univoco prodotto |
| `descrizione` | text | Descrizione completa |
| `prezzo` | decimal(10,2) | Prezzo in euro |
| `immagine` | string | Path immagine (es: `products/nome.jpg`) |
| `attivo` | boolean | Se il prodotto è attivo |
| `scorte` | integer | Quantità disponibile |
| `created_at` | timestamp | Data creazione |
| `updated_at` | timestamp | Data ultimo aggiornamento |

### ⚠️ Campi Importanti per il Frontend

Il **backend Laravel** restituisce questi campi aggiuntivi nell'API:

```json
{
  "id": 1,
  "nome": "Smartphone Samsung Galaxy S24",
  "descrizione": "...",
  "prezzo": "999.99",
  "immagine_url": "http://localhost:8000/images/products/smartphone-samsung-s24.jpg",
  "attivo": true,
  "scorte": 25,
  "disponibile": true  // Campo calcolato: attivo && scorte > 0
}
```

### 📝 Mapping Frontend ↔ Backend

Il frontend Vue usa questi campi:

| Frontend | Backend | Note |
|----------|---------|------|
| `product.attivo` | `attivo` | Boolean se prodotto è attivo |
| `product.scorte` | `scorte` | Quantità disponibile |
| `product.immagine_url` | `immagine_url` | URL completo immagine (formattato dal backend) |
| `product.disponibile` | `disponibile` | Campo calcolato dal backend |

⚠️ **NON usare** `quantita` o `disponibile` come campi del database - sono mappati da `scorte` e `attivo`.

---

## 🚀 Verifiche Rapide

### Controlla numero prodotti
```bash
php artisan tinker --execute="echo 'Prodotti: ' . \App\Models\Product::count();"
```

### Controlla primo prodotto
```bash
php artisan tinker --execute="\App\Models\Product::first();"
```

### Testa API
```bash
curl http://localhost:8000/api/products | jq '.products | length'
```

---

## 📦 Stato Attuale

✅ Database popolato con 10 prodotti reali  
✅ Immagini prodotto presenti in `/public/images/products/`  
✅ Frontend aggiornato per usare campi corretti (`scorte`, `attivo`)  
✅ API funzionante e restituisce dati formattati correttamente  
✅ Fallback immagine su `placeholder.svg` se manca  

---

## 🔗 Files Correlati

- **Seeder**: `database/seeders/ProductSeeder.php`
- **Migration**: `database/migrations/2025_10_14_161509_create_products_table.php`
- **Model**: `app/Models/Product.php`
- **Controller API**: `app/Http/Controllers/ProductController.php`
- **Frontend**: 
  - `resources/js/pages/products/Products.vue`
  - `resources/js/pages/products/ProductDetail.vue`

---

*Ultimo aggiornamento: 16 Ottobre 2025*
