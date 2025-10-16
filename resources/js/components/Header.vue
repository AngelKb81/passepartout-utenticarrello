<template>
  <!-- Header principale: logo e navigazione -->
  <header class="main-header">
    <nav class="nav-container">
      <!-- Logo -->
      <div class="logo-wrapper">
        <router-link to="/" class="logo-link">
          <span class="logo-text">UtentiCarrello</span>
        </router-link>
      </div>

      <!-- Menu principale -->
      <ul class="nav-menu">
        <li><router-link to="/">Home</router-link></li>
        <li><router-link to="/products">Prodotti</router-link></li>
        
        <!-- Link condizionali in base allo stato di autenticazione -->
        <template v-if="!authStore.isAuthenticated">
          <li><router-link to="/login">Accedi</router-link></li>
          <li><router-link to="/register" class="btn-register">Registrati</router-link></li>
        </template>
        
        <template v-else>
          <!-- Area utente autenticato -->
          <li><router-link to="/cart">Carrello ({{ cartStore.itemCount }})</router-link></li>
          <li v-if="authStore.isAdmin"><router-link to="/dashboard">Dashboard</router-link></li>
          <li>
            <button @click="handleLogout" class="btn-logout">Esci</button>
          </li>
        </template>
      </ul>
    </nav>
  </header>
</template>

<script setup>
import { useAuthStore } from '../stores/authStore'
import { useCartStore } from '../stores/cartStore'
import { useRouter } from 'vue-router'

// Store e router
const authStore = useAuthStore()
const cartStore = useCartStore()
const router = useRouter()

// Funzione per il logout
const handleLogout = async () => {
  await authStore.logout()
  router.push('/')
}
</script>

<style scoped>
/* Header fisso in alto */
.main-header {
  background: #ffffff;
  border-bottom: 1px solid #e5e7eb;
  position: sticky;
  top: 0;
  z-index: 100;
}

/* Container di navigazione */
.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

/* Logo */
.logo-wrapper {
  flex-shrink: 0;
}

.logo-text {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
  text-decoration: none;
}

/* Menu navigazione */
.nav-menu {
  display: flex;
  gap: 2rem;
  list-style: none;
  margin: 0;
  padding: 0;
  align-items: center;
}

.nav-menu a {
  color: #4b5563;
  text-decoration: none;
  font-weight: 500;
  font-size: 1rem;
  transition: color 0.2s ease;
}

.nav-menu a:hover {
  color: #1f2937;
}

/* Link attivo */
.nav-menu a.router-link-active {
  color: #2563eb;
}

/* Pulsante registrati */
.btn-register {
  padding: 0.5rem 1.25rem;
  background: #2563eb;
  color: #ffffff !important;
  border-radius: 4px;
  transition: background 0.2s ease;
}

.btn-register:hover {
  background: #1d4ed8;
}

/* Pulsante logout */
.btn-logout {
  padding: 0.5rem 1.25rem;
  background: #ef4444;
  color: #ffffff;
  border: none;
  border-radius: 4px;
  font-weight: 500;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s ease;
}

.btn-logout:hover {
  background: #dc2626;
}

/* Responsive: mobile */
@media (max-width: 768px) {
  .nav-container {
    flex-direction: column;
    gap: 1rem;
    padding: 1rem;
  }

  .nav-menu {
    flex-wrap: wrap;
    gap: 1rem;
    justify-content: center;
  }
}
</style>
