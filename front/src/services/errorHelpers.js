export const normalizeMessage = (message) => {
  return message
    .toLowerCase()
    .trim()
    .replace(/[àáâãäå]/g, 'a')
    .replace(/[èéêë]/g, 'e')
    .replace(/[ìíîï]/g, 'i')
    .replace(/[òóôõö]/g, 'o')
    .replace(/[ùúûü]/g, 'u')
    .replace(/[ç]/g, 'c')
    .replace(/[ñ]/g, 'n')
}

import { STORAGE_KEYS, DEFAULT_LANGUAGE } from '../constants/storage.js'

export const translateError = (message) => {
  const lang = localStorage.getItem(STORAGE_KEYS.LANGUAGE) || DEFAULT_LANGUAGE
  const isEn = lang === 'en'

  const translations = {
    'invalid credentials': {
      pt: 'Credenciais inválidas. Verifique seu e-mail e senha.',
      en: 'Invalid credentials. Please check your email and password.',
    },
    'invalid data': {
      pt: 'Dados inválidos',
      en: 'Invalid data',
    },
    'could not connect to server': {
      pt: 'Não foi possível conectar com o servidor. Verifique se a API está rodando.',
      en: 'Could not connect to server. Please check if the API is running.',
    },
    'connection error': {
      pt: 'Erro de conexão. Verifique sua internet.',
      en: 'Connection error. Please check your internet connection.',
    },
    'unexpected error while making request': {
      pt: 'Erro inesperado ao fazer requisição.',
      en: 'Unexpected error while making request.',
    },
    'unknown error': {
      pt: 'Erro desconhecido',
      en: 'Unknown error',
    },
    'the password field must be at least 6 characters': {
      pt: 'O campo senha deve ter pelo menos 6 caracteres.',
      en: 'The password field must be at least 6 characters.',
    },
    'the email has already been taken': {
      pt: 'Este e-mail já está em uso.',
      en: 'The email has already been taken.',
    },
  }

  const normalized = normalizeMessage(message)

  if (translations[normalized]) {
    return translations[normalized][isEn ? 'en' : 'pt']
  }

  for (const [key, translation] of Object.entries(translations)) {
    if (normalized.includes(key) || key.includes(normalized)) {
      return translation[isEn ? 'en' : 'pt']
    }
  }

  if (
    normalized.includes('password') &&
    (normalized.includes('at least') || normalized.includes('pelo menos'))
  ) {
    return translations['the password field must be at least 6 characters'][isEn ? 'en' : 'pt']
  }

  if (
    normalized.includes('email') &&
    (normalized.includes('already been taken') ||
      normalized.includes('ja esta em uso') ||
      normalized.includes('já está em uso'))
  ) {
    return translations['the email has already been taken'][isEn ? 'en' : 'pt']
  }

  const errorMatch = message.match(/(?:Erro|Error) (\d+): (.+)/)
  if (errorMatch) {
    const [, status, errorMsg] = errorMatch
    const normalizedErrorMsg = normalizeMessage(errorMsg)
    const translatedMsg = translations[normalizedErrorMsg]
      ? translations[normalizedErrorMsg][isEn ? 'en' : 'pt']
      : errorMsg
    return isEn ? `Error ${status}: ${translatedMsg}` : `Erro ${status}: ${translatedMsg}`
  }

  return message
}
