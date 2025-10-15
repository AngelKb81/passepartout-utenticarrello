<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Hero Section -->
    <section class="py-20">
      <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold text-gray-900 mb-6">
          🎭 Benvenuto in <span class="text-blue-600">Passepartout</span>
        </h1>
        <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
          Sistema completo di gestione utenti e carrello prodotti con dashboard amministrativa avanzata.
          Sviluppato con Laravel 11, Vue 3 e architettura Repository Pattern.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link 
            to="/products" 
            class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
            🛍️ Esplora Prodotti
          </router-link>
          <router-link 
            v-if="!authStore.isAuthenticated"
            to="/register" 
            class="bg-white text-blue-600 px-8 py-3 rounded-lg border-2 border-blue-600 hover:bg-blue-50 transition-colors font-semibold">
            📝 Registrati Ora
          </router-link>
          <router-link 
            v-else
            to="/profile" 
            class="bg-white text-blue-600 px-8 py-3 rounded-lg border-2 border-blue-600 hover:bg-blue-50 transition-colors font-semibold">
            👤 Il Mio Profilo
          </router-link>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">
          🌟 Caratteristiche Principali
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="text-center p-6 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors">
            <div class="text-4xl mb-4">👥</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Gestione Utenti</h3>
            <p class="text-gray-600">
              Sistema completo di autenticazione con profili dettagliati,
              gestione ruoli e informazioni demografiche complete.
            </p>
          </div>
          
          <div class="text-center p-6 rounded-lg bg-green-50 hover:bg-green-100 transition-colors">
            <div class="text-4xl mb-4">🛒</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Carrello Avanzato</h3>
            <p class="text-gray-600">
              Carrello intelligente con gestione quantità, calcolo totali automatico
              e processo di checkout guidato.
            </p>
          </div>
          
          <div class="text-center p-6 rounded-lg bg-purple-50 hover:bg-purple-100 transition-colors">
            <div class="text-4xl mb-4">📊</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Dashboard Admin</h3>
            <p class="text-gray-600">
              Dashboard amministrativa con grafici interattivi, statistiche business
              e analisi demografiche avanzate.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-gray-50" v-if="stats">
      <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">
          📈 Statistiche Piattaforma
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ stats.users_count || 0 }}</div>
            <div class="text-gray-600">Utenti Registrati</div>
          </div>
          
          <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <div class="text-3xl font-bold text-green-600 mb-2">{{ stats.products_count || 0 }}</div>
            <div class="text-gray-600">Prodotti Disponibili</div>
          </div>
          
          <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <div class="text-3xl font-bold text-purple-600 mb-2">{{ stats.orders_count || 0 }}</div>
            <div class="text-gray-600">Ordini Completati</div>
          </div>
          
          <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <div class="text-3xl font-bold text-orange-600 mb-2">€{{ formatCurrency(stats.total_revenue || 0) }}</div>
            <div class="text-gray-600">Fatturato Totale</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Technology Stack -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">
          🛠️ Stack Tecnologico
        </h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
          <div class="text-center">
            <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
              <span class="text-2xl">⚡</span>
            </div>
            <h4 class="font-semibold text-gray-900 mb-1">Laravel 11</h4>
            <p class="text-gray-600 text-sm">PHP Framework</p>
          </div>
          
          <div class="text-center">
            <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
              <span class="text-2xl">🌐</span>
            </div>
            <h4 class="font-semibold text-gray-900 mb-1">Vue 3</h4>
            <p class="text-gray-600 text-sm">Frontend Framework</p>
          </div>
          
          <div class="text-center">
            <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
              <span class="text-2xl">🗃️</span>
            </div>
            <h4 class="font-semibold text-gray-900 mb-1">MySQL</h4>
            <p class="text-gray-600 text-sm">Database</p>
          </div>
          
          <div class="text-center">
            <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
              <span class="text-2xl">🔐</span>
            </div>
            <h4 class="font-semibold text-gray-900 mb-1">Sanctum</h4>
            <p class="text-gray-600 text-sm">Authentication</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action -->
    <section class="py-20 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
      <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold mb-6">
          Pronto per iniziare?
        </h2>
        <p class="text-xl mb-8 opacity-90">
          Unisciti alla nostra piattaforma e scopri tutte le funzionalità avanzate per gestire 
          la tua esperienza shopping in modo professionale.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link 
            v-if="!authStore.isAuthenticated"
            to="/register" 
            class="bg-white text-blue-600 px-8 py-3 rounded-lg hover:bg-gray-100 transition-colors font-semibold">
            🚀 Registrati Gratis
          </router-link>
          <router-link 
            to="/products" 
            class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg hover:bg-white hover:text-blue-600 transition-colors font-semibold">
            🛍️ Inizia Shopping
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import axios from 'axios'

const authStore = useAuthStore()
const stats = ref(null)
const loading = ref(false)

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('it-IT').format(amount)
}

const loadStats = async () => {
  try {
    loading.value = true
    // Simuliamo delle statistiche per ora
    stats.value = {
      users_count: 156,
      products_count: 42,
      orders_count: 89,
      total_revenue: 15420.50
    }
    
    // TODO: Implementare API per statistiche pubbliche
    // const response = await axios.get('/stats/public')
    // stats.value = response.data
  } catch (error) {
    console.error('Errore nel caricamento delle statistiche:', error)
    stats.value = {
      users_count: 0,
      products_count: 0,
      orders_count: 0,
      total_revenue: 0
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadStats()
})
</script>

<style scoped>
/* Custom animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fade-in-up {
  animation: fadeInUp 0.6s ease-out;
}

/* Hover effects */
.hover-scale:hover {
  transform: scale(1.05);
  transition: transform 0.2s ease-in-out;
}

/* Custom gradient text */
.gradient-text {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
</style>