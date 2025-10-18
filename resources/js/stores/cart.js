import { defineStore } from 'pinia'
import axios from 'axios'
import { useAuthStore } from './auth'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    loading: false,
    itemsCount: 0,
    total: 0,
    guestCartKey: 'passepartout_guest_cart'
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
      const authStore = useAuthStore()
      
      try {
        this.loading = true
        console.log('📥 Caricamento carrello...')
        
        if (authStore.isAuthenticated) {
          console.log('👤 Utente autenticato, carico dal server...')
          // Utente autenticato: carica dal server
          const response = await axios.get('/cart')
          console.log('📡 Risposta API /cart:', response.data)
          
          if (response.data.cart && response.data.cart.items) {
            this.items = response.data.cart.items
            console.log('✅ Items caricati dal server:', this.items.length)
          } else {
            this.items = []
            console.log('📭 Nessun item dal server')
          }
        } else {
          console.log('👻 Utente guest, carico da localStorage...')
          // Utente guest: carica da localStorage
          this.loadGuestCart()
        }
        
        this.updateCounts()
      } catch (error) {
        console.error('❌ Errore nel caricamento del carrello:', error)
        if (!authStore.isAuthenticated) {
          // Se fallisce l'API per guest, carica da localStorage comunque
          this.loadGuestCart()
        }
        this.updateCounts()
      } finally {
        this.loading = false
      }
    },

    loadGuestCart() {
      try {
        const savedCart = localStorage.getItem(this.guestCartKey)
        if (savedCart) {
          const cartData = JSON.parse(savedCart)
          this.items = cartData.items || []
        } else {
          this.items = []
        }
      } catch (error) {
        console.error('Errore caricamento carrello guest:', error)
        this.items = []
      }
    },

    saveGuestCart() {
      try {
        const cartData = {
          items: this.items,
          timestamp: new Date().toISOString()
        }
        localStorage.setItem(this.guestCartKey, JSON.stringify(cartData))
      } catch (error) {
        console.error('Errore salvataggio carrello guest:', error)
      }
    },

    async addToCart(productId, quantity = 1) {
      const authStore = useAuthStore()
      
      try {
        this.loading = true
        
        if (authStore.isAuthenticated) {
          // Utente autenticato: usa API
          const response = await axios.post('/cart/add', {
            product_id: productId,
            quantita: quantity
          })
          
          if (response.status === 200) {
            await this.loadCartItems()
            return { success: true, message: response.data.message }
          }
          
          return { success: false, message: response.data.message || 'Errore nell\'aggiunta al carrello' }
        } else {
          // Utente guest: gestisci localStorage
          return await this.addToGuestCart(productId, quantity)
        }
      } catch (error) {
        console.error('Errore aggiunta al carrello:', error)
        return { success: false, message: error.response?.data?.message || 'Errore nell\'aggiunta al carrello' }
      } finally {
        this.loading = false
      }
    },

    async addToGuestCart(productId, quantity = 1) {
      try {
        // Carica info prodotto
        const response = await axios.get(`/products/${productId}`)
        const product = response.data.product
        
        if (!product) {
          return { success: false, message: 'Prodotto non trovato' }
        }
        
        // Controlla se il prodotto è già nel carrello
        const existingItem = this.items.find(item => item.id == productId)
        
        if (existingItem) {
          existingItem.quantity += quantity
        } else {
          this.items.push({
            id: product.id,
            product_id: product.id,
            quantity: quantity,
            product: {
              id: product.id,
              nome: product.nome,
              codice: product.codice,
              descrizione: product.descrizione,
              prezzo: product.prezzo,
              immagine: product.immagine
            }
          })
        }
        
        this.saveGuestCart()
        this.updateCounts()
        
        return { success: true, message: 'Prodotto aggiunto al carrello' }
      } catch (error) {
        console.error('Errore aggiunta prodotto guest:', error)
        return { success: false, message: 'Errore nell\'aggiunta al carrello' }
      }
    },

    async updateQuantity(itemId, quantity) {
      const authStore = useAuthStore()
      
      try {
        this.loading = true
        
        if (authStore.isAuthenticated) {
          // Utente autenticato: usa API
          const response = await axios.put('/cart/update', {
            product_id: itemId,
            quantita: quantity
          })
          
          if (response.status === 200) {
            await this.loadCartItems()
            return { success: true }
          }
          
          return { success: false, message: response.data.message || 'Errore nell\'aggiornamento quantità' }
        } else {
          // Utente guest: aggiorna localStorage
          const item = this.items.find(item => item.product_id == itemId)
          if (item) {
            if (quantity <= 0) {
              this.items = this.items.filter(item => item.product_id != itemId)
            } else {
              item.quantity = quantity
            }
            this.saveGuestCart()
            this.updateCounts()
          }
          return { success: true }
        }
      } catch (error) {
        console.error('Errore aggiornamento quantità:', error)
        return { success: false, message: error.response?.data?.message || 'Errore nell\'aggiornamento quantità' }
      } finally {
        this.loading = false
      }
    },

    async removeFromCart(itemId) {
      const authStore = useAuthStore()
      
      try {
        this.loading = true
        
        if (authStore.isAuthenticated) {
          // Utente autenticato: usa API
          const response = await axios.delete('/cart/remove', {
            data: { product_id: itemId }
          })
          
          if (response.status === 200) {
            await this.loadCartItems()
            return { success: true, message: response.data.message }
          }
          
          return { success: false, message: response.data.message || 'Errore nella rimozione dal carrello' }
        } else {
          // Utente guest: rimuovi da localStorage
          this.items = this.items.filter(item => item.product_id != itemId)
          this.saveGuestCart()
          this.updateCounts()
          return { success: true, message: 'Prodotto rimosso dal carrello' }
        }
      } catch (error) {
        console.error('Errore rimozione dal carrello:', error)
        return { success: false, message: error.response?.data?.message || 'Errore nella rimozione dal carrello' }
      } finally {
        this.loading = false
      }
    },

    async clearCart() {
      const authStore = useAuthStore()
      
      try {
        this.loading = true
        console.log('🗑️ Inizio clearCart - Items prima:', this.items.length)
        
        if (authStore.isAuthenticated) {
          console.log('👤 Utente autenticato, chiamata API...')
          // Utente autenticato: svuota via API
          const response = await axios.delete('/cart/clear')
          console.log('📡 Risposta API clearCart:', response.status, response.data)
          
          if (response.status !== 200) {
            throw new Error(response.data?.message || 'Errore API')
          }
        } else {
          console.log('👻 Utente guest, nessuna API')
        }
        
        // Svuota sempre lo stato locale e localStorage
        console.log('🧹 Svuotamento stato locale...')
        this.items = []
        this.updateCounts()
        localStorage.removeItem(this.guestCartKey)
        console.log('✅ Items dopo svuotamento:', this.items.length)

        return { success: true, message: 'Carrello svuotato con successo' }
      } catch (error) {
        console.error('❌ Errore svuotamento carrello:', error)
        
        // Se l'API fallisce, svuota comunque il frontend
        this.items = []
        this.updateCounts()
        localStorage.removeItem(this.guestCartKey)
        
        const message = error.response?.data?.message || 'Carrello svuotato (solo locale)'
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



    async syncGuestCartOnLogin() {
      try {
        const savedCart = localStorage.getItem(this.guestCartKey)
        if (!savedCart) {
          // Nessun carrello guest, carica semplicemente quello dell'utente
          await this.loadCartItems()
          return
        }
        
        const guestCartData = JSON.parse(savedCart)
        const guestItems = guestCartData.items || []
        
        if (guestItems.length === 0) {
          await this.loadCartItems()
          return
        }
        
        console.log('Sincronizzazione carrello guest con', guestItems.length, 'prodotti')
        
        // Carica prima il carrello dell'utente autenticato
        await this.loadCartItems()
        
        // Aggiungi ogni prodotto del carrello guest
        for (const guestItem of guestItems) {
          try {
            const response = await axios.post('/cart/add', {
              product_id: guestItem.product_id,
              quantita: guestItem.quantity
            })
            
            if (response.status !== 200) {
              console.warn('Errore aggiunta prodotto durante sync:', response.data.message)
            }
          } catch (error) {
            console.error('Errore sync prodotto:', guestItem.product_id, error)
          }
        }
        
        // Ricarica il carrello finale e pulisci quello guest
        await this.loadCartItems()
        localStorage.removeItem(this.guestCartKey)
        
        console.log('Sincronizzazione completata')
        
      } catch (error) {
        console.error('Errore durante la sincronizzazione del carrello:', error)
        // In caso di errore, carica comunque il carrello dell'utente
        await this.loadCartItems()
      }
    },

    // Reset dello store
    reset() {
      this.items = []
      this.loading = false
      this.itemsCount = 0
      this.total = 0
      localStorage.removeItem(this.guestCartKey)
    }
  }
})