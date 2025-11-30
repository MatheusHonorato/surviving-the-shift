import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import { ROUTE_NAMES } from '../router/index.js'

export function useAuth() {
  const router = useRouter()
  const auth = useAuthStore()

  const handleLogout = async () => {
    await auth.logout()
    router.push({ name: ROUTE_NAMES.LOGIN })
  }

  return {
    auth,
    handleLogout,
    isAuthenticated: auth.isAuthenticated,
  }
}
