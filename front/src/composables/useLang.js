import { computed } from 'vue'
import { useLocaleStore } from '../stores/localeStore'
import { LANGUAGES } from '../constants/storage.js'

export function useLang() {
  const locale = useLocaleStore()
  const lang = computed(() => locale.language)

  const t = (value, fallback = '') => {
    if (value == null) return fallback
    if (typeof value === 'string') return value
    if (typeof value === 'object') {
      const pt = value.pt ?? fallback
      const en = value.en ?? pt
      return locale.language === LANGUAGES.EN ? en : pt
    }
    return String(value)
  }

  return {
    lang,
    t,
    setLanguage: (l) => locale.setLanguage(l),
    toggleLanguage: () => locale.toggle(),
    isEnglish: computed(() => locale.isEnglish),
    isPortuguese: computed(() => locale.isPortuguese),
  }
}
