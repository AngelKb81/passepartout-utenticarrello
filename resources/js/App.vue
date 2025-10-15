<template>
  <div id="app">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex items-center">
            <router-link to="/" class="text-xl font-bold text-blue-600">
              🎭 Passepartout
            </router-link>
          </div>

          <!-- Navigation Menu -->
          <div class="hidden md:flex items-center space-x-8">
            <router-link to="/" class="text-gray-700 hover:text-blue-600 transition-colors">
              Home
            </router-link>
            <router-link to="/products" class="text-gray-700 hover:text-blue-600 transition-colors">
              Prodotti
            </router-link>
            
            <!-- Authenticated User Menu -->
            <div v-if="authStore.isAuthenticated" class="flex items-center space-x-4">
              <router-link to="/cart" class="relative text-gray-700 hover:text-blue-600 transition-colors">
                🛒 Carrello
                <span v-if="cartStore.itemsCount > 0" 
                      class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                  {{ cartStore.itemsCount }}
                </span>
              </router-link>
              
              <!-- Admin Menu -->
              <div v-if="authStore.isAdmin" class="relative">
                <button @click="showAdminMenu = !showAdminMenu" 
                        class="text-gray-700 hover:text-blue-600 transition-colors">
                  👑 Admin
                </button>
                <div v-if="showAdminMenu" 
                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                  <router-link to="/admin/dashboard" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    📊 Dashboard
                  </router-link>
                  <router-link to="/admin/products" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    📦 Gestione Prodotti
                  </router-link>
                </div>
              </div>
              
              <!-- User Menu -->
              <div class="relative">
                <button @click="showUserMenu = !showUserMenu" 
                        class="flex items-center text-gray-700 hover:text-blue-600 transition-colors">
                  👤 {{ authStore.user?.nome }}
                </button>
                <div v-if="showUserMenu" 
                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                  <router-link to="/profile" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    👤 Profilo
                  </router-link>
                  <button @click="logout" 
                          class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    🚪 Logout
                  </button>
                </div>
              </div>
            </div>

            <!-- Guest Menu -->
            <div v-else class="flex items-center space-x-4">
              <router-link to="/login" class="text-gray-700 hover:text-blue-600 transition-colors">
                🔐 Login
              </router-link>
              <router-link to="/register" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                📝 Registrati
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
              <div v-if="authStore.isAdmin">
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

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
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
          <p>&copy; 2024 Passepartout. Tutti i diritti riservati.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from './stores/auth'
import { useCartStore } from './stores/cart'

// Store references
const authStore = useAuthStore()
const cartStore = useCartStore()

// Reactive state
const showUserMenu = ref(false)
const showAdminMenu = ref(false)
const showMobileMenu = ref(false)
const isLoading = ref(false)
const flashMessage = ref('')
const errorMessage = ref('')

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

// Close menus when clicking outside
const closeMenus = () => {
  showUserMenu.value = false
  showAdminMenu.value = false
  showMobileMenu.value = false
}

// Initialize app
onMounted(async () => {
  try {
    isLoading.value = true
    await authStore.initializeAuth()
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