<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
          🛍️ Catalogo Prodotti
        </h1>
        <p class="text-gray-600">
          Scopri la nostra selezione di prodotti di qualità
        </p>
      </div>

      <!-- Filters and Search -->
      <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Search -->
          <div class="md:col-span-2">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
              🔍 Cerca prodotti
            </label>
            <input
              v-model="filters.search"
              type="text"
              id="search"
              placeholder="Cerca per nome o descrizione..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @input="debouncedSearch"
            />
          </div>

          <!-- Category Filter -->
          <div>
            <label for="categoria" class="block text-sm font-medium text-gray-700 mb-2">
              📂 Categoria
            </label>
            <select
              v-model="filters.categoria"
              id="categoria"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @change="loadProducts"
            >
              <option value="">Tutte le categorie</option>
              <option value="elettronica">Elettronica</option>
              <option value="abbigliamento">Abbigliamento</option>
              <option value="casa">Casa e Giardino</option>
              <option value="sport">Sport e Tempo Libero</option>
              <option value="libri">Libri</option>
            </select>
          </div>

          <!-- Sort -->
          <div>
            <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">
              🔀 Ordina per
            </label>
            <select
              v-model="filters.sort"
              id="sort"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @change="loadProducts"
            >
              <option value="nome_asc">Nome A-Z</option>
              <option value="nome_desc">Nome Z-A</option>
              <option value="prezzo_asc">Prezzo crescente</option>
              <option value="prezzo_desc">Prezzo decrescente</option>
              <option value="created_desc">Più recenti</option>
            </select>
          </div>
        </div>

        <!-- Price Range -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="min_price" class="block text-sm font-medium text-gray-700 mb-2">
              💰 Prezzo minimo (€)
            </label>
            <input
              v-model.number="filters.min_price"
              type="number"
              id="min_price"
              min="0"
              step="0.01"
              placeholder="0.00"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @input="debouncedSearch"
            />
          </div>
          <div>
            <label for="max_price" class="block text-sm font-medium text-gray-700 mb-2">
              💰 Prezzo massimo (€)
            </label>
            <input
              v-model.number="filters.max_price"
              type="number"
              id="max_price"
              min="0"
              step="0.01"
              placeholder="1000.00"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @input="debouncedSearch"
            />
          </div>
        </div>

        <!-- Active Filters -->
        <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
          <span class="text-sm font-medium text-gray-700">Filtri attivi:</span>
          <button
            v-if="filters.search"
            @click="clearFilter('search')"
            class="inline-flex items-center px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-full hover:bg-blue-200"
          >
            🔍 {{ filters.search }} ✕
          </button>
          <button
            v-if="filters.categoria"
            @click="clearFilter('categoria')"
            class="inline-flex items-center px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full hover:bg-green-200"
          >
            📂 {{ filters.categoria }} ✕
          </button>
          <button
            v-if="filters.min_price"
            @click="clearFilter('min_price')"
            class="inline-flex items-center px-3 py-1 text-xs bg-purple-100 text-purple-800 rounded-full hover:bg-purple-200"
          >
            💰 Min €{{ filters.min_price }} ✕
          </button>
          <button
            v-if="filters.max_price"
            @click="clearFilter('max_price')"
            class="inline-flex items-center px-3 py-1 text-xs bg-purple-100 text-purple-800 rounded-full hover:bg-purple-200"
          >
            💰 Max €{{ filters.max_price }} ✕
          </button>
          <button
            @click="clearAllFilters"
            class="inline-flex items-center px-3 py-1 text-xs bg-gray-100 text-gray-800 rounded-full hover:bg-gray-200"
          >
            🗑️ Pulisci tutti
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <span class="ml-3 text-gray-600">Caricamento prodotti...</span>
      </div>

      <!-- Products Grid -->
      <div v-else-if="products.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
          v-for="product in products"
          :key="product.id"
          class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden group cursor-pointer"
          @click="goToProduct(product.id)"
        >
          <!-- Product Image -->
          <div class="relative aspect-square overflow-hidden">
            <img
              :src="getProductImage(product)"
              :alt="product.nome"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
              @error="handleImageError"
            />
            
            <!-- Disponibilità Badge -->
            <div class="absolute top-2 right-2">
              <span
                v-if="product.disponibile && product.quantita > 0"
                class="bg-green-500 text-white text-xs px-2 py-1 rounded-full"
              >
                ✅ Disponibile
              </span>
              <span
                v-else-if="product.disponibile && product.quantita <= 5"
                class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full"
              >
                ⚠️ Ultimi {{ product.quantita }}
              </span>
              <span
                v-else
                class="bg-red-500 text-white text-xs px-2 py-1 rounded-full"
              >
                ❌ Esaurito
              </span>
            </div>

            <!-- Quick Actions -->
            <div class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
              <button
                v-if="authStore.isAuthenticated && product.disponibile && product.quantita > 0"
                @click.stop="quickAddToCart(product)"
                class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full shadow-lg transition-colors"
                :disabled="addingToCart[product.id]"
              >
                <div v-if="addingToCart[product.id]" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                <span v-else>🛒</span>
              </button>
            </div>
          </div>

          <!-- Product Info -->
          <div class="p-4">
            <div class="mb-2">
              <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">
                {{ product.categoria || 'Generale' }}
              </span>
            </div>
            
            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
              {{ product.nome }}
            </h3>
            
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">
              {{ product.descrizione }}
            </p>
            
            <div class="flex items-center justify-between">
              <span class="text-lg font-bold text-blue-600">
                €{{ formatPrice(product.prezzo) }}
              </span>
              
              <div class="text-sm text-gray-500">
                Qtà: {{ product.quantita }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="!loading" class="text-center py-12">
        <div class="text-6xl mb-4">🔍</div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">
          Nessun prodotto trovato
        </h3>
        <p class="text-gray-500 mb-4">
          Prova a modificare i filtri di ricerca o naviga in una categoria diversa.
        </p>
        <button
          @click="clearAllFilters"
          class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
        >
          🗑️ Rimuovi tutti i filtri
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.total > pagination.per_page" class="mt-8 flex justify-center">
        <nav class="flex space-x-2">
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            :class="[
              'px-3 py-2 text-sm rounded-md transition-colors',
              page === pagination.current_page
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-100 border'
            ]"
          >
            {{ page }}
          </button>
        </nav>
      </div>
    </div>

    <!-- Success Message -->
    <div
      v-if="successMessage"
      class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
    >
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

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

// State
const loading = ref(false)
const products = ref([])
const addingToCart = ref({})
const successMessage = ref('')

const filters = reactive({
  search: '',
  categoria: '',
  sort: 'nome_asc',
  min_price: null,
  max_price: null
})

const pagination = reactive({
  current_page: 1,
  per_page: 12,
  total: 0,
  last_page: 1
})

// Computed
const hasActiveFilters = computed(() => {
  return filters.search || filters.categoria || filters.min_price || filters.max_price
})

const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, pagination.current_page - 2)
  const end = Math.min(pagination.last_page, start + 4)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Methods
const loadProducts = async (page = 1) => {
  try {
    loading.value = true
    
    const params = new URLSearchParams({
      page: page.toString(),
      per_page: pagination.per_page.toString()
    })
    
    if (filters.search) params.append('search', filters.search)
    if (filters.categoria) params.append('categoria', filters.categoria)
    if (filters.sort) params.append('sort', filters.sort)
    if (filters.min_price) params.append('min_price', filters.min_price.toString())
    if (filters.max_price) params.append('max_price', filters.max_price.toString())
    
    const response = await axios.get(`/products?${params}`)
    
    products.value = response.data.products || response.data.data || []
    
    // Update pagination
    if (response.data.pagination) {
      Object.assign(pagination, response.data.pagination)
    } else if (response.data.meta) {
      pagination.current_page = response.data.meta.current_page
      pagination.per_page = response.data.meta.per_page
      pagination.total = response.data.meta.total
      pagination.last_page = response.data.meta.last_page
    }
    
  } catch (error) {
    console.error('Errore nel caricamento prodotti:', error)
    products.value = []
  } finally {
    loading.value = false
  }
}

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
      alert(result.message || 'Errore nell\'aggiunta al carrello')
    }
  } catch (error) {
    console.error('Errore aggiunta carrello:', error)
    alert('Errore nell\'aggiunta al carrello')
  } finally {
    addingToCart.value[product.id] = false
  }
}

const goToProduct = (productId) => {
  router.push(`/products/${productId}`)
}

const goToPage = (page) => {
  if (page !== pagination.current_page) {
    loadProducts(page)
  }
}

const clearFilter = (filterName) => {
  filters[filterName] = filterName.includes('price') ? null : ''
  loadProducts()
}

const clearAllFilters = () => {
  filters.search = ''
  filters.categoria = ''
  filters.min_price = null
  filters.max_price = null
  loadProducts()
}

// Debounced search
let searchTimeout = null
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadProducts()
  }, 500)
}

const getProductImage = (product) => {
  if (product.immagine && product.immagine.startsWith('/images/')) {
    return product.immagine
  }
  return '/images/products/default-product.jpg'
}

const handleImageError = (event) => {
  event.target.src = '/images/products/default-product.jpg'
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('it-IT', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(price)
}

// Lifecycle
onMounted(() => {
  loadProducts()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Custom scrollbar for the page */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}

/* Smooth animations */
.group:hover .group-hover\:scale-105 {
  transform: scale(1.05);
}

.group:hover .group-hover\:opacity-100 {
  opacity: 1;
}
</style>