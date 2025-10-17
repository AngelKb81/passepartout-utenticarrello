<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
        <p class="text-gray-600 mt-1">Panoramica generale del sistema</p>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="flex items-center justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        <span class="ml-2 text-gray-600">Caricamento...</span>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <p class="text-red-800">{{ error }}</p>
      </div>

      <!-- Dashboard Content -->
      <div v-else>
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Users Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Utenti Totali</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_users }}</p>
              </div>
              <div class="p-3 bg-blue-100 rounded-full">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
              </div>
            </div>
          </div>

          <!-- Products Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Prodotti</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_products }}</p>
              </div>
              <div class="p-3 bg-green-100 rounded-full">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
              </div>
            </div>
          </div>

          <!-- Carts Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Carrelli</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_carts }}</p>
              </div>
              <div class="p-3 bg-yellow-100 rounded-full">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13v6a2 2 0 002 2h2"/>
                </svg>
              </div>
            </div>
          </div>

          <!-- Revenue Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Fatturato</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">€{{ stats.total_revenue }}</p>
              </div>
              <div class="p-3 bg-purple-100 rounded-full">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <!-- Quick Actions Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Azioni Rapide</h3>
            <div class="grid grid-cols-2 gap-4">
              <router-link 
                to="/admin/emails" 
                class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-indigo-100 rounded-lg">
                  <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                </div>
                <div>
                  <p class="font-medium text-gray-900">Email Logs</p>
                  <p class="text-sm text-gray-600">Gestisci email</p>
                </div>
              </router-link>

              <div class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                <div class="p-2 bg-green-100 rounded-lg">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                  </svg>
                </div>
                <div>
                  <p class="font-medium text-gray-900">Nuovo Prodotto</p>
                  <p class="text-sm text-gray-600">Aggiungi prodotto</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Top Products -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Prodotti Più Venduti</h3>
            <div class="space-y-3">
              <div v-for="product in stats.top_products" :key="product.product_id" 
                   class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <span class="font-medium">{{ product.product.nome }}</span>
                <span class="text-sm text-gray-600">{{ product.total_sold }} venduti</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Placeholder -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Analytics</h3>
          <p class="text-gray-600">I grafici e le analitiche dettagliate saranno implementati nella prossima versione.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useAuthRedirect } from '../../composables/useAuthRedirect'

// Store references
const authStore = useAuthStore()

// Setup auth redirect watchers
const { watchAdminAccess } = useAuthRedirect()
watchAdminAccess()

// Reactive state
const isLoading = ref(true)
const error = ref('')
const stats = ref({})

// Methods
const loadData = async () => {
  try {
    isLoading.value = true
    
    // Simula dati statici per ora
    await new Promise(resolve => setTimeout(resolve, 500)) // Simula caricamento
    
    stats.value = {
      total_users: 156,
      total_products: 45,
      total_carts: 89,
      total_revenue: '12,450.00',
      top_products: [
        { product_id: 1, total_sold: 25, product: { nome: 'Smartphone Samsung' } },
        { product_id: 2, total_sold: 18, product: { nome: 'Laptop Dell XPS' } },
        { product_id: 3, total_sold: 12, product: { nome: 'Cuffie Sony' } }
      ]
    }
    
    
  } catch (err) {
    console.error('Errore caricamento dashboard:', err)
    error.value = 'Errore nel caricamento dei dati'
  } finally {
    isLoading.value = false
  }
}

// Lifecycle
onMounted(() => {
  loadData()
})
</script>