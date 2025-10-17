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
        
        <!-- Nome e Cognome -->
        <div class="form-row">
          <div class="form-group">
            <label for="nome">Nome</label>
            <input
              v-model="form.nome"
              id="nome"
              type="text"
              placeholder="Mario"
              required
              autocomplete="given-name"
            />
            <span v-if="errors.nome" class="error-message">{{ errors.nome }}</span>
          </div>

          <div class="form-group">
            <label for="cognome">Cognome</label>
            <input
              v-model="form.cognome"
              id="cognome"
              type="text"
              placeholder="Rossi"
              required
              autocomplete="family-name"
            />
            <span v-if="errors.cognome" class="error-message">{{ errors.cognome }}</span>
          </div>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label for="email">Email</label>
          <input
            v-model="form.email"
            id="email"
            type="email"
            placeholder="tua@email.com"
            required
            autocomplete="email"
          />
          <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
        </div>

        <!-- Titolo di studi (opzionale) -->
        <div class="form-group">
          <label for="titolo_studi">Titolo di Studi <span class="optional">(opzionale)</span></label>
          <select v-model="form.titolo_studi" id="titolo_studi">
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

        <!-- Data di nascita (opzionale) -->
        <div class="form-group">
          <label for="data_nascita">Data di Nascita <span class="optional">(opzionale)</span></label>
          <input
            v-model="form.data_nascita"
            id="data_nascita"
            type="date"
            :max="new Date().toISOString().split('T')[0]"
          />
        </div>

        <!-- Città di nascita (opzionale) -->
        <div class="form-group">
          <label for="citta_nascita">Città di Nascita <span class="optional">(opzionale)</span></label>
          <input
            v-model="form.citta_nascita"
            id="citta_nascita"
            type="text"
            placeholder="Es: Roma"
          />
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
            <span>Accetto i <a href="#" @click.prevent="showTerms = true">termini e condizioni</a></span>
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

    <!-- Modal Termini e Condizioni -->
    <div v-if="showTerms" class="modal-overlay" @click="showTerms = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>Termini e Condizioni</h2>
          <button @click="showTerms = false" class="modal-close">✕</button>
        </div>
        <div class="modal-body">
          <h3>1. Accettazione dei Termini</h3>
          <p>Utilizzando questo servizio, accetti di essere vincolato dai presenti termini e condizioni. Se non accetti questi termini, ti preghiamo di non utilizzare il servizio.</p>
          
          <h3>2. Descrizione del Servizio</h3>
          <p>UtentiCarrello è una piattaforma e-commerce che consente agli utenti di acquistare prodotti online. Ci impegniamo a fornire un servizio di qualità e a proteggere i tuoi dati personali.</p>
          
          <h3>3. Account Utente</h3>
          <p>Per utilizzare alcune funzionalità del servizio, potrebbe essere necessario creare un account. Sei responsabile di mantenere la confidenzialità delle tue credenziali di accesso.</p>
          
          <h3>4. Privacy e Protezione Dati</h3>
          <p>Raccogliamo e utilizziamo i tuoi dati personali in conformità con la nostra Informativa sulla Privacy e il GDPR. I tuoi dati non saranno venduti a terze parti.</p>
          
          <h3>5. Ordini e Pagamenti</h3>
          <p>Tutti gli ordini sono soggetti a disponibilità. I prezzi possono variare senza preavviso. I pagamenti vengono elaborati in modo sicuro.</p>
          
          <h3>6. Diritto di Recesso</h3>
          <p>Hai il diritto di recedere dall'acquisto entro 14 giorni dalla ricezione dei prodotti, salvo eccezioni previste dalla legge.</p>
          
          <h3>7. Limitazione di Responsabilità</h3>
          <p>Il servizio è fornito "così com'è". Non ci assumiamo responsabilità per eventuali danni derivanti dall'uso del servizio.</p>
          
          <h3>8. Modifiche ai Termini</h3>
          <p>Ci riserviamo il diritto di modificare questi termini in qualsiasi momento. Le modifiche entreranno in vigore immediatamente dopo la pubblicazione.</p>
          
          <h3>9. Legge Applicabile</h3>
          <p>Questi termini sono regolati dalla legge italiana. Qualsiasi controversia sarà soggetta alla giurisdizione esclusiva dei tribunali italiani.</p>
          
          <h3>10. Contatti</h3>
          <p>Per domande sui termini e condizioni, contattaci all'indirizzo: info@utenticarrello.it</p>
        </div>
        <div class="modal-footer">
          <button @click="showTerms = false" class="btn-primary">Ho Capito</button>
        </div>
      </div>
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
const showTerms = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

// Form data
const form = reactive({
  nome: '',
  cognome: '',
  email: '',
  titolo_studi: '',
  data_nascita: '',
  citta_nascita: '',
  password: '',
  password_confirmation: '',
  accept_terms: false
})

// Errori validazione
const errors = reactive({
  nome: '',
  cognome: '',
  email: '',
  password: '',
  password_confirmation: ''
})

// Validazione client-side
const validateForm = () => {
  // Reset errori
  errors.nome = ''
  errors.cognome = ''
  errors.email = ''
  errors.password = ''
  errors.password_confirmation = ''
  
  let isValid = true
  
  // Validazione nome
  if (!form.nome || form.nome.trim().length < 2) {
    errors.nome = 'Nome troppo corto (minimo 2 caratteri)'
    isValid = false
  }
  
  // Validazione cognome
  if (!form.cognome || form.cognome.trim().length < 2) {
    errors.cognome = 'Cognome troppo corto (minimo 2 caratteri)'
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
    
    // DEBUG: Log dei dati inviati
    const dataToSend = {
      nome: form.nome,
      cognome: form.cognome,
      email: form.email,
      titolo_studi: form.titolo_studi || null,
      data_nascita: form.data_nascita || null,
      citta_nascita: form.citta_nascita || null,
      password: form.password,
      password_confirmation: form.password_confirmation
    }
    console.log('📤 Dati inviati al backend:', dataToSend)
    
    // Chiamata API tramite store
    const result = await authStore.register(dataToSend)
    
    if (result.success) {
      successMessage.value = 'Registrazione completata! Reindirizzamento...'
      
      // Reindirizza dopo 500ms (il login è automatico nello store)
      setTimeout(() => {
        router.push('/products')
      }, 500)
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

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-row .form-group {
  margin-bottom: 0;
}

.form-group label {
  display: block;
  font-size: 0.95rem;
  font-weight: 500;
  color: #4b5563;
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
  border-radius: 4px;
  font-size: 1rem;
  color: #1f2937;
  transition: border-color 0.2s ease;
  background-color: #fff;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #2563eb;
}

.form-group select {
  cursor: pointer;
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

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-content {
  background: #ffffff;
  border-radius: 4px;
  max-width: 600px;
  width: 100%;
  max-height: 80vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.modal-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-header h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #6b7280;
  cursor: pointer;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  transition: background 0.2s ease;
}

.modal-close:hover {
  background: #f3f4f6;
}

.modal-body {
  padding: 1.5rem;
  overflow-y: auto;
  flex: 1;
}

.modal-body h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin-top: 1.5rem;
  margin-bottom: 0.5rem;
}

.modal-body h3:first-child {
  margin-top: 0;
}

.modal-body p {
  font-size: 0.95rem;
  color: #4b5563;
  line-height: 1.6;
  margin-bottom: 1rem;
}

.modal-footer {
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  justify-content: flex-end;
}

.modal-footer .btn-primary {
  width: auto;
  padding: 0.625rem 1.5rem;
}

/* Responsive */
@media (max-width: 640px) {
  .auth-header h1 {
    font-size: 1.75rem;
  }

  .auth-form {
    padding: 1.5rem;
  }

  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>
