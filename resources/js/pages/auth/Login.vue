<template>
  <!-- Pagina Login: form di accesso minimale -->
  <div class="auth-page">
    <div class="auth-container">
      
      <!-- Logo e titolo -->
      <header class="auth-header">
        <router-link to="/" class="logo">UtentiCarrello</router-link>
        <h1>Accedi</h1>
        <p>Benvenuto! Inserisci le tue credenziali</p>
      </header>

      <!-- Form login -->
      <form @submit.prevent="handleLogin" class="auth-form">
        
        <!-- Email -->
        <div class="form-group">
          <label for="email">Email</label>
          <input
            v-model="form.email"
            id="email"
            type="email"
            placeholder="tuaemail@esempio.it"
            required
            autocomplete="email"
          />
          <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label for="password">Password</label>
          <div class="password-wrapper">
            <input
              v-model="form.password"
              id="password"
              :type="showPassword ? 'text' : 'password'"
              placeholder="Inserisci la tua password"
              required
              autocomplete="current-password"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="toggle-password"
            >
              {{ showPassword ? '👁️' : '👁️‍🗨️' }}
            </button>
          </div>
          <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
        </div>

        <!-- Ricordami e Password Dimenticata -->
        <div class="form-extras">
          <label class="checkbox-label">
            <input v-model="form.remember" type="checkbox" />
            <span>Ricordami</span>
          </label>
          <router-link to="/forgot-password" class="forgot-password-link">
            Password dimenticata?
          </router-link>
        </div>

        <!-- Messaggio di errore globale -->
        <div v-if="errorMessage" class="alert alert-error">
          {{ errorMessage }}
        </div>

        <!-- Messaggio di successo -->
        <div v-if="successMessage" class="alert alert-success">
          {{ successMessage }}
        </div>

        <!-- Pulsante submit -->
        <button type="submit" class="btn-primary" :disabled="loading">
          <span v-if="loading">Accesso in corso...</span>
          <span v-else>Accedi</span>
        </button>

        <!-- Link registrazione -->
        <p class="form-footer">
          Non hai un account?
          <router-link to="/register">Registrati</router-link>
        </p>
      </form>

    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

// Router e store
const router = useRouter()
const authStore = useAuthStore()

// State
const loading = ref(false)
const showPassword = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

// Form data
const form = reactive({
  email: '',
  password: '',
  remember: false
})

// Errori validazione
const errors = reactive({
  email: '',
  password: ''
})

// Validazione client-side
const validateForm = () => {
  errors.email = ''
  errors.password = ''
  
  if (!form.email) {
    errors.email = 'Email richiesta'
    return false
  }
  
  if (!form.email.includes('@')) {
    errors.email = 'Email non valida'
    return false
  }
  
  if (!form.password) {
    errors.password = 'Password richiesta'
    return false
  }
  
  if (form.password.length < 6) {
    errors.password = 'Password troppo corta (min 6 caratteri)'
    return false
  }
  
  return true
}

// Gestione login
const handleLogin = async () => {
  // Reset messaggi
  errorMessage.value = ''
  successMessage.value = ''
  
  // Validazione
  if (!validateForm()) {
    return
  }
  
  try {
    loading.value = true
    
    // Chiamata API tramite store
    const result = await authStore.login(form.email, form.password, form.remember)
    
    if (result.success) {
      successMessage.value = 'Accesso effettuato con successo!'
      
      // Reindirizza dopo 500ms
      setTimeout(() => {
        // Se è admin vai alla dashboard, altrimenti ai prodotti
        if (authStore.isAdmin) {
          router.push('/admin/dashboard')
        } else {
          router.push('/products')
        }
      }, 500)
    } else {
      errorMessage.value = result.message || 'Credenziali non valide'
    }
    
  } catch (error) {
    console.error('Errore login:', error)
    errorMessage.value = error.response?.data?.message || 'Errore durante il login. Riprova.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Pagina autenticazione */
.auth-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f9fafb;
  padding: 2rem 1rem;
  font-family: 'Inter', Arial, sans-serif;
}

.auth-container {
  width: 100%;
  max-width: 420px;
}

/* Header */
.auth-header {
  text-align: center;
  margin-bottom: 2rem;
}

.logo {
  display: inline-block;
  font-size: 1.5rem;
  font-weight: 700;
  color: #2563eb;
  text-decoration: none;
  margin-bottom: 1.5rem;
}

.auth-header h1 {
  font-size: 2rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.auth-header p {
  font-size: 1rem;
  color: #6b7280;
}

/* Form */
.auth-form {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  padding: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  font-size: 0.95rem;
  font-weight: 500;
  color: #4b5563;
  margin-bottom: 0.5rem;
}

.form-group input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  font-size: 1rem;
  color: #1f2937;
  transition: border-color 0.2s ease;
}

.form-group input:focus {
  outline: none;
  border-color: #2563eb;
}

.form-group input::placeholder {
  color: #9ca3af;
}

/* Password con toggle */
.password-wrapper {
  position: relative;
}

.password-wrapper input {
  padding-right: 3rem;
}

.toggle-password {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  font-size: 1.25rem;
  cursor: pointer;
  padding: 0;
}

/* Errori */
.error-message {
  display: block;
  margin-top: 0.375rem;
  font-size: 0.875rem;
  color: #ef4444;
}

/* Extras (checkbox e forgot password) */
.form-extras {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  cursor: pointer;
}

.checkbox-label input {
  margin-right: 0.5rem;
  width: auto;
}

.checkbox-label span {
  font-size: 0.95rem;
  color: #4b5563;
}

.forgot-password-link {
  font-size: 0.9rem;
  color: #2563eb;
  text-decoration: none;
  transition: color 0.2s;
}

.forgot-password-link:hover {
  color: #1d4ed8;
  text-decoration: underline;
}

/* Alert */
.alert {
  padding: 0.75rem 1rem;
  border-radius: 4px;
  margin-bottom: 1rem;
  font-size: 0.95rem;
}

.alert-error {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

.alert-success {
  background: #d1fae5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

/* Pulsante primario */
.btn-primary {
  width: 100%;
  padding: 0.875rem;
  background: #2563eb;
  color: #ffffff;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s ease;
}

.btn-primary:hover:not(:disabled) {
  background: #1d4ed8;
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Footer form */
.form-footer {
  margin-top: 1.5rem;
  text-align: center;
  font-size: 0.95rem;
  color: #6b7280;
}

.form-footer a {
  color: #2563eb;
  font-weight: 500;
  text-decoration: none;
  transition: color 0.2s ease;
}

.form-footer a:hover {
  color: #1d4ed8;
}

/* Responsive */
@media (max-width: 640px) {
  .auth-header h1 {
    font-size: 1.75rem;
  }

  .auth-form {
    padding: 1.5rem;
  }
}
</style>
