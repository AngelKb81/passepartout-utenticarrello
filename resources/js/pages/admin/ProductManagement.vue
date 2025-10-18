<template>
  <div class="product-management-page">
    <div class="page-container">
      
      <!-- Header -->
      <div class="page-header">
        <h1>📦 Gestione Prodotti</h1>
        <p>Aggiungi, modifica e gestisci i prodotti del catalogo</p>
        <button @click="showCreateForm = true" class="btn-primary">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Nuovo Prodotto
        </button>
      </div>

      <!-- Statistics Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">📦</div>
          <div class="stat-info">
            <span class="stat-label">Prodotti Totali</span>
            <span class="stat-value">{{ stats.total }}</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">✅</div>
          <div class="stat-info">
            <span class="stat-label">Attivi</span>
            <span class="stat-value">{{ stats.active }}</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">📊</div>
          <div class="stat-info">
            <span class="stat-label">Categorie</span>
            <span class="stat-value">{{ stats.categories }}</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">💰</div>
          <div class="stat-info">
            <span class="stat-label">Valore Totale</span>
            <span class="stat-value">€{{ formatPrice(stats.totalValue) }}</span>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="filters-section">
        <div class="filters-grid">
          <div class="filter-group">
            <label>Cerca prodotto</label>
            <input 
              v-model="filters.search" 
              type="text" 
              placeholder="Nome, codice o descrizione..."
              class="filter-input"
              @input="debouncedSearch"
            />
          </div>
          <div class="filter-group">
            <label>Categoria</label>
            <select v-model="filters.category" class="filter-select" @change="loadProducts">
              <option value="">Tutte le categorie</option>
              <option v-for="category in categories" :key="category" :value="category">
                {{ category }}
              </option>
            </select>
          </div>
          <div class="filter-group">
            <label>Stato</label>
            <select v-model="filters.status" class="filter-select" @change="loadProducts">
              <option value="">Tutti</option>
              <option value="active">Attivi</option>
              <option value="inactive">Inattivi</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Products Table -->
      <div class="products-section">
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Caricamento prodotti...</p>
        </div>

        <div v-else-if="products.length === 0" class="empty-state">
          <p class="empty-icon">📦</p>
          <h3>Nessun prodotto trovato</h3>
          <p>Non ci sono prodotti che corrispondono ai filtri selezionati</p>
          <button @click="showCreateForm = true" class="btn-primary">Aggiungi Primo Prodotto</button>
        </div>

        <div v-else class="products-table-container">
          <table class="products-table">
            <thead>
              <tr>
                <th>Immagine</th>
                <th>Nome</th>
                <th>Codice</th>
                <th>Categoria</th>
                <th>Prezzo</th>
                <th>Scorte</th>
                <th>Stato</th>
                <th>Azioni</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in products" :key="product.id" class="product-row">
                <td class="product-image-cell">
                  <img 
                    :src="getProductImage(product)" 
                    :alt="product.nome" 
                    class="product-thumb"
                    @error="handleImageError"
                  />
                </td>
                <td class="product-name-cell">
                  <div class="product-name">{{ product.nome }}</div>
                  <div class="product-description">{{ truncateDescription(product.descrizione) }}</div>
                </td>
                <td class="product-code">{{ product.codice }}</td>
                <td class="product-category">{{ product.categoria }}</td>
                <td class="product-price">€{{ formatPrice(product.prezzo) }}</td>
                <td class="product-stock">
                  <span :class="getStockClass(product.scorte)">{{ product.scorte }}</span>
                </td>
                <td class="product-status">
                  <span :class="getStatusClass(product.attivo)">
                    {{ product.attivo ? 'Attivo' : 'Inattivo' }}
                  </span>
                </td>
                <td class="product-actions">
                  <button @click="editProduct(product)" class="btn-edit" title="Modifica">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </button>
                  <button @click="toggleProductStatus(product)" class="btn-toggle" :title="product.attivo ? 'Disattiva' : 'Attiva'">
                    <svg v-if="product.attivo" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029"/>
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                  </button>
                  <button @click="deleteProduct(product)" class="btn-delete" title="Elimina">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.totalPages > 1" class="pagination">
          <button 
            @click="goToPage(pagination.currentPage - 1)" 
            :disabled="pagination.currentPage === 1"
            class="pagination-btn"
          >
            Precedente
          </button>
          <span class="pagination-info">
            Pagina {{ pagination.currentPage }} di {{ pagination.totalPages }}
          </span>
          <button 
            @click="goToPage(pagination.currentPage + 1)" 
            :disabled="pagination.currentPage === pagination.totalPages"
            class="pagination-btn"
          >
            Successiva
          </button>
        </div>
      </div>

      <!-- Product Form Modal -->
      <div v-if="showCreateForm || editingProduct" class="modal-overlay" @click="closeForm">
        <div class="modal-content" @click.stop>
          <div class="modal-header">
            <h2>{{ editingProduct ? 'Modifica Prodotto' : 'Nuovo Prodotto' }}</h2>
            <button @click="closeForm" class="modal-close">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <form @submit.prevent="saveProduct" class="product-form">
            <div class="form-grid">
              <div class="form-group">
                <label for="nome">Nome Prodotto *</label>
                <input 
                  v-model="productForm.nome" 
                  type="text" 
                  id="nome" 
                  required
                  placeholder="Es. Smartphone Samsung Galaxy S24"
                  class="form-input"
                />
              </div>
              
              <div class="form-group">
                <label for="codice">Codice Prodotto *</label>
                <input 
                  v-model="productForm.codice" 
                  type="text" 
                  id="codice" 
                  required
                  placeholder="Es. SAMSUNG-S24-001"
                  class="form-input"
                />
              </div>
              
              <div class="form-group">
                <label for="categoria">Categoria *</label>
                <input 
                  v-model="productForm.categoria" 
                  type="text" 
                  id="categoria" 
                  required
                  placeholder="Es. Smartphone"
                  class="form-input"
                />
              </div>
              
              <div class="form-group">
                <label for="prezzo">Prezzo (€) *</label>
                <input 
                  v-model="productForm.prezzo" 
                  type="number" 
                  id="prezzo" 
                  step="0.01" 
                  min="0" 
                  required
                  placeholder="0.00"
                  class="form-input"
                />
              </div>
              
              <div class="form-group">
                <label for="scorte">Scorte *</label>
                <input 
                  v-model="productForm.scorte" 
                  type="number" 
                  id="scorte" 
                  min="0" 
                  required
                  placeholder="0"
                  class="form-input"
                />
              </div>
              
              <div class="form-group">
                <label for="immagine">URL Immagine</label>
                <input 
                  v-model="productForm.immagine" 
                  type="url" 
                  id="immagine" 
                  placeholder="https://example.com/image.jpg"
                  class="form-input"
                />
              </div>
            </div>
            
            <div class="form-group full-width">
              <label for="descrizione">Descrizione *</label>
              <textarea 
                v-model="productForm.descrizione" 
                id="descrizione" 
                rows="4" 
                required
                placeholder="Descrizione dettagliata del prodotto..."
                class="form-textarea"
              ></textarea>
            </div>
            
            <div class="form-group">
              <label class="checkbox-label">
                <input 
                  v-model="productForm.attivo" 
                  type="checkbox" 
                  class="form-checkbox"
                />
                Prodotto attivo
              </label>
            </div>

            <div class="form-actions">
              <button type="button" @click="closeForm" class="btn-secondary">Annulla</button>
              <button type="submit" :disabled="saving" class="btn-primary">
                <span v-if="saving">Salvataggio...</span>
                <span v-else>{{ editingProduct ? 'Aggiorna' : 'Crea' }} Prodotto</span>
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useAuthRedirect } from '../../composables/useAuthRedirect'
import axios from 'axios'

// Setup auth redirect watchers
const { watchAdminAccess } = useAuthRedirect()
watchAdminAccess()

// Reactive state
const loading = ref(false)
const saving = ref(false)
const products = ref([])
const categories = ref([])
const stats = ref({ total: 0, active: 0, categories: 0, totalValue: 0 })
const showCreateForm = ref(false)
const editingProduct = ref(null)

// Filters and pagination
const filters = reactive({
  search: '',
  category: '',
  status: ''
})

const pagination = reactive({
  currentPage: 1,
  totalPages: 1,
  perPage: 10
})

// Product form
const productForm = reactive({
  nome: '',
  codice: '',
  categoria: '',
  descrizione: '',
  prezzo: '',
  immagine: '',
  scorte: 0,
  attivo: true
})

// Methods
const loadProducts = async () => {
  try {
    loading.value = true
    const params = {
      page: pagination.currentPage,
      per_page: pagination.perPage,
      search: filters.search,
      category: filters.category,
      status: filters.status
    }
    
    const response = await axios.get('/admin/products', { params })
    products.value = response.data.products?.data || []
    
    if (response.data.products?.meta) {
      pagination.currentPage = response.data.products.meta.current_page
      pagination.totalPages = response.data.products.meta.last_page
    }
    
    if (response.data.stats) {
      stats.value = response.data.stats
    }
    
    if (response.data.categories) {
      categories.value = response.data.categories
    }
    
  } catch (error) {
    console.error('Errore caricamento prodotti:', error)
  } finally {
    loading.value = false
  }
}

const debouncedSearch = debounce(() => {
  pagination.currentPage = 1
  loadProducts()
}, 300)

const editProduct = (product) => {
  editingProduct.value = product
  Object.assign(productForm, {
    nome: product.nome,
    codice: product.codice,
    categoria: product.categoria,
    descrizione: product.descrizione,
    prezzo: product.prezzo,
    immagine: product.immagine,
    scorte: product.scorte,
    attivo: product.attivo
  })
}

const saveProduct = async () => {
  try {
    saving.value = true
    
    if (editingProduct.value) {
      await axios.put(`/admin/products/${editingProduct.value.id}`, productForm)
    } else {
      await axios.post('/admin/products', productForm)
    }
    
    closeForm()
    loadProducts()
    
  } catch (error) {
    console.error('Errore salvataggio prodotto:', error)
    alert('Errore nel salvataggio del prodotto')
  } finally {
    saving.value = false
  }
}

const toggleProductStatus = async (product) => {
  try {
    await axios.patch(`/admin/products/${product.id}/toggle-status`)
    product.attivo = !product.attivo
  } catch (error) {
    console.error('Errore toggle status:', error)
  }
}

const deleteProduct = async (product) => {
  if (!confirm(`Sei sicuro di voler eliminare "${product.nome}"?`)) return
  
  try {
    await axios.delete(`/admin/products/${product.id}`)
    loadProducts()
  } catch (error) {
    console.error('Errore eliminazione prodotto:', error)
    alert('Errore nell\'eliminazione del prodotto')
  }
}

const closeForm = () => {
  showCreateForm.value = false
  editingProduct.value = null
  Object.assign(productForm, {
    nome: '',
    codice: '',
    categoria: '',
    descrizione: '',
    prezzo: '',
    immagine: '',
    scorte: 0,
    attivo: true
  })
}

const goToPage = (page) => {
  pagination.currentPage = page
  loadProducts()
}

// Utility functions
const formatPrice = (price) => {
  return parseFloat(price).toFixed(2)
}

const getProductImage = (product) => {
  if (!product.immagine) return '/images/placeholder-product.jpg'
  if (product.immagine.startsWith('http')) return product.immagine
  return `/storage/${product.immagine}`
}

const handleImageError = (event) => {
  event.target.src = '/images/placeholder-product.jpg'
}

const truncateDescription = (text, maxLength = 100) => {
  if (!text) return ''
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text
}

const getStockClass = (stock) => {
  if (stock === 0) return 'stock-empty'
  if (stock < 10) return 'stock-low'
  return 'stock-good'
}

const getStatusClass = (active) => {
  return active ? 'status-active' : 'status-inactive'
}

// Debounce utility
function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Lifecycle
onMounted(() => {
  loadProducts()
})
</script>

<style scoped>
/* Main layout */
.product-management-page {
  min-height: 100vh;
  background-color: #f9fafb;
  padding: 2rem 0;
}

.page-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1rem;
}

/* Header */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.page-header h1 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.page-header p {
  color: #6b7280;
  margin: 0.5rem 0 0 0;
}

/* Stats */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-icon {
  font-size: 2rem;
  width: 3rem;
  height: 3rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f3f4f6;
  border-radius: 8px;
}

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 0.25rem;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
}

/* Filters */
.filters-section {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.filters-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.filter-group {
  display: flex;
  flex-direction: column;
}

.filter-group label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.filter-input,
.filter-select {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
}

.filter-input:focus,
.filter-select:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Products section */
.products-section {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.products-table-container {
  overflow-x: auto;
}

.products-table {
  width: 100%;
  border-collapse: collapse;
}

.products-table th {
  background: #f9fafb;
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.products-table td {
  padding: 1rem;
  border-bottom: 1px solid #f3f4f6;
  vertical-align: middle;
}

.product-row:hover {
  background: #f9fafb;
}

.product-image-cell {
  width: 80px;
}

.product-thumb {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 6px;
}

.product-name-cell {
  min-width: 200px;
}

.product-name {
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.product-description {
  font-size: 0.875rem;
  color: #6b7280;
}

.product-code {
  font-family: 'Courier New', monospace;
  font-size: 0.875rem;
  color: #4b5563;
}

.product-price {
  font-weight: 600;
  color: #059669;
}

.stock-good { color: #059669; }
.stock-low { color: #d97706; }
.stock-empty { color: #dc2626; }

.status-active {
  color: #059669;
  background: #d1fae5;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 500;
}

.status-inactive {
  color: #dc2626;
  background: #fee2e2;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 500;
}

.product-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-edit,
.btn-toggle,
.btn-delete {
  padding: 0.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-edit {
  background: #eff6ff;
  color: #2563eb;
}

.btn-edit:hover {
  background: #dbeafe;
}

.btn-toggle {
  background: #f0fdf4;
  color: #16a34a;
}

.btn-toggle:hover {
  background: #dcfce7;
}

.btn-delete {
  background: #fef2f2;
  color: #dc2626;
}

.btn-delete:hover {
  background: #fee2e2;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 800px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
}

.modal-close {
  background: none;
  border: none;
  cursor: pointer;
  color: #6b7280;
  padding: 0.5rem;
}

.modal-close:hover {
  color: #374151;
}

/* Form */
.product-form {
  padding: 1.5rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
  margin-bottom: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-input,
.form-textarea {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.form-checkbox {
  width: 1rem;
  height: 1rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

/* Buttons */
.btn-primary,
.btn-secondary {
  display: flex;
  align-items: center;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background: #2563eb;
  color: white;
}

.btn-primary:hover {
  background: #1d4ed8;
}

.btn-primary:disabled {
  background: #9ca3af;
  cursor: not-allowed;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
  border: 1px solid #d1d5db;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.pagination-btn {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 6px;
  cursor: pointer;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-btn:hover:not(:disabled) {
  background: #f3f4f6;
}

.pagination-info {
  font-size: 0.875rem;
  color: #6b7280;
}

/* Loading and empty states */
.loading-state,
.empty-state {
  text-align: center;
  padding: 3rem;
}

.spinner {
  display: inline-block;
  width: 2rem;
  height: 2rem;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #2563eb;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.empty-state h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.empty-state p {
  color: #6b7280;
  margin-bottom: 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .filters-grid {
    grid-template-columns: 1fr;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .modal-content {
    width: 95%;
    margin: 1rem;
  }

  .form-actions {
    flex-direction: column;
  }

  .pagination {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>