<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
          👤 Il Mio Profilo
        </h1>
        <p class="text-gray-600">
          Gestisci le tue informazioni personali e le preferenze dell'account
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Navigation -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-center mb-6">
              <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-2xl">👤</span>
              </div>
              <h3 class="text-lg font-semibold text-gray-900">
                {{ authStore.userFullName }}
              </h3>
              <p class="text-gray-600 text-sm">{{ authStore.user?.email }}</p>
              <div class="mt-2">
                <span v-if="authStore.isAdmin" class="inline-block bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">
                  👑 Amministratore
                </span>
                <span v-else-if="authStore.isBusiness" class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">
                  💼 Business
                </span>
                <span v-else class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                  👤 Utente
                </span>
              </div>
            </div>

            <nav class="space-y-2">
              <button
                @click="activeTab = 'profile'"
                :class="[
                  'w-full text-left px-4 py-3 rounded-md transition-colors',
                  activeTab === 'profile' 
                    ? 'bg-blue-100 text-blue-700 font-medium' 
                    : 'text-gray-700 hover:bg-gray-100'
                ]"
              >
                📝 Informazioni Personali
              </button>
              
              <button
                @click="activeTab = 'security'"
                :class="[
                  'w-full text-left px-4 py-3 rounded-md transition-colors',
                  activeTab === 'security' 
                    ? 'bg-blue-100 text-blue-700 font-medium' 
                    : 'text-gray-700 hover:bg-gray-100'
                ]"
              >
                🔒 Sicurezza
              </button>
              
              <button
                @click="activeTab = 'orders'"
                :class="[
                  'w-full text-left px-4 py-3 rounded-md transition-colors',
                  activeTab === 'orders' 
                    ? 'bg-blue-100 text-blue-700 font-medium' 
                    : 'text-gray-700 hover:bg-gray-100'
                ]"
              >
                📦 I Miei Ordini
              </button>

              <div class="pt-4 border-t">
                <button
                  @click="logout"
                  class="w-full text-left px-4 py-3 rounded-md text-red-700 hover:bg-red-50 transition-colors"
                >
                  🚪 Logout
                </button>
              </div>
            </nav>
          </div>
        </div>

        <!-- Profile Content -->
        <div class="lg:col-span-2">
          <!-- Personal Information Tab -->
          <div v-if="activeTab === 'profile'" class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">📝 Informazioni Personali</h2>
            
            <form @submit.prevent="updateProfile">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome -->
                <div>
                  <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                    Nome *
                  </label>
                  <input
                    v-model="profileForm.nome"
                    id="nome"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                  <div v-if="errors.nome" class="mt-1 text-sm text-red-600">
                    {{ errors.nome }}
                  </div>
                </div>

                <!-- Cognome -->
                <div>
                  <label for="cognome" class="block text-sm font-medium text-gray-700 mb-2">
                    Cognome *
                  </label>
                  <input
                    v-model="profileForm.cognome"
                    id="cognome"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                  v-model="profileForm.email"
                  id="email"
                  type="email"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
                <div v-if="errors.email" class="mt-1 text-sm text-red-600">
                  {{ errors.email }}
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Telefono -->
                <div>
                  <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                    📱 Telefono
                  </label>
                  <input
                    v-model="profileForm.telefono"
                    id="telefono"
                    type="tel"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                    v-model="profileForm.data_nascita"
                    id="data_nascita"
                    type="date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                    v-model="profileForm.citta_residenza"
                    id="citta_residenza"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                    v-model="profileForm.titolo_studio"
                    id="titolo_studio"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                  v-model="profileForm.professione"
                  id="professione"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
                <div v-if="errors.professione" class="mt-1 text-sm text-red-600">
                  {{ errors.professione }}
                </div>
              </div>

              <!-- Submit Button -->
              <div class="mt-8">
                <button
                  type="submit"
                  :disabled="updating"
                  class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-semibold"
                >
                  <div v-if="updating" class="flex items-center">
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                    Aggiornamento...
                  </div>
                  <span v-else>💾 Salva Modifiche</span>
                </button>
              </div>
            </form>
          </div>

          <!-- Security Tab -->
          <div v-else-if="activeTab === 'security'" class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">🔒 Sicurezza</h2>
            
            <div class="space-y-6">
              <!-- Change Password -->
              <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Cambia Password</h3>
                <form @submit.prevent="changePassword">
                  <div class="space-y-4">
                    <div>
                      <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password Attuale
                      </label>
                      <input
                        v-model="passwordForm.current_password"
                        id="current_password"
                        type="password"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      />
                    </div>
                    
                    <div>
                      <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Nuova Password
                      </label>
                      <input
                        v-model="passwordForm.new_password"
                        id="new_password"
                        type="password"
                        required
                        minlength="8"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      />
                    </div>
                    
                    <div>
                      <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Conferma Nuova Password
                      </label>
                      <input
                        v-model="passwordForm.new_password_confirmation"
                        id="new_password_confirmation"
                        type="password"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      />
                    </div>
                  </div>
                  
                  <button
                    type="submit"
                    :disabled="changingPassword"
                    class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                  >
                    <div v-if="changingPassword" class="flex items-center">
                      <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                      Aggiornamento...
                    </div>
                    <span v-else>🔒 Cambia Password</span>
                  </button>
                </form>
              </div>

              <!-- Account Info -->
              <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informazioni Account</h3>
                <div class="space-y-3 text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Account creato:</span>
                    <span class="font-medium">{{ formatDate(authStore.user?.created_at) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Ultimo accesso:</span>
                    <span class="font-medium">{{ formatDate(authStore.user?.updated_at) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Ruolo:</span>
                    <span class="font-medium">
                      {{ authStore.isAdmin ? 'Amministratore' : authStore.isBusiness ? 'Business' : 'Utente' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Orders Tab -->
          <div v-else-if="activeTab === 'orders'" class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">📦 I Miei Ordini</h2>
            
            <div class="text-center py-12 text-gray-500">
              <div class="text-6xl mb-4">📦</div>
              <h3 class="text-lg font-medium mb-2">Nessun ordine ancora</h3>
              <p class="mb-4">Quando effettuerai il tuo primo ordine, lo vedrai qui.</p>
              <router-link 
                to="/products" 
                class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors inline-block"
              >
                🛍️ Inizia Shopping
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- Messages -->
      <div
        v-if="successMessage"
        class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
      >
        {{ successMessage }}
      </div>
      
      <div
        v-if="errorMessage"
        class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
      >
        {{ errorMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// State
const activeTab = ref('profile')
const updating = ref(false)
const changingPassword = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const errors = ref({})

const profileForm = reactive({
  nome: '',
  cognome: '',
  email: '',
  telefono: '',
  data_nascita: '',
  citta_residenza: '',
  titolo_studio: '',
  professione: ''
})

const passwordForm = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

// Methods
const loadProfile = () => {
  if (authStore.user) {
    Object.keys(profileForm).forEach(key => {
      profileForm[key] = authStore.user[key] || ''
    })
  }
}

const updateProfile = async () => {
  try {
    updating.value = true
    errors.value = {}
    errorMessage.value = ''

    const result = await authStore.updateProfile(profileForm)

    if (result.success) {
      showSuccess('Profilo aggiornato con successo!')
    } else {
      showError(result.message)
    }
  } catch (error) {
    console.error('Errore aggiornamento profilo:', error)
    showError('Errore durante l\'aggiornamento del profilo')
  } finally {
    updating.value = false
  }
}

const changePassword = async () => {
  if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
    showError('Le nuove password non coincidono')
    return
  }

  if (passwordForm.new_password.length < 8) {
    showError('La nuova password deve essere di almeno 8 caratteri')
    return
  }

  try {
    changingPassword.value = true
    
    // Simulate password change (implement API call)
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    showSuccess('Password cambiata con successo!')
    
    // Reset form
    Object.keys(passwordForm).forEach(key => {
      passwordForm[key] = ''
    })
    
  } catch (error) {
    console.error('Errore cambio password:', error)
    showError('Errore durante il cambio password')
  } finally {
    changingPassword.value = false
  }
}

const logout = async () => {
  if (confirm('Sei sicuro di voler uscire?')) {
    try {
      await authStore.logout()
      router.push('/')
    } catch (error) {
      console.error('Errore logout:', error)
    }
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('it-IT', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const showSuccess = (message) => {
  successMessage.value = message
  setTimeout(() => successMessage.value = '', 3000)
}

const showError = (message) => {
  errorMessage.value = message
  setTimeout(() => errorMessage.value = '', 5000)
}

// Lifecycle
onMounted(() => {
  loadProfile()
})
</script>

<style scoped>
/* Custom select styling */
select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
}

/* Smooth transitions */
.transition-colors {
  transition-property: color, background-color, border-color;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
</style>