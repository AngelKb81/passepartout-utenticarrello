<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Breadcrumb -->
      <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm">
          <li>
            <router-link to="/" class="text-blue-600 hover:text-blue-800">Home</router-link>
          </li>
          <li class="text-gray-500">></li>
          <li>
            <router-link to="/products" class="text-blue-600 hover:text-blue-800">Prodotti</router-link>
          </li>
          <li class="text-gray-500">></li>
          <li class="text-gray-900 font-medium">{{ product?.nome || 'Prodotto' }}</li>
        </ol>
      </nav>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <span class="ml-3 text-gray-600">Caricamento prodotto...</span>
      </div>

      <!-- Product Not Found -->
      <div v-else-if="!product" class="text-center py-20">
        <div class="text-6xl mb-4">😵</div>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Prodotto non trovato</h2>
        <p class="text-gray-600 mb-8">Il prodotto che stai cercando non esiste o non è più disponibile.</p>
        <router-link 
          to="/products" 
          class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors"
        >
          🔙 Torna al catalogo
        </router-link>
      </div>

      <!-- Product Details -->
      <div v-else class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Product Images -->
          <div class="p-6">
            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden mb-4">
              <img
                :src="getProductImage(product)"
                :alt="product.nome"
                class="w-full h-full object-cover"
                @error="handleImageError"
              />
            </div>
            
            <!-- Thumbnail Gallery (per future implementation) -->
            <div class="grid grid-cols-4 gap-2">
              <div 
                v-for="i in 4" 
                :key="i"
                class="aspect-square bg-gray-100 rounded-lg overflow-hidden opacity-50"
              >
                <img
                  :src="getProductImage(product)"
                  :alt="`${product.nome} - Vista ${i}`"
                  class="w-full h-full object-cover"
                  @error="handleImageError"
                />
              </div>
            </div>
          </div>

          <!-- Product Info -->
          <div class="p-6">
            <!-- Category Badge -->
            <div class="mb-4">
              <span class="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                📂 {{ product.categoria || 'Generale' }}
              </span>
            </div>

            <!-- Product Name -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">
              {{ product.nome }}
            </h1>

            <!-- Price -->
            <div class="mb-6">
              <div class="flex items-baseline space-x-2">
                <span class="text-4xl font-bold text-blue-600">
                  €{{ formatPrice(product.prezzo) }}
                </span>
                <span class="text-lg text-gray-500">IVA inclusa</span>
              </div>
            </div>

            <!-- Availability -->
            <div class="mb-6 p-4 rounded-lg" :class="availabilityClass">
              <div class="flex items-center">
                <span class="text-lg mr-2">{{ availabilityIcon }}</span>
                <div>
                  <div class="font-semibold">{{ availabilityText }}</div>
                  <div v-if="product.disponibile && product.quantita > 0" class="text-sm opacity-75">
                    {{ product.quantita }} pezzi disponibili
                  </div>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">📋 Descrizione</h3>
              <p class="text-gray-600 leading-relaxed">
                {{ product.descrizione || 'Nessuna descrizione disponibile.' }}
              </p>
            </div>

            <!-- Product Details -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">🔍 Dettagli Prodotto</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex justify-between py-2 border-b border-gray-200">
                  <span class="text-gray-600">Codice Prodotto:</span>
                  <span class="font-medium">#{{ product.id.toString().padStart(6, '0') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                  <span class="text-gray-600">Categoria:</span>
                  <span class="font-medium">{{ product.categoria || 'Non specificata' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                  <span class="text-gray-600">Disponibilità:</span>
                  <span class="font-medium">{{ product.quantita }} pezzi</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                  <span class="text-gray-600">Aggiunto il:</span>
                  <span class="font-medium">{{ formatDate(product.created_at) }}</span>
                </div>
              </div>
            </div>

            <!-- Add to Cart Section -->
            <div class="border-t pt-6">
              <div v-if="!authStore.isAuthenticated" class="text-center">
                <p class="text-gray-600 mb-4">Devi effettuare l'accesso per acquistare questo prodotto.</p>
                <router-link 
                  to="/login" 
                  class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 transition-colors inline-block"
                >
                  🔐 Accedi per acquistare
                </router-link>
              </div>

              <div v-else-if="!product.disponibile || product.quantita <= 0" class="text-center">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                  <p class="text-red-800 font-medium">❌ Prodotto non disponibile</p>
                  <p class="text-red-600 text-sm mt-1">
                    Questo prodotto è attualmente esaurito. Torna più tardi per verificare la disponibilità.
                  </p>
                </div>
              </div>

              <div v-else>
                <!-- Quantity Selector -->
                <div class="flex items-center space-x-4 mb-6">
                  <label for="quantity" class="text-sm font-medium text-gray-700">
                    Quantità:
                  </label>
                  <div class="flex items-center border border-gray-300 rounded-md">
                    <button
                      @click="decrementQuantity"
                      :disabled="quantity <= 1"
                      class="px-3 py-2 text-gray-600 hover:text-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      −
                    </button>
                    <input
                      v-model.number="quantity"
                      type="number"
                      id="quantity"
                      min="1"
                      :max="product.quantita"
                      class="w-16 text-center border-0 focus:ring-0 focus:outline-none"
                      @input="validateQuantity"
                    />
                    <button
                      @click="incrementQuantity"
                      :disabled="quantity >= product.quantita"
                      class="px-3 py-2 text-gray-600 hover:text-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      +
                    </button>
                  </div>
                  <span class="text-sm text-gray-500">
                    (Max: {{ product.quantita }})
                  </span>
                </div>

                <!-- Total Price -->
                <div class="mb-6">
                  <div class="text-lg font-semibold text-gray-900">
                    Totale: <span class="text-blue-600">€{{ formatPrice(product.prezzo * quantity) }}</span>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-4">
                  <button
                    @click="addToCart"
                    :disabled="addingToCart"
                    class="w-full bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-semibold"
                  >
                    <div v-if="addingToCart" class="flex items-center justify-center">
                      <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                      Aggiunta in corso...
                    </div>
                    <span v-else>🛒 Aggiungi al Carrello</span>
                  </button>

                  <button
                    @click="buyNow"
                    :disabled="addingToCart"
                    class="w-full bg-green-600 text-white py-3 px-6 rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-semibold"
                  >
                    ⚡ Compra Subito
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Related Products Section -->
        <div v-if="relatedProducts.length > 0" class="border-t bg-gray-50 p-6">
          <h3 class="text-xl font-semibold text-gray-900 mb-6">🔗 Prodotti Correlati</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div
              v-for="relatedProduct in relatedProducts"
              :key="relatedProduct.id"
              class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer"
              @click="goToProduct(relatedProduct.id)"
            >
              <div class="aspect-square overflow-hidden rounded-t-lg">
                <img
                  :src="getProductImage(relatedProduct)"
                  :alt="relatedProduct.nome"
                  class="w-full h-full object-cover hover:scale-105 transition-transform duration-200"
                  @error="handleImageError"
                />
              </div>
              <div class="p-3">
                <h4 class="font-medium text-gray-900 line-clamp-2 mb-2">
                  {{ relatedProduct.nome }}
                </h4>
                <p class="text-lg font-bold text-blue-600">
                  €{{ formatPrice(relatedProduct.prezzo) }}
                </p>
              </div>
            </div>
          </div>
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useCartStore } from '../../stores/cart'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

// Props
const props = defineProps({
  id: {
    type: String,
    required: true
  }
})

// State
const loading = ref(false)
const product = ref(null)
const relatedProducts = ref([])
const quantity = ref(1)
const addingToCart = ref(false)
const successMessage = ref('')

// Computed
const availabilityClass = computed(() => {
  if (!product.value?.disponibile || product.value?.quantita <= 0) {
    return 'bg-red-50 border border-red-200 text-red-800'
  } else if (product.value?.quantita <= 5) {
    return 'bg-yellow-50 border border-yellow-200 text-yellow-800'
  } else {
    return 'bg-green-50 border border-green-200 text-green-800'
  }
})

const availabilityIcon = computed(() => {
  if (!product.value?.disponibile || product.value?.quantita <= 0) {
    return '❌'
  } else if (product.value?.quantita <= 5) {
    return '⚠️'
  } else {
    return '✅'
  }
})

const availabilityText = computed(() => {
  if (!product.value?.disponibile || product.value?.quantita <= 0) {
    return 'Non disponibile'
  } else if (product.value?.quantita <= 5) {
    return `Ultimi ${product.value?.quantita} pezzi`
  } else {
    return 'Disponibile'
  }
})

// Methods
const loadProduct = async () => {
  try {
    loading.value = true
    
    const response = await axios.get(`/products/${props.id}`)
    product.value = response.data.product || response.data
    
    // Load related products
    loadRelatedProducts()
    
  } catch (error) {
    console.error('Errore nel caricamento del prodotto:', error)
    if (error.response?.status === 404) {
      product.value = null
    }
  } finally {
    loading.value = false
  }
}

const loadRelatedProducts = async () => {
  if (!product.value) return
  
  try {
    const response = await axios.get('/products', {
      params: {
        categoria: product.value.categoria,
        per_page: 4,
        exclude: product.value.id
      }
    })
    
    relatedProducts.value = (response.data.products || response.data.data || [])
      .filter(p => p.id !== product.value.id)
      .slice(0, 4)
      
  } catch (error) {
    console.error('Errore nel caricamento prodotti correlati:', error)
    relatedProducts.value = []
  }
}

const addToCart = async () => {
  if (!authStore.isAuthenticated) {
    router.push('/login')
    return
  }
  
  try {
    addingToCart.value = true
    
    const result = await cartStore.addToCart(product.value.id, quantity.value)
    
    if (result.success) {
      successMessage.value = `${product.value.nome} aggiunto al carrello!`
      setTimeout(() => successMessage.value = '', 3000)
    } else {
      alert(result.message || 'Errore nell\'aggiunta al carrello')
    }
  } catch (error) {
    console.error('Errore aggiunta carrello:', error)
    alert('Errore nell\'aggiunta al carrello')
  } finally {
    addingToCart.value = false
  }
}

const buyNow = async () => {
  await addToCart()
  if (successMessage.value) {
    // Redirect to cart after a short delay
    setTimeout(() => {
      router.push('/cart')
    }, 1000)
  }
}

const incrementQuantity = () => {
  if (quantity.value < product.value.quantita) {
    quantity.value++
  }
}

const decrementQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--
  }
}

const validateQuantity = () => {
  if (quantity.value < 1) {
    quantity.value = 1
  } else if (quantity.value > product.value.quantita) {
    quantity.value = product.value.quantita
  }
}

const goToProduct = (productId) => {
  router.push(`/products/${productId}`)
}

const getProductImage = (prod) => {
  if (prod?.immagine && prod.immagine.startsWith('/images/')) {
    return prod.immagine
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

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('it-IT', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Watchers
watch(() => props.id, () => {
  loadProduct()
}, { immediate: true })

// Lifecycle
onMounted(() => {
  loadProduct()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Remove spinner from number input */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
}

/* Smooth animations */
.hover\:scale-105:hover {
  transform: scale(1.05);
}
</style>