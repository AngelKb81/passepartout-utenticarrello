<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <router-link to="/" class="text-3xl font-bold text-blue-600">
          🎭 Passepartout
        </router-link>
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
          Crea il tuo account
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Hai già un account?
          <router-link to="/login" class="font-medium text-blue-600 hover:text-blue-500">
            Accedi qui
          </router-link>
        </p>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleRegister" class="mt-8 space-y-6">
        <div class="bg-white shadow-lg rounded-lg p-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nome -->
            <div>
              <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                👤 Nome *
              </label>
              <input
                v-model="form.nome"
                id="nome"
                name="nome"
                type="text"
                required
                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Il tuo nome"
              />
              <div v-if="errors.nome" class="mt-1 text-sm text-red-600">
                {{ errors.nome }}
              </div>
            </div>

            <!-- Cognome -->
            <div>
              <label for="cognome" class="block text-sm font-medium text-gray-700 mb-2">
                👤 Cognome *
              </label>
              <input
                v-model="form.cognome"
                id="cognome"
                name="cognome"
                type="text"
                required
                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Il tuo cognome"
              />
              <div v-if="errors.cognome" class="mt-1 text-sm text-red-600">
                {{ errors.cognome }}
              </div>
            </div>
          </div>

          <!-- Email -->
          <div class="mt-6">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              📧 Email *
            </label>
            <input
              v-model="form.email"
              id="email"
              name="email"
              type="email"
              autocomplete="email"
              required
              class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="La tua email"
            />
            <div v-if="errors.email" class="mt-1 text-sm text-red-600">
              {{ errors.email }}
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <!-- Password -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                🔒 Password *
              </label>
              <div class="relative">
                <input
                  v-model="form.password"
                  id="password"
                  name="password"
                  :type="showPassword ? 'text' : 'password'"
                  autocomplete="new-password"
                  required
                  class="appearance-none relative block w-full px-3 py-2 pr-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  placeholder="Inserisci password"
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

            <!-- Conferma Password -->
            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                🔒 Conferma Password *
              </label>
              <input
                v-model="form.password_confirmation"
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                required
                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Conferma password"
              />
              <div v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">
                {{ errors.password_confirmation }}
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <!-- Telefono -->
            <div>
              <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                📱 Telefono
              </label>
              <input
                v-model="form.telefono"
                id="telefono"
                name="telefono"
                type="tel"
                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Es: +39 123 456 7890"
              />
              <div v-if="errors.telefono" class="mt-1 text-sm text-red-600">
                {{ errors.telefono }}
              </div>
            </div>

            <!-- Data di Nascita -->
            <div>
              <label for="data_nascita" class="block text-sm font-medium text-gray-700 mb-2">
                🎂 Data di Nascita
              </label>
              <input
                v-model="form.data_nascita"
                id="data_nascita"
                name="data_nascita"
                type="date"
                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
              <div v-if="errors.data_nascita" class="mt-1 text-sm text-red-600">
                {{ errors.data_nascita }}
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <!-- Città di Residenza -->
            <div>
              <label for="citta_residenza" class="block text-sm font-medium text-gray-700 mb-2">
                🏠 Città di Residenza
              </label>
              <input
                v-model="form.citta_residenza"
                id="citta_residenza"
                name="citta_residenza"
                type="text"
                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Es: Milano, Roma, Napoli"
              />
              <div v-if="errors.citta_residenza" class="mt-1 text-sm text-red-600">
                {{ errors.citta_residenza }}
              </div>
            </div>

            <!-- Titolo di Studio -->
            <div>
              <label for="titolo_studio" class="block text-sm font-medium text-gray-700 mb-2">
                🎓 Titolo di Studio
              </label>
              <select
                v-model="form.titolo_studio"
                id="titolo_studio"
                name="titolo_studio"
                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              >
                <option value="">Seleziona titolo di studio</option>
                <option value="licenza_media">Licenza Media</option>
                <option value="diploma">Diploma</option>
                <option value="laurea_triennale">Laurea Triennale</option>
                <option value="laurea_magistrale">Laurea Magistrale</option>
                <option value="master">Master</option>
                <option value="dottorato">Dottorato</option>
              </select>
              <div v-if="errors.titolo_studio" class="mt-1 text-sm text-red-600">
                {{ errors.titolo_studio }}
              </div>
            </div>
          </div>

          <!-- Professione -->
          <div class="mt-6">
            <label for="professione" class="block text-sm font-medium text-gray-700 mb-2">
              💼 Professione
            </label>
            <input
              v-model="form.professione"
              id="professione"
              name="professione"
              type="text"
              class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Es: Sviluppatore, Designer, Manager"
            />
            <div v-if="errors.professione" class="mt-1 text-sm text-red-600">
              {{ errors.professione }}
            </div>
          </div>

          <!-- Privacy Checkbox -->
          <div class="mt-6">
            <div class="flex items-center">
              <input
                v-model="form.privacy_accepted"
                id="privacy"
                name="privacy"
                type="checkbox"
                required
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label for="privacy" class="ml-2 block text-sm text-gray-900">
                Accetto i <a href="#" class="text-blue-600 hover:text-blue-500">termini e condizioni</a> e 
                l'<a href="#" class="text-blue-600 hover:text-blue-500">informativa sulla privacy</a> *
              </label>
            </div>
            <div v-if="errors.privacy_accepted" class="mt-1 text-sm text-red-600">
              {{ errors.privacy_accepted }}
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="errorMessage" class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ errorMessage }}
          </div>

          <!-- Success Message -->
          <div v-if="successMessage" class="mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ successMessage }}
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading"
            class="mt-6 group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <div v-if="loading" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
            {{ loading ? 'Registrazione in corso...' : '🚀 Crea Account' }}
          </button>
        </div>
      </form>

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

const router = useRouter()
const authStore = useAuthStore()

// State
const loading = ref(false)
const showPassword = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const errors = ref({})

const form = reactive({
  nome: '',
  cognome: '',
  email: '',
  password: '',
  password_confirmation: '',
  telefono: '',
  data_nascita: '',
  citta_residenza: '',
  titolo_studio: '',
  professione: '',
  privacy_accepted: false
})

// Methods
const handleRegister = async () => {
  if (!validateForm()) {
    return
  }

  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''
  errors.value = {}

  try {
    const result = await authStore.register(form)

    if (result.success) {
      successMessage.value = 'Registrazione completata con successo! Ora puoi effettuare il login.'
      
      // Reset form
      Object.keys(form).forEach(key => {
        if (typeof form[key] === 'boolean') {
          form[key] = false
        } else {
          form[key] = ''
        }
      })
      
      // Redirect dopo 2 secondi
      setTimeout(() => {
        router.push('/login')
      }, 2000)
    } else {
      errorMessage.value = result.message
    }
  } catch (error) {
    errorMessage.value = 'Errore di connessione. Riprova più tardi.'
  } finally {
    loading.value = false
  }
}

// Validation
const validateForm = () => {
  errors.value = {}
  
  // Campi obbligatori
  if (!form.nome.trim()) {
    errors.value.nome = 'Nome è richiesto'
  }
  
  if (!form.cognome.trim()) {
    errors.value.cognome = 'Cognome è richiesto'
  }
  
  if (!form.email.trim()) {
    errors.value.email = 'Email è richiesta'
  } else if (!/\S+@\S+\.\S+/.test(form.email)) {
    errors.value.email = 'Email non valida'
  }
  
  if (!form.password) {
    errors.value.password = 'Password è richiesta'
  } else if (form.password.length < 8) {
    errors.value.password = 'Password deve essere di almeno 8 caratteri'
  }
  
  if (form.password !== form.password_confirmation) {
    errors.value.password_confirmation = 'Le password non coincidono'
  }
  
  if (!form.privacy_accepted) {
    errors.value.privacy_accepted = 'Devi accettare i termini e condizioni'
  }
  
  // Validazione telefono (se fornito)
  if (form.telefono && !/^[\+]?[0-9\s\-\(\)]{10,}$/.test(form.telefono)) {
    errors.value.telefono = 'Formato telefono non valido'
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
input:focus, select:focus {
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

/* Custom select styling */
select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
}
</style>