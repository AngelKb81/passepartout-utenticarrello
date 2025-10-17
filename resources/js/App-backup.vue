<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <!-- Modern Navigation Bar - nascosto per pagine auth -->
    <nav v-if="!isAuthPage" class="bg-white border-b border-gray-200 sticky top-0 z-50 backdrop-blur-lg bg-white/95">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
          <!-- Logo & Brand -->
          <div class="flex items-center">
            <router-link to="/" class="flex items-center gap-3 group">
              <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
              </div>
              <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Passepartout
              </span>
            </router-link>
          </div>

          <!-- Desktop Navigation Menu -->
          <div class="hidden md:flex items-center gap-8">
            <router-link 
              to="/" 
              class="text-gray-700 hover:text-indigo-600 transition-colors font-medium flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
              </svg>
              Home
            </router-link>
            <router-link 
              to="/products" 
              class="text-gray-700 hover:text-indigo-600 transition-colors font-medium flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
              Prodotti
            </router-link>
            
            <!-- Authenticated User Menu -->
                        <!-- User Profile Menu -->
              <div v-if="true" class="text-xs text-red-500 bg-yellow-100 p-1 rounded">
                Debug: Auth={{ authStore.isAuthenticated }}, Admin={{ isUserAdmin }}, User={{ authStore.user?.email }}
              </div>
            <div v-if="authStore.isAuthenticated" class="flex items-center gap-4">
              <!-- Cart -->
              <router-link 
                to="/cart" 
                class="relative text-gray-700 hover:text-indigo-600 transition-colors font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Carrello
                <span 
                  v-if="cartStore.itemsCount > 0" 
                  class="absolute -top-2 -right-2 bg-gradient-to-br from-pink-500 to-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold shadow-lg animate-pulse">
                  {{ cartStore.itemsCount }}
                </span>
              </router-link>
              
              <!-- Admin Dropdown (DEBUG: {{ isUserAdmin }}) -->
              <div v-if="isUserAdmin" class="relative">
                <button 
                  @click="showAdminMenu = !showAdminMenu" 
                  class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 text-white font-semibold hover:from-amber-600 hover:to-orange-600 transition-all shadow-md hover:shadow-lg">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                  </svg>
                  Admin
                  <svg class="w-4 h-4" :class="{ 'rotate-180': showAdminMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>
                <div 
                  v-if="showAdminMenu" 
                  class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl py-2 border border-gray-100 animate-fadeIn">
                  <router-link 
                    to="/admin/dashboard" 
                    @click="showAdminMenu = false"
                    class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="font-medium">Dashboard</span>
                  </router-link>
                  <router-link 
                    to="/admin/products" 
                    @click="showAdminMenu = false"
                    class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span class="font-medium">Gestione Prodotti</span>
                  </router-link>
                  <router-link 
                    to="/admin/emails" 
                    @click="showAdminMenu = false"
                    class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-medium">📧 Log Email</span>
                  </router-link>
                </div>
              </div>
              
              <!-- User Dropdown -->
              <div class="relative">
                <button 
                  @click="showUserMenu = !showUserMenu" 
                  class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                  <div class="w-8 h-8 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                    {{ authStore.user?.name?.charAt(0).toUpperCase() }}
                  </div>
                  <span class="font-medium text-gray-700">{{ authStore.user?.name }}</span>
                  <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': showUserMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>
                <div 
                  v-if="showUserMenu" 
                  class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl py-2 border border-gray-100 animate-fadeIn">
                  <router-link 
                    to="/profile" 
                    @click="showUserMenu = false"
                    class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="font-medium">Il Mio Profilo</span>
                  </router-link>
                  <div class="border-t border-gray-100 my-2"></div>
                  <button 
                    @click="logout" 
                    class="flex items-center gap-3 w-full px-4 py-3 text-red-600 hover:bg-red-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="font-medium">Esci</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Guest Menu -->
            <div v-else class="flex items-center gap-3">
              <router-link 
                to="/login" 
                class="text-gray-700 hover:text-indigo-600 transition-colors font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Login
              </router-link>
              <router-link 
                to="/register" 
                class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold hover:from-indigo-700 hover:to-purple-700 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                Registrati Gratis
              </router-link>
            </div>
          </div>
          
          <!-- Mobile menu button -->
          <div class="md:hidden">
            <button @click="showMobileMenu = !showMobileMenu" 
                    class="text-gray-700 hover:text-blue-600 focus:outline-none">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M4 6h16M4 12h16M4 18h16"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div v-if="showMobileMenu" class="md:hidden py-4 border-t">
          <div class="flex flex-col space-y-2">
            <router-link to="/" class="text-gray-700 hover:text-blue-600 py-2">Home</router-link>
            <router-link to="/products" class="text-gray-700 hover:text-blue-600 py-2">Prodotti</router-link>
            
            <div v-if="authStore.isAuthenticated">
              <router-link to="/cart" class="text-gray-700 hover:text-blue-600 py-2">🛒 Carrello</router-link>
              <router-link to="/profile" class="text-gray-700 hover:text-blue-600 py-2">👤 Profilo</router-link>
              <div v-if="isUserAdmin">
                <router-link to="/admin/dashboard" class="text-gray-700 hover:text-blue-600 py-2 pl-4">📊 Dashboard</router-link>
                <router-link to="/admin/products" class="text-gray-700 hover:text-blue-600 py-2 pl-4">📦 Prodotti</router-link>
              </div>
              <button @click="logout" class="text-gray-700 hover:text-blue-600 py-2 text-left">🚪 Logout</button>
            </div>
            
            <div v-else>
              <router-link to="/login" class="text-gray-700 hover:text-blue-600 py-2">🔐 Login</router-link>
              <router-link to="/register" class="text-gray-700 hover:text-blue-600 py-2">📝 Registrati</router-link>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen bg-gray-50">
      <!-- Loading indicator -->
      <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
          <p class="mt-2 text-gray-600">Caricamento...</p>
        </div>
      </div>

      <!-- Flash Messages -->
      <div v-if="flashMessage" class="bg-green-500 text-white p-4 text-center">
        {{ flashMessage }}
      </div>
      <div v-if="errorMessage" class="bg-red-500 text-white p-4 text-center">
        {{ errorMessage }}
      </div>

      <!-- Router View -->
      <router-view />
    </main>

    <!-- Footer - nascosto per pagine auth -->
    <footer v-if="!isAuthPage" class="bg-gray-800 text-white py-8">
      <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div>
            <h3 class="text-lg font-semibold mb-4">🎭 Passepartout</h3>
            <p class="text-gray-300">
              Sistema di gestione utenti e carrello prodotti con dashboard amministrativa.
            </p>
          </div>
          <div>
            <h3 class="text-lg font-semibold mb-4">Links</h3>
            <ul class="space-y-2 text-gray-300">
              <li><router-link to="/">Home</router-link></li>
              <li><router-link to="/products">Prodotti</router-link></li>
              <li v-if="!authStore.isAuthenticated"><router-link to="/register">Registrati</router-link></li>
            </ul>
          </div>
          <div>
            <h3 class="text-lg font-semibold mb-4">Info</h3>
            <p class="text-gray-300">
              Laravel 11 + Vue 3<br>
              Repository Pattern<br>
              Sanctum Authentication
            </p>
          </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
          <p>&copy; 2025 Passepartout. Tutti i diritti riservati.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useAuthStore } from './stores/auth'
import { useCartStore } from './stores/cart'
import { useRouter } from 'vue-router'

// Store references
const authStore = useAuthStore()
const cartStore = useCartStore()
const router = useRouter()

// Reactive state
const showUserMenu = ref(false)
const showAdminMenu = ref(false)
const showMobileMenu = ref(false)
const isLoading = ref(false)
const flashMessage = ref('')
const errorMessage = ref('')

// Computed properties per reattività
const isUserAdmin = computed(() => authStore.isAdmin)

// Methods
const logout = async () => {
  try {
    isLoading.value = true
    await authStore.logout()
    showUserMenu.value = false
    flashMessage.value = 'Logout effettuato con successo!'
    setTimeout(() => flashMessage.value = '', 3000)
  } catch (error) {
    errorMessage.value = 'Errore durante il logout'
    setTimeout(() => errorMessage.value = '', 3000)
  } finally {
    isLoading.value = false
  }
}

// Computed property per rilevare pagine di auth
const isAuthPage = computed(() => {
  const authRoutes = ['/login', '/register', '/forgot-password', '/reset-password']
  return authRoutes.includes(router.currentRoute.value.path)
})

// Close menus when clicking outside
const closeMenus = () => {
  showUserMenu.value = false
  showAdminMenu.value = false
  showMobileMenu.value = false
}

// Watch auth changes
watch(() => authStore.isAdmin, (newValue) => {
  console.log('🔄 Auth isAdmin changed:', newValue)
  console.log('Current user:', authStore.user)
  console.log('User roles:', authStore.user?.roles)
})

// Watch per verificare il computed
watch(isUserAdmin, (newValue) => {
  console.log('📊 Computed isUserAdmin changed:', newValue)
})

// Initialize app
onMounted(async () => {
  try {
    isLoading.value = true
    await authStore.initializeAuth()
    
    console.log('🚀 AUTH DEBUG:', {
      isAuthenticated: authStore.isAuthenticated,
      isAdmin: authStore.isAdmin,
      user: authStore.user,
      computedIsAdmin: isUserAdmin.value
    })
    
    if (authStore.isAuthenticated) {
      cartStore.loadCartItems()
    }
  } catch (error) {
    console.error('Error initializing app:', error)
  } finally {
    isLoading.value = false
  }
})

// Global click handler to close menus
document.addEventListener('click', (e) => {
  if (!e.target.closest('.relative')) {
    closeMenus()
  }
})
</script>

<style>
/* Global styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  line-height: 1.6;
}

/* Router link active states */
.router-link-active {
  color: #2563eb !important;
  font-weight: 600;
}

/* Custom animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.fade-in {
  animation: fadeIn 0.3s ease-out;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}
</style>