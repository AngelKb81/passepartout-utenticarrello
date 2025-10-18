<template>
  <!-- Sezione Hero: presentazione principale del sito -->
  <section class="hero">
    <div class="hero-content">
      <!-- Titolo principale -->
      <h1 class="hero-title">
        Gestione Utenti e Carrello Semplificata
      </h1>

      <!-- Sottotitolo descrittivo -->
      <p class="hero-description">
        Una piattaforma moderna per gestire prodotti, utenti e ordini in modo efficiente. 
        Interfaccia pulita, funzionalità essenziali, nessuna complicazione.
      </p>

      <!-- Call to Action principale -->
      <div class="hero-actions">
        <router-link 
          v-if="!authStore.isAuthenticated" 
          to="/register" 
          class="btn-primary"
        >
          Inizia ora
        </router-link>
        
        <router-link 
          v-else 
          to="/products" 
          class="btn-primary"
        >
          Scopri i prodotti
        </router-link>

        <router-link 
          to="/products" 
          class="btn-secondary"
        >
          Esplora
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'

// Store autenticazione per controllo stato utente
const authStore = useAuthStore()
</script>

<style scoped>
/* Sezione Hero: centrata con ampio respiro */
.hero {
  background: #f9fafb;
  padding: 5rem 2rem 4rem 2rem;
  text-align: center;
}

.hero-content {
  max-width: 800px;
  margin: 0 auto;
}

/* Titolo principale */
.hero-title {
  font-size: 3rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 1.25rem 0;
  line-height: 1.2;
  font-family: 'Inter', Arial, sans-serif;
}

/* Descrizione */
.hero-description {
  font-size: 1.25rem;
  color: #6b7280;
  margin: 0 0 2rem 0;
  line-height: 1.6;
}

/* Contenitore pulsanti */
.hero-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

/* Pulsante primario */
.btn-primary {
  padding: 0.875rem 2rem;
  background: #2563eb;
  color: #ffffff;
  text-decoration: none;
  border-radius: 4px;
  font-weight: 500;
  font-size: 1.125rem;
  transition: background 0.2s ease;
  display: inline-block;
}

.btn-primary:hover {
  background: #1d4ed8;
}

/* Pulsante secondario */
.btn-secondary {
  padding: 0.875rem 2rem;
  background: #ffffff;
  color: #2563eb;
  text-decoration: none;
  border: 1px solid #2563eb;
  border-radius: 4px;
  font-weight: 500;
  font-size: 1.125rem;
  transition: all 0.2s ease;
  display: inline-block;
}

.btn-secondary:hover {
  background: #eff6ff;
}

/* Responsive: tablet */
@media (max-width: 1024px) {
  .hero {
    padding: 4rem 2rem 3rem 2rem;
  }

  .hero-title {
    font-size: 2.5rem;
  }

  .hero-description {
    font-size: 1.125rem;
  }
}

/* Responsive: mobile */
@media (max-width: 768px) {
  .hero {
    padding: 3rem 1.5rem 2.5rem 1.5rem;
  }

  .hero-title {
    font-size: 2rem;
  }

  .hero-description {
    font-size: 1rem;
  }

  .hero-actions {
    flex-direction: column;
    align-items: center;
  }

  .btn-primary,
  .btn-secondary {
    width: 100%;
    max-width: 300px;
  }
}
</style>
