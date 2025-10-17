<template>
  <div class="forgot-password-page">
    <div class="forgot-password-container">
      
      <!-- Logo/Header -->
      <div class="page-header">
        <router-link to="/" class="back-link">
          ← Torna alla home
        </router-link>
        <h1>🔑 Password Dimenticata?</h1>
        <p class="subtitle">Nessun problema! Inserisci la tua email e ti invieremo le istruzioni per reimpostare la password.</p>
      </div>

      <!-- Form Reset Password -->
      <div v-if="!emailSent" class="reset-form-card">
        <form @submit.prevent="sendResetLink" class="reset-form">
          <div class="form-group">
            <label for="email">📧 Indirizzo Email</label>
            <input
              v-model="email"
              id="email"
              type="email"
              required
              placeholder="tua@email.com"
              :disabled="loading"
            />
            <p class="help-text">Inserisci l'email che hai usato per registrarti</p>
          </div>

          <div v-if="errorMessage" class="alert alert-error">
            ❌ {{ errorMessage }}
          </div>

          <button type="submit" class="btn-primary" :disabled="loading">
            <span v-if="loading">📧 Invio in corso...</span>
            <span v-else>📧 Invia Link di Reset</span>
          </button>
        </form>

        <div class="form-footer">
          <p>Ti sei ricordato la password?</p>
          <router-link to="/login" class="link-primary">
            Vai al Login
          </router-link>
        </div>
      </div>

      <!-- Messaggio di Successo -->
      <div v-else class="success-card">
        <div class="success-icon">✅</div>
        <h2>Email Inviata!</h2>
        <p class="success-message">
          Abbiamo inviato le istruzioni per il reset della password a:<br>
          <strong>{{ email }}</strong>
        </p>
        <div class="info-box">
          <p><strong>Controlla la tua casella di posta!</strong></p>
          <ul>
            <li>📧 Riceverai un'email con il link per reimpostare la password</li>
            <li>⏰ Il link è valido per 60 minuti</li>
            <li>📂 Se non vedi l'email, controlla la cartella SPAM</li>
          </ul>
        </div>

        <div class="actions">
          <button @click="resetForm" class="btn-secondary">
            🔄 Invia di Nuovo
          </button>
          <router-link to="/login" class="btn-primary">
            🔑 Vai al Login
          </router-link>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

// State
const email = ref('')
const loading = ref(false)
const emailSent = ref(false)
const errorMessage = ref('')

// Send reset link
const sendResetLink = async () => {
  errorMessage.value = ''
  
  if (!email.value) {
    errorMessage.value = 'Inserisci un indirizzo email valido'
    return
  }
  
  try {
    loading.value = true
    
    await axios.post('/auth/forgot-password', {
      email: email.value
    })
    
    emailSent.value = true
    
  } catch (error) {
    console.error('Errore reset password:', error)
    errorMessage.value = error.response?.data?.message || 'Errore durante l\'invio dell\'email. Riprova.'
  } finally {
    loading.value = false
  }
}

// Reset form
const resetForm = () => {
  emailSent.value = false
  email.value = ''
  errorMessage.value = ''
}
</script>

<style scoped>
.forgot-password-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
  font-family: 'Inter', Arial, sans-serif;
}

.forgot-password-container {
  max-width: 500px;
  width: 100%;
}

/* Header */
.page-header {
  text-align: center;
  color: #fff;
  margin-bottom: 2rem;
}

.back-link {
  display: inline-block;
  color: rgba(255, 255, 255, 0.9);
  text-decoration: none;
  font-size: 0.95rem;
  margin-bottom: 1rem;
  transition: color 0.2s;
}

.back-link:hover {
  color: #fff;
}

.page-header h1 {
  font-size: 2rem;
  font-weight: 700;
  margin: 0 0 1rem 0;
}

.subtitle {
  font-size: 1rem;
  line-height: 1.6;
  color: rgba(255, 255, 255, 0.95);
  max-width: 400px;
  margin: 0 auto;
}

/* Cards */
.reset-form-card,
.success-card {
  background: #fff;
  border-radius: 12px;
  padding: 2.5rem;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

/* Form */
.reset-form {
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  font-size: 0.95rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-group input {
  width: 100%;
  padding: 0.875rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.2s;
}

.form-group input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group input:disabled {
  background: #f9fafb;
  cursor: not-allowed;
}

.help-text {
  font-size: 0.875rem;
  color: #6b7280;
  margin-top: 0.5rem;
}

/* Alert */
.alert {
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  font-size: 0.95rem;
}

.alert-error {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #ef4444;
}

/* Buttons */
.btn-primary,
.btn-secondary {
  width: 100%;
  padding: 0.875rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
  border: 2px solid #e5e7eb;
  margin-bottom: 1rem;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

/* Form Footer */
.form-footer {
  text-align: center;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.form-footer p {
  font-size: 0.95rem;
  color: #6b7280;
  margin-bottom: 0.5rem;
}

.link-primary {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s;
}

.link-primary:hover {
  color: #764ba2;
}

/* Success Card */
.success-card {
  text-align: center;
}

.success-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
  animation: scaleIn 0.5s ease-out;
}

@keyframes scaleIn {
  from {
    transform: scale(0);
  }
  to {
    transform: scale(1);
  }
}

.success-card h2 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 1rem 0;
}

.success-message {
  font-size: 1rem;
  color: #4b5563;
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.success-message strong {
  color: #667eea;
}

/* Info Box */
.info-box {
  background: #f0f9ff;
  border: 2px solid #bfdbfe;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  text-align: left;
}

.info-box p {
  font-weight: 600;
  color: #1e40af;
  margin: 0 0 0.75rem 0;
}

.info-box ul {
  margin: 0;
  padding-left: 1.5rem;
}

.info-box li {
  color: #1e3a8a;
  line-height: 1.8;
  font-size: 0.95rem;
}

/* Actions */
.actions {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

/* Responsive */
@media (max-width: 640px) {
  .forgot-password-page {
    padding: 1rem;
  }
  
  .reset-form-card,
  .success-card {
    padding: 1.5rem;
  }
  
  .page-header h1 {
    font-size: 1.5rem;
  }
}
</style>
