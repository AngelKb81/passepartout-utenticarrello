<template>
  <!-- Modal Overlay -->
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="close">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-lg rounded-md bg-white" @click.stop>
      
      <!-- Header -->
      <div class="flex items-center justify-between pb-4 border-b">
        <div>
          <h3 class="text-lg font-medium text-gray-900">Dettagli Email</h3>
          <p class="text-sm text-gray-500">ID: {{ email.id }}</p>
        </div>
        <button @click="close" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Email Details -->
      <div class="mt-6">
        
        <!-- Basic Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Destinatario</label>
            <div class="p-3 bg-gray-50 rounded-lg">
              <div class="font-medium">{{ email.recipient_email }}</div>
              <div v-if="email.recipient_name" class="text-sm text-gray-600">{{ email.recipient_name }}</div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Stato</label>
            <div class="p-3 bg-gray-50 rounded-lg">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="getStatusClass(email.status_type)">
                {{ getStatusLabel(email.status_type) }}
              </span>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo Email</label>
            <div class="p-3 bg-gray-50 rounded-lg">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="getTemplateClass(email.template_type)">
                {{ getTemplateLabel(email.template_type) }}
              </span>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Data Invio</label>
            <div class="p-3 bg-gray-50 rounded-lg">
              {{ formatDate(email.sent_at) }}
            </div>
          </div>
        </div>

        <!-- Subject -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Oggetto</label>
          <div class="p-3 bg-gray-50 rounded-lg">
            <div class="font-medium">{{ email.subject }}</div>
          </div>
        </div>

        <!-- Email Body -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Contenuto Email</label>
          <div class="border border-gray-300 rounded-lg overflow-hidden">
            
            <!-- Tabs -->
            <div class="bg-gray-50 border-b border-gray-300 flex">
              <button
                @click="activeTab = 'preview'"
                class="px-4 py-2 text-sm font-medium transition-colors"
                :class="activeTab === 'preview' ? 'bg-white text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900'"
              >
                Anteprima
              </button>
              <button
                @click="activeTab = 'html'"
                class="px-4 py-2 text-sm font-medium transition-colors"
                :class="activeTab === 'html' ? 'bg-white text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900'"
              >
                Codice HTML
              </button>
            </div>

            <!-- Tab Content -->
            <div class="p-4 bg-white">
              <!-- Preview Tab -->
              <div v-if="activeTab === 'preview'" class="max-h-96 overflow-y-auto">
                <div v-html="email.body" class="prose prose-sm max-w-none"></div>
              </div>

              <!-- HTML Tab -->
              <div v-if="activeTab === 'html'" class="max-h-96 overflow-y-auto">
                <pre class="text-sm text-gray-800 whitespace-pre-wrap"><code>{{ email.body }}</code></pre>
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Info -->
        <div v-if="email.error_message" class="mb-6">
          <label class="block text-sm font-medium text-red-700 mb-2">Messaggio di Errore</label>
          <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
            <div class="text-red-800">{{ email.error_message }}</div>
          </div>
        </div>

      </div>

      <!-- Actions -->
      <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
        <button
          @click="close"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg transition-colors"
        >
          Chiudi
        </button>
        
        <button
          v-if="email.status_type === 'failed'"
          @click="resendEmail"
          :disabled="resending"
          class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 border border-transparent rounded-lg transition-colors disabled:opacity-50"
        >
          <span v-if="resending" class="flex items-center">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Reinviando...
          </span>
          <span v-else>Reinvia Email</span>
        </button>

        <button
          @click="downloadEmail"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg transition-colors"
        >
          <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Scarica
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import axios from 'axios'

export default {
  name: 'EmailDetailModal',
  props: {
    email: {
      type: Object,
      required: true
    }
  },
  emits: ['close'],
  setup(props, { emit }) {
    const activeTab = ref('preview')
    const resending = ref(false)

    const close = () => {
      emit('close')
    }

    const resendEmail = async () => {
      try {
        resending.value = true
        await axios.post(`/emails/${props.email.id}/resend`)
        
        // Aggiorna lo stato dell'email
        props.email.status_type = 'pending'
        
        console.log('Email reinviata con successo')
      } catch (error) {
        console.error('Errore nel reinvio email:', error)
        // Per demo, simula il reinvio
        props.email.status_type = 'pending'
      } finally {
        resending.value = false
      }
    }

    const downloadEmail = () => {
      // Crea un file di testo con i dettagli dell'email
      const emailContent = `
Dettagli Email - ID: ${props.email.id}
=====================================

Destinatario: ${props.email.recipient_email}
Nome: ${props.email.recipient_name || 'N/A'}
Oggetto: ${props.email.subject}
Tipo: ${getTemplateLabel(props.email.template_type)}
Stato: ${getStatusLabel(props.email.status_type)}
Data Invio: ${formatDate(props.email.sent_at)}

Contenuto:
----------
${props.email.body}

${props.email.error_message ? `Errore: ${props.email.error_message}` : ''}
      `.trim()

      const blob = new Blob([emailContent], { type: 'text/plain' })
      const url = URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      link.download = `email-${props.email.id}-${props.email.recipient_email}.txt`
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      URL.revokeObjectURL(url)
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

    return {
      activeTab,
      resending,
      close,
      resendEmail,
      downloadEmail,
      getTemplateClass,
      getTemplateLabel,
      getStatusClass,
      getStatusLabel,
      formatDate
    }
  }
}
</script>

<style scoped>
/* Stili per il contenuto HTML dell'email */
.prose {
  color: #374151;
}

.prose img {
  max-width: 100%;
  height: auto;
}

.prose a {
  color: #2563eb;
  text-decoration: underline;
}

.prose table {
  width: 100%;
  border-collapse: collapse;
}

.prose th,
.prose td {
  border: 1px solid #d1d5db;
  padding: 8px;
  text-align: left;
}

.prose th {
  background-color: #f3f4f6;
  font-weight: 600;
}
</style>