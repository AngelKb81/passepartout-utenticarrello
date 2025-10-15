<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
          🛒 Il Mio Carrello
        </h1>
        <p class="text-gray-600">
          Rivedi i tuoi prodotti prima di procedere al checkout
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <span class="ml-3 text-gray-600">Caricamento carrello...</span>
      </div>

      <!-- Empty Cart -->
      <div v-else-if="cartStore.isEmpty" class="bg-white rounded-lg shadow-sm p-12 text-center">
        <div class="text-6xl mb-6">🛒</div>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Il tuo carrello è vuoto</h2>
        <p class="text-gray-600 mb-8">
          Non hai ancora aggiunto prodotti al carrello. Inizia a fare shopping!
        </p>
        <router-link 
          to="/products" 
          class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 transition-colors inline-block"
        >
          🛍️ Inizia Shopping
        </router-link>
      </div>

      <!-- Cart Content -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <!-- Cart Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
              <div class="grid grid-cols-12 gap-4 text-sm font-medium text-gray-700">
                <div class="col-span-1"></div>
                <div class="col-span-4">Prodotto</div>
                <div class="col-span-2 text-center">Prezzo</div>
                <div class="col-span-2 text-center">Quantità</div>
                <div class="col-span-2 text-center">Totale</div>
                <div class="col-span-1"></div>
              </div>
            </div>

            <!-- Cart Items List -->
            <div class="divide-y divide-gray-200">
              <div
                v-for="item in cartStore.items"
                :key="item.id"
                class="px-6 py-6 grid grid-cols-12 gap-4 items-center hover:bg-gray-50 transition-colors"
              >
                <!-- Product Image -->
                <div class="col-span-1">
                  <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden">
                    <img
                      :src="getProductImage(item.product)"
                      :alt="item.product.nome"
                      class="w-full h-full object-cover"
                      @error="handleImageError"
                    />
                  </div>
                </div>

                <!-- Product Info -->
                <div class="col-span-4">
                  <h3 class="font-semibold text-gray-900 mb-1">
                    {{ item.product.nome }}
                  </h3>
                  <p class="text-sm text-gray-600 line-clamp-2">
                    {{ item.product.descrizione }}
                  </p>
                  <div class="mt-2">
                    <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">
                      {{ item.product.categoria || 'Generale' }}
                    </span>
                  </div>
                </div>

                <!-- Price -->
                <div class="col-span-2 text-center">
                  <span class="text-lg font-semibold text-gray-900">
                    €{{ formatPrice(item.product.prezzo) }}
                  </span>
                </div>

                <!-- Quantity Controls -->
                <div class="col-span-2">
                  <div class="flex items-center justify-center space-x-2">
                    <button
                      @click="decrementQuantity(item)"
                      :disabled="updatingItem[item.id]"
                      class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                      −
                    </button>
                    
                    <div class="flex items-center">
                      <input
                        v-model.number="item.quantita"
                        type="number"
                        min="1"
                        :max="item.product.quantita"
                        class="w-16 text-center border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        @blur="updateQuantity(item)"
                        @keyup.enter="updateQuantity(item)"
                      />
                      <div v-if="updatingItem[item.id]" class="ml-2">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
                      </div>
                    </div>
                    
                    <button
                      @click="incrementQuantity(item)"
                      :disabled="updatingItem[item.id] || item.quantita >= item.product.quantita"
                      class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                      +
                    </button>
                  </div>
                  
                  <div class="text-xs text-gray-500 text-center mt-1">
                    Max: {{ item.product.quantita }}
                  </div>
                </div>

                <!-- Item Total -->
                <div class="col-span-2 text-center">
                  <span class="text-lg font-bold text-blue-600">
                    €{{ formatPrice(item.product.prezzo * item.quantita) }}
                  </span>
                </div>

                <!-- Remove Button -->
                <div class="col-span-1 text-center">
                  <button
                    @click="removeItem(item)"
                    :disabled="removingItem[item.id]"
                    class="w-8 h-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center"
                    title="Rimuovi dal carrello"
                  >
                    <div v-if="removingItem[item.id]" class="animate-spin rounded-full h-4 w-4 border-b-2 border-red-600"></div>
                    <span v-else>🗑️</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Cart Actions -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
              <div class="flex justify-between items-center">
                <router-link 
                  to="/products" 
                  class="text-blue-600 hover:text-blue-800 font-medium"
                >
                  ← Continua Shopping
                </router-link>
                
                <button
                  @click="clearCart"
                  :disabled="clearingCart"
                  class="bg-red-100 text-red-700 px-4 py-2 rounded-md hover:bg-red-200 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                  <div v-if="clearingCart" class="flex items-center">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-red-700 mr-2"></div>
                    Svuotando...
                  </div>
                  <span v-else>🗑️ Svuota Carrello</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-sm p-6 sticky top-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              📋 Riepilogo Ordine
            </h3>

            <!-- Items Summary -->
            <div class="space-y-3 mb-6">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Prodotti ({{ cartStore.cartItemsCount }})</span>
                <span class="font-medium">€{{ formatPrice(subtotal) }}</span>
              </div>
              
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Spedizione</span>
                <span class="font-medium text-green-600">Gratuita</span>
              </div>
              
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">IVA (22%)</span>
                <span class="font-medium">€{{ formatPrice(taxAmount) }}</span>
              </div>
              
              <div class="border-t pt-3">
                <div class="flex justify-between">
                  <span class="text-lg font-semibold text-gray-900">Totale</span>
                  <span class="text-xl font-bold text-blue-600">€{{ formatPrice(total) }}</span>
                </div>
              </div>
            </div>

            <!-- Shipping Form -->
            <div class="mb-6">
              <h4 class="font-medium text-gray-900 mb-3">🚚 Dati Spedizione</h4>
              <div class="space-y-3">
                <input
                  v-model="shippingData.indirizzo"
                  type="text"
                  placeholder="Indirizzo di spedizione"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
                <div class="grid grid-cols-2 gap-2">
                  <input
                    v-model="shippingData.citta"
                    type="text"
                    placeholder="Città"
                    class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                  <input
                    v-model="shippingData.cap"
                    type="text"
                    placeholder="CAP"
                    class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
                <textarea
                  v-model="shippingData.note"
                  rows="2"
                  placeholder="Note aggiuntive (opzionale)"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                ></textarea>
              </div>
            </div>

            <!-- Checkout Button -->
            <button
              @click="proceedToCheckout"
              :disabled="checkingOut || !canCheckout"
              class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-semibold"
            >
              <div v-if="checkingOut" class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                Elaborando...
              </div>
              <span v-else>💳 Procedi al Checkout</span>
            </button>

            <!-- Security Notice -->
            <div class="mt-4 text-xs text-gray-500 text-center">
              🔒 Pagamento sicuro e protetto<br>
              I tuoi dati sono al sicuro con noi
            </div>
          </div>
        </div>
      </div>

      <!-- Success/Error Messages -->
      <div
        v-if="successMessage"
        class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
      >
        {{ successMessage }}
      </div>
      
      <div
        v-if="errorMessage"
        class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
      >
        {{ errorMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '../../stores/cart'

const router = useRouter()
const cartStore = useCartStore()

// State
const loading = ref(false)
const updatingItem = ref({})
const removingItem = ref({})
const clearingCart = ref(false)
const checkingOut = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const shippingData = reactive({
  indirizzo: '',
  citta: '',
  cap: '',
  note: ''
})

// Computed
const subtotal = computed(() => {
  return cartStore.items.reduce((total, item) => {
    return total + (item.product.prezzo * item.quantita)
  }, 0)
})

const taxAmount = computed(() => {
  return subtotal.value * 0.22 // 22% IVA
})

const total = computed(() => {
  return subtotal.value + taxAmount.value
})

const canCheckout = computed(() => {
  return cartStore.items.length > 0 && 
         shippingData.indirizzo.trim() !== '' && 
         shippingData.citta.trim() !== '' && 
         shippingData.cap.trim() !== ''
})

// Methods
const loadCart = async () => {
  try {
    loading.value = true
    await cartStore.loadCartItems()
  } catch (error) {
    console.error('Errore nel caricamento del carrello:', error)
    showError('Errore nel caricamento del carrello')
  } finally {
    loading.value = false
  }
}

const updateQuantity = async (item) => {
  if (item.quantita < 1) {
    item.quantita = 1
    return
  }
  
  if (item.quantita > item.product.quantita) {
    item.quantita = item.product.quantita
    showError(`Quantità massima disponibile: ${item.product.quantita}`)
    return
  }
  
  try {
    updatingItem.value[item.id] = true
    
    const result = await cartStore.updateQuantity(item.product_id, item.quantita)
    
    if (!result.success) {
      showError(result.message || 'Errore nell\'aggiornamento della quantità')
    }
  } catch (error) {
    console.error('Errore aggiornamento quantità:', error)
    showError('Errore nell\'aggiornamento della quantità')
  } finally {
    updatingItem.value[item.id] = false
  }
}

const incrementQuantity = async (item) => {
  if (item.quantita < item.product.quantita) {
    item.quantita++
    await updateQuantity(item)
  } else {
    showError(`Quantità massima disponibile: ${item.product.quantita}`)
  }
}

const decrementQuantity = async (item) => {
  if (item.quantita > 1) {
    item.quantita--
    await updateQuantity(item)
  }
}

const removeItem = async (item) => {
  if (!confirm(`Sei sicuro di voler rimuovere "${item.product.nome}" dal carrello?`)) {
    return
  }
  
  try {
    removingItem.value[item.id] = true
    
    const result = await cartStore.removeFromCart(item.product_id)
    
    if (result.success) {
      showSuccess('Prodotto rimosso dal carrello')
    } else {
      showError(result.message || 'Errore nella rimozione del prodotto')
    }
  } catch (error) {
    console.error('Errore rimozione prodotto:', error)
    showError('Errore nella rimozione del prodotto')
  } finally {
    removingItem.value[item.id] = false
  }
}

const clearCart = async () => {
  if (!confirm('Sei sicuro di voler svuotare completamente il carrello?')) {
    return
  }
  
  try {
    clearingCart.value = true
    
    const result = await cartStore.clearCart()
    
    if (result.success) {
      showSuccess('Carrello svuotato')
    } else {
      showError(result.message || 'Errore nello svuotamento del carrello')
    }
  } catch (error) {
    console.error('Errore svuotamento carrello:', error)
    showError('Errore nello svuotamento del carrello')
  } finally {
    clearingCart.value = false
  }
}

const proceedToCheckout = async () => {
  if (!canCheckout.value) {
    showError('Completa tutti i campi obbligatori per procedere')
    return
  }
  
  try {
    checkingOut.value = true
    
    const result = await cartStore.checkout(shippingData)
    
    if (result.success) {
      showSuccess('Ordine completato con successo!')
      
      // Reset shipping data
      Object.keys(shippingData).forEach(key => {
        shippingData[key] = ''
      })
      
      // Redirect to success page or products after delay
      setTimeout(() => {
        router.push('/products')
      }, 2000)
    } else {
      showError(result.message || 'Errore durante il checkout')
    }
  } catch (error) {
    console.error('Errore checkout:', error)
    showError('Errore durante il checkout')
  } finally {
    checkingOut.value = false
  }
}

const getProductImage = (product) => {
  if (product?.immagine && product.immagine.startsWith('/images/')) {
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

const showSuccess = (message) => {
  successMessage.value = message
  setTimeout(() => successMessage.value = '', 3000)
}

const showError = (message) => {
  errorMessage.value = message
  setTimeout(() => errorMessage.value = '', 5000)
}

// Lifecycle
onMounted(() => {
  loadCart()
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

/* Sticky positioning */
.sticky {
  position: sticky;
}

/* Smooth transitions */
.transition-colors {
  transition-property: color, background-color, border-color;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
</style>