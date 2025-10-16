# API Documentation - Utenti Carrello

Complete API reference for the e-commerce platform.

## Base URL

```
http://127.0.0.1:8000/api
```

## Authentication

All protected endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer YOUR_TOKEN_HERE
```

## Response Format

All responses follow this structure:

**Success Response:**
```json
{
    "data": { ... },
    "message": "Success message"
}
```

**Error Response:**
```json
{
    "message": "Error message",
    "errors": {
        "field": ["Validation error"]
    }
}
```

---

## Authentication Endpoints

### Register User

Creates a new user account.

**Endpoint:** `POST /api/register`  
**Auth Required:** No

**Request Body:**
```json
{
    "name": "Mario",
    "cognome": "Rossi",
    "email": "mario.rossi@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "address": "Via Roma 1",
    "city": "Milano",
    "zip_code": "20100",
    "titolo_studio": "Laurea",
    "role": "user"
}
```

**Validation Rules:**
- `name`: required, string, max:255
- `cognome`: nullable, string, max:255
- `email`: required, email, unique
- `password`: required, min:8, confirmed
- `address`: nullable, string
- `city`: nullable, string
- `zip_code`: nullable, string
- `titolo_studio`: nullable, string
- `role`: optional, one of: 'user', 'business', 'admin'

**Success Response (201):**
```json
{
    "user": {
        "id": 1,
        "name": "Mario",
        "cognome": "Rossi",
        "email": "mario.rossi@example.com",
        "created_at": "2025-10-16T10:00:00.000000Z"
    },
    "token": "1|abcdef123456..."
}
```

---

### Login

Authenticate user and get access token.

**Endpoint:** `POST /api/login`  
**Auth Required:** No

**Request Body:**
```json
{
    "email": "mario.rossi@example.com",
    "password": "password123"
}
```

**Success Response (200):**
```json
{
    "user": {
        "id": 1,
        "name": "Mario",
        "cognome": "Rossi",
        "email": "mario.rossi@example.com"
    },
    "token": "2|ghijkl789012..."
}
```

**Error Response (401):**
```json
{
    "message": "Credenziali non valide"
}
```

---

### Logout

Revoke current access token.

**Endpoint:** `POST /api/logout`  
**Auth Required:** Yes

**Success Response (200):**
```json
{
    "message": "Logout effettuato con successo"
}
```

---

### Get Current User

Get authenticated user profile.

**Endpoint:** `GET /api/user`  
**Auth Required:** Yes

**Success Response (200):**
```json
{
    "id": 1,
    "name": "Mario",
    "cognome": "Rossi",
    "email": "mario.rossi@example.com",
    "address": "Via Roma 1",
    "city": "Milano",
    "zip_code": "20100",
    "titolo_studio": "Laurea",
    "roles": [
        {
            "id": 2,
            "name": "user",
            "display_name": "User"
        }
    ]
}
```

---

## Product Endpoints

### List Products

Get paginated list of all products.

**Endpoint:** `GET /api/products`  
**Auth Required:** No

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15)
- `search`: Search term for product name
- `categoria`: Filter by category
- `min_prezzo`: Minimum price
- `max_prezzo`: Maximum price

**Example:**
```
GET /api/products?search=laptop&categoria=Elettronica&min_prezzo=500
```

**Success Response (200):**
```json
{
    "data": [
        {
            "id": 1,
            "nome": "Laptop Dell XPS 15",
            "codice": "DELL-XPS15",
            "descrizione": "Laptop professionale",
            "prezzo": "1299.99",
            "categoria": "Elettronica",
            "immagine": "/storage/products/laptop.jpg",
            "scorte": 10,
            "created_at": "2025-10-16T10:00:00.000000Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "total": 50,
        "per_page": 15,
        "last_page": 4
    }
}
```

---

### Get Single Product

Get detailed information about a specific product.

**Endpoint:** `GET /api/products/{id}`  
**Auth Required:** No

**Success Response (200):**
```json
{
    "id": 1,
    "nome": "Laptop Dell XPS 15",
    "codice": "DELL-XPS15",
    "descrizione": "Laptop professionale con schermo 4K",
    "prezzo": "1299.99",
    "categoria": "Elettronica",
    "immagine": "/storage/products/laptop.jpg",
    "scorte": 10,
    "created_at": "2025-10-16T10:00:00.000000Z"
}
```

**Error Response (404):**
```json
{
    "message": "Prodotto non trovato"
}
```

---

### Create Product (Admin Only)

Create a new product.

**Endpoint:** `POST /api/products`  
**Auth Required:** Yes (Admin only)

**Request Body:**
```json
{
    "nome": "Nuovo Prodotto",
    "codice": "PROD123",
    "descrizione": "Descrizione dettagliata",
    "prezzo": 99.99,
    "categoria": "Elettronica",
    "scorte": 50,
    "immagine": "optional_image_url"
}
```

**Validation Rules:**
- `nome`: required, string, max:255
- `codice`: required, string, unique
- `descrizione`: nullable, text
- `prezzo`: required, numeric, min:0
- `categoria`: nullable, string
- `scorte`: required, integer, min:0
- `immagine`: nullable, string

**Success Response (201):**
```json
{
    "id": 51,
    "nome": "Nuovo Prodotto",
    "codice": "PROD123",
    "prezzo": "99.99",
    "message": "Prodotto creato con successo"
}
```

---

### Update Product (Admin Only)

Update existing product.

**Endpoint:** `PUT /api/products/{id}`  
**Auth Required:** Yes (Admin only)

**Request Body:** (all fields optional)
```json
{
    "nome": "Nome Aggiornato",
    "prezzo": 149.99,
    "scorte": 30
}
```

**Success Response (200):**
```json
{
    "id": 1,
    "nome": "Nome Aggiornato",
    "prezzo": "149.99",
    "message": "Prodotto aggiornato con successo"
}
```

---

### Delete Product (Admin Only)

Soft delete a product.

**Endpoint:** `DELETE /api/products/{id}`  
**Auth Required:** Yes (Admin only)

**Success Response (200):**
```json
{
    "message": "Prodotto eliminato con successo"
}
```

---

## Cart Endpoints

### Get Cart

Get current user's active cart with all items.

**Endpoint:** `GET /api/cart`  
**Auth Required:** Yes

**Success Response (200):**
```json
{
    "cart": {
        "id": 1,
        "user_id": 1,
        "stato": "attivo",
        "items": [
            {
                "id": 1,
                "product_id": 5,
                "quantity": 2,
                "prezzo_unitario": "99.99",
                "subtotal": 199.98,
                "product": {
                    "id": 5,
                    "nome": "Mouse Wireless",
                    "immagine": "/storage/products/mouse.jpg"
                }
            }
        ],
        "updated_at": "2025-10-16T10:00:00.000000Z"
    },
    "total": 199.98,
    "items_count": 1
}
```

---

### Add to Cart

Add a product to cart or increase quantity if already exists.

**Endpoint:** `POST /api/cart/add`  
**Auth Required:** Yes

**Request Body:**
```json
{
    "product_id": 5,
    "quantity": 2
}
```

**Validation Rules:**
- `product_id`: required, exists in products table
- `quantity`: required, integer, min:1

**Success Response (200):**
```json
{
    "message": "Prodotto aggiunto al carrello",
    "cart_item": {
        "id": 1,
        "product_id": 5,
        "quantity": 2,
        "prezzo_unitario": "99.99"
    }
}
```

**Error Response (400):**
```json
{
    "message": "Quantità non disponibile"
}
```

---

### Update Cart Item

Update quantity of item in cart.

**Endpoint:** `PUT /api/cart/update/{cart_item_id}`  
**Auth Required:** Yes

**Request Body:**
```json
{
    "quantity": 5
}
```

**Success Response (200):**
```json
{
    "message": "Carrello aggiornato",
    "cart_item": {
        "id": 1,
        "quantity": 5,
        "subtotal": 499.95
    }
}
```

---

### Remove from Cart

Remove an item from cart.

**Endpoint:** `DELETE /api/cart/remove/{cart_item_id}`  
**Auth Required:** Yes

**Success Response (200):**
```json
{
    "message": "Prodotto rimosso dal carrello"
}
```

---

### Checkout

Complete purchase and create order.

**Endpoint:** `POST /api/cart/checkout`  
**Auth Required:** Yes

**Success Response (200):**
```json
{
    "message": "Ordine completato con successo",
    "order": {
        "id": 1,
        "cart_id": 1,
        "total": 199.98,
        "items_count": 2,
        "created_at": "2025-10-16T10:00:00.000000Z"
    }
}
```

**Error Response (400):**
```json
{
    "message": "Il carrello è vuoto"
}
```

---

## Dashboard Endpoints (Admin Only)

### Get Statistics

Get complete dashboard statistics.

**Endpoint:** `GET /api/dashboard/stats`  
**Auth Required:** Yes (Admin only)

**Success Response (200):**
```json
{
    "users": {
        "total": 150,
        "by_role": {
            "admin": 5,
            "user": 120,
            "business": 25
        },
        "by_city": [
            {"city": "Milano", "count": 45},
            {"city": "Roma", "count": 30}
        ]
    },
    "products": {
        "total": 200,
        "by_category": {
            "Elettronica": 80,
            "Abbigliamento": 50,
            "Libri": 70
        },
        "low_stock": 15
    },
    "orders": {
        "total": 500,
        "total_revenue": 125000.50,
        "monthly_revenue": [
            {"month": "2025-01", "revenue": 25000.00},
            {"month": "2025-02", "revenue": 30000.00}
        ]
    }
}
```

---

## Error Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Resource created |
| 400 | Bad request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Resource not found |
| 422 | Validation error |
| 500 | Server error |

---

## Rate Limiting

API requests are limited to:
- **60 requests per minute** for authenticated users
- **30 requests per minute** for unauthenticated users

Rate limit headers are included in responses:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1634567890
```

---

## Testing with cURL

**Register:**
```bash
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Login:**
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

**Get Products:**
```bash
curl -X GET http://127.0.0.1:8000/api/products \
  -H "Accept: application/json"
```

**Add to Cart (with token):**
```bash
curl -X POST http://127.0.0.1:8000/api/cart/add \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'
```

---

## Postman Collection

Import this Postman collection for easy testing: [Download Collection](./postman_collection.json)

---

**Last Updated:** October 16, 2025  
**API Version:** 1.0.0
