<template>
  <!-- Pagina Carrello: riepilogo acquisti -->
  <div class="cart-page">
    <div class="cart-container">
      
      <!-- Header -->
      <header class="cart-header">
        <h1>Il Mio Carrello</h1>
        <p>Rivedi i tuoi prodotti prima del checkout</p>
      </header>

      <!-- Loading state -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Caricamento carrello...</p>
      </div>

      <!-- Carrello vuoto -->
      <div v-else-if="cartStore.isEmpty || items.length === 0" class="empty-cart">
        <p class="empty-icon">🛒</p>
        <h2>Il tuo carrello è vuoto</h2>
        <p>Non hai ancora aggiunto prodotti</p>
        <router-link to="/products" class="btn-primary">Vai ai Prodotti</router-link>
      </div>

      <!-- Contenuto carrello -->
      <div v-else class="cart-content">
        
        <!-- Lista prodotti -->
        <section class="cart-items">
          <article
            v-for="item in items"
            :key="item.id"
            class="cart-item"
          >
            <!-- Immagine prodotto -->
            <div class="item-image">
              <img
                :src="getProductImage(item.product)"
                :alt="item.product.nome"
                @error="handleImageError"
              />
            </div>

            <!-- Info prodotto completa -->
            <div class="item-info">
              <h3 class="item-title">{{ item.product.nome }}</h3>
              <p class="item-code">Codice: <strong>{{ item.product.codice }}</strong></p>
              <p class="item-category">Categoria: {{ item.product.categoria }}</p>
              <div class="item-description">
                <p>{{ item.product.descrizione }}</p>
              </div>
              <div class="item-price-info">
                <span class="price-label">Prezzo unitario:</span>
                <span class="item-price">€{{ formatPrice(item.product.prezzo) }}</span>
              </div>
            </div>

            <!-- Controlli quantità -->
            <div class="item-controls">
              <div class="quantity-controls">
                <button
                  @click="decreaseQuantity(item)"
                  :disabled="updatingItem[item.id]"
                  class="qty-btn"
                >
                  -
                </button>
                <span class="qty-display">{{ item.quantita }}</span>
                <button
                  @click="increaseQuantity(item)"
                  :disabled="updatingItem[item.id] || item.quantita >= item.product.quantita"
                  class="qty-btn"
                >
                  +
                </button>
              </div>
              
              <!-- Subtotale -->
              <div class="item-subtotal">
                <span class="label">Subtotale:</span>
                <span class="value">€{{ formatPrice(item.product.prezzo * item.quantita) }}</span>
              </div>

              <!-- Pulsante rimuovi -->
              <button
                @click="removeItem(item)"
                :disabled="updatingItem[item.id]"
                class="btn-remove"
              >
                Rimuovi
              </button>
            </div>
          </article>
        </section>

        <!-- Riepilogo ordine -->
        <aside class="cart-summary">
          <h2>Riepilogo Ordine</h2>
          
          <div class="summary-row">
            <span>Subtotale</span>
            <span>€{{ formatPrice(subtotal) }}</span>
          </div>
          
          <div class="summary-row">
            <span>Spedizione</span>
            <span>{{ shippingCost > 0 ? '€' + formatPrice(shippingCost) : 'Gratis' }}</span>
          </div>
          
          <div class="summary-row total">
            <span>Totale</span>
            <span>€{{ formatPrice(total) }}</span>
          </div>

          <p class="free-shipping-info" v-if="subtotal < 50">
            Aggiungi €{{ formatPrice(50 - subtotal) }} per la spedizione gratuita
          </p>

          <!-- Pulsante checkout -->
          <button
            @click="proceedToCheckout"
            :disabled="processingCheckout"
            class="btn-checkout"
          >
            <span v-if="processingCheckout">Elaborazione...</span>
            <span v-else>Procedi al Checkout</span>
          </button>

          <router-link to="/products" class="link-continue">
            Continua shopping
          </router-link>
        </aside>

      </div>

    </div>

    <!-- Toast messaggi -->
    <div v-if="message" class="toast-message" :class="messageType">
      {{ message }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '../../stores/cart'
import { useAuthStore } from '../../stores/auth'

// Router e stores
const router = useRouter()
const cartStore = useCartStore()
const authStore = useAuthStore()

// State
const loading = ref(false)
const processingCheckout = ref(false)
const updatingItem = ref({})
const message = ref('')
const messageType = ref('success')

// Computed: items dal carrello
const items = computed(() => cartStore.items || [])

// Computed: subtotale
const subtotal = computed(() => {
  return items.value.reduce((sum, item) => {
    // Converte il prezzo a numero e gestisce valori non validi
    const prezzo = parseFloat(item.product.prezzo)
    const quantita = parseInt(item.quantita)
    
    if (isNaN(prezzo) || isNaN(quantita)) {
      console.warn('Valori non validi per item:', item)
      return sum
    }
    
    return sum + (prezzo * quantita)
  }, 0)
})

// Computed: costo spedizione (gratis sopra 50€)
const shippingCost = computed(() => {
  return subtotal.value >= 50 ? 0 : 5.99
})

// Computed: totale
const total = computed(() => {
  return subtotal.value + shippingCost.value
})

// Carica carrello
const loadCart = async () => {
  try {
    loading.value = true
    await cartStore.loadCartItems()
  } catch (error) {
    console.error('Errore caricamento carrello:', error)
    showMessage('Errore caricamento carrello', 'error')
  } finally {
    loading.value = false
  }
}

// Aumenta quantità
const increaseQuantity = async (item) => {
  if (item.quantita >= item.product.quantita) {
    showMessage('Quantità massima disponibile raggiunta', 'error')
    return
  }
  
  await updateQuantity(item, item.quantita + 1)
}

// Diminuisci quantità
const decreaseQuantity = async (item) => {
  if (item.quantita <= 1) {
    await removeItem(item)
    return
  }
  
  await updateQuantity(item, item.quantita - 1)
}

// Aggiorna quantità
const updateQuantity = async (item, newQuantity) => {
  try {
    updatingItem.value[item.id] = true
    
    const result = await cartStore.updateQuantity(item.product_id || item.product.id, newQuantity)
    
    if (result.success) {
      showMessage('Quantità aggiornata', 'success')
    } else {
      showMessage(result.message || 'Errore aggiornamento', 'error')
    }
  } catch (error) {
    console.error('Errore aggiornamento quantità:', error)
    showMessage('Errore aggiornamento quantità', 'error')
  } finally {
    updatingItem.value[item.id] = false
  }
}

// Rimuovi prodotto
const removeItem = async (item) => {
  if (!confirm(`Rimuovere ${item.product.nome} dal carrello?`)) {
    return
  }
  
  try {
    updatingItem.value[item.id] = true
    
    const result = await cartStore.removeFromCart(item.product_id || item.product.id)
    
    if (result.success) {
      showMessage('Prodotto rimosso dal carrello', 'success')
    } else {
      showMessage(result.message || 'Errore rimozione', 'error')
    }
  } catch (error) {
    console.error('Errore rimozione prodotto:', error)
    showMessage('Errore rimozione prodotto', 'error')
  } finally {
    updatingItem.value[item.id] = false
  }
}

// Procedi al checkout
const proceedToCheckout = async () => {
  if (!authStore.isAuthenticated) {
    router.push('/login')
    return
  }
  
  // Simula processing per UX
  processingCheckout.value = true
  
  // Attendi un po' per mostrare il loading
  await new Promise(resolve => setTimeout(resolve, 800))
  
  processingCheckout.value = false
  
  // Mostra popup informativo
  const userConfirm = confirm(
    `🛒 DEMO E-COMMERCE - CHECKOUT SIMULATO\n\n` +
    `✅ Il carrello funziona perfettamente!\n` +
    `✅ Prodotti: ${items.value.length} articoli\n` +
    `✅ Totale: €${formatPrice(total.value)}\n\n` +
    `ℹ️ Questo è un sito di test/demo.\n` +
    `💡 La funzionalità di checkout completa sarà implementata in futuro.\n\n` +
    `Vuoi svuotare il carrello per testare di nuovo?`
  )
  
  if (userConfirm) {
    // Se l'utente conferma, svuota il carrello
    try {
      console.log('🗑️ Inizio processo svuotamento carrello...')
      
      // Forza svuotamento completo
      await cartStore.clearCart()
      
      // Forza ricaricamento per essere sicuri
      console.log('🔄 Ricarico carrello dopo clear...')
      await cartStore.loadCartItems()
      
      showMessage('🛒 Carrello svuotato! Grazie per aver testato il sistema.', 'success')
      setTimeout(() => {
        router.push('/products')
      }, 2000)
    } catch (error) {
      console.error('❌ Errore svuotamento:', error)
      showMessage('Errore nello svuotamento del carrello', 'error')
    }
  } else {
    showMessage('💡 Puoi continuare a testare le funzionalità del carrello!', 'info')
  }
}

// Mostra messaggio toast
const showMessage = (text, type = 'success') => {
  message.value = text
  messageType.value = type
  setTimeout(() => {
    message.value = ''
  }, 3000)
}

// Ottieni immagine prodotto
const getProductImage = (product) => {
  if (!product || !product.immagine) {
    return '/images/products/default-product.jpg'
  }
  
  // Se è già un URL completo, usalo così com'è
  if (product.immagine.startsWith('http')) {
    return product.immagine
  }
  
  // Se inizia con /, è già un percorso assoluto
  if (product.immagine.startsWith('/')) {
    return product.immagine
  }
  
  // Se le immagini sono in public/images/products
  if (product.immagine.startsWith('products/')) {
    return `/images/${product.immagine}`
  }
  
  // Altrimenti, è un percorso relativo da storage
  return `/storage/${product.immagine}`
}

// Gestisci errore immagine
const handleImageError = (event) => {
  event.target.src = '/images/products/default-product.jpg'
}

// Formatta prezzo
const formatPrice = (price) => {
  // Converte a numero e gestisce valori non validi
  const numPrice = parseFloat(price)
  if (isNaN(numPrice)) {
    console.warn('Prezzo non valido:', price)
    return '0.00'
  }
  
  return new Intl.NumberFormat('it-IT', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(numPrice)
}

// Carica carrello al mount
onMounted(() => {
  if (authStore.isAuthenticated) {
    loadCart()
  } else {
    router.push('/login')
  }
})
</script>

<style scoped>
/* Pagina carrello */
.cart-page {
  min-height: 100vh;
  background: #ffffff;
  padding: 2rem 0;
  font-family: 'Inter', Arial, sans-serif;
}

.cart-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

/* Header */
.cart-header {
  margin-bottom: 2.5rem;
  text-align: center;
}

.cart-header h1 {
  font-size: 2.5rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.cart-header p {
  font-size: 1.125rem;
  color: #6b7280;
}

/* Loading */
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
}

/* Carrello vuoto */
.empty-cart {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  padding: 4rem 2rem;
  text-align: center;
}

.empty-icon {
  font-size: 5rem;
  margin-bottom: 1.5rem;
}

.empty-cart h2 {
  font-size: 1.75rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.75rem;
}

.empty-cart p {
  font-size: 1.125rem;
  color: #6b7280;
  margin-bottom: 2rem;
}

/* Contenuto carrello */
.cart-content {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 2rem;
}

/* Lista prodotti */
.cart-items {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

/* Singolo item */
.cart-item {
  display: grid;
  grid-template-columns: 140px 1fr 280px;
  gap: 2rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 2rem;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.cart-item:hover {
  border-color: #2563eb;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
}

/* Immagine item */
.item-image {
  width: 140px;
  height: 140px;
  border-radius: 8px;
  overflow: hidden;
  background: #f3f4f6;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Info item */
.item-info {
  display: flex;
  flex-direction: column;
  justify-content: center;
  flex: 1;
  gap: 0.5rem;
}

.item-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.item-code {
  font-size: 0.875rem;
  color: #4b5563;
  font-family: 'Courier New', monospace;
}

.item-category {
  font-size: 0.875rem;
  color: #6b7280;
  text-transform: capitalize;
}

.item-description {
  margin: 0.5rem 0;
  max-width: 400px;
}

.item-description p {
  font-size: 0.875rem;
  color: #374151;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.item-price-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.price-label {
  font-size: 0.875rem;
  color: #6b7280;
}

.item-price {
  font-size: 1.25rem;
  font-weight: 700;
  color: #2563eb;
}

/* Controlli item */
.item-controls {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  align-items: flex-end;
}

/* Controlli quantità */
.quantity-controls {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  padding: 0.25rem;
}

.qty-btn {
  width: 32px;
  height: 32px;
  border: none;
  background: #f3f4f6;
  color: #4b5563;
  font-size: 1.25rem;
  font-weight: 600;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.2s ease;
}

.qty-btn:hover:not(:disabled) {
  background: #e5e7eb;
}

.qty-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.qty-display {
  min-width: 40px;
  text-align: center;
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
}

/* Subtotale item */
.item-subtotal {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 0.25rem;
}

.item-subtotal .label {
  font-size: 0.875rem;
  color: #6b7280;
}

.item-subtotal .value {
  font-size: 1.125rem;
  font-weight: 700;
  color: #1f2937;
}

/* Pulsante rimuovi */
.btn-remove {
  padding: 0.5rem 1rem;
  background: #ffffff;
  color: #ef4444;
  border: 1px solid #ef4444;
  border-radius: 4px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-remove:hover:not(:disabled) {
  background: #fef2f2;
}

.btn-remove:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Riepilogo ordine */
.cart-summary {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  padding: 2rem;
  height: fit-content;
  position: sticky;
  top: 2rem;
}

.cart-summary h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1.5rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem 0;
  font-size: 1rem;
  color: #4b5563;
  border-bottom: 1px solid #e5e7eb;
}

.summary-row.total {
  padding-top: 1rem;
  margin-top: 0.5rem;
  font-size: 1.25rem;
  font-weight: 700;
  color: #1f2937;
  border-bottom: none;
}

.free-shipping-info {
  margin-top: 1rem;
  padding: 0.75rem;
  background: #eff6ff;
  color: #1e40af;
  border-radius: 4px;
  font-size: 0.875rem;
  text-align: center;
}

/* Pulsante checkout */
.btn-checkout {
  width: 100%;
  padding: 1rem;
  margin-top: 1.5rem;
  background: #2563eb;
  color: #ffffff;
  border: none;
  border-radius: 4px;
  font-size: 1.125rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s ease;
}

.btn-checkout:hover:not(:disabled) {
  background: #1d4ed8;
}

.btn-checkout:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Link continua shopping */
.link-continue {
  display: block;
  text-align: center;
  margin-top: 1rem;
  color: #2563eb;
  font-size: 0.95rem;
  text-decoration: none;
  transition: color 0.2s ease;
}

.link-continue:hover {
  color: #1d4ed8;
  text-decoration: underline;
}

/* Pulsante primario (carrello vuoto) */
.btn-primary {
  display: inline-block;
  padding: 0.875rem 2rem;
  background: #2563eb;
  color: #ffffff;
  border-radius: 4px;
  font-size: 1.125rem;
  font-weight: 500;
  text-decoration: none;
  transition: background 0.2s ease;
}

.btn-primary:hover {
  background: #1d4ed8;
}

/* Toast message */
.toast-message {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  padding: 1rem 1.5rem;
  border-radius: 4px;
  font-weight: 500;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  animation: slideInUp 0.3s ease;
}

.toast-message.success {
  background: #10b981;
  color: #ffffff;
}

.toast-message.error {
  background: #ef4444;
  color: #ffffff;
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
  .cart-content {
    grid-template-columns: 1fr;
  }

  .cart-summary {
    position: static;
  }

  .cart-item {
    grid-template-columns: 100px 1fr;
    gap: 1rem;
  }

  .item-controls {
    grid-column: 1 / -1;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
}

/* Responsive: mobile */
@media (max-width: 640px) {
  .cart-header h1 {
    font-size: 2rem;
  }

  .cart-item {
    grid-template-columns: 80px 1fr;
    padding: 1rem;
  }

  .item-image {
    width: 80px;
    height: 80px;
  }

  .item-title {
    font-size: 1rem;
  }

  .cart-summary {
    padding: 1.5rem;
  }
}
</style>
