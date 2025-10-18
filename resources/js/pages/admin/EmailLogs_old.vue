<template>
  <div class="email-logs-page">
    <div class="page-header">
      <h1>📧 Log Email Inviate</h1>
      <p>Monitora tutte le email inviate ai clienti</p>
    </div>

    <!-- Stats Cards -->
    <div v-if="stats" class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon total">📬</div>
        <div class="stat-info">
          <span class="stat-label">Totale Email</span>
          <span class="stat-value">{{ stats.total }}</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon sent">✅</div>
        <div class="stat-info">
          <span class="stat-label">Inviate</span>
          <span class="stat-value">{{ stats.sent }}</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon failed">❌</div>
        <div class="stat-info">
          <span class="stat-label">Fallite</span>
          <span class="stat-value">{{ stats.failed }}</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon pending">⏳</div>
        <div class="stat-info">
          <span class="stat-label">In Attesa</span>
          <span class="stat-value">{{ stats.pending }}</span>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
      <div class="filter-group">
        <label>Cerca Email</label>
        <input 
          v-model="filters.search" 
          type="text" 
          placeholder="Cerca per email..."
          @input="debouncedFetch"
        />
      </div>
      
      <div class="filter-group">
        <label>Tipo</label>
        <select v-model="filters.type" @change="fetchEmails">
          <option value="">Tutti</option>
          <option value="welcome">Benvenuto</option>
          <option value="reset_password">Reset Password</option>
          <option value="order_confirmation">Conferma Ordine</option>
        </select>
      </div>
      
      <div class="filter-group">
        <label>Status</label>
        <select v-model="filters.status" @change="fetchEmails">
          <option value="">Tutti</option>
          <option value="sent">Inviate</option>
          <option value="failed">Fallite</option>
          <option value="pending">In Attesa</option>
        </select>
      </div>

      <button @click="resetFilters" class="btn-reset">Reset Filtri</button>
    </div>

    <!-- Email Table -->
    <div class="table-container">
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Caricamento email...</p>
      </div>

      <table v-else-if="emails.length > 0" class="emails-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Destinatario</th>
            <th>Tipo</th>
            <th>Oggetto</th>
            <th>Status</th>
            <th>Inviata il</th>
            <th>Azioni</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="email in emails" :key="email.id">
            <td>#{{ email.id }}</td>
            <td>
              <div class="recipient-info">
                <strong>{{ email.recipient_name || 'N/A' }}</strong>
                <small>{{ email.recipient_email }}</small>
              </div>
            </td>
            <td>
              <span class="badge" :class="'badge-' + email.type">
                {{ getTypeLabel(email.type) }}
              </span>
            </td>
            <td>{{ email.subject }}</td>
            <td>
              <span class="status-badge" :class="'status-' + email.status">
                {{ getStatusLabel(email.status) }}
              </span>
            </td>
            <td>{{ formatDate(email.sent_at || email.created_at) }}</td>
            <td>
              <button @click="viewEmail(email)" class="btn-view">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-else class="empty-state">
        <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        <h3>Nessuna email trovata</h3>
        <p>Non ci sono email da visualizzare con i filtri attuali</p>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.total > 0" class="pagination">
      <button 
        @click="goToPage(pagination.current_page - 1)" 
        :disabled="pagination.current_page === 1"
        class="btn-page">
        ← Precedente
      </button>
      
      <span class="page-info">
        Pagina {{ pagination.current_page }} di {{ pagination.last_page }}
        ({{ pagination.total }} totali)
      </span>
      
      <button 
        @click="goToPage(pagination.current_page + 1)" 
        :disabled="pagination.current_page === pagination.last_page"
        class="btn-page">
        Successiva →
      </button>
    </div>

    <!-- Modal Email Detail -->
    <div v-if="selectedEmail" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>Dettaglio Email</h2>
          <button @click="closeModal" class="btn-close">×</button>
        </div>
        <div class="modal-body">
          <div class="detail-row">
            <strong>ID:</strong>
            <span>#{{ selectedEmail.id }}</span>
          </div>
          <div class="detail-row">
            <strong>Destinatario:</strong>
            <span>{{ selectedEmail.recipient_name }} ({{ selectedEmail.recipient_email }})</span>
          </div>
          <div class="detail-row">
            <strong>Tipo:</strong>
            <span class="badge" :class="'badge-' + selectedEmail.type">
              {{ getTypeLabel(selectedEmail.type) }}
            </span>
          </div>
          <div class="detail-row">
            <strong>Oggetto:</strong>
            <span>{{ selectedEmail.subject }}</span>
          </div>
          <div class="detail-row">
            <strong>Status:</strong>
            <span class="status-badge" :class="'status-' + selectedEmail.status">
              {{ getStatusLabel(selectedEmail.status) }}
            </span>
          </div>
          <div class="detail-row">
            <strong>Inviata il:</strong>
            <span>{{ formatDate(selectedEmail.sent_at || selectedEmail.created_at) }}</span>
          </div>
          <div v-if="selectedEmail.content" class="detail-row full">
            <strong>Contenuto:</strong>
            <p>{{ selectedEmail.content }}</p>
          </div>
          <div v-if="selectedEmail.error_message" class="detail-row full error">
            <strong>Errore:</strong>
            <p>{{ selectedEmail.error_message }}</p>
          </div>
          <div v-if="selectedEmail.user" class="detail-row">
            <strong>Utente:</strong>
            <span>{{ selectedEmail.user.nome }} {{ selectedEmail.user.cognome }} (ID: {{ selectedEmail.user.id }})</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuthRedirect } from '../../composables/useAuthRedirect';

// Setup auth redirect watchers
const { watchAdminAccess } = useAuthRedirect();
watchAdminAccess();

const emails = ref([]);
const stats = ref(null);
const loading = ref(false);
const selectedEmail = ref(null);
const filters = ref({
  search: '',
  type: '',
  status: '',
});
const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
});

let debounceTimer = null;

onMounted(() => {
  fetchStats();
  fetchEmails();
});

const fetchStats = async () => {
  try {
    const response = await axios.get('/emails/stats');
    stats.value = response.data;
  } catch (error) {
    console.error('Errore caricamento statistiche:', error);
  }
};

const fetchEmails = async (page = 1) => {
  loading.value = true;
  try {
    const params = {
      page,
      per_page: 20,
      ...filters.value,
    };
    
    const response = await axios.get('/emails', { params });
    emails.value = response.data.data;
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total,
    };
  } catch (error) {
    console.error('Errore caricamento email:', error);
  } finally {
    loading.value = false;
  }
};

const debouncedFetch = () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    fetchEmails();
  }, 500);
};

const resetFilters = () => {
  filters.value = {
    search: '',
    type: '',
    status: '',
  };
  fetchEmails();
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchEmails(page);
  }
};

const viewEmail = (email) => {
  selectedEmail.value = email;
};

const closeModal = () => {
  selectedEmail.value = null;
};

const getTypeLabel = (type) => {
  const labels = {
    welcome: 'Benvenuto',
    reset_password: 'Reset Password',
    order_confirmation: 'Conferma Ordine',
  };
  return labels[type] || type;
};

const getStatusLabel = (status) => {
  const labels = {
    sent: 'Inviata',
    failed: 'Fallita',
    pending: 'In Attesa',
  };
  return labels[status] || status;
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleString('it-IT', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>

<style scoped>
.email-logs-page {
  padding: 30px;
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 30px;
}

.page-header h1 {
  font-size: 32px;
  font-weight: bold;
  color: #333;
  margin: 0 0 8px 0;
}

.page-header p {
  color: #666;
  font-size: 16px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
}

.stat-icon.total { background: #e3f2fd; }
.stat-icon.sent { background: #e8f5e9; }
.stat-icon.failed { background: #ffebee; }
.stat-icon.pending { background: #fff3e0; }

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-label {
  font-size: 14px;
  color: #666;
  margin-bottom: 4px;
}

.stat-value {
  font-size: 28px;
  font-weight: bold;
  color: #333;
}

.filters-section {
  background: white;
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 24px;
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
  align-items: flex-end;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  flex: 1;
  min-width: 200px;
}

.filter-group label {
  font-size: 14px;
  font-weight: 600;
  color: #333;
}

.filter-group input,
.filter-group select {
  padding: 10px 14px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 14px;
  transition: border-color 0.2s;
}

.filter-group input:focus,
.filter-group select:focus {
  outline: none;
  border-color: #667eea;
}

.btn-reset {
  padding: 10px 20px;
  background: #f5f5f5;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-reset:hover {
  background: #e0e0e0;
}

.table-container {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 24px;
}

.loading-state {
  text-align: center;
  padding: 60px 20px;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.emails-table {
  width: 100%;
  border-collapse: collapse;
}

.emails-table th {
  background: #f8f9fa;
  padding: 12px 16px;
  text-align: left;
  font-weight: 600;
  color: #333;
  border-bottom: 2px solid #e0e0e0;
}

.emails-table td {
  padding: 14px 16px;
  border-bottom: 1px solid #f0f0f0;
}

.recipient-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.recipient-info strong {
  color: #333;
}

.recipient-info small {
  color: #666;
  font-size: 12px;
}

.badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.badge-welcome {
  background: #e3f2fd;
  color: #1976d2;
}

.badge-reset_password {
  background: #fff3e0;
  color: #f57c00;
}

.badge-order_confirmation {
  background: #e8f5e9;
  color: #388e3c;
}

.status-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.status-sent {
  background: #e8f5e9;
  color: #2e7d32;
}

.status-failed {
  background: #ffebee;
  color: #c62828;
}

.status-pending {
  background: #fff3e0;
  color: #f57c00;
}

.btn-view {
  padding: 8px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}

.btn-view:hover {
  background: #5568d3;
}

.btn-view svg {
  width: 18px;
  height: 18px;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  width: 80px;
  height: 80px;
  color: #ccc;
  margin: 0 auto 20px;
}

.empty-state h3 {
  font-size: 20px;
  color: #333;
  margin: 0 0 8px 0;
}

.empty-state p {
  color: #666;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  padding: 20px;
}

.btn-page {
  padding: 10px 20px;
  background: white;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-page:hover:not(:disabled) {
  border-color: #667eea;
  color: #667eea;
}

.btn-page:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  color: #666;
  font-size: 14px;
}

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
  padding: 20px;
}

.modal-content {
  background: white;
  border-radius: 16px;
  max-width: 700px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
  border-bottom: 1px solid #e0e0e0;
}

.modal-header h2 {
  margin: 0;
  font-size: 24px;
  color: #333;
}

.btn-close {
  width: 36px;
  height: 36px;
  background: #f5f5f5;
  border: none;
  border-radius: 8px;
  font-size: 24px;
  line-height: 1;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-close:hover {
  background: #e0e0e0;
}

.modal-body {
  padding: 24px;
}

.detail-row {
  display: grid;
  grid-template-columns: 150px 1fr;
  gap: 16px;
  padding: 12px 0;
  border-bottom: 1px solid #f0f0f0;
}

.detail-row.full {
  grid-template-columns: 1fr;
}

.detail-row strong {
  color: #666;
  font-size: 14px;
}

.detail-row span,
.detail-row p {
  color: #333;
  font-size: 14px;
}

.detail-row.error p {
  color: #c62828;
}
</style>
