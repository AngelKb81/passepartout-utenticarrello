<template>
  <div class="p-8">
    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
      🐛 DEBUG: Template renderizzato {{ new Date().toLocaleTimeString() }}
    </div>
    
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Dashboard Admin - Test Version</h1>
    
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
      ✅ Dashboard caricata correttamente!
    </div>
    
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
      🔍 Stato: mounted = {{ mounted }}, user = {{ authStore?.user?.email || 'nessuno' }}
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold">Utenti</h3>
        <p class="text-2xl">100</p>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold">Prodotti</h3>
        <p class="text-2xl">50</p>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold">Ordini</h3>
        <p class="text-2xl">25</p>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold">Vendite</h3>
        <p class="text-2xl">€1000</p>
      </div>
    </div>
    
    <div class="mt-8 space-y-4">
      <p class="text-gray-600">Se vedi questo messaggio, la dashboard funziona!</p>
      <div class="bg-gray-100 p-4 rounded">
        <h3 class="font-semibold mb-2">Info Debug:</h3>
        <ul class="text-sm space-y-1">
          <li>✅ Componente montato: {{ mounted }}</li>
          <li>✅ Store auth caricato: {{ !!authStore }}</li>
          <li>✅ Utente autenticato: {{ authStore?.isAuthenticated }}</li>
          <li>✅ Utente admin: {{ authStore?.isAdmin }}</li>
          <li>✅ Email utente: {{ authStore?.user?.email }}</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()
const mounted = ref(false)

console.log('🚀 Dashboard Test component mounted')
console.log('📊 Auth store:', authStore)
console.log('👤 User:', authStore?.user)
console.log('🔐 Is authenticated:', authStore?.isAuthenticated)
console.log('👑 Is admin:', authStore?.isAdmin)

onMounted(() => {
  mounted.value = true
  console.log('✅ Dashboard Test onMounted completed')
})
</script>