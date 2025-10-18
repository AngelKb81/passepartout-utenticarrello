<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Gestione Prodotti</h1>
              <p class="mt-2 text-gray-600">Amministra il catalogo prodotti</p>
            </div>
            <button 
              @click="showCreateModal = true" 
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              Nuovo Prodotto
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Prodotti Totali</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ products.length }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Attivi</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ activeProducts.length }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Categorie</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ uniqueCategories.length }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Scorte Basse</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ lowStockProducts.length }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters and Search -->
      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cerca</label>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Nome, codice, descrizione..."
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Categoria</label>
            <select
              v-model="filters.categoria"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Tutte le categorie</option>
              <option v-for="cat in uniqueCategories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Stato</label>
            <select
              v-model="filters.attivo"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Tutti</option>
              <option value="true">Attivi</option>
              <option value="false">Disattivi</option>
            </select>
          </div>
          <div class="flex items-end">
            <button 
              @click="clearFilters"
              class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors"
            >
              Pulisci Filtri
            </button>
          </div>
        </div>
      </div>

      <!-- Products Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Prodotti ({{ filteredProducts.length }})</h3>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>

        <!-- Table -->
        <div v-else-if="filteredProducts.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prodotto</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Codice</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prezzo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scorte</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stato</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <ProductRow 
                v-for="product in filteredProducts" 
                :key="product.id"
                :product="product"
                @update="updateProduct"
                @delete="deleteProduct"
                @toggle-status="toggleProductStatus"
              />
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Nessun prodotto trovato</h3>
          <p class="mt-1 text-sm text-gray-500">
            {{ filters.search || filters.categoria || filters.attivo ? 'Prova a modificare i filtri' : 'Inizia creando il tuo primo prodotto' }}
          </p>
        </div>
      </div>
    </div>

    <!-- Create Product Modal -->
    <CreateProductModal 
      v-if="showCreateModal"
      @close="showCreateModal = false"
      @created="onProductCreated"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from 'axios'
import ProductRow from '../../components/admin/ProductRow.vue'
import CreateProductModal from '../../components/admin/CreateProductModal.vue'

export default {
  name: 'ProductManagement',
  components: {
    ProductRow,
    CreateProductModal
  },
  setup() {
    const authStore = useAuthStore()
    const loading = ref(false)
    const products = ref([])
    const showCreateModal = ref(false)
    
    const filters = ref({
      search: '',
      categoria: '',
      attivo: ''
    })

    // Computed properties
    const activeProducts = computed(() => products.value.filter(p => p.attivo))
    const lowStockProducts = computed(() => products.value.filter(p => p.scorte < 10))
    const uniqueCategories = computed(() => {
      const categories = products.value.map(p => p.categoria).filter(Boolean)
      return [...new Set(categories)].sort()
    })

    const filteredProducts = computed(() => {
      let filtered = products.value

      if (filters.value.search) {
        const search = filters.value.search.toLowerCase()
        filtered = filtered.filter(p => 
          p.nome.toLowerCase().includes(search) ||
          p.codice.toLowerCase().includes(search) ||
          (p.descrizione && p.descrizione.toLowerCase().includes(search))
        )
      }

      if (filters.value.categoria) {
        filtered = filtered.filter(p => p.categoria === filters.value.categoria)
      }

      if (filters.value.attivo !== '') {
        const isActive = filters.value.attivo === 'true'
        filtered = filtered.filter(p => p.attivo === isActive)
      }

      return filtered
    })

    // Methods
    const loadProducts = async () => {
      try {
        loading.value = true
        const response = await axios.get('/admin/products')
        products.value = response.data.products || []
      } catch (error) {
        console.error('Errore nel caricamento prodotti:', error)
        // Fallback con dati mock per demo
        products.value = [
          {
            id: 1,
            nome: 'Smartphone Samsung Galaxy S24',
            codice: 'SAMSUNG-S24-001',
            categoria: 'Smartphone',
            descrizione: 'Smartphone di ultima generazione con fotocamera da 200MP',
            prezzo_originale: '1199.99',
            prezzo_attuale: '999.99',
            scorte: 25,
            attivo: true,
            immagine_url: '/images/products/smartphone-samsung-s24.jpg'
          },
          {
            id: 2,
            nome: 'Laptop Dell XPS 13',
            codice: 'DELL-XPS13-001',
            categoria: 'Computer',
            descrizione: 'Ultrabook premium con Intel Core i7, 16GB RAM, SSD 512GB',
            prezzo_originale: '1499.99',
            prezzo_attuale: '1299.99',
            scorte: 15,
            attivo: true,
            immagine_url: '/images/products/laptop-dell-xps13.jpg'
          },
          {
            id: 3,
            nome: 'Cuffie Sony WH-1000XM5',
            codice: 'SONY-WH1000XM5',
            categoria: 'Audio',
            descrizione: 'Cuffie wireless con cancellazione attiva del rumore',
            prezzo_originale: '449.99',
            prezzo_attuale: '399.99',
            scorte: 8,
            attivo: true,
            immagine_url: '/images/products/cuffie-sony-wh1000xm5.jpg'
          }
        ]
      } finally {
        loading.value = false
      }
    }

    const updateProduct = async (productId, updates) => {
      try {
        const response = await axios.put(`/admin/products/${productId}`, updates)
        const index = products.value.findIndex(p => p.id === productId)
        if (index !== -1) {
          products.value[index] = { ...products.value[index], ...updates }
        }
        console.log('Prodotto aggiornato:', response.data)
      } catch (error) {
        console.error('Errore aggiornamento prodotto:', error)
        // Per demo, aggiorna comunque localmente
        const index = products.value.findIndex(p => p.id === productId)
        if (index !== -1) {
          products.value[index] = { ...products.value[index], ...updates }
        }
      }
    }

    const deleteProduct = async (productId) => {
      if (!confirm('Sei sicuro di voler eliminare questo prodotto?')) return
      
      try {
        await axios.delete(`/admin/products/${productId}`)
        products.value = products.value.filter(p => p.id !== productId)
        console.log('Prodotto eliminato')
      } catch (error) {
        console.error('Errore eliminazione prodotto:', error)
        // Per demo, elimina comunque localmente
        products.value = products.value.filter(p => p.id !== productId)
      }
    }

    const toggleProductStatus = async (productId) => {
      try {
        await axios.patch(`/admin/products/${productId}/toggle-status`)
        const product = products.value.find(p => p.id === productId)
        if (product) {
          product.attivo = !product.attivo
        }
      } catch (error) {
        console.error('Errore toggle status:', error)
        // Per demo, cambia comunque localmente
        const product = products.value.find(p => p.id === productId)
        if (product) {
          product.attivo = !product.attivo
        }
      }
    }

    const onProductCreated = (newProduct) => {
      products.value.unshift(newProduct)
      showCreateModal.value = false
    }

    const clearFilters = () => {
      filters.value = {
        search: '',
        categoria: '',
        attivo: ''
      }
    }

    onMounted(() => {
      loadProducts()
    })

    return {
      loading,
      products,
      showCreateModal,
      filters,
      activeProducts,
      lowStockProducts,
      uniqueCategories,
      filteredProducts,
      updateProduct,
      deleteProduct,
      toggleProductStatus,
      onProductCreated,
      clearFilters
    }
  }
}
</script>