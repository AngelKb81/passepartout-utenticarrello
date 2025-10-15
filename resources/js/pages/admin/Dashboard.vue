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

    <!-- Content -->
    <div v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Prodotti</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.total_products }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13v6a2 2 0 002 2h2M7 13H5.4M19 13v6a2 2 0 01-2 2h-2m-4-6a2 2 0 100 4 2 2 0 000-4zm-4 2a2 2 0 100 4 2 2 0 000-4z"></path>
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Carrelli</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.total_carts }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Fatturato</dt>
                  <dd class="text-lg font-medium text-gray-900">€{{ stats.total_revenue }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- User Gender Distribution -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Distribuzione per Sesso</h3>
          <canvas ref="genderChart" width="400" height="200"></canvas>
        </div>

        <!-- Education Distribution -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Titoli di Studio</h3>
          <canvas ref="educationChart" width="400" height="200"></canvas>
        </div>

        <!-- Monthly Registrations -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Registrazioni Mensili</h3>
          <canvas ref="registrationsChart" width="400" height="200"></canvas>
        </div>

        <!-- Top Cities -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Città di Nascita</h3>
          <canvas ref="regionsChart" width="400" height="200"></canvas>
        </div>

        <!-- Categories Distribution -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Prodotti per Categoria</h3>
          <canvas ref="categoriesChart" width="400" height="200"></canvas>
        </div>

        <!-- Top Products -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Prodotti Più Venduti</h3>
          <div class="space-y-3">
            <div v-for="product in stats.top_products" :key="product.product_id" 
                 class="flex justify-between items-center p-3 bg-gray-50 rounded">
              <span class="font-medium">{{ product.product.nome }}</span>
              <span class="text-sm text-gray-600">{{ product.total_sold }} venduti</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { Chart, registerables } from 'chart.js'
import { useAuthStore } from '../stores/auth'
import axios from 'axios'

Chart.register(...registerables)

export default {
  name: 'AdminDashboard',
  setup() {
    const authStore = useAuthStore()
    const loading = ref(true)
    const stats = ref({})
    const userStats = ref({})
    const salesStats = ref({})

    const genderChart = ref(null)
    const educationChart = ref(null)
    const registrationsChart = ref(null)
    const regionsChart = ref(null)
    const categoriesChart = ref(null)

    const loadDashboardData = async () => {
      try {
        const token = authStore.token
        const headers = { Authorization: `Bearer ${token}` }

        // Carica statistiche principali
        const [dashboardRes, userStatsRes, salesStatsRes] = await Promise.all([
          axios.get('/api/admin/dashboard', { headers }),
          axios.get('/api/admin/users/stats', { headers }),
          axios.get('/api/admin/sales/stats', { headers })
        ])

        stats.value = dashboardRes.data.stats
        userStats.value = userStatsRes.data.user_stats
        salesStats.value = salesStatsRes.data.sales_stats

        // Crea i grafici dopo aver caricato i dati
        setTimeout(() => {
          createCharts()
        }, 100)

      } catch (error) {
        console.error('Errore nel caricamento della dashboard:', error)
        if (error.response?.status === 403) {
          alert('Accesso negato. Solo gli amministratori possono visualizzare questa pagina.')
          authStore.logout()
        }
      } finally {
        loading.value = false
      }
    }

    const createCharts = () => {
      // Gender Distribution Chart (non disponibile)
      if (genderChart.value) {
        new Chart(genderChart.value, {
          type: 'doughnut',
          data: {
            labels: ['Dati non disponibili'],
            datasets: [{
              data: [1],
              backgroundColor: ['#9CA3AF']
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false
          }
        })
      }

      // Education Distribution Chart
      if (educationChart.value && userStats.value.education) {
        new Chart(educationChart.value, {
          type: 'bar',
          data: {
            labels: userStats.value.education.map(item => item.titolo_studi),
            datasets: [{
              label: 'Utenti',
              data: userStats.value.education.map(item => item.count),
              backgroundColor: '#10B981'
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        })
      }

      // Monthly Registrations Chart
      if (registrationsChart.value && userStats.value.monthly_registrations) {
        new Chart(registrationsChart.value, {
          type: 'line',
          data: {
            labels: userStats.value.monthly_registrations.map(item => `${item.month}/${item.year}`),
            datasets: [{
              label: 'Registrazioni',
              data: userStats.value.monthly_registrations.map(item => item.count),
              borderColor: '#8B5CF6',
              backgroundColor: 'rgba(139, 92, 246, 0.1)',
              tension: 0.4
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        })
      }

      // Regions Chart
      if (regionsChart.value && userStats.value.regions) {
        new Chart(regionsChart.value, {
          type: 'bar',
          data: {
            labels: userStats.value.regions.map(item => item.citta_nascita),
            datasets: [{
              label: 'Utenti',
              data: userStats.value.regions.map(item => item.count),
              backgroundColor: '#F59E0B'
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            scales: {
              x: {
                beginAtZero: true
              }
            }
          }
        })
      }

      // Categories Chart
      if (categoriesChart.value && salesStats.value.categories) {
        new Chart(categoriesChart.value, {
          type: 'pie',
          data: {
            labels: salesStats.value.categories.map(item => item.categoria),
            datasets: [{
              data: salesStats.value.categories.map(item => item.count),
              backgroundColor: [
                '#EF4444', '#10B981', '#3B82F6', '#F59E0B', 
                '#8B5CF6', '#EC4899', '#6B7280', '#14B8A6'
              ]
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false
          }
        })
      }
    }

    onMounted(() => {
      // Verifica che l'utente sia admin
      if (!authStore.user?.is_admin) {
        alert('Accesso negato. Solo gli amministratori possono visualizzare questa pagina.')
        authStore.logout()
        return
      }

      loadDashboardData()
    })

    return {
      loading,
      stats,
      userStats,
      salesStats,
      genderChart,
      educationChart,
      registrationsChart,
      regionsChart,
      categoriesChart
    }
  }
}
</script>