<template>
  <div class="profile-page">
    <div class="profile-container">
      
      <!-- Header -->
      <div class="profile-header">
        <div class="user-avatar">
          <span>{{ getInitials() }}</span>
        </div>
        <div class="user-info">
          <h1>{{ authStore.userFullName }}</h1>
          <p>{{ authStore.user?.email }}</p>
          <div class="user-badge">
            <span v-if="authStore.isAdmin" class="badge badge-admin">👑 Admin</span>
            <span v-else-if="authStore.isBusiness" class="badge badge-business">💼 Business</span>
            <span v-else class="badge badge-user">👤 Utente</span>
          </div>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="profile-tabs">
        <button 
          @click="activeTab = 'profile'" 
          :class="{ active: activeTab === 'profile' }"
          class="tab-button"
        >
          📝 Dati Personali
        </button>
        <button 
          @click="activeTab = 'security'" 
          :class="{ active: activeTab === 'security' }"
          class="tab-button"
        >
          🔒 Sicurezza
        </button>
        <button 
          @click="activeTab = 'orders'" 
          :class="{ active: activeTab === 'orders' }"
          class="tab-button"
        >
          📦 Ordini
        </button>
      </div>

      <!-- Tab Content: Dati Personali -->
      <div v-if="activeTab === 'profile'" class="tab-content">
        <h2>📝 Informazioni Personali</h2>
        <p class="tab-subtitle">Aggiorna i tuoi dati anagrafici</p>

        <form @submit.prevent="updateProfile" class="profile-form">
          <!-- Nome e Cognome -->
          <div class="form-row">
            <div class="form-group">
              <label for="nome">Nome *</label>
              <input
                v-model="profileForm.nome"
                id="nome"
                type="text"
                required
                placeholder="Il tuo nome"
              />
              <span v-if="errors.nome" class="error-message">{{ errors.nome }}</span>
            </div>

            <div class="form-group">
              <label for="cognome">Cognome *</label>
              <input
                v-model="profileForm.cognome"
                id="cognome"
                type="text"
                required
                placeholder="Il tuo cognome"
              />
              <span v-if="errors.cognome" class="error-message">{{ errors.cognome }}</span>
            </div>
          </div>

          <!-- Email (readonly) -->
          <div class="form-group">
            <label for="email">📧 Email *</label>
            <input
              v-model="profileForm.email"
              id="email"
              type="email"
              readonly
              class="readonly-input"
            />
            <p class="help-text">Per cambiare email, vai alla sezione Sicurezza</p>
          </div>

          <!-- Titolo di Studi -->
          <div class="form-group">
            <label for="titolo_studi">🎓 Titolo di Studi <span class="optional">(opzionale)</span></label>
            <select v-model="profileForm.titolo_studi" id="titolo_studi">
              <option value="">-- Seleziona --</option>
              <option value="Licenza elementare">Licenza elementare</option>
              <option value="Licenza media">Licenza media</option>
              <option value="Diploma">Diploma</option>
              <option value="Laurea triennale">Laurea triennale</option>
              <option value="Laurea magistrale">Laurea magistrale</option>
              <option value="Master">Master</option>
              <option value="Dottorato">Dottorato</option>
            </select>
          </div>

          <!-- Data di nascita -->
          <div class="form-group">
            <label for="data_nascita">📅 Data di Nascita <span class="optional">(opzionale)</span></label>
            <input
              v-model="profileForm.data_nascita"
              id="data_nascita"
              type="date"
              :max="new Date().toISOString().split('T')[0]"
            />
          </div>

          <!-- Città di nascita -->
          <div class="form-group">
            <label for="citta_nascita">🏙️ Città di Nascita <span class="optional">(opzionale)</span></label>
            <input
              v-model="profileForm.citta_nascita"
              id="citta_nascita"
              type="text"
              placeholder="Es: Roma"
            />
          </div>

          <!-- Messages -->
          <div v-if="successMessage" class="alert alert-success">
            ✅ {{ successMessage }}
          </div>
          <div v-if="errorMessage" class="alert alert-error">
            ❌ {{ errorMessage }}
          </div>

          <!-- Submit Button -->
          <div class="form-actions">
            <button type="submit" class="btn-primary" :disabled="loading">
              <span v-if="loading">💾 Salvataggio...</span>
              <span v-else>💾 Salva Modifiche</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Tab Content: Sicurezza -->
      <div v-if="activeTab === 'security'" class="tab-content">
        <h2>🔒 Sicurezza</h2>
        <p class="tab-subtitle">Gestisci password e email</p>

        <!-- Change Password Section -->
        <div class="security-section">
          <h3>Cambia Password</h3>
          <form @submit.prevent="changePassword" class="profile-form">
            <div class="form-group">
              <label for="current_password">Password Attuale *</label>
              <input
                v-model="passwordForm.current_password"
                id="current_password"
                type="password"
                placeholder="La tua password attuale"
              />
            </div>

            <div class="form-group">
              <label for="new_password">Nuova Password *</label>
              <input
                v-model="passwordForm.new_password"
                id="new_password"
                type="password"
                placeholder="Minimo 8 caratteri"
              />
            </div>

            <div class="form-group">
              <label for="new_password_confirmation">Conferma Nuova Password *</label>
              <input
                v-model="passwordForm.new_password_confirmation"
                id="new_password_confirmation"
                type="password"
                placeholder="Ripeti la nuova password"
              />
            </div>

            <div v-if="passwordSuccess" class="alert alert-success">
              ✅ {{ passwordSuccess }}
            </div>
            <div v-if="passwordError" class="alert alert-error">
              ❌ {{ passwordError }}
            </div>

            <button type="submit" class="btn-primary" :disabled="loadingPassword">
              <span v-if="loadingPassword">🔄 Aggiornamento...</span>
              <span v-else>🔑 Cambia Password</span>
            </button>
          </form>
        </div>

        <!-- Change Email Section -->
        <div class="security-section">
          <h3>Cambia Email</h3>
          <form @submit.prevent="changeEmail" class="profile-form">
            <div class="form-group">
              <label for="new_email">Nuova Email *</label>
              <input
                v-model="emailForm.new_email"
                id="new_email"
                type="email"
                placeholder="nuova@email.com"
              />
            </div>

            <div class="form-group">
              <label for="password_confirm">Password (per conferma) *</label>
              <input
                v-model="emailForm.password"
                id="password_confirm"
                type="password"
                placeholder="La tua password"
              />
            </div>

            <div v-if="emailSuccess" class="alert alert-success">
              ✅ {{ emailSuccess }}
            </div>
            <div v-if="emailError" class="alert alert-error">
              ❌ {{ emailError }}
            </div>

            <button type="submit" class="btn-primary" :disabled="loadingEmail">
              <span v-if="loadingEmail">🔄 Aggiornamento...</span>
              <span v-else>📧 Cambia Email</span>
            </button>
          </form>
        </div>

      </div>

      <!-- Tab Content: Ordini -->
      <div v-if="activeTab === 'orders'" class="tab-content">
        <h2>📦 I Miei Ordini</h2>
        <p class="tab-subtitle">Visualizza lo storico dei tuoi acquisti</p>
        
        <!-- Disclaimer -->
        <div class="alert alert-info" style="background: #e3f2fd; border: 1px solid #2196f3; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
          <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 20px;">ℹ️</span>
            <div>
              <strong>Sito di Test</strong>
              <p style="margin: 4px 0 0 0; color: #1976d2;">Questo è un ambiente di dimostrazione. Non verranno effettuati ordini reali o addebiti.</p>
            </div>
          </div>
        </div>

        <div v-if="orders.length === 0" class="empty-state">
          <p class="empty-icon">📦</p>
          <p>Non hai ancora effettuato ordini</p>
          <router-link to="/products" class="btn-primary">Vai ai Prodotti</router-link>
        </div>

        <div v-else class="orders-list">
          <div v-for="order in orders" :key="order.id" class="order-card">
            <div class="order-header">
              <div>
                <strong>Ordine #{{ order.id }}</strong>
                <span class="order-date">{{ formatDate(order.created_at) }}</span>
              </div>
              <div class="order-total">€{{ order.total }}</div>
            </div>
            <div class="order-status">
              <span :class="`status-badge status-${order.status}`">
                {{ order.status }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Logout Button (bottom) -->
      <div class="profile-footer">
        <button @click="logout" class="btn-logout">
          🚪 Esci
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useAuthRedirect } from '../../composables/useAuthRedirect'
import axios from 'axios'

const router = useRouter()
const authStore = useAuthStore()

// Setup auth redirect watchers
useAuthRedirect()

// State
const activeTab = ref('profile')
const loading = ref(false)
const loadingPassword = ref(false)
const loadingEmail = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const passwordSuccess = ref('')
const passwordError = ref('')
const emailSuccess = ref('')
const emailError = ref('')
const orders = ref([])

// Form data
const profileForm = reactive({
  nome: '',
  cognome: '',
  email: '',
  titolo_studi: '',
  data_nascita: '',
  citta_nascita: ''
})

const passwordForm = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

const emailForm = reactive({
  new_email: '',
  password: ''
})

const errors = reactive({
  nome: '',
  cognome: '',
  email: ''
})

// Initialize form with user data
onMounted(() => {
  if (authStore.user) {
    profileForm.nome = authStore.user.nome || ''
    profileForm.cognome = authStore.user.cognome || ''
    profileForm.email = authStore.user.email || ''
    profileForm.titolo_studi = authStore.user.titolo_studi || ''
    profileForm.data_nascita = authStore.user.data_nascita || ''
    profileForm.citta_nascita = authStore.user.citta_nascita || ''
  }
})

// Get user initials for avatar
const getInitials = () => {
  if (!authStore.user) return '?'
  const nome = authStore.user.nome || ''
  const cognome = authStore.user.cognome || ''
  return `${nome.charAt(0)}${cognome.charAt(0)}`.toUpperCase()
}

// Update profile
const updateProfile = async () => {
  successMessage.value = ''
  errorMessage.value = ''
  errors.nome = ''
  errors.cognome = ''
  errors.email = ''
  
  try {
    loading.value = true
    
    const response = await axios.put('/auth/profile', {
      nome: profileForm.nome,
      cognome: profileForm.cognome,
      titolo_studi: profileForm.titolo_studi || null,
      data_nascita: profileForm.data_nascita || null,
      citta_nascita: profileForm.citta_nascita || null
    })
    
    // Update local store
    await authStore.fetchProfile()
    
    successMessage.value = 'Profilo aggiornato con successo!'
    
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)
    
  } catch (error) {
    console.error('Errore aggiornamento profilo:', error)
    
    if (error.response?.data?.errors) {
      const errs = error.response.data.errors
      errors.nome = errs.nome?.[0] || ''
      errors.cognome = errs.cognome?.[0] || ''
      errors.email = errs.email?.[0] || ''
    }
    
    errorMessage.value = error.response?.data?.message || 'Errore durante l\'aggiornamento del profilo'
  } finally {
    loading.value = false
  }
}

// Change password
const changePassword = async () => {
  passwordSuccess.value = ''
  passwordError.value = ''
  
  if (!passwordForm.current_password || !passwordForm.new_password || !passwordForm.new_password_confirmation) {
    passwordError.value = 'Compila tutti i campi'
    return
  }
  
  if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
    passwordError.value = 'Le nuove password non corrispondono'
    return
  }
  
  if (passwordForm.new_password.length < 8) {
    passwordError.value = 'La nuova password deve essere di almeno 8 caratteri'
    return
  }
  
  try {
    loadingPassword.value = true
    
    await axios.post('/auth/change-password', {
      current_password: passwordForm.current_password,
      new_password: passwordForm.new_password,
      new_password_confirmation: passwordForm.new_password_confirmation
    })
    
    passwordSuccess.value = 'Password cambiata con successo!'
    
    // Reset form
    passwordForm.current_password = ''
    passwordForm.new_password = ''
    passwordForm.new_password_confirmation = ''
    
    setTimeout(() => {
      passwordSuccess.value = ''
    }, 3000)
    
  } catch (error) {
    passwordError.value = error.response?.data?.message || 'Errore durante il cambio password'
  } finally {
    loadingPassword.value = false
  }
}

// Change email
const changeEmail = async () => {
  emailSuccess.value = ''
  emailError.value = ''
  
  if (!emailForm.new_email || !emailForm.password) {
    emailError.value = 'Compila tutti i campi'
    return
  }
  
  try {
    loadingEmail.value = true
    
    await axios.post('/auth/change-email', {
      new_email: emailForm.new_email,
      password: emailForm.password
    })
    
    emailSuccess.value = 'Email cambiata con successo! Effettua nuovamente il login.'
    
    // Logout after email change
    setTimeout(async () => {
      await authStore.logout()
      router.push('/login')
    }, 2000)
    
  } catch (error) {
    emailError.value = error.response?.data?.message || 'Errore durante il cambio email'
  } finally {
    loadingEmail.value = false
  }
}

// Format date
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('it-IT', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })
}

// Logout
const logout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.profile-page {
  min-height: 100vh;
  background: #f9fafb;
  padding: 2rem 1rem;
  font-family: 'Inter', Arial, sans-serif;
}

.profile-container {
  max-width: 900px;
  margin: 0 auto;
}

/* Header */
.profile-header {
  background: #fff;
  border-radius: 8px;
  padding: 2rem;
  margin-bottom: 2rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.user-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: 700;
  color: #fff;
  flex-shrink: 0;
}

.user-info h1 {
  font-size: 1.75rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.25rem 0;
}

.user-info p {
  font-size: 1rem;
  color: #6b7280;
  margin: 0 0 0.75rem 0;
}

.user-badge {
  display: flex;
  gap: 0.5rem;
}

.badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 999px;
  font-size: 0.875rem;
  font-weight: 500;
}

.badge-admin {
  background: #fee2e2;
  color: #991b1b;
}

.badge-business {
  background: #ede9fe;
  color: #5b21b6;
}

.badge-user {
  background: #dbeafe;
  color: #1e40af;
}

/* Tabs */
.profile-tabs {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  border-bottom: 2px solid #e5e7eb;
  overflow-x: auto;
}

.tab-button {
  padding: 1rem 1.5rem;
  background: none;
  border: none;
  border-bottom: 3px solid transparent;
  font-size: 1rem;
  font-weight: 500;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.tab-button:hover {
  color: #2563eb;
}

.tab-button.active {
  color: #2563eb;
  border-bottom-color: #2563eb;
}

/* Tab Content */
.tab-content {
  background: #fff;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.tab-content h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
}

.tab-subtitle {
  font-size: 1rem;
  color: #6b7280;
  margin: 0 0 2rem 0;
}

/* Form */
.profile-form {
  max-width: 600px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  font-size: 0.95rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-group label .optional {
  font-weight: 400;
  color: #9ca3af;
  font-size: 0.875rem;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 1rem;
  color: #1f2937;
  transition: border-color 0.2s;
  background: #fff;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.readonly-input {
  background: #f3f4f6 !important;
  cursor: not-allowed;
}

.help-text {
  font-size: 0.875rem;
  color: #6b7280;
  margin-top: 0.5rem;
}

.error-message {
  display: block;
  margin-top: 0.5rem;
  font-size: 0.875rem;
  color: #ef4444;
}

/* Alerts */
.alert {
  padding: 1rem;
  border-radius: 6px;
  margin-bottom: 1.5rem;
  font-size: 0.95rem;
}

.alert-success {
  background: #d1fae5;
  color: #065f46;
  border: 1px solid #10b981;
}

.alert-error {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #ef4444;
}

/* Buttons */
.form-actions {
  padding-top: 1rem;
}

.btn-primary,
.btn-secondary,
.btn-logout {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background: #2563eb;
  color: #fff;
}

.btn-primary:hover:not(:disabled) {
  background: #1d4ed8;
}

.btn-primary:disabled {
  background: #9ca3af;
  cursor: not-allowed;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
  border: 1px solid #d1d5db;
}

.btn-secondary:hover:not(:disabled) {
  background: #e5e7eb;
}

.btn-logout {
  background: #fee2e2;
  color: #991b1b;
  width: 100%;
}

.btn-logout:hover {
  background: #fecaca;
}

/* Security Sections */
.security-section {
  padding: 2rem;
  background: #f9fafb;
  border-radius: 8px;
  margin-bottom: 2rem;
}

.security-section h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 1rem 0;
}

.mt-3 {
  margin-top: 1rem;
}

/* Orders */
.empty-state {
  text-align: center;
  padding: 3rem 1rem;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.empty-state p {
  font-size: 1.125rem;
  color: #6b7280;
  margin-bottom: 1.5rem;
}

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.order-card {
  padding: 1.5rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #fff;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.order-date {
  display: block;
  font-size: 0.875rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

.order-total {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 999px;
  font-size: 0.875rem;
  font-weight: 500;
}

.status-completed {
  background: #d1fae5;
  color: #065f46;
}

.status-pending {
  background: #fef3c7;
  color: #92400e;
}

/* Footer */
.profile-footer {
  margin-top: 2rem;
}

/* Responsive */
@media (max-width: 768px) {
  .profile-header {
    flex-direction: column;
    text-align: center;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .profile-tabs {
    gap: 0.5rem;
  }
  
  .tab-button {
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
  }
}
</style>