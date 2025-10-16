<template>
  <!-- Pagina Dettaglio Prodotto -->
  <div class="product-detail-page">
    <div class="product-detail-container">
      <!-- Loading -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Caricamento prodotto...</p>
      </div>
      <!-- Prodotto non trovato -->
      <div v-else-if="!product" class="empty-state">
        <p class="empty-icon">❓</p>
        <h2>Prodotto non trovato</h2>
        <router-link to="/products" class="btn-primary">Torna al catalogo</router-link>
      </div>
      <!-- Dettaglio prodotto -->
      <div v-else class="product-detail-content">
        <!-- Immagine -->
        <div class="product-image">
          <img :src="getProductImage(product)" :alt="product.nome" @error="handleImageError" />
        </div>
        <!-- Info -->
        <div class="product-info">
          <h1 class="product-title">{{ product.nome }}</h1>
          <span class="product-category">{{ product.categoria || 'Generale' }}</span>
          <p class="product-description">{{ product.descrizione }}</p>
          <div class="product-meta">
            <span class="product-price">€{{ formatPrice(product.prezzo) }}</span>
            <span v-if="product.disponibile && product.quantita > 0" class="badge available">Disponibile</span>
            <span v-else-if="product.quantita <= 5 && product.quantita > 0" class="badge low-stock">Ultimi {{ product.quantita }}</span>
            <span v-else class="badge out-of-stock">Esaurito</span>
          </div>
          <!-- Quantità e aggiungi al carrello -->
          <div class="add-to-cart-section">
            <label for="qty">Quantità</label>
            <input id="qty" type="number" v-model.number="quantity" min="1" :max="product.quantita" :disabled="!product.disponibile || product.quantita === 0" />
            <button @click="addToCart" :disabled="!product.disponibile || product.quantita === 0 || adding" class="btn-primary">
              <span v-if="adding">Aggiunta...</span>
              <span v-else>Aggiungi al carrello</span>
            </button>
          </div>
          <router-link to="/products" class="link-back">← Torna al catalogo</router-link>
        </div>
      </div>
    </div>
    <!-- Toast -->
    <div v-if="message" class="toast-message" :class="messageType">{{ message }}</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCartStore } from '../../stores/cart'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()

const loading = ref(true)
const product = ref(null)
const quantity = ref(1)
const adding = ref(false)
const message = ref('')
const messageType = ref('success')

const getProduct = async () => {
  try {
    loading.value = true
    const id = route.params.id
    const response = await axios.get(`/api/products/${id}`)
    product.value = response.data.data || response.data.product || response.data
    quantity.value = 1
  } catch (error) {
    product.value = null
  } finally {
    loading.value = false
  }
}

const addToCart = async () => {
  if (!product.value || !product.value.disponibile || product.value.quantita === 0) return
  try {
    adding.value = true
    const result = await cartStore.addToCart(product.value.id, quantity.value)
    if (result.success) {
      showMessage('Prodotto aggiunto al carrello!', 'success')
    } else {
      showMessage(result.message || 'Errore aggiunta al carrello', 'error')
    }
  } catch (error) {
    showMessage('Errore aggiunta al carrello', 'error')
  } finally {
    adding.value = false
  }
}

const showMessage = (text, type = 'success') => {
  message.value = text
  messageType.value = type
  setTimeout(() => { message.value = '' }, 3000)
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
  return new Intl.NumberFormat('it-IT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(price)
}

onMounted(() => {
  getProduct()
})
</script>

<style scoped>
.product-detail-page {
  min-height: 100vh;
  background: #fff;
  padding: 2rem 0;
  font-family: 'Inter', Arial, sans-serif;
}
.product-detail-container {
  max-width: 900px;
  margin: 0 auto;
  padding: 0 1.5rem;
}
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
@keyframes spin { to { transform: rotate(360deg); } }
.loading-state p { margin-top: 1rem; color: #6b7280; }
.empty-state {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  padding: 4rem 2rem;
  text-align: center;
}
.empty-icon { font-size: 4rem; margin-bottom: 1rem; }
.product-detail-content {
  display: flex;
  gap: 2.5rem;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  padding: 2rem;
  margin-top: 1.5rem;
}
.product-image {
  flex: 0 0 320px;
  width: 320px;
  height: 320px;
  border-radius: 4px;
  overflow: hidden;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
}
.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.product-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}
.product-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
}
.product-category {
  display: inline-block;
  padding: 0.25rem 0.625rem;
  background: #f3f4f6;
  color: #6b7280;
  font-size: 0.85rem;
  border-radius: 4px;
  margin-bottom: 0.75rem;
  text-transform: capitalize;
}
.product-description {
  font-size: 1.1rem;
  color: #4b5563;
  line-height: 1.6;
}
.product-meta {
  display: flex;
  align-items: center;
  gap: 1rem;
}
.product-price {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2563eb;
}
.badge {
  padding: 0.375rem 0.75rem;
  border-radius: 4px;
  font-size: 0.9rem;
  font-weight: 500;
  color: #fff;
}
.badge.available { background: #10b981; }
.badge.low-stock { background: #f59e0b; }
.badge.out-of-stock { background: #ef4444; }
.add-to-cart-section {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-top: 1rem;
}
.add-to-cart-section label {
  font-size: 1rem;
  color: #4b5563;
}
.add-to-cart-section input[type="number"] {
  width: 70px;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  font-size: 1rem;
  color: #1f2937;
  background: #fff;
  margin: 0 0.5rem;
}
.add-to-cart-section input:disabled {
  background: #f3f4f6;
  color: #9ca3af;
}
.btn-primary {
  padding: 0.75rem 1.5rem;
  background: #2563eb;
  color: #fff;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s ease;
}
.btn-primary:hover:not(:disabled) { background: #1d4ed8; }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
.link-back {
  display: inline-block;
  margin-top: 1.5rem;
  color: #2563eb;
  font-size: 0.95rem;
  text-decoration: none;
  transition: color 0.2s ease;
}
.link-back:hover { color: #1d4ed8; text-decoration: underline; }
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
.toast-message.success { background: #10b981; color: #fff; }
.toast-message.error { background: #ef4444; color: #fff; }
@keyframes slideInUp {
  from { transform: translateY(100%); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
@media (max-width: 900px) {
  .product-detail-content { flex-direction: column; align-items: center; }
  .product-image { width: 100%; height: 260px; }
}
@media (max-width: 640px) {
  .product-title { font-size: 1.25rem; }
  .product-detail-content { padding: 1rem; }
  .product-image { height: 180px; }
}
</style>
