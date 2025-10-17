import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('auth_token'),
    initialized: false,
    loading: false
  }),

  getters: {
    isAuthenticated: (state) => !!state.token && !!state.user,
    isAdmin: (state) => {
      // Usa is_admin dall'API o fallback su roles array
      if (state.user?.is_admin !== undefined) {
        return state.user.is_admin
      }
      return state.user?.roles?.includes('admin') || false
    },
    isBusiness: (state) => {
      return state.user?.roles?.includes('business') || false
    },
    userFullName: (state) => {
      return state.user ? `${state.user.nome} ${state.user.cognome}` : ''
    }
  },

  actions: {
    async initializeAuth() {
      if (this.initialized) return
      
      this.loading = true
      const token = localStorage.getItem('auth_token')
      const userData = localStorage.getItem('user')
      
      if (token && userData) {
        try {
          this.token = token
          this.user = JSON.parse(userData)
          
          // Verifica che il token sia ancora valido
          await this.fetchProfile()
        } catch (error) {
          this.clearAuth()
        }
      }
      
      this.initialized = true
      this.loading = false
    },

    async login(email, password, remember = false) {
      try {
        this.loading = true
        const response = await axios.post('/auth/login', {
          email,
          password,
          remember
        })
        
        const { token, user } = response.data
        
        this.token = token
        this.user = user
        
        localStorage.setItem('auth_token', token)
        localStorage.setItem('user', JSON.stringify(user))
        
        return { success: true, data: response.data }
      } catch (error) {
        const message = error.response?.data?.message || 'Errore durante il login'
        return { success: false, message }
      } finally {
        this.loading = false
      }
    },

    async register(userData) {
      try {
        this.loading = true
        const response = await axios.post('/auth/register', userData)
        
        // Dopo la registrazione, prova a fare login automaticamente
        if (response.data && userData.email && userData.password) {
          await this.login(userData.email, userData.password)
        }
        
        return { success: true, data: response.data }
      } catch (error) {
        let message = 'Errore durante la registrazione'
        
        if (error.response?.data?.errors) {
          const errors = error.response?.data?.errors
          return { success: false, message, errors }
        } else if (error.response?.data?.message) {
          message = error.response.data.message
        }
        
        return { success: false, message }
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        if (this.token) {
          await axios.post('/auth/logout')
        }
      } catch (error) {
        console.error('Errore durante il logout:', error)
      } finally {
        this.clearAuth()
      }
    },

    async fetchProfile() {
      try {
        const response = await axios.get('/auth/user')
        this.user = response.data.user
        localStorage.setItem('user', JSON.stringify(this.user))
        return { success: true, data: response.data }
      } catch (error) {
        const message = error.response?.data?.message || 'Errore nel caricamento del profilo'
        return { success: false, message }
      }
    },

    async updateProfile(profileData) {
      try {
        this.loading = true
        const response = await axios.put('/auth/profile', profileData)
        
        this.user = response.data.user
        localStorage.setItem('user', JSON.stringify(this.user))
        
        return { success: true, data: response.data }
      } catch (error) {
        let message = 'Errore durante l\'aggiornamento del profilo'
        
        if (error.response?.data?.errors) {
          const errors = Object.values(error.response.data.errors).flat()
          message = errors.join(', ')
        } else if (error.response?.data?.message) {
          message = error.response.data.message
        }
        
        return { success: false, message }
      } finally {
        this.loading = false
      }
    },

    clearAuth() {
      this.user = null
      this.token = null
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
    },

    setUser(user) {
      this.user = user
      localStorage.setItem('user', JSON.stringify(user))
    },

    setToken(token) {
      this.token = token
      localStorage.setItem('auth_token', token)
    }
  }
})