<template>
  <!-- Pagina Home: layout pulito e minimale -->
  <div class="home-page">
    <!-- Header con navigazione -->
    <Header />

    <!-- Hero: sezione presentazione principale -->
    <Hero />

    <!-- Sezione Caratteristiche (opzionale, commentata per massima semplicità) -->
    <!-- 
    <section class="features-section">
      <div class="features-container">
        <h2>Caratteristiche principali</h2>
        <div class="features-grid">
          <div class="feature-card">
            <h3>Gestione Prodotti</h3>
            <p>Catalogo completo e facile da navigare</p>
          </div>
          <div class="feature-card">
            <h3>Carrello Intuitivo</h3>
            <p>Aggiungi e gestisci i tuoi acquisti in modo semplice</p>
          </div>
          <div class="feature-card">
            <h3>Area Admin</h3>
            <p>Dashboard per amministratori con statistiche essenziali</p>
          </div>
        </div>
      </div>
    </section>
    -->

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
// Import componenti
import Header from '../components/Header.vue'
import Hero from '../components/Hero.vue'
import Footer from '../components/Footer.vue'

// Import store (se necessario per logica futura)
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
</script>

<style scoped>
/* Pagina Home: reset e layout base */
.home-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: #ffffff;
  font-family: 'Inter', Arial, sans-serif;
  color: #1a1a1a;
}

/* Sezione caratteristiche (opzionale, commentata nel template) */
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
  font-size: 2rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 3rem;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
}

.feature-card {
  padding: 2rem;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  transition: transform 0.2s ease;
}

.feature-card:hover {
  transform: translateY(-2px);
}

.feature-card h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.75rem;
}

.feature-card p {
  color: #6b7280;
  font-size: 1rem;
  line-height: 1.5;
}

/* Responsive: mobile */
@media (max-width: 768px) {
  .features-section {
    padding: 3rem 1.5rem;
  }

  .features-container h2 {
    font-size: 1.75rem;
  }
}
</style>
