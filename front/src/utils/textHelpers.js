import { LANGUAGES } from '../constants/storage.js'

export const getText = (text, language = LANGUAGES.PT, fallback = '') => {
  if (!text) return fallback
  if (typeof text === 'string') return text
  if (typeof text === 'object') {
    const currentLang = language === LANGUAGES.EN ? LANGUAGES.EN : LANGUAGES.PT
    return text[currentLang] || text.pt || text.en || fallback
  }
  return String(text)
}

export const getTextLowercase = (text, language = LANGUAGES.PT) => {
  return getText(text, language, '').toLowerCase().trim()
}
