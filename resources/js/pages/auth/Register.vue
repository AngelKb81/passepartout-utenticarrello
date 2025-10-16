<template>
  <!-- Pagina Registrazione: form registrazione minimale -->
  <div class="auth-page">
    <div class="auth-container">
      
      <!-- Logo e titolo -->
      <header class="auth-header">
        <router-link to="/" class="logo">UtentiCarrello</router-link>
        <h1>Registrati</h1>
        <p>Crea il tuo account gratuito</p>
      </header>

      <!-- Form registrazione -->
      <form @submit.prevent="handleRegister" class="auth-form">
        
        <!-- Nome -->
        <div class="form-group">
          <label for="name">Nome completo</label>
          <input
            v-model="form.name"
            id="name"
            type="text"
            placeholder="Mario Rossi"
            required
            autocomplete="name"
          />
          <span v-if="errors.name" class="error-message">{{ errors.name }}</span>
        </div>

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
              placeholder="Minimo 8 caratteri"
              required
              autocomplete="new-password"
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

        <!-- Conferma Password -->
        <div class="form-group">
          <label for="password_confirmation">Conferma Password</label>
          <div class="password-wrapper">
            <input
              v-model="form.password_confirmation"
              id="password_confirmation"
              :type="showPasswordConfirm ? 'text' : 'password'"
              placeholder="Ripeti la password"
              required
              autocomplete="new-password"
            />
            <button
              type="button"
              @click="showPasswordConfirm = !showPasswordConfirm"
              class="toggle-password"
            >
              {{ showPasswordConfirm ? '👁️' : '👁️‍🗨️' }}
            </button>
          </div>
          <span v-if="errors.password_confirmation" class="error-message">{{ errors.password_confirmation }}</span>
        </div>

        <!-- Termini e condizioni -->
        <div class="form-extras">
          <label class="checkbox-label">
            <input v-model="form.accept_terms" type="checkbox" required />
            <span>Accetto i <a href="/terms" target="_blank">termini e condizioni</a></span>
          </label>
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
          <span v-if="loading">Registrazione in corso...</span>
          <span v-else>Crea Account</span>
        </button>

        <!-- Link login -->
        <p class="form-footer">
          Hai già un account?
          <router-link to="/login">Accedi</router-link>
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
const showPasswordConfirm = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

// Form data
const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  accept_terms: false
})

// Errori validazione
const errors = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

// Validazione client-side
const validateForm = () => {
  // Reset errori
  errors.name = ''
  errors.email = ''
  errors.password = ''
  errors.password_confirmation = ''
  
  let isValid = true
  
  // Validazione nome
  if (!form.name || form.name.trim().length < 3) {
    errors.name = 'Nome troppo corto (minimo 3 caratteri)'
    isValid = false
  }
  
  // Validazione email
  if (!form.email) {
    errors.email = 'Email richiesta'
    isValid = false
  } else if (!form.email.includes('@') || !form.email.includes('.')) {
    errors.email = 'Email non valida'
    isValid = false
  }
  
  // Validazione password
  if (!form.password) {
    errors.password = 'Password richiesta'
    isValid = false
  } else if (form.password.length < 8) {
    errors.password = 'Password troppo corta (minimo 8 caratteri)'
    isValid = false
  }
  
  // Validazione conferma password
  if (!form.password_confirmation) {
    errors.password_confirmation = 'Conferma password richiesta'
    isValid = false
  } else if (form.password !== form.password_confirmation) {
    errors.password_confirmation = 'Le password non corrispondono'
    isValid = false
  }
  
  // Validazione termini
  if (!form.accept_terms) {
    errorMessage.value = 'Devi accettare i termini e condizioni'
    isValid = false
  }
  
  return isValid
}

// Gestione registrazione
const handleRegister = async () => {
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
    const result = await authStore.register({
      name: form.name,
      email: form.email,
      password: form.password,
      password_confirmation: form.password_confirmation
    })
    
    if (result.success) {
      successMessage.value = 'Registrazione completata! Reindirizzamento...'
      
      // Reindirizza dopo 1 secondo
      setTimeout(() => {
        router.push('/products')
      }, 1000)
    } else {
      errorMessage.value = result.message || 'Errore durante la registrazione'
      
      // Mostra errori specifici per campo
      if (result.errors) {
        Object.keys(result.errors).forEach(key => {
          if (errors.hasOwnProperty(key)) {
            errors[key] = result.errors[key][0]
          }
        })
      }
    }
    
  } catch (error) {
    console.error('Errore registrazione:', error)
    errorMessage.value = error.response?.data?.message || 'Errore durante la registrazione. Riprova.'
    
    // Gestisci errori di validazione Laravel
    if (error.response?.data?.errors) {
      Object.keys(error.response.data.errors).forEach(key => {
        if (errors.hasOwnProperty(key)) {
          errors[key] = error.response.data.errors[key][0]
        }
      })
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Riutilizzo gli stili di Login.vue */
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
  max-width: 480px;
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

/* Extras (checkbox) */
.form-extras {
  margin-bottom: 1.5rem;
}

.checkbox-label {
  display: flex;
  align-items: flex-start;
  cursor: pointer;
}

.checkbox-label input {
  margin-right: 0.5rem;
  margin-top: 0.25rem;
  width: auto;
  flex-shrink: 0;
}

.checkbox-label span {
  font-size: 0.95rem;
  color: #4b5563;
  line-height: 1.4;
}

.checkbox-label a {
  color: #2563eb;
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
