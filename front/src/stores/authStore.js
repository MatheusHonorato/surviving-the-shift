import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useGameStore } from './gameStore.js'
import { useLang } from '../composables/useLang'
import api from '../services/api'
import { STORAGE_KEYS } from '../constants/storage.js'
import { MESSAGES } from '../constants/messages.js'

export const useAuthStore = defineStore('auth', () => {
  const { t } = useLang()
  const token = ref(localStorage.getItem(STORAGE_KEYS.AUTH_TOKEN) || '')
  const user = ref(null)
  const loading = ref(false)
  const error = ref('')

  const isAuthenticated = computed(() => !!token.value)

  const setToken = (newToken) => {
    token.value = newToken || ''
    if (token.value) {
      localStorage.setItem(STORAGE_KEYS.AUTH_TOKEN, token.value)
    } else {
      localStorage.removeItem(STORAGE_KEYS.AUTH_TOKEN)
    }
  }

  const setUser = (u) => {
    user.value = u || null
  }

  const login = async (email, password) => {
    loading.value = true
    error.value = ''
    try {
      const response = await api.post('/auth/login', { email, password })
      const payload = response?.data || {}
      setToken(payload.token || '')
      setUser(payload.user || null)
      return true
    } catch (err) {
      error.value = err?.message || t(MESSAGES.ERRORS.LOGIN_FAILED)
      return false
    } finally {
      loading.value = false
    }
  }

  const register = async (name, email, password, passwordConfirmation) => {
    loading.value = true
    error.value = ''
    try {
      await api.post('/auth/register', {
        name,
        email,
        password,
        password_confirmation: passwordConfirmation,
      })
      return await login(email, password)
    } catch (err) {
      error.value = err?.message || t(MESSAGES.ERRORS.REGISTER_FAILED)
      return false
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    try {
      if (token.value) await api.post('/auth/logout')
    } catch {
    } finally {
      setToken('')
      setUser(null)
      localStorage.removeItem(STORAGE_KEYS.AUTH_TOKEN)
      useGameStore().resetGame()
    }
  }

  const clearError = () => {
    error.value = ''
  }

  return {
    token,
    user,
    loading,
    error,
    isAuthenticated,
    setToken,
    setUser,
    login,
    register,
    logout,
    clearError,
  }
})
