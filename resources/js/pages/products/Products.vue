<template>
  <!-- Pagina Prodotti: catalogo completo con filtri -->
  <div class="products-page">
    <div class="products-container">
      
      <!-- Header sezione -->
      <header class="products-header">
        <h1>Catalogo Prodotti</h1>
        <p>Esplora la nostra selezione di prodotti</p>
      </header>

      <!-- Filtri e ricerca -->
      <section class="filters-section">
        <div class="filters-grid">
          
          <!-- Ricerca -->
          <div class="filter-item search-input">
            <label for="search">Cerca prodotto</label>
            <input
              v-model="filters.search"
              type="text"
              id="search"
              placeholder="Nome o descrizione..."
              @input="debouncedSearch"
            />
          </div>

          <!-- Categoria -->
          <div class="filter-item">
            <label for="categoria">Categoria</label>
            <select v-model="filters.categoria" id="categoria" @change="loadProducts">
              <option value="">Tutte</option>
              <option value="elettronica">Elettronica</option>
              <option value="abbigliamento">Abbigliamento</option>
              <option value="casa">Casa</option>
              <option value="sport">Sport</option>
              <option value="libri">Libri</option>
            </select>
          </div>

          <!-- Ordinamento -->
          <div class="filter-item">
            <label for="sort">Ordina per</label>
            <select v-model="filters.sort" id="sort" @change="loadProducts">
              <option value="nome_asc">Nome A-Z</option>
              <option value="nome_desc">Nome Z-A</option>
              <option value="prezzo_asc">Prezzo ↑</option>
              <option value="prezzo_desc">Prezzo ↓</option>
            </select>
          </div>
        </div>

        <!-- Filtri attivi (badge) -->
        <div v-if="hasActiveFilters" class="active-filters">
          <span class="label">Filtri attivi:</span>
          <button v-if="filters.search" @click="clearFilter('search')" class="filter-badge">
            {{ filters.search }} ✕
          </button>
          <button v-if="filters.categoria" @click="clearFilter('categoria')" class="filter-badge">
            {{ filters.categoria }} ✕
          </button>
          <button @click="clearAllFilters" class="clear-all-btn">Pulisci tutto</button>
        </div>
      </section>

      <!-- Loading state -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Caricamento prodotti...</p>
      </div>

      <!-- Griglia prodotti -->
      <section v-else-if="products.length > 0" class="products-grid">
        <article
          v-for="product in products"
          :key="product.id"
          class="product-card"
          @click="goToProduct(product.id)"
        >
          <!-- Immagine prodotto -->
          <div class="product-image">
            <img
              :src="getProductImage(product)"
              :alt="product.nome"
              @error="handleImageError"
            />
            
            <!-- Badge disponibilità -->
            <span v-if="product.disponibile && product.quantita > 0" class="badge available">
              Disponibile
            </span>
            <span v-else-if="product.quantita <= 5 && product.quantita > 0" class="badge low-stock">
              Ultimi {{ product.quantita }}
            </span>
            <span v-else class="badge out-of-stock">
              Esaurito
            </span>
          </div>

          <!-- Info prodotto -->
          <div class="product-info">
            <span class="product-category">{{ product.categoria || 'Generale' }}</span>
            <h3 class="product-title">{{ product.nome }}</h3>
            <p class="product-description">{{ product.descrizione }}</p>
            
            <div class="product-footer">
              <span class="product-price">€{{ formatPrice(product.prezzo) }}</span>
              <button
                v-if="authStore.isAuthenticated && product.disponibile && product.quantita > 0"
                @click.stop="quickAddToCart(product)"
                class="add-to-cart-btn"
                :disabled="addingToCart[product.id]"
              >
                <span v-if="addingToCart[product.id]">...</span>
                <span v-else>Aggiungi</span>
              </button>
            </div>
          </div>
        </article>
      </section>

      <!-- Empty state: nessun prodotto trovato -->
      <div v-else class="empty-state">
        <p class="empty-icon">📦</p>
        <h3>Nessun prodotto trovato</h3>
        <p>Prova a modificare i filtri di ricerca</p>
        <button @click="clearAllFilters" class="btn-secondary">Rimuovi filtri</button>
      </div>

      <!-- Paginazione -->
      <nav v-if="pagination.total > pagination.per_page" class="pagination">
        <button
          v-for="page in visiblePages"
          :key="page"
          @click="goToPage(page)"
          :class="['page-btn', { active: page === pagination.current_page }]"
        >
          {{ page }}
        </button>
      </nav>

    </div>

    <!-- Messaggio successo (toast) -->
    <div v-if="successMessage" class="success-toast">
      {{ successMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useCartStore } from '../../stores/cart'
import axios from 'axios'

// Router e stores
const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

// State
const loading = ref(false)
const products = ref([])
const addingToCart = ref({})
const successMessage = ref('')

// Filtri
const filters = reactive({
  search: '',
  categoria: '',
  sort: 'nome_asc'
})

// Paginazione
const pagination = reactive({
  current_page: 1,
  per_page: 12,
  total: 0,
  last_page: 1
})

// Computed: filtri attivi
const hasActiveFilters = computed(() => {
  return filters.search || filters.categoria
})

// Computed: pagine visibili per paginazione
const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, pagination.current_page - 2)
  const end = Math.min(pagination.last_page, start + 4)
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

// Carica prodotti dall'API
const loadProducts = async (page = 1) => {
  try {
    loading.value = true
    
    // Costruisci parametri query
    const params = new URLSearchParams({
      page: page.toString(),
      per_page: pagination.per_page.toString()
    })
    
    if (filters.search) params.append('search', filters.search)
    if (filters.categoria) params.append('categoria', filters.categoria)
    if (filters.sort) params.append('sort', filters.sort)
    
    // Chiamata API (verifica endpoint nel controller Laravel)
    const response = await axios.get(`/api/products?${params}`)
    
    // Estrai prodotti dalla risposta
    products.value = response.data.data || response.data.products || response.data || []
    
    // Aggiorna paginazione se presente
    if (response.data.meta) {
      pagination.current_page = response.data.meta.current_page
      pagination.per_page = response.data.meta.per_page
      pagination.total = response.data.meta.total
      pagination.last_page = response.data.meta.last_page
    } else if (response.data.pagination) {
      Object.assign(pagination, response.data.pagination)
    }
    
    console.log('Prodotti caricati:', products.value.length)
    
  } catch (error) {
    console.error('Errore caricamento prodotti:', error)
    products.value = []
    
    // Se l'endpoint API non esiste, carica prodotti di esempio
    if (error.response?.status === 404) {
      console.warn('Endpoint /api/products non trovato, carico dati di esempio')
      loadMockProducts()
    }
  } finally {
    loading.value = false
  }
}

// Carica prodotti di esempio (fallback)
const loadMockProducts = () => {
  products.value = [
    {
      id: 1,
      nome: 'Laptop Dell XPS 13',
      descrizione: 'Laptop ultraleggero con processore Intel Core i7',
      prezzo: 1299.99,
      categoria: 'elettronica',
      quantita: 15,
      disponibile: true,
      immagine: '/images/products/laptop.jpg'
    },
    {
      id: 2,
      nome: 'Smartphone Samsung Galaxy S21',
      descrizione: 'Smartphone 5G con fotocamera da 64MP',
      prezzo: 799.99,
      categoria: 'elettronica',
      quantita: 3,
      disponibile: true,
      immagine: '/images/products/phone.jpg'
    },
    {
      id: 3,
      nome: 'Cuffie Sony WH-1000XM4',
      descrizione: 'Cuffie wireless con cancellazione del rumore',
      prezzo: 349.99,
      categoria: 'elettronica',
      quantita: 0,
      disponibile: false,
      immagine: '/images/products/headphones.jpg'
    },
    {
      id: 4,
      nome: 'T-shirt Nike Sportswear',
      descrizione: 'Maglietta in cotone 100% con logo Nike',
      prezzo: 29.99,
      categoria: 'abbigliamento',
      quantita: 50,
      disponibile: true,
      immagine: '/images/products/tshirt.jpg'
    },
    {
      id: 5,
      nome: 'Jeans Levi\'s 501',
      descrizione: 'Jeans classici a vita alta in denim blu',
      prezzo: 89.99,
      categoria: 'abbigliamento',
      quantita: 25,
      disponibile: true,
      immagine: '/images/products/jeans.jpg'
    },
    {
      id: 6,
      nome: 'Sneakers Adidas Ultraboost',
      descrizione: 'Scarpe da running con tecnologia Boost',
      prezzo: 179.99,
      categoria: 'sport',
      quantita: 12,
      disponibile: true,
      immagine: '/images/products/sneakers.jpg'
    }
  ]
  
  // Applica filtri ai prodotti mock
  if (filters.categoria) {
    products.value = products.value.filter(p => p.categoria === filters.categoria)
  }
  if (filters.search) {
    const search = filters.search.toLowerCase()
    products.value = products.value.filter(p => 
      p.nome.toLowerCase().includes(search) || 
      p.descrizione.toLowerCase().includes(search)
    )
  }
  
  pagination.total = products.value.length
  pagination.last_page = Math.ceil(products.value.length / pagination.per_page)
}

// Aggiungi al carrello veloce
const quickAddToCart = async (product) => {
  if (!authStore.isAuthenticated) {
    router.push('/login')
    return
  }
  
  try {
    addingToCart.value[product.id] = true
    
    const result = await cartStore.addToCart(product.id, 1)
    
    if (result.success) {
      successMessage.value = `${product.nome} aggiunto al carrello!`
      setTimeout(() => successMessage.value = '', 3000)
    } else {
      alert(result.message || 'Errore aggiunta al carrello')
    }
  } catch (error) {
    console.error('Errore aggiunta carrello:', error)
    alert('Errore aggiunta al carrello')
  } finally {
    addingToCart.value[product.id] = false
  }
}

// Vai al dettaglio prodotto
const goToProduct = (productId) => {
  router.push(`/products/${productId}`)
}

// Vai a pagina specifica
const goToPage = (page) => {
  if (page !== pagination.current_page) {
    loadProducts(page)
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

// Rimuovi singolo filtro
const clearFilter = (filterName) => {
  filters[filterName] = ''
  loadProducts()
}

// Rimuovi tutti i filtri
const clearAllFilters = () => {
  filters.search = ''
  filters.categoria = ''
  loadProducts()
}

// Ricerca con debounce (attendi 500ms dopo l'ultima digitazione)
let searchTimeout = null
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadProducts()
  }, 500)
}

// Ottieni URL immagine prodotto
const getProductImage = (product) => {
  if (product.immagine && product.immagine.startsWith('/images/')) {
    return product.immagine
  }
  return '/images/products/default-product.jpg'
}

// Gestisci errore caricamento immagine
const handleImageError = (event) => {
  event.target.src = '/images/products/default-product.jpg'
}

// Formatta prezzo in formato italiano
const formatPrice = (price) => {
  return new Intl.NumberFormat('it-IT', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(price)
}

// Carica prodotti al mount
onMounted(() => {
  loadProducts()
})
</script>

<style scoped>
/* Pagina prodotti */
.products-page {
  min-height: 100vh;
  background: #ffffff;
  padding: 2rem 0;
  font-family: 'Inter', Arial, sans-serif;
}

.products-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

/* Header sezione */
.products-header {
  margin-bottom: 2.5rem;
  text-align: center;
}

.products-header h1 {
  font-size: 2.5rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.products-header p {
  font-size: 1.125rem;
  color: #6b7280;
}

/* Sezione filtri */
.filters-section {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  padding: 1.5rem;
  margin-bottom: 2rem;
}

.filters-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 1rem;
}

.filter-item label {
  display: block;
  font-size: 0.95rem;
  font-weight: 500;
  color: #4b5563;
  margin-bottom: 0.5rem;
}

.filter-item input,
.filter-item select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  font-size: 1rem;
  color: #1f2937;
  background: #ffffff;
  transition: border-color 0.2s ease;
}

.filter-item input:focus,
.filter-item select:focus {
  outline: none;
  border-color: #2563eb;
}

/* Filtri attivi */
.active-filters {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
}

.active-filters .label {
  font-size: 0.9rem;
  font-weight: 500;
  color: #6b7280;
}

.filter-badge {
  padding: 0.375rem 0.75rem;
  background: #eff6ff;
  color: #2563eb;
  border: none;
  border-radius: 4px;
  font-size: 0.875rem;
  cursor: pointer;
  transition: background 0.2s ease;
}

.filter-badge:hover {
  background: #dbeafe;
}

.clear-all-btn {
  padding: 0.375rem 0.75rem;
  background: #f3f4f6;
  color: #4b5563;
  border: none;
  border-radius: 4px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s ease;
}

.clear-all-btn:hover {
  background: #e5e7eb;
}

/* Loading state */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 0;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e5e7eb;
  border-top-color: #2563eb;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-state p {
  margin-top: 1rem;
  color: #6b7280;
  font-size: 1rem;
}

/* Griglia prodotti */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

/* Card prodotto */
.product-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s ease;
}

.product-card:hover {
  border-color: #2563eb;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  transform: translateY(-2px);
}

/* Immagine prodotto */
.product-image {
  position: relative;
  width: 100%;
  aspect-ratio: 1;
  overflow: hidden;
  background: #f3f4f6;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.2s ease;
}

.product-card:hover .product-image img {
  transform: scale(1.05);
}

/* Badge disponibilità */
.badge {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  padding: 0.25rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 500;
  border-radius: 4px;
  color: #ffffff;
}

.badge.available {
  background: #10b981;
}

.badge.low-stock {
  background: #f59e0b;
}

.badge.out-of-stock {
  background: #ef4444;
}

/* Info prodotto */
.product-info {
  padding: 1rem;
}

.product-category {
  display: inline-block;
  padding: 0.25rem 0.625rem;
  background: #f3f4f6;
  color: #6b7280;
  font-size: 0.75rem;
  border-radius: 4px;
  margin-bottom: 0.75rem;
  text-transform: capitalize;
}

.product-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-description {
  font-size: 0.9rem;
  color: #6b7280;
  line-height: 1.5;
  margin-bottom: 1rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 0.75rem;
  border-top: 1px solid #f3f4f6;
}

.product-price {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2563eb;
}

.add-to-cart-btn {
  padding: 0.5rem 1rem;
  background: #2563eb;
  color: #ffffff;
  border: none;
  border-radius: 4px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s ease;
}

.add-to-cart-btn:hover:not(:disabled) {
  background: #1d4ed8;
}

.add-to-cart-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Empty state */
.empty-state {
  text-align: center;
  padding: 4rem 1.5rem;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.empty-state h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.empty-state p {
  font-size: 1rem;
  color: #6b7280;
  margin-bottom: 1.5rem;
}

.btn-secondary {
  padding: 0.75rem 1.5rem;
  background: #f3f4f6;
  color: #4b5563;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

/* Paginazione */
.pagination {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  padding: 2rem 0;
}

.page-btn {
  padding: 0.5rem 1rem;
  background: #ffffff;
  color: #4b5563;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.page-btn:hover {
  background: #f9fafb;
  border-color: #2563eb;
}

.page-btn.active {
  background: #2563eb;
  color: #ffffff;
  border-color: #2563eb;
}

/* Toast messaggio successo */
.success-toast {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  padding: 1rem 1.5rem;
  background: #10b981;
  color: #ffffff;
  border-radius: 4px;
  font-weight: 500;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  animation: slideInUp 0.3s ease;
}

@keyframes slideInUp {
  from {
    transform: translateY(100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

/* Responsive: tablet */
@media (max-width: 1024px) {
  .filters-grid {
    grid-template-columns: 1fr;
  }

  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  }
}

/* Responsive: mobile */
@media (max-width: 768px) {
  .products-header h1 {
    font-size: 2rem;
  }

  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 1rem;
  }

  .product-title {
    font-size: 1rem;
  }

  .product-price {
    font-size: 1.25rem;
  }
}
</style>
