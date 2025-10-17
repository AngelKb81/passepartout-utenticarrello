<template>
  <div class="reset-password-page">
    <div class="reset-password-container">
      <div class="reset-password-card">
        <div class="card-header">
          <h1>Reimposta Password</h1>
          <p>Inserisci la tua nuova password</p>
        </div>

        <!-- Success State -->
        <div v-if="success" class="success-state">
          <div class="success-icon">✓</div>
          <h2>Password Reimpostata!</h2>
          <p>La tua password è stata cambiata con successo.</p>
          <button @click="goToLogin" class="btn-primary">
            Vai al Login
          </button>
        </div>

        <!-- Form State -->
        <form v-else @submit.prevent="handleSubmit" class="reset-form">
          <div v-if="error" class="error-message">
            {{ error }}
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              readonly
              class="form-control"
            />
          </div>

          <div class="form-group">
            <label for="password">Nuova Password</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              minlength="8"
              class="form-control"
              placeholder="Minimo 8 caratteri"
            />
          </div>

          <div class="form-group">
            <label for="password_confirmation">Conferma Password</label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              required
              class="form-control"
              placeholder="Ripeti la password"
            />
          </div>

          <button 
            type="submit" 
            class="btn-submit"
            :disabled="loading"
          >
            <span v-if="loading">Attendere...</span>
            <span v-else>Reimposta Password</span>
          </button>

          <div class="form-footer">
            <router-link to="/login" class="back-link">
              ← Torna al Login
            </router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const route = useRoute();

const form = ref({
  email: '',
  password: '',
  password_confirmation: ''
});

const loading = ref(false);
const error = ref('');
const success = ref(false);

onMounted(() => {
  // Ottieni l'email dal parametro URL
  form.value.email = route.query.email || '';
  
  if (!form.value.email) {
    error.value = 'Email non specificata. Link non valido.';
  }
});

const handleSubmit = async () => {
  error.value = '';
  
  // Validazione client-side
  if (form.value.password.length < 8) {
    error.value = 'La password deve contenere almeno 8 caratteri';
    return;
  }
  
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'Le password non corrispondono';
    return;
  }
  
  loading.value = true;
  
  try {
    // Per ora usiamo l'endpoint di cambio password pubblico
    // TODO: Implementare sistema di token per reset password
    await axios.post('/auth/reset-password', {
      email: form.value.email,
      password: form.value.password,
      password_confirmation: form.value.password_confirmation
    });
    
    success.value = true;
  } catch (err) {
    if (err.response?.data?.message) {
      error.value = err.response.data.message;
    } else if (err.response?.data?.errors) {
      const errors = Object.values(err.response.data.errors).flat();
      error.value = errors.join(', ');
    } else {
      error.value = 'Errore durante il reset della password. Riprova.';
    }
  } finally {
    loading.value = false;
  }
};

const goToLogin = () => {
  router.push('/login');
};
</script>

<style scoped>
.reset-password-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.reset-password-container {
  width: 100%;
  max-width: 450px;
}

.reset-password-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  overflow: hidden;
}

.card-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 40px 30px;
  text-align: center;
}

.card-header h1 {
  margin: 0 0 10px 0;
  font-size: 28px;
  font-weight: 600;
}

.card-header p {
  margin: 0;
  opacity: 0.9;
  font-size: 16px;
}

.reset-form {
  padding: 40px 30px;
}

.success-state {
  padding: 60px 30px;
  text-align: center;
}

.success-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 30px;
  font-size: 48px;
  color: white;
  font-weight: bold;
}

.success-state h2 {
  margin: 0 0 15px 0;
  color: #333;
  font-size: 24px;
}

.success-state p {
  margin: 0 0 30px 0;
  color: #666;
  font-size: 16px;
}

.error-message {
  background: #fee;
  color: #c33;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 20px;
  border-left: 4px solid #c33;
  font-size: 14px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  color: #333;
  font-weight: 500;
  font-size: 14px;
}

.form-control {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 15px;
  transition: all 0.3s ease;
  box-sizing: border-box;
}

.form-control:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-control:read-only {
  background: #f5f5f5;
  cursor: not-allowed;
}

.btn-submit,
.btn-primary {
  width: 100%;
  padding: 14px 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 10px;
}

.btn-submit:hover:not(:disabled),
.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.form-footer {
  margin-top: 25px;
  text-align: center;
}

.back-link {
  color: #667eea;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: color 0.3s ease;
}

.back-link:hover {
  color: #764ba2;
  text-decoration: underline;
}
</style>
