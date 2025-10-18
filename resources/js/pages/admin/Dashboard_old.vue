<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <h1 class="text-3xl font-bold text-gray-900">Dashboard Amministrativa</h1>
          <p class="mt-2 text-gray-600">Panoramica statistiche e gestione del sistema</p>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <p class="text-red-800">{{ error }}</p>
        <button @click="loadData" class="mt-2 text-red-600 hover:text-red-800 underline">
          Riprova
        </button>
      </div>
    </div>

    <!-- Content -->
    <div v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Utenti Totali -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Utenti Totali</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.total_users }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Prodotti -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Prodotti</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.total_products }}</dd>
                  <dd class="text-sm text-green-600">{{ stats.active_products }} attivi</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Carrelli -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13v6a2 2 0 002 2h2M7 13H5.4M19 13v6a2 2 0 01-2 2h-2m-4-6a2 2 0 100 4 2 2 0 000-4zm-4 2a2 2 0 100 4 2 2 0 000-4z"></path>
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Carrelli</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.total_carts }}</dd>
                  <dd class="text-sm text-purple-600">{{ stats.avg_cart_items }} items/carrello</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Media prodotti -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Media Carrello</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.avg_cart_items }}</dd>
                  <dd class="text-sm text-yellow-600">prodotti per carrello</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="mb-8">
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Azioni Rapide</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                <p class="text-sm text-gray-600">Gestisci invio email</p>
              </div>
            </router-link>

            <router-link 
              to="/admin/products" 
              class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
              <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
              </div>
              <div>
                <p class="font-medium text-gray-900">Gestione Prodotti</p>
                <p class="text-sm text-gray-600">Amministra catalogo</p>
              </div>
            </router-link>

            <div class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors cursor-not-allowed opacity-50">
              <div class="p-2 bg-yellow-100 rounded-lg">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
              </div>
              <div>
                <p class="font-medium text-gray-900">Gestisci Utenti</p>
                <p class="text-sm text-gray-600">In sviluppo</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        <!-- Prodotti per Categoria -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Prodotti per Categoria</h3>
          <div v-if="stats.products_by_category && stats.products_by_category.length > 0" class="space-y-4">
            <div 
              v-for="category in stats.products_by_category" 
              :key="category.categoria"
              class="flex items-center justify-between"
            >
              <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                <span class="text-sm font-medium text-gray-700">{{ category.categoria }}</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-20 bg-gray-200 rounded-full h-2">
                  <div 
                    class="bg-blue-600 h-2 rounded-full" 
                    :style="`width: ${(category.count / stats.total_products) * 100}%`"
                  ></div>
                </div>
                <span class="text-sm text-gray-500 w-8 text-right">{{ category.count }}</span>
              </div>
            </div>
          </div>
          <div v-else class="text-gray-500 text-center py-8">
            Nessun dato disponibile
          </div>
        </div>

        <!-- Prodotti più nei Carrelli -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Prodotti più Aggiunti ai Carrelli</h3>
          <div v-if="stats.top_cart_products && stats.top_cart_products.length > 0" class="space-y-4">
            <div 
              v-for="(item, index) in stats.top_cart_products.slice(0, 5)" 
              :key="item.product_id"
              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
            >
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                  <span class="text-sm font-bold text-green-600">{{ index + 1 }}</span>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    {{ item.product?.nome || 'Prodotto N/A' }}
                  </p>
                  <p class="text-xs text-gray-500">{{ item.product?.categoria }}</p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm font-bold text-gray-900">{{ item.count }}</p>
                <p class="text-xs text-gray-500">aggiunte</p>
              </div>
            </div>
          </div>
          <div v-else class="text-gray-500 text-center py-8">
            Nessun dato disponibile
          </div>
        </div>

      </div>

      <!-- Registrazioni Utenti -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Registrazioni Utenti (Ultimi 6 Mesi)</h3>
        <div v-if="stats.user_registrations && stats.user_registrations.length > 0" class="mt-4">
          <div class="flex items-end justify-between h-40 space-x-2">
            <div 
              v-for="reg in stats.user_registrations" 
              :key="reg.month"
              class="flex flex-col items-center flex-1"
            >
              <div 
                class="w-full bg-blue-500 rounded-t-sm min-h-[4px]"
                :style="`height: ${(reg.count / Math.max(...stats.user_registrations.map(r => r.count))) * 120}px`"
              ></div>
              <div class="mt-2 text-xs text-gray-600 text-center">
                <div>{{ formatMonth(reg.month) }}</div>
                <div class="font-semibold">{{ reg.count }}</div>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-gray-500 text-center py-8">
          Nessun dato disponibile
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useAdminStats } from '../../composables/useAdminStats'

export default {
  name: 'AdminDashboard',
  setup() {
    const authStore = useAuthStore()
    const { loading, error, stats, loadGeneralStats, loadMockData } = useAdminStats()

    const formatMonth = (monthStr) => {
      const [year, month] = monthStr.split('-')
      const date = new Date(year, month - 1)
      return date.toLocaleDateString('it-IT', { month: 'short', year: '2-digit' })
    }

    const loadData = async () => {
      try {
        // Prova prima a caricare i dati reali dall'API
        await loadGeneralStats()
      } catch (err) {
        console.log('API non disponibile, carico dati mock')
        // Se l'API non è disponibile, carica i dati mock
        loadMockData()
      }
    }

    onMounted(() => {
      loadData()
    })

    return {
      loading,
      error,
      stats,
      loadData,
      formatMonth
    }
  }
}
</script>