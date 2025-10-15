<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <router-link to="/" class="text-3xl font-bold text-blue-600">
          🎭 Passepartout
        </router-link>
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
          Accedi al tuo account
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Non hai un account?
          <router-link to="/register" class="font-medium text-blue-600 hover:text-blue-500">
            Registrati qui
          </router-link>
        </p>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleLogin" class="mt-8 space-y-6">
        <div class="bg-white shadow-lg rounded-lg p-8">
          <!-- Email Field -->
          <div class="mb-6">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              📧 Email
            </label>
            <input
              v-model="form.email"
              id="email"
              name="email"
              type="email"
              autocomplete="email"
              required
              class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
              placeholder="Inserisci la tua email"
            />
            <div v-if="errors.email" class="mt-1 text-sm text-red-600">
              {{ errors.email }}
            </div>
          </div>

          <!-- Password Field -->
          <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              🔒 Password
            </label>
            <div class="relative">
              <input
                v-model="form.password"
                id="password"
                name="password"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="current-password"
                required
                class="appearance-none relative block w-full px-3 py-2 pr-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                placeholder="Inserisci la tua password"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
              >
                <span class="text-gray-400">{{ showPassword ? '🙈' : '👁️' }}</span>
              </button>
            </div>
            <div v-if="errors.password" class="mt-1 text-sm text-red-600">
              {{ errors.password }}
            </div>
          </div>

          <!-- Remember Me -->
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
              <input
                v-model="form.remember"
                id="remember"
                name="remember"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label for="remember" class="ml-2 block text-sm text-gray-900">
                Ricordami
              </label>
            </div>
            
            <div class="text-sm">
              <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                Password dimenticata?
              </a>
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ errorMessage }}
          </div>

          <!-- Success Message -->
          <div v-if="successMessage" class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ successMessage }}
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <div v-if="loading" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
            {{ loading ? 'Accesso in corso...' : '🔐 Accedi' }}
          </button>
        </div>
      </form>

      <!-- Demo Credentials -->
      <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <h3 class="text-sm font-medium text-yellow-800 mb-2">🧪 Credenziali Demo</h3>
        <div class="text-xs text-yellow-700 space-y-1">
          <div><strong>Admin:</strong> admin@test.com / password123</div>
          <div><strong>User:</strong> user@test.com / password123</div>
          <div><strong>Business:</strong> business@test.com / password123</div>
        </div>
        <div class="mt-2 flex space-x-2">
          <button
            @click="setDemoCredentials('admin')"
            class="text-xs bg-yellow-200 hover:bg-yellow-300 px-2 py-1 rounded"
          >
            Admin
          </button>
          <button
            @click="setDemoCredentials('user')"
            class="text-xs bg-yellow-200 hover:bg-yellow-300 px-2 py-1 rounded"
          >
            User
          </button>
          <button
            @click="setDemoCredentials('business')"
            class="text-xs bg-yellow-200 hover:bg-yellow-300 px-2 py-1 rounded"
          >
            Business
          </button>
        </div>
      </div>

      <!-- Footer Links -->
      <div class="text-center text-sm text-gray-500">
        <router-link to="/" class="hover:text-blue-600">← Torna alla Home</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useCartStore } from '../../stores/cart'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

// State
const loading = ref(false)
const showPassword = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const errors = ref({})

const form = reactive({
  email: '',
  password: '',
  remember: false
})

// Methods
const handleLogin = async () => {
  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''
  errors.value = {}

  try {
    const result = await authStore.login({
      email: form.email,
      password: form.password,
      remember: form.remember
    })

    if (result.success) {
      successMessage.value = 'Login effettuato con successo!'
      
      // Carica il carrello dopo il login
      await cartStore.loadCartItems()
      
      // Redirect dopo 1 secondo
      setTimeout(() => {
        router.push('/')
      }, 1000)
    } else {
      errorMessage.value = result.message
    }
  } catch (error) {
    errorMessage.value = 'Errore di connessione. Riprova più tardi.'
  } finally {
    loading.value = false
  }
}

const setDemoCredentials = (type) => {
  const credentials = {
    admin: { email: 'admin@test.com', password: 'password123' },
    user: { email: 'user@test.com', password: 'password123' },
    business: { email: 'business@test.com', password: 'password123' }
  }

  form.email = credentials[type].email
  form.password = credentials[type].password
}

// Validation
const validateForm = () => {
  errors.value = {}
  
  if (!form.email) {
    errors.value.email = 'Email è richiesta'
  } else if (!/\S+@\S+\.\S+/.test(form.email)) {
    errors.value.email = 'Email non valida'
  }
  
  if (!form.password) {
    errors.value.password = 'Password è richiesta'
  } else if (form.password.length < 6) {
    errors.value.password = 'Password deve essere di almeno 6 caratteri'
  }
  
  return Object.keys(errors.value).length === 0
}
</script>

<style scoped>
/* Custom animations */
@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.slide-in-up {
  animation: slideInUp 0.5s ease-out;
}

/* Focus states */
input:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Button hover effect */
button:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

button:active:not(:disabled) {
  transform: translateY(0);
}
</style>