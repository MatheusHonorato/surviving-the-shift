import axios from 'axios'
import { translateError } from './errorHelpers.js'
import { STORAGE_KEYS } from '../constants/storage.js'
import { useAuthStore } from '../stores/authStore.js'

const API_CONFIG = {
  BASE_URL: import.meta.env.VITE_API_URL || 'http://localhost',
  TIMEOUT: 10000,
  DEFAULT_PER_PAGE: 100,
}

const api = axios.create({
  baseURL: API_CONFIG.BASE_URL,
  timeout: API_CONFIG.TIMEOUT,
  headers: {
    'Content-Type': 'application/json',
  },
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem(STORAGE_KEYS.AUTH_TOKEN)
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.code === 'ECONNREFUSED') {
      throw new Error(translateError('could not connect to server'))
    }
    if (error.response) {
      const { status, data } = error.response

      if (status === 401) {
        const authStore = useAuthStore()
        authStore.setToken('')
        authStore.setUser(null)
        localStorage.removeItem(STORAGE_KEYS.AUTH_TOKEN)
        
        if (window.location.pathname !== '/login' && window.location.pathname !== '/register') {
          window.location.href = '/login'
        }
        
        return Promise.reject(new Error(translateError('unauthorized')))
      }

      if (status === 422 && data?.errors) {
        const firstField = Object.keys(data.errors)[0]
        const firstMessage = Array.isArray(data.errors[firstField])
          ? data.errors[firstField][0]
          : data.errors[firstField]

        const translatedMessage = translateError(firstMessage || 'invalid data')
        throw new Error(translatedMessage)
      }

      const message = data?.message || 'unknown error'
      const lang = localStorage.getItem(STORAGE_KEYS.LANGUAGE) || 'pt'
      const isEn = lang === 'en'
      const errorMessage = message.match(/^(?:Erro|Error) \d+:/)
        ? message
        : isEn
          ? `Error ${status}: ${message}`
          : `Erro ${status}: ${message}`
      throw new Error(translateError(errorMessage))
    }
    if (error.request) {
      throw new Error(translateError('connection error'))
    }
    throw new Error(translateError('unexpected error while making request'))
  },
)

export default api

const extractData = (payload) => {
  if (Array.isArray(payload?.data)) return payload.data
  if (Array.isArray(payload?.data?.data)) return payload.data.data
  return []
}

export const patientsApi = {
  async getAll() {
    const response = await api.get('/patients', {
      params: { per_page: API_CONFIG.DEFAULT_PER_PAGE },
    })
    return extractData(response.data)
  },
}

export const stepsApi = {
  async getById(stepId) {
    const response = await api.get(`/steps/${stepId}`)
    const payload = response.data
    const data = payload?.data || payload

    return {
      id: data?.id,
      patient_id: data?.patient_id,
      type: data?.type,
      alternatives: Array.isArray(data?.alternatives)
        ? data.alternatives.map((a) => ({
            id: a.id,
            text: a.description,
          }))
        : [],
    }
  },
}

export const answerKeysApi = {
  async getByStepId(stepId) {
    const response = await api.get('/answer-keys', {
      params: {
        step_id: stepId,
        per_page: API_CONFIG.DEFAULT_PER_PAGE,
      },
    })
    const list = extractData(response.data)
    return list.map((ak) => ak.alternative_id)
  },
}

export const answersApi = {
  async start({ stepId }) {
    const response = await api.post('/answers/start', {
      step_id: stepId,
    })
    return response?.data?.data
  },
  async create({ stepId, alternativeId, isCorrect }) {
    await api.post('/answers', {
      step_id: stepId,
      alternative_id: alternativeId,
      is_correct: isCorrect,
    })
    return true
  },
  async finish({ stepId, alternativeId, isCorrect }) {
    const response = await api.post('/answers/finish', {
      step_id: stepId,
      alternative_id: alternativeId,
      is_correct: isCorrect,
    })
    return response?.data?.data
  },
}

export const dashboardApi = {
  async getDashboard() {
    const response = await api.get('/dashboard')
    return response.data?.data || { patients: [], users: null }
  },
}

export const personalReportApi = {
  async getReport() {
    const response = await api.get('/personal-report')
    return response?.data?.data || {}
  },
}
