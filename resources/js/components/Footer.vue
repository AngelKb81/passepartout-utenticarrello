<template>
  <!-- Footer: informazioni copyright e link utili -->
  <footer class="main-footer">
    <div class="footer-content">
      <!-- Copyright -->
      <p class="footer-copyright">
        © {{ currentYear }} UtentiCarrello - Tutti i diritti riservati
      </p>

      <!-- Link opzionali -->
      <div class="footer-links">
        <a href="/privacy">Privacy Policy</a>
        <span class="separator">·</span>
        <a href="/terms">Termini di servizio</a>
        <span class="separator">·</span>
        <a href="/contact">Contatti</a>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue'

// Anno corrente dinamico
const currentYear = computed(() => new Date().getFullYear())
</script>

<style scoped>
/* Footer principale */
.main-footer {
  background: #f9fafb;
  border-top: 1px solid #e5e7eb;
  margin-top: 4rem;
  padding: 2rem 1.5rem;
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
  text-align: center;
}

/* Copyright */
.footer-copyright {
  color: #6b7280;
  font-size: 0.95rem;
  margin: 0 0 1rem 0;
}

/* Link footer */
.footer-links {
  display: flex;
  gap: 0.75rem;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
}

.footer-links a {
  color: #6b7280;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.2s ease;
}

.footer-links a:hover {
  color: #2563eb;
}

/* Separatore */
.separator {
  color: #d1d5db;
  user-select: none;
}

/* Responsive: mobile */
@media (max-width: 768px) {
  .footer-links {
    flex-direction: column;
    gap: 0.5rem;
  }

  .separator {
    display: none;
  }
}
</style>
