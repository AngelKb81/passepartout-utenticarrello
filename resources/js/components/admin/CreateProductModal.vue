<template>
  <!-- Modal Overlay -->
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="close">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white" @click.stop>
      
      <!-- Header -->
      <div class="flex items-center justify-between pb-4 border-b">
        <h3 class="text-lg font-medium text-gray-900">Nuovo Prodotto</h3>
        <button @click="close" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="mt-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          
          <!-- Nome Prodotto -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Nome Prodotto *</label>
            <input
              v-model="form.nome"
              type="text"
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-500': errors.nome }"
              placeholder="Es: Smartphone Samsung Galaxy S24"
            >
            <p v-if="errors.nome" class="mt-1 text-sm text-red-600">{{ errors.nome }}</p>
          </div>

          <!-- Codice Prodotto -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Codice Prodotto *</label>
            <input
              v-model="form.codice"
              type="text"
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-500': errors.codice }"
              placeholder="Es: SAMSUNG-S24-001"
            >
            <p v-if="errors.codice" class="mt-1 text-sm text-red-600">{{ errors.codice }}</p>
          </div>

          <!-- Categoria -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Categoria *</label>
            <select
              v-model="form.categoria"
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-500': errors.categoria }"
            >
              <option value="">Seleziona categoria</option>
              <option value="Smartphone">Smartphone</option>
              <option value="Computer">Computer</option>
              <option value="Audio">Audio</option>
              <option value="Tablet">Tablet</option>
              <option value="Wearable">Wearable</option>
              <option value="Fotocamere">Fotocamere</option>
              <option value="Gaming">Gaming</option>
              <option value="Networking">Networking</option>
              <option value="Monitor">Monitor</option>
            </select>
            <p v-if="errors.categoria" class="mt-1 text-sm text-red-600">{{ errors.categoria }}</p>
          </div>

          <!-- Prezzo -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Prezzo (€) *</label>
            <input
              v-model="form.prezzo"
              type="number"
              step="0.01"
              min="0"
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-500': errors.prezzo }"
              placeholder="999.99"
            >
            <p v-if="errors.prezzo" class="mt-1 text-sm text-red-600">{{ errors.prezzo }}</p>
          </div>

          <!-- Scorte -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Scorte *</label>
            <input
              v-model="form.scorte"
              type="number"
              min="0"
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-500': errors.scorte }"
              placeholder="50"
            >
            <p v-if="errors.scorte" class="mt-1 text-sm text-red-600">{{ errors.scorte }}</p>
          </div>

          <!-- Stato Attivo -->
          <div>
            <label class="flex items-center">
              <input
                v-model="form.attivo"
                type="checkbox"
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
              >
              <span class="ml-2 text-sm font-medium text-gray-700">Prodotto attivo</span>
            </label>
          </div>

          <!-- Descrizione -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Descrizione</label>
            <textarea
              v-model="form.descrizione"
              rows="3"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-500': errors.descrizione }"
              placeholder="Descrizione dettagliata del prodotto..."
            ></textarea>
            <p v-if="errors.descrizione" class="mt-1 text-sm text-red-600">{{ errors.descrizione }}</p>
          </div>

          <!-- Upload Immagine -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Immagine Prodotto</label>
            <div class="flex items-center space-x-4">
              <input
                ref="fileInput"
                type="file"
                accept="image/*"
                @change="handleFileUpload"
                class="hidden"
              >
              <button
                type="button"
                @click="$refs.fileInput.click()"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg border border-gray-300 transition-colors"
              >
                Scegli File
              </button>
              <span v-if="form.immagine" class="text-sm text-gray-600">{{ form.immagine.name }}</span>
              <span v-else class="text-sm text-gray-500">Nessun file selezionato</span>
            </div>
            <p v-if="errors.immagine" class="mt-1 text-sm text-red-600">{{ errors.immagine }}</p>
          </div>

        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
          <button
            type="button"
            @click="close"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg transition-colors"
          >
            Annulla
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 border border-transparent rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Creazione...
            </span>
            <span v-else>Crea Prodotto</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, reactive } from 'vue'
import axios from 'axios'

export default {
  name: 'CreateProductModal',
  emits: ['close', 'created'],
  setup(props, { emit }) {
    const loading = ref(false)
    const errors = ref({})

    const form = reactive({
      nome: '',
      codice: '',
      categoria: '',
      descrizione: '',
      prezzo: '',
      scorte: '',
      attivo: true,
      immagine: null
    })

    const close = () => {
      emit('close')
    }

    const handleFileUpload = (event) => {
      const file = event.target.files[0]
      if (file) {
        // Validazione file
        if (!file.type.startsWith('image/')) {
          errors.value.immagine = 'Il file deve essere un\'immagine'
          return
        }
        if (file.size > 5 * 1024 * 1024) { // 5MB
          errors.value.immagine = 'Il file non deve superare i 5MB'
          return
        }
        
        form.immagine = file
        errors.value.immagine = null
      }
    }

    const validateForm = () => {
      errors.value = {}

      if (!form.nome.trim()) {
        errors.value.nome = 'Il nome è obbligatorio'
      }

      if (!form.codice.trim()) {
        errors.value.codice = 'Il codice è obbligatorio'
      }

      if (!form.categoria) {
        errors.value.categoria = 'La categoria è obbligatoria'
      }

      if (!form.prezzo || form.prezzo <= 0) {
        errors.value.prezzo = 'Il prezzo deve essere maggiore di 0'
      }

      if (form.scorte === '' || form.scorte < 0) {
        errors.value.scorte = 'Le scorte devono essere maggiori o uguali a 0'
      }

      return Object.keys(errors.value).length === 0
    }

    const submitForm = async () => {
      if (!validateForm()) {
        return
      }

      try {
        loading.value = true

        // Crea FormData per l'upload del file
        const formData = new FormData()
        Object.keys(form).forEach(key => {
          if (form[key] !== null && form[key] !== '') {
            // Gestisce il campo boolean attivo
            if (key === 'attivo') {
              formData.append(key, form[key] ? '1' : '0')
            } else {
              formData.append(key, form[key])
            }
          }
        })
        
        // Assicuriamoci che il campo attivo sia sempre presente
        if (!formData.has('attivo')) {
          formData.append('attivo', form.attivo ? '1' : '0')
        }

        const response = await axios.post('/admin/products', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        console.log('Prodotto creato:', response.data)
        
        // Simula il prodotto creato per demo
        const newProduct = {
          id: Date.now(), // ID temporaneo
          nome: form.nome,
          codice: form.codice,
          categoria: form.categoria,
          descrizione: form.descrizione,
          prezzo: form.prezzo,
          scorte: parseInt(form.scorte),
          attivo: form.attivo,
          immagine_url: form.immagine ? URL.createObjectURL(form.immagine) : '/images/placeholder-product.jpg'
        }

        emit('created', newProduct)

      } catch (error) {
        console.error('Errore creazione prodotto:', error)
        
        if (error.response?.data?.errors) {
          errors.value = error.response.data.errors
        } else {
          // Per demo, simula comunque la creazione
          const newProduct = {
            id: Date.now(),
            nome: form.nome,
            codice: form.codice,
            categoria: form.categoria,
            descrizione: form.descrizione,
            prezzo: form.prezzo,
            scorte: parseInt(form.scorte),
            attivo: form.attivo,
            immagine_url: form.immagine ? URL.createObjectURL(form.immagine) : '/images/placeholder-product.jpg'
          }
          
          emit('created', newProduct)
        }
      } finally {
        loading.value = false
      }
    }

    return {
      loading,
      errors,
      form,
      close,
      handleFileUpload,
      submitForm
    }
  }
}
</script>