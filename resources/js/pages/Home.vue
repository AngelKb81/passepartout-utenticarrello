<template>
  <!-- Pagina Home: contenuto pulito senza header/footer duplicati -->
  <div class="home-page">
    <!-- Hero: sezione presentazione principale -->
    <Hero />

    <!-- Sezione Caratteristiche -->
    <section class="features-section">
      <div class="features-container">
        <h2>Caratteristiche principali</h2>
        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon">
              <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
            </div>
            <h3>Gestione Prodotti</h3>
            <p>Catalogo completo e facile da navigare con filtri avanzati per categoria</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">
              <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <h3>Carrello Intelligente</h3>
            <p>Sistema avanzato che sincronizza automaticamente i tuoi acquisti tra sessioni</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">
              <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
            </div>
            <h3>Dashboard Admin</h3>
            <p>Pannello di controllo completo con statistiche, grafici e gestione utenti</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Sezione CTA secondaria -->
    <section class="cta-section">
      <div class="cta-container">
        <h2>Pronto per iniziare?</h2>
        <p>Unisciti alla nostra piattaforma e scopri tutti i vantaggi di un sistema moderno e intuitivo.</p>
        <div class="cta-actions">
          <router-link 
            v-if="!authStore.isAuthenticated" 
            to="/register" 
            class="btn-primary-large"
          >
            Registrati ora
          </router-link>
          <router-link 
            v-else 
            to="/products" 
            class="btn-primary-large"
          >
            Esplora i prodotti
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
// Import componenti
import Hero from '../components/Hero.vue'

// Import store (se necessario per logica futura)
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
</script>

<style scoped>
/* Pagina Home: reset e layout base */
.home-page {
  display: flex;
  flex-direction: column;
  background: #ffffff;
  font-family: 'Inter', Arial, sans-serif;
  color: #1a1a1a;
}

/* Sezione caratteristiche */
.features-section {
  padding: 4rem 2rem;
  background: #ffffff;
}

.features-container {
  max-width: 1200px;
  margin: 0 auto;
  text-align: center;
}

.features-container h2 {
  font-size: 2.25rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 3rem;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.feature-card {
  padding: 2.5rem 2rem;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  transition: all 0.3s ease;
  text-align: center;
}

.feature-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
  border-color: #d1d5db;
}

.feature-icon {
  margin: 0 auto 1.5rem auto;
  width: 64px;
  height: 64px;
  background: rgba(99, 102, 241, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.feature-card h3 {
  font-size: 1.375rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1rem;
}

.feature-card p {
  color: #6b7280;
  font-size: 1rem;
  line-height: 1.6;
}

/* Sezione CTA secondaria */
.cta-section {
  padding: 4rem 2rem;
  background: #f3f4f6;
}

.cta-container {
  max-width: 800px;
  margin: 0 auto;
  text-align: center;
}

.cta-container h2 {
  font-size: 2rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 1rem;
}

.cta-container p {
  font-size: 1.125rem;
  color: #6b7280;
  margin-bottom: 2rem;
  line-height: 1.6;
}

.cta-actions {
  display: flex;
  justify-content: center;
}

.btn-primary-large {
  padding: 1rem 2.5rem;
  background: #2563eb;
  color: #ffffff;
  text-decoration: none;
  border-radius: 6px;
  font-weight: 600;
  font-size: 1.125rem;
  transition: background 0.2s ease;
  display: inline-block;
}

.btn-primary-large:hover {
  background: #1d4ed8;
}

/* Responsive: mobile */
@media (max-width: 768px) {
  .features-section {
    padding: 3rem 1.5rem;
  }

  .features-container h2 {
    font-size: 1.875rem;
  }

  .features-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .feature-card {
    padding: 2rem 1.5rem;
  }

  .cta-section {
    padding: 3rem 1.5rem;
  }

  .cta-container h2 {
    font-size: 1.75rem;
  }

  .cta-container p {
    font-size: 1rem;
  }

  .btn-primary-large {
    width: 100%;
    max-width: 300px;
  }
}
</style>
