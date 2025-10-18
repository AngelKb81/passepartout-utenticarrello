import { ref, reactive } from 'vue'
import axios from 'axios'

export function useAdminStats() {
  const loading = ref(false)
  const error = ref(null)
  
  const stats = reactive({
    total_users: 0,
    total_products: 0,
    active_products: 0,
    total_carts: 0,
    avg_cart_items: 0,
    products_by_category: [],
    top_cart_products: [],
    user_registrations: []
  })

  const productStats = reactive({
    total: 0,
    active: 0,
    inactive: 0,
    low_stock: 0,
    out_of_stock: 0,
    by_category: []
  })

  const loadGeneralStats = async () => {
    try {
      loading.value = true
      error.value = null
      
      console.log('Caricamento statistiche dashboard da API...')
      
      const response = await axios.get('/admin/dashboard/stats')
      
      if (response.data && response.data.stats) {
        Object.assign(stats, response.data.stats)
        console.log('✅ Statistiche caricate dalla API:', stats)
      } else {
        throw new Error('Formato dati API non valido')
      }
      
    } catch (err) {
      error.value = err.message || 'Errore nel caricamento delle statistiche'
      console.error('Errore statistiche generali:', err)
      throw err // Rilancia l'errore per permettere il fallback ai dati mock
    } finally {
      loading.value = false
    }
  }

  const loadProductStats = async () => {
    try {
      loading.value = true
      error.value = null
      
      const response = await axios.get('/admin/dashboard/products')
      
      if (response.data && response.data.stats) {
        Object.assign(productStats, response.data.stats)
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Errore nel caricamento statistiche prodotti'
      console.error('Errore statistiche prodotti:', err)
      
      // Fallback con dati mock
      loadMockProductStats()
    } finally {
      loading.value = false
    }
  }



  const loadMockData = () => {
    // Dati mock come fallback quando l'API non è disponibile
    Object.assign(stats, {
      total_users: 12,
      total_products: 10,
      active_products: 10,
      total_carts: 8,
      avg_cart_items: 2.1,
      products_by_category: [
        { categoria: 'Smartphone', count: 1 },
        { categoria: 'Computer', count: 1 },
        { categoria: 'Audio', count: 1 },
        { categoria: 'Tablet', count: 1 },
        { categoria: 'Wearable', count: 1 },
        { categoria: 'Fotocamere', count: 1 },
        { categoria: 'Gaming', count: 1 },
        { categoria: 'Networking', count: 1 },
        { categoria: 'Monitor', count: 2 }
      ],
      top_cart_products: [
        { 
          product_id: 1, 
          count: 5, 
          product: { nome: 'Smartphone Samsung Galaxy S24', categoria: 'Smartphone', prezzo: '999.99' }
        },
        { 
          product_id: 2, 
          count: 3, 
          product: { nome: 'Laptop Dell XPS 13', categoria: 'Computer', prezzo: '1299.99' }
        },
        { 
          product_id: 3, 
          count: 2, 
          product: { nome: 'Cuffie Sony WH-1000XM5', categoria: 'Audio', prezzo: '399.99' }
        },
        { 
          product_id: 9, 
          count: 2, 
          product: { nome: 'Console PlayStation 5', categoria: 'Gaming', prezzo: '549.99' }
        },
        { 
          product_id: 4, 
          count: 1, 
          product: { nome: 'Tablet iPad Pro', categoria: 'Tablet', prezzo: '1199.99' }
        }
      ],
      user_registrations: [
        { month: '2025-05', count: 1 },
        { month: '2025-06', count: 2 },
        { month: '2025-07', count: 1 },
        { month: '2025-08', count: 3 },
        { month: '2025-09', count: 2 },
        { month: '2025-10', count: 3 }
      ]
    })
  }

  const loadMockProductStats = () => {
    Object.assign(productStats, {
      total: 10,
      active: 10,
      inactive: 0,
      low_stock: 2,
      out_of_stock: 0,
      by_category: [
        { categoria: 'Smartphone', count: 1 },
        { categoria: 'Computer', count: 1 },
        { categoria: 'Audio', count: 1 },
        { categoria: 'Tablet', count: 1 },
        { categoria: 'Wearable', count: 1 },
        { categoria: 'Fotocamere', count: 1 },
        { categoria: 'Gaming', count: 1 },
        { categoria: 'Networking', count: 1 },
        { categoria: 'Monitor', count: 2 }
      ]
    })
  }

  return {
    loading,
    error,
    stats,
    productStats,
    loadGeneralStats,
    loadProductStats,
    loadMockData,
    loadMockProductStats
  }
}