import { watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

export function useAuthRedirect() {
  const router = useRouter()
  const authStore = useAuthStore()

  // Watch per stato autenticazione - redirect se non autenticato
  watch(() => authStore.isAuthenticated, (isAuth) => {
    if (!isAuth) {
      console.log('🔄 User not authenticated, redirecting to logout page')
      router.push('/logout')
    }
  }, { immediate: true })

  // Watch per admin - redirect se non admin su pagine admin
  const watchAdminAccess = () => {
    watch(() => authStore.isAdmin, (isAdmin) => {
      if (authStore.isAuthenticated && !isAdmin && router.currentRoute.value.meta?.requiresAdmin) {
        console.log('🚫 User not admin, redirecting to home')
        router.push('/')
      }
    }, { immediate: true })
  }

  return {
    watchAdminAccess
  }
}