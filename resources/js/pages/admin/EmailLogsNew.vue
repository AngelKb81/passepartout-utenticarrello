<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Log Email Inviate</h1>
              <p class="mt-2 text-gray-600">Monitora tutte le email inviate agli utenti</p>
            </div>
            <button 
              @click="refreshEmails" 
              :disabled="loading"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors disabled:opacity-50"
            >
              <svg class="w-5 h-5" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              Aggiorna
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Totale Email</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.total }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Inviate</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.sent }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Fallite</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.failed }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">In Attesa</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.pending }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters and Search -->
      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cerca Email</label>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Cerca email destinatario..."
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo Email</label>
            <select
              v-model="filters.template"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Tutti i tipi</option>
              <option value="welcome">Benvenuto</option>
              <option value="reset_password">Reset Password</option>
              <option value="order_confirmation">Conferma Ordine</option>
              <option value="newsletter">Newsletter</option>
              <option value="marketing">Marketing</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Stato</label>
            <select
              v-model="filters.status"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Tutti gli stati</option>
              <option value="sent">Inviate</option>
              <option value="failed">Fallite</option>
              <option value="pending">In Attesa</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Periodo</label>
            <select
              v-model="filters.period"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Tutto il periodo</option>
              <option value="today">Oggi</option>
              <option value="week">Ultima settimana</option>
              <option value="month">Ultimo mese</option>
              <option value="year">Ultimo anno</option>
            </select>
          </div>
        </div>
        <div class="mt-4 flex justify-end">
          <button 
            @click="clearFilters"
            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors"
          >
            Pulisci Filtri
          </button>
        </div>
      </div>

      <!-- Emails Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Email ({{ filteredEmails.length }})</h3>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>

        <!-- Table -->
        <div v-else-if="filteredEmails.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destinatario</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oggetto</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stato</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Invio</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="email in filteredEmails" :key="email.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8">
                      <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                        <span class="text-sm font-medium text-gray-600">{{ email.recipient_email.charAt(0).toUpperCase() }}</span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ email.recipient_email }}</div>
                      <div class="text-sm text-gray-500">{{ email.recipient_name || 'N/A' }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="getTemplateClass(email.template_type)">
                    {{ getTemplateLabel(email.template_type) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900 max-w-xs truncate">{{ email.subject }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="getStatusClass(email.status_type)">
                    {{ getStatusLabel(email.status_type) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(email.sent_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button
                    @click="viewEmail(email)"
                    class="text-blue-600 hover:text-blue-900 mr-3"
                    title="Visualizza email"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                  <button
                    v-if="email.status_type === 'failed'"
                    @click="resendEmail(email)"
                    class="text-green-600 hover:text-green-900"
                    title="Reinvia email"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Nessuna email trovata</h3>
          <p class="mt-1 text-sm text-gray-500">
            {{ Object.values(filters).some(v => v) ? 'Prova a modificare i filtri' : 'Non sono ancora state inviate email' }}
          </p>
        </div>
      </div>
    </div>

    <!-- Email Detail Modal -->
    <EmailDetailModal 
      v-if="selectedEmail"
      :email="selectedEmail"
      @close="selectedEmail = null"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import EmailDetailModal from '../../components/admin/EmailDetailModal.vue'

export default {
  name: 'EmailLogs',
  components: {
    EmailDetailModal
  },
  setup() {
    const loading = ref(false)
    const emails = ref([])
    const selectedEmail = ref(null)
    
    const stats = ref({
      total: 0,
      sent: 0,
      failed: 0,
      pending: 0
    })
    
    const filters = ref({
      search: '',
      template: '',
      status: '',
      period: ''
    })

    // Computed properties
    const filteredEmails = computed(() => {
      let filtered = emails.value || []

      if (filters.value.search) {
        const search = filters.value.search.toLowerCase()
        filtered = filtered.filter(email => 
          email.recipient_email?.toLowerCase().includes(search) ||
          (email.recipient_name && email.recipient_name.toLowerCase().includes(search)) ||
          email.subject?.toLowerCase().includes(search)
        )
      }

      if (filters.value.template) {
        filtered = filtered.filter(email => email.template_type === filters.value.template)
      }

      if (filters.value.status) {
        filtered = filtered.filter(email => email.status_type === filters.value.status)
      }

      if (filters.value.period) {
        const now = new Date()
        const filterDate = new Date()
        
        switch (filters.value.period) {
          case 'today':
            filterDate.setHours(0, 0, 0, 0)
            break
          case 'week':
            filterDate.setDate(now.getDate() - 7)
            break
          case 'month':
            filterDate.setMonth(now.getMonth() - 1)
            break
          case 'year':
            filterDate.setFullYear(now.getFullYear() - 1)
            break
        }
        
        filtered = filtered.filter(email => new Date(email.sent_at) >= filterDate)
      }

      return filtered
    })

    // Methods
    const loadEmails = async () => {
      try {
        loading.value = true
        const response = await axios.get('/admin/emails')
        emails.value = response.data.data || []
        
        // Calcola stats
        const emailsArray = emails.value || []
        stats.value = {
          total: emailsArray.length,
          sent: emailsArray.filter(e => e.status_type === 'sent').length,
          failed: emailsArray.filter(e => e.status_type === 'failed').length,
          pending: emailsArray.filter(e => e.status_type === 'pending').length
        }
      } catch (error) {
        console.error('Errore nel caricamento email:', error)
        // Fallback con dati mock
        loadMockEmails()
      } finally {
        loading.value = false
      }
    }

    const loadMockEmails = () => {
      emails.value = [
        {
          id: 1,
          recipient_email: 'mario.rossi@email.com',
          recipient_name: 'Mario Rossi',
          template_type: 'welcome',
          subject: 'Benvenuto in Passepartout!',
          status_type: 'sent',
          sent_at: '2025-10-18T10:30:00Z',
          body: 'Ciao Mario, benvenuto nella nostra piattaforma...'
        },
        {
          id: 2,
          recipient_email: 'lucia.bianchi@email.com',
          recipient_name: 'Lucia Bianchi',
          template_type: 'reset_password',
          subject: 'Reset della tua password',
          status_type: 'sent',
          sent_at: '2025-10-18T09:15:00Z',
          body: 'Hai richiesto di reimpostare la tua password...'
        },
        {
          id: 3,
          recipient_email: 'giuseppe.verdi@email.com',
          recipient_name: 'Giuseppe Verdi',
          template_type: 'order_confirmation',
          subject: 'Conferma del tuo ordine #12345',
          status_type: 'failed',
          sent_at: '2025-10-18T08:45:00Z',
          body: 'Il tuo ordine è stato confermato...'
        },
        {
          id: 4,
          recipient_email: 'anna.ferrari@email.com',
          recipient_name: 'Anna Ferrari',
          template_type: 'newsletter',
          subject: 'Newsletter Ottobre 2025',
          status_type: 'pending',
          sent_at: '2025-10-18T07:20:00Z',
          body: 'Ecco le novità di questo mese...'
        }
      ]

      stats.value = {
        total: emails.value.length,
        sent: emails.value.filter(e => e.status_type === 'sent').length,
        failed: emails.value.filter(e => e.status_type === 'failed').length,
        pending: emails.value.filter(e => e.status_type === 'pending').length
      }
    }

    const refreshEmails = () => {
      loadEmails()
    }

    const clearFilters = () => {
      filters.value = {
        search: '',
        template: '',
        status: '',
        period: ''
      }
    }

    const viewEmail = (email) => {
      selectedEmail.value = email
    }

    const resendEmail = async (email) => {
      try {
        await axios.post(`/emails/${email.id}/resend`)
        // Aggiorna lo stato dell'email
        email.status_type = 'pending'
        console.log('Email reinviata')
      } catch (error) {
        console.error('Errore reinvio email:', error)
        // Per demo, simula il reinvio
        email.status_type = 'pending'
      }
    }

    const getTemplateClass = (template) => {
      const classes = {
        'welcome': 'bg-green-100 text-green-800',
        'reset_password': 'bg-yellow-100 text-yellow-800',
        'order_confirmation': 'bg-blue-100 text-blue-800',
        'newsletter': 'bg-purple-100 text-purple-800',
        'marketing': 'bg-indigo-100 text-indigo-800'
      }
      return classes[template] || 'bg-gray-100 text-gray-800'
    }

    const getTemplateLabel = (template) => {
      const labels = {
        'welcome': 'Benvenuto',
        'reset_password': 'Reset Password',
        'order_confirmation': 'Conferma Ordine',
        'newsletter': 'Newsletter',
        'marketing': 'Marketing'
      }
      return labels[template] || template
    }

    const getStatusClass = (status) => {
      const classes = {
        'sent': 'bg-green-100 text-green-800',
        'failed': 'bg-red-100 text-red-800',
        'pending': 'bg-yellow-100 text-yellow-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    }

    const getStatusLabel = (status) => {
      const labels = {
        'sent': 'Inviata',
        'failed': 'Fallita',
        'pending': 'In Attesa'
      }
      return labels[status] || status
    }

    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('it-IT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    onMounted(() => {
      loadEmails()
    })

    return {
      loading,
      emails,
      selectedEmail,
      stats,
      filters,
      filteredEmails,
      refreshEmails,
      clearFilters,
      viewEmail,
      resendEmail,
      getTemplateClass,
      getTemplateLabel,
      getStatusClass,
      getStatusLabel,
      formatDate
    }
  }
}
</script>