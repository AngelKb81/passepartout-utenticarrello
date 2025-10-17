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

            <div class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
              <div class="p-2 bg-yellow-100 rounded-lg">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
              </div>
              <div>
                <p class="font-medium text-gray-900">Gestisci Utenti</p>
                <p class="text-sm text-gray-600">Amministra utenti</p>
              </div>
            </div>
          </div>
        </div>
      </div>

            </div>

      <!-- Charts temporaneamente rimossi -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Grafici in sviluppo</h3>
        <p class="text-gray-600">I grafici saranno implementati in una versione futura.</p>
      </div>
    </div>
  </div>
</template>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, nextTick } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from 'axios'

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

    const loadData = async () => {
      try {
        isLoading.value = true
        
        // Per ora usiamo dati statici - in futuro collegheremo alle API
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
        
        userStats.value = [
          { gender: 'M', count: 89 },
          { gender: 'F', count: 67 }
        ]
        
        salesStats.value = [
          { month: '2025-01', total: 3200 },
          { month: '2025-02', total: 4100 },
          { month: '2025-03', total: 3800 },
          { month: '2025-04', total: 4500 },
          { month: '2025-05', total: 5200 }
        ]

        // Grafici rimossi temporaneamente
        console.log('✅ Dashboard dati caricati:', stats.value)
        
      } catch (error) {
        console.error('Errore nel caricamento dei dati:', error)
        error.value = 'Errore nel caricamento dei dati della dashboard'
      } finally {
        isLoading.value = false
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