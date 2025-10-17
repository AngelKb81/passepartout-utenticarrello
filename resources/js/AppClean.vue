<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <!-- Unified Header - nascosto per pagine auth -->
    <header v-if="!isAuthPage" class="bg-white shadow-sm sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <router-link to="/" class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
              </svg>
            </div>
            <span class="text-xl font-bold text-gray-900">Passepartout</span>
          </router-link>

          <!-- Desktop Navigation -->
          <nav class="hidden md:flex items-center space-x-8">
            <router-link to="/" class="text-gray-700 hover:text-indigo-600 transition-colors font-medium">
              Home
            </router-link>
            <router-link to="/products" class="text-gray-700 hover:text-indigo-600 transition-colors font-medium">
              Prodotti
            </router-link>

            <!-- User Area -->
            <div v-if="authStore.isAuthenticated" class="flex items-center space-x-4">
              <!-- Cart -->
              <router-link to="/cart" class="relative text-gray-700 hover:text-indigo-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span v-if="cartStore.itemsCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                  {{ cartStore.itemsCount }}
                </span>
              </router-link>

              <!-- Unified User Menu -->
              <div class="relative">
                <button @click="showUserMenu = !showUserMenu" class="flex items-center space-x-2 bg-gray-100 hover:bg-gray-200 rounded-lg px-3 py-2 transition-colors">
                  <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white font-medium text-sm">
                    {{ getUserInitials() }}
                  </div>
                  <span class="font-medium text-gray-900">{{ authStore.user?.nome || 'User' }}</span>
                  <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': showUserMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>

                <!-- Dropdown Menu -->
                <div v-if="showUserMenu" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-100 py-2" @click="showUserMenu = false">
                  <!-- User Info Section -->
                  <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-medium text-gray-900">{{ authStore.user?.nome }} {{ authStore.user?.cognome }}</p>
                    <p class="text-sm text-gray-500">{{ authStore.user?.email }}</p>
                    <span v-if="authStore.isAdmin" class="inline-block mt-1 px-2 py-1 bg-amber-100 text-amber-800 text-xs rounded-full font-medium">
                      Amministratore
                    </span>
                  </div>

                  <!-- Standard User Options -->
                  <router-link to="/profile" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Il Mio Profilo
                  </router-link>

                  <!-- Admin Only Options -->
                  <div v-if="authStore.isAdmin" class="border-t border-gray-100 pt-2">
                    <div class="px-4 py-2">
                      <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Amministrazione</span>
                    </div>
                    <router-link to="/admin/dashboard" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                      <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                      </svg>
                      Dashboard
                    </router-link>
                    <router-link to="/admin/emails" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                      <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                      </svg>
                      Email Logs
                    </router-link>
                  </div>

                  <!-- Logout -->
                  <div class="border-t border-gray-100 pt-2">
                    <button @click="logout" class="flex items-center w-full px-4 py-3 text-red-600 hover:bg-red-50 transition-colors">
                      <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                      </svg>
                      Esci
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Guest Menu -->
            <div v-else class="flex items-center space-x-4">
              <router-link to="/login" class="text-gray-700 hover:text-indigo-600 transition-colors font-medium">
                Accedi
              </router-link>
              <router-link to="/register" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                Registrati
              </router-link>
            </div>
          </nav>

          <!-- Mobile menu button -->
          <button @click="showMobileMenu = !showMobileMenu" class="md:hidden p-2 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!showMobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Mobile Menu -->
        <div v-if="showMobileMenu" class="md:hidden border-t border-gray-200 pt-4 pb-4">
          <div class="space-y-1">
            <router-link to="/" @click="showMobileMenu = false" class="block px-3 py-2 text-gray-700 hover:text-indigo-600 font-medium">Home</router-link>
            <router-link to="/products" @click="showMobileMenu = false" class="block px-3 py-2 text-gray-700 hover:text-indigo-600 font-medium">Prodotti</router-link>
            
            <div v-if="authStore.isAuthenticated" class="border-t border-gray-200 pt-4 mt-4">
              <router-link to="/cart" @click="showMobileMenu = false" class="block px-3 py-2 text-gray-700 hover:text-indigo-600 font-medium">Carrello</router-link>
              <router-link to="/profile" @click="showMobileMenu = false" class="block px-3 py-2 text-gray-700 hover:text-indigo-600 font-medium">Profilo</router-link>
              
              <div v-if="authStore.isAdmin" class="border-t border-gray-200 pt-2 mt-2">
                <p class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Admin</p>
                <router-link to="/admin/dashboard" @click="showMobileMenu = false" class="block px-3 py-2 text-gray-700 hover:text-indigo-600 font-medium">Dashboard</router-link>
                <router-link to="/admin/emails" @click="showMobileMenu = false" class="block px-3 py-2 text-gray-700 hover:text-indigo-600 font-medium">Email Logs</router-link>
              </div>
              
              <button @click="logout; showMobileMenu = false" class="block w-full text-left px-3 py-2 text-red-600 hover:bg-red-50 font-medium">Esci</button>
            </div>
            
            <div v-else class="border-t border-gray-200 pt-4 mt-4">
              <router-link to="/login" @click="showMobileMenu = false" class="block px-3 py-2 text-gray-700 hover:text-indigo-600 font-medium">Accedi</router-link>
              <router-link to="/register" @click="showMobileMenu = false" class="block px-3 py-2 bg-indigo-600 text-white rounded-lg font-medium">Registrati</router-link>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Flash Messages -->
    <div v-if="flashMessage" class="fixed top-20 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg animate-fadeIn">
      {{ flashMessage }}
    </div>
    <div v-if="errorMessage" class="fixed top-20 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg animate-fadeIn">
      {{ errorMessage }}
    </div>

    <!-- Main Content -->
    <main class="flex-1">
      <router-view />
    </main>

    <!-- Clean Footer - nascosto per pagine auth -->
    <footer v-if="!isAuthPage" class="bg-white border-t border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Brand -->
          <div class="text-center md:text-left">
            <div class="flex items-center justify-center md:justify-start space-x-2 mb-4">
              <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
              </div>
              <span class="text-xl font-bold text-gray-900">Passepartout</span>
            </div>
            <p class="text-gray-600 max-w-xs mx-auto md:mx-0">
              Sistema di gestione utenti e carrello con dashboard amministrativa.
            </p>
          </div>

          <!-- Links -->
          <div class="text-center md:text-left">
            <h3 class="font-semibold text-gray-900 mb-4">Links Utili</h3>
            <ul class="space-y-2">
              <li><router-link to="/" class="text-gray-600 hover:text-indigo-600 transition-colors">Home</router-link></li>
              <li><router-link to="/products" class="text-gray-600 hover:text-indigo-600 transition-colors">Prodotti</router-link></li>
              <li v-if="!authStore.isAuthenticated"><router-link to="/register" class="text-gray-600 hover:text-indigo-600 transition-colors">Registrati</router-link></li>
            </ul>
          </div>

          <!-- Tech Info -->
          <div class="text-center md:text-left">
            <h3 class="font-semibold text-gray-900 mb-4">Tecnologie</h3>
            <ul class="space-y-2 text-gray-600">
              <li>Laravel 11</li>
              <li>Vue 3 + Vite</li>
              <li>Sanctum Auth</li>
              <li>MySQL</li>
            </ul>
          </div>
        </div>

        <!-- Bottom -->
        <div class="border-t border-gray-200 pt-8 mt-8 text-center">
          <p class="text-gray-600">&copy; 2024 Passepartout. Tutti i diritti riservati.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from './stores/auth'
import { useCartStore } from './stores/cart'
import { useRouter } from 'vue-router'

// Store references
const authStore = useAuthStore()
const cartStore = useCartStore()
const router = useRouter()

// Reactive state
const showUserMenu = ref(false)
const showMobileMenu = ref(false)
const flashMessage = ref('')
const errorMessage = ref('')

// Methods
const getUserInitials = () => {
  const user = authStore.user
  if (!user) return 'U'
  const nome = user.nome || ''
  const cognome = user.cognome || ''
  return (nome.charAt(0) + cognome.charAt(0)).toUpperCase() || 'U'
}

const logout = async () => {
  try {
    await authStore.logout()
    showUserMenu.value = false
    flashMessage.value = 'Logout effettuato con successo!'
    setTimeout(() => flashMessage.value = '', 3000)
  } catch (error) {
    errorMessage.value = 'Errore durante il logout'
    setTimeout(() => errorMessage.value = '', 3000)
  }
}

// Computed property per rilevare pagine di auth
const isAuthPage = computed(() => {
  const authRoutes = ['/login', '/register', '/forgot-password', '/reset-password']
  return authRoutes.includes(router.currentRoute.value.path)
})

// Initialize stores
import { onMounted } from 'vue'
onMounted(async () => {
  if (!authStore.initialized) {
    await authStore.initializeAuth()
  }
})
</script>

<style>
/* Custom animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}
</style>