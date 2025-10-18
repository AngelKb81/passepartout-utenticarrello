<template>
  <div class="bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4">
      <!-- Header -->
      <div class="mb-6">
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
          <!-- Users Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
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
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
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
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
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

          <!-- Email Sent Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Email Inviate</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_emails }}</p>
              </div>
              <div class="p-3 bg-purple-100 rounded-full">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <!-- Quick Actions Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
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

          <!-- Prodotti per Categoria -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Prodotti per Categoria</h3>
            <div class="space-y-3">
              <div v-for="category in stats.top_products" :key="category.categoria" 
                   class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <span class="font-medium capitalize">{{ category.categoria }}</span>
                <span class="text-sm text-gray-600">{{ category.count }} prodotti</span>
              </div>
              <div v-if="stats.top_products.length === 0" class="text-center text-gray-500 py-4">
                Nessuna categoria disponibile
              </div>
            </div>
          </div>
        </div>

                <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Registrazioni Utenti per Mese -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Registrazioni Utenti</h3>
              <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">DEMO</span>
            </div>
            <div class="h-40">
              <canvas ref="userChart"></canvas>
            </div>
          </div>

          <!-- Vendite per Categoria -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Vendite per Categoria</h3>
              <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">DEMO</span>
            </div>
            <div class="h-40">
              <canvas ref="salesChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../../stores/auth'
import { useAuthRedirect } from '../../composables/useAuthRedirect'
import { Chart, registerables } from 'chart.js'

// Registra tutti i componenti Chart.js
Chart.register(...registerables)

// Store references
const authStore = useAuthStore()

// Setup auth redirect watchers
const { watchAdminAccess } = useAuthRedirect()
watchAdminAccess()

// Reactive state
const isLoading = ref(true)
const error = ref('')
const stats = ref({})

// Chart refs
const userChart = ref(null)
const salesChart = ref(null)
let userChartInstance = null
let salesChartInstance = null

// Methods
const loadData = async () => {
  try {
    isLoading.value = true
    
    // Carica dati reali dal backend
    const response = await axios.get('/admin/dashboard/stats')
    
    stats.value = {
      total_users: response.data.stats.total_users || 0,
      total_products: response.data.stats.total_products || 0,
      total_carts: response.data.stats.total_carts || 0,
      total_emails: response.data.stats.total_emails || 0,
      top_products: response.data.stats.products_by_category || []
    }
    
  } catch (err) {
    console.error('Errore caricamento dashboard:', err)
    error.value = 'Errore nel caricamento dei dati. Usando dati di fallback.'
    
    // Fallback con dati minimi reali
    stats.value = {
      total_users: 0,
      total_products: 0,
      total_carts: 0,
      total_emails: 0,
      top_products: []
    }
  } finally {
    isLoading.value = false
  }
}

// Chart functions
const createUserChart = () => {
  if (userChart.value) {
    userChartInstance = new Chart(userChart.value, {
      type: 'line',
      data: {
        labels: ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu'],
        datasets: [{
          label: 'Nuovi Utenti',
          data: [12, 19, 8, 15, 25, 18],
          borderColor: 'rgb(59, 130, 246)',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          title: {
            display: true,
            text: 'Dati dimostrativi - Non reali',
            font: { size: 10 },
            color: '#666',
            padding: 10
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0,0,0,0.05)'
            },
            ticks: {
              font: { size: 11 }
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              font: { size: 11 }
            }
          }
        }
      }
    })
  }
}

const createSalesChart = () => {
  if (salesChart.value) {
    salesChartInstance = new Chart(salesChart.value, {
      type: 'doughnut',
      data: {
        labels: ['Elettronica', 'Abbigliamento', 'Casa', 'Sport', 'Libri'],
        datasets: [{
          data: [35, 25, 20, 12, 8],
          backgroundColor: [
            'rgb(59, 130, 246)',
            'rgb(16, 185, 129)',
            'rgb(245, 158, 11)',
            'rgb(239, 68, 68)',
            'rgb(139, 92, 246)'
          ],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 8,
              usePointStyle: true,
              font: { size: 11 }
            }
          },
          title: {
            display: true,
            text: 'Dati dimostrativi - Non reali',
            font: { size: 10 },
            color: '#666',
            padding: 8
          }
        }
      }
    })
  }
}

// Lifecycle
onMounted(async () => {
  await loadData()
  
  // Crea i grafici dopo il caricamento dei dati
  setTimeout(() => {
    createUserChart()
    createSalesChart()
  }, 100)
})
</script>