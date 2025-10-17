import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import { createPinia } from 'pinia'
import axios from 'axios'

// Import styles
import '../css/app.css'

// Import delle pagine
import App from './App.vue'
import Home from './pages/Home.vue'
import Login from './pages/auth/Login.vue'
import Register from './pages/auth/Register.vue'
import ForgotPassword from './pages/auth/ForgotPassword.vue'
import ResetPassword from './pages/auth/ResetPassword.vue'
import Profile from './pages/auth/Profile.vue'
import Logout from './pages/Logout.vue'
import Products from './pages/products/Products.vue'
import ProductDetail from './pages/products/ProductDetail.vue'
import Cart from './pages/cart/Cart.vue'
import Dashboard from './pages/admin/DashboardSimple.vue'
import EmailLogs from './pages/admin/EmailLogs.vue'

// Import dei composables/stores
import { useAuthStore } from './stores/auth'

// Configurazione Axios
axios.defaults.baseURL = 'http://127.0.0.1:8000/api'
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'

// Interceptor per aggiungere token a tutte le richieste
axios.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token')
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config
})

// Interceptor per gestire errori di autorizzazione
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('auth_token')
            localStorage.removeItem('user')
            window.location.href = '/login'
        }
        return Promise.reject(error)
    }
)

// Router configuration
const routes = [
    {
        path: '/',
        name: 'home',
        component: Home,
        meta: { requiresAuth: false }
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { requiresGuest: true }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: { requiresGuest: true }
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: ForgotPassword,
        meta: { requiresGuest: true }
    },
    {
        path: '/reset-password',
        name: 'reset-password',
        component: ResetPassword,
        meta: { requiresGuest: true }
    },
    {
        path: '/profile',
        name: 'profile',
        component: Profile,
        meta: { requiresAuth: true }
    },
    {
        path: '/logout',
        name: 'logout',
        component: Logout,
        meta: { requiresAuth: false }
    },
    {
        path: '/products',
        name: 'products',
        component: Products,
        meta: { requiresAuth: false }
    },
    {
        path: '/products/:id',
        name: 'product-detail',
        component: ProductDetail,
        props: true,
        meta: { requiresAuth: false }
    },
    {
        path: '/cart',
        name: 'cart',
        component: Cart,
        meta: { requiresAuth: true }
    },
    {
        path: '/admin/dashboard',
        name: 'admin-dashboard',
        component: Dashboard,
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/admin/emails',
        name: 'admin-emails',
        component: EmailLogs,
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/'
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// Route guards
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore()
    
    // Inizializza lo store auth se necessario
    if (!authStore.initialized) {
        await authStore.initializeAuth()
    }
    
    // Controlla se la route richiede autenticazione
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next('/login')
        return
    }
    
    // Controlla se la route è solo per ospiti
    if (to.meta.requiresGuest && authStore.isAuthenticated) {
        next('/')
        return
    }
    
    // Controlla se la route richiede privilegi admin
    if (to.meta.requiresAdmin && !authStore.isAdmin) {
        next('/')
        return
    }
    
    next()
})

// Crea l'app Vue
const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// Configura axios come plugin globale
app.config.globalProperties.$http = axios

app.mount('#app')
