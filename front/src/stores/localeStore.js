import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { STORAGE_KEYS, LANGUAGES, DEFAULT_LANGUAGE } from '../constants/storage.js'

export const useLocaleStore = defineStore('locale', () => {
  const language = ref(localStorage.getItem(STORAGE_KEYS.LANGUAGE) || DEFAULT_LANGUAGE)

  const isEnglish = computed(() => language.value === LANGUAGES.EN)
  const isPortuguese = computed(() => language.value === LANGUAGES.PT)

  const setLanguage = (lang) => {
    if (lang !== LANGUAGES.PT && lang !== LANGUAGES.EN) return
    language.value = lang
    localStorage.setItem(STORAGE_KEYS.LANGUAGE, lang)
  }

  const toggle = () => {
    setLanguage(language.value === LANGUAGES.PT ? LANGUAGES.EN : LANGUAGES.PT)
  }

  return {
    language,
    isEnglish,
    isPortuguese,
    setLanguage,
    toggle,
  }
})
