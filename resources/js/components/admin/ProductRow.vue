<template>
  <tr class="hover:bg-gray-50">
    <!-- Prodotto (Immagine + Nome) -->
    <td class="px-6 py-4 whitespace-nowrap">
      <div class="flex items-center">
        <div class="flex-shrink-0 h-12 w-12">
          <img 
            class="h-12 w-12 rounded-lg object-cover"
            :src="product.immagine_url || '/images/placeholder-product.jpg'"
            :alt="product.nome"
          >
        </div>
        <div class="ml-4">
          <div v-if="!editing.nome" class="text-sm font-medium text-gray-900">
            {{ product.nome }}
          </div>
          <input
            v-else
            v-model="editForm.nome"
            @blur="saveField('nome')"
            @keydown.enter="saveField('nome')"
            @keydown.escape="cancelEdit('nome')"
            class="text-sm font-medium text-gray-900 border-b border-blue-500 bg-transparent focus:outline-none"
            ref="nomeInput"
          >
          <div 
            class="text-sm text-gray-500 cursor-pointer"
            @click="startEdit('nome')"
            v-if="!editing.nome"
          >
            Click per modificare
          </div>
        </div>
      </div>
    </td>

    <!-- Codice -->
    <td class="px-6 py-4 whitespace-nowrap">
      <div v-if="!editing.codice" 
           class="text-sm text-gray-900 cursor-pointer hover:text-blue-600"
           @click="startEdit('codice')">
        {{ product.codice }}
      </div>
      <input
        v-else
        v-model="editForm.codice"
        @blur="saveField('codice')"
        @keydown.enter="saveField('codice')"
        @keydown.escape="cancelEdit('codice')"
        class="text-sm text-gray-900 border-b border-blue-500 bg-transparent focus:outline-none w-full"
        ref="codiceInput"
      >
    </td>

    <!-- Categoria -->
    <td class="px-6 py-4 whitespace-nowrap">
      <div v-if="!editing.categoria" 
           class="cursor-pointer"
           @click="startEdit('categoria')">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
              :class="getCategoryClass(product.categoria)">
          {{ product.categoria }}
        </span>
      </div>
      <select
        v-else
        v-model="editForm.categoria"
        @change="saveField('categoria')"
        @blur="saveField('categoria')"
        @keydown.escape="cancelEdit('categoria')"
        class="text-xs border border-blue-500 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
        ref="categoriaInput"
      >
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
    </td>

    <!-- Prezzo -->
    <td class="px-6 py-4 whitespace-nowrap">
      <div v-if="!editing.prezzo" 
           class="cursor-pointer"
           @click="startEdit('prezzo')">
        <div class="text-sm font-medium text-gray-900">€{{ formatPrice(product.prezzo) }}</div>
      </div>
      <div v-else class="flex flex-col space-y-1">
        <input
          v-model="editForm.prezzo"
          @blur="saveField('prezzo')"
          @keydown.enter="saveField('prezzo')"
          @keydown.escape="cancelEdit('prezzo')"
          type="number"
          step="0.01"
          class="text-sm font-medium text-gray-900 border-b border-blue-500 bg-transparent focus:outline-none w-20"
          data-field="prezzo"
        >
      </div>
    </td>

    <!-- Scorte -->
    <td class="px-6 py-4 whitespace-nowrap">
      <div v-if="!editing.scorte" 
           class="cursor-pointer"
           @click="startEdit('scorte')">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
              :class="getStockClass(product.scorte)">
          {{ product.scorte }}
        </span>
      </div>
      <input
        v-else
        v-model="editForm.scorte"
        @blur="saveField('scorte')"
        @keydown.enter="saveField('scorte')"
        @keydown.escape="cancelEdit('scorte')"
        type="number"
        min="0"
        class="text-xs border border-blue-500 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 w-16"
        ref="scorteInput"
      >
    </td>

    <!-- Stato -->
    <td class="px-6 py-4 whitespace-nowrap">
      <label class="flex items-center cursor-pointer">
        <input
          type="checkbox"
          :checked="product.attivo"
          @change="toggleStatus"
          class="sr-only"
        >
        <div class="relative">
          <div class="block bg-gray-600 w-14 h-8 rounded-full"
               :class="product.attivo ? 'bg-green-600' : 'bg-gray-600'"></div>
          <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition transform"
               :class="product.attivo ? 'translate-x-6' : 'translate-x-0'"></div>
        </div>
        <span class="ml-3 text-sm font-medium text-gray-900">
          {{ product.attivo ? 'Attivo' : 'Disattivo' }}
        </span>
      </label>
    </td>

    <!-- Azioni -->
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
      <div class="flex items-center space-x-2">
        <!-- Salva modifiche (visibile solo se editing) -->
        <button
          v-if="hasChanges"
          @click="saveAllChanges"
          class="text-green-600 hover:text-green-900 p-1 rounded"
          title="Salva tutte le modifiche"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
        </button>

        <!-- Annulla modifiche -->
        <button
          v-if="hasChanges"
          @click="cancelAllEdits"
          class="text-yellow-600 hover:text-yellow-900 p-1 rounded"
          title="Annulla modifiche"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>

        <!-- Elimina -->
        <button
          @click="confirmDelete"
          class="text-red-600 hover:text-red-900 p-1 rounded"
          title="Elimina prodotto"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </button>
      </div>
    </td>
  </tr>
</template>

<script>
import { ref, reactive, computed, nextTick } from 'vue'

export default {
  name: 'ProductRow',
  props: {
    product: {
      type: Object,
      required: true
    }
  },
  emits: ['update', 'delete', 'toggle-status'],
  setup(props, { emit }) {
    const editing = ref({
      nome: false,
      codice: false,
      categoria: false,
      prezzo_attuale: false,
      scorte: false
    })

    const editForm = reactive({
      nome: props.product.nome,
      codice: props.product.codice,
      categoria: props.product.categoria,
      prezzo_attuale: props.product.prezzo_attuale,
      scorte: props.product.scorte
    })

    const originalValues = reactive({ ...editForm })

    const hasChanges = computed(() => {
      return Object.keys(editForm).some(key => editForm[key] !== originalValues[key])
    })

    const startEdit = async (field) => {
      editing.value[field] = true
      await nextTick()
      
      // Focus sull'input corretto - usa querySelector come fallback
      const inputElement = document.querySelector(`input[data-field="${field}"]`)
      if (inputElement) {
        inputElement.focus()
      }
    }

    const saveField = (field) => {
      editing.value[field] = false
      
      if (editForm[field] !== originalValues[field]) {
        emit('update', props.product.id, { [field]: editForm[field] })
        originalValues[field] = editForm[field]
      }
    }

    const cancelEdit = (field) => {
      editForm[field] = originalValues[field]
      editing.value[field] = false
    }

    const saveAllChanges = () => {
      const changes = {}
      Object.keys(editForm).forEach(key => {
        if (editForm[key] !== originalValues[key]) {
          changes[key] = editForm[key]
        }
      })

      if (Object.keys(changes).length > 0) {
        emit('update', props.product.id, changes)
        Object.assign(originalValues, changes)
      }

      // Chiudi tutti gli edit
      Object.keys(editing.value).forEach(key => {
        editing.value[key] = false
      })
    }

    const cancelAllEdits = () => {
      Object.keys(editForm).forEach(key => {
        editForm[key] = originalValues[key]
      })
      Object.keys(editing.value).forEach(key => {
        editing.value[key] = false
      })
    }

    const toggleStatus = () => {
      emit('toggle-status', props.product.id)
    }

    const confirmDelete = () => {
      if (confirm(`Sei sicuro di voler eliminare "${props.product.nome}"?`)) {
        emit('delete', props.product.id)
      }
    }

    const formatPrice = (price) => {
      return parseFloat(price).toFixed(2)
    }

    const getCategoryClass = (categoria) => {
      const classes = {
        'Smartphone': 'bg-blue-100 text-blue-800',
        'Computer': 'bg-green-100 text-green-800',
        'Audio': 'bg-purple-100 text-purple-800',
        'Tablet': 'bg-indigo-100 text-indigo-800',
        'Wearable': 'bg-pink-100 text-pink-800',
        'Fotocamere': 'bg-yellow-100 text-yellow-800',
        'Gaming': 'bg-red-100 text-red-800',
        'Networking': 'bg-gray-100 text-gray-800',
        'Monitor': 'bg-teal-100 text-teal-800'
      }
      return classes[categoria] || 'bg-gray-100 text-gray-800'
    }

    const getStockClass = (scorte) => {
      if (scorte === 0) return 'bg-red-100 text-red-800'
      if (scorte < 10) return 'bg-yellow-100 text-yellow-800'
      return 'bg-green-100 text-green-800'
    }

    return {
      editing,
      editForm,
      hasChanges,
      startEdit,
      saveField,
      cancelEdit,
      saveAllChanges,
      cancelAllEdits,
      toggleStatus,
      confirmDelete,
      formatPrice,
      getCategoryClass,
      getStockClass
    }
  }
}
</script>

<style scoped>
.dot {
  transition: transform 0.3s ease;
}
</style>