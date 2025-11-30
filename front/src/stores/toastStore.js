import { defineStore } from 'pinia'
import { ref } from 'vue'
import { TOAST_DURATIONS } from '../constants/game.js'

export const TOAST_TYPES = {
  SUCCESS: 'success',
  ERROR: 'error',
  INFO: 'info',
  WARNING: 'warning',
}

export const useToastStore = defineStore('toast', () => {
  const toasts = ref([])

  const generateToastId = () => Date.now() + Math.random()

  const showToast = (message, type = TOAST_TYPES.SUCCESS, duration = TOAST_DURATIONS.MEDIUM) => {
    const id = generateToastId()
    const toast = {
      id,
      message,
      type,
      duration,
    }

    toasts.value.push(toast)

    if (duration > 0) {
      setTimeout(() => {
        removeToast(id)
      }, duration)
    }

    return id
  }

  const removeToast = (id) => {
    const index = toasts.value.findIndex((t) => t.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }

  const clearAll = () => {
    toasts.value = []
  }

  const success = (message, duration = TOAST_DURATIONS.MEDIUM) =>
    showToast(message, TOAST_TYPES.SUCCESS, duration)

  const error = (message, duration = TOAST_DURATIONS.MEDIUM) =>
    showToast(message, TOAST_TYPES.ERROR, duration)

  const info = (message, duration = TOAST_DURATIONS.MEDIUM) =>
    showToast(message, TOAST_TYPES.INFO, duration)

  const warning = (message, duration = TOAST_DURATIONS.MEDIUM) =>
    showToast(message, TOAST_TYPES.WARNING, duration)

  return {
    toasts,
    showToast,
    removeToast,
    clearAll,
    success,
    error,
    info,
    warning,
  }
})
