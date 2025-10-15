import { defineStore } from 'pinia'
import axios from 'axios'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    loading: false,
    itemsCount: 0,
    total: 0
  }),

  getters: {
    cartTotal: (state) => {
      return state.items.reduce((total, item) => {
        return total + (item.product.prezzo * item.quantita)
      }, 0)
    },

    cartItemsCount: (state) => {
      return state.items.reduce((count, item) => count + item.quantita, 0)
    },

    isEmpty: (state) => state.items.length === 0,

    getItemByProductId: (state) => (productId) => {
      return state.items.find(item => item.product_id === productId)
    }
  },

  actions: {
    async loadCartItems() {
      try {
        this.loading = true
        const response = await axios.get('/cart')
        
        if (response.data.cart && response.data.cart.items) {
          this.items = response.data.cart.items
          this.updateCounts()
        }
      } catch (error) {
        console.error('Errore nel caricamento del carrello:', error)
        this.items = []
        this.updateCounts()
      } finally {
        this.loading = false
      }
    },

    async addToCart(productId, quantity = 1) {
      try {
        this.loading = true
        const response = await axios.post('/cart/add', {
          product_id: productId,
          quantita: quantity
        })

        // Ricarica il carrello per avere i dati aggiornati
        await this.loadCartItems()

        return { success: true, data: response.data }
      } catch (error) {
        const message = error.response?.data?.message || 'Errore nell\'aggiunta al carrello'
        return { success: false, message }
      } finally {
        this.loading = false
      }
    },

    async updateQuantity(productId, quantity) {
      if (quantity <= 0) {
        return await this.removeFromCart(productId)
      }

      try {
        this.loading = true
        const response = await axios.put('/cart/update', {
          product_id: productId,
          quantita: quantity
        })

        // Aggiorna localmente l'item
        const item = this.items.find(item => item.product_id === productId)
        if (item) {
          item.quantita = quantity
          this.updateCounts()
        }

        return { success: true, data: response.data }
      } catch (error) {
        const message = error.response?.data?.message || 'Errore nell\'aggiornamento della quantità'
        return { success: false, message }
      } finally {
        this.loading = false
      }
    },

    async removeFromCart(productId) {
      try {
        this.loading = true
        const response = await axios.delete('/cart/remove', {
          data: { product_id: productId }
        })

        // Rimuovi localmente l'item
        this.items = this.items.filter(item => item.product_id !== productId)
        this.updateCounts()

        return { success: true, data: response.data }
      } catch (error) {
        const message = error.response?.data?.message || 'Errore nella rimozione dal carrello'
        return { success: false, message }
      } finally {
        this.loading = false
      }
    },

    async clearCart() {
      try {
        this.loading = true
        const response = await axios.delete('/cart/clear')

        this.items = []
        this.updateCounts()

        return { success: true, data: response.data }
      } catch (error) {
        const message = error.response?.data?.message || 'Errore nello svuotamento del carrello'
        return { success: false, message }
      } finally {
        this.loading = false
      }
    },

    async checkout(shippingData = {}) {
      try {
        this.loading = true
        const response = await axios.post('/cart/checkout', shippingData)

        // Svuota il carrello dopo checkout successful
        this.items = []
        this.updateCounts()

        return { success: true, data: response.data }
      } catch (error) {
        const message = error.response?.data?.message || 'Errore durante il checkout'
        return { success: false, message }
      } finally {
        this.loading = false
      }
    },

    async getCartTotal() {
      try {
        const response = await axios.get('/cart/total')
        this.total = response.data.total
        return response.data.total
      } catch (error) {
        console.error('Errore nel calcolo del totale:', error)
        this.total = this.cartTotal
        return this.cartTotal
      }
    },

    updateCounts() {
      this.itemsCount = this.cartItemsCount
      this.total = this.cartTotal
    },

    // Metodo helper per incrementare la quantità
    async incrementQuantity(productId) {
      const item = this.getItemByProductId(productId)
      const currentQuantity = item ? item.quantita : 0
      return await this.updateQuantity(productId, currentQuantity + 1)
    },

    // Metodo helper per decrementare la quantità
    async decrementQuantity(productId) {
      const item = this.getItemByProductId(productId)
      if (item && item.quantita > 1) {
        return await this.updateQuantity(productId, item.quantita - 1)
      } else if (item) {
        return await this.removeFromCart(productId)
      }
      return { success: false, message: 'Prodotto non trovato nel carrello' }
    },

    // Reset dello store
    reset() {
      this.items = []
      this.loading = false
      this.itemsCount = 0
      this.total = 0
    }
  }
})