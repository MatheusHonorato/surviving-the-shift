export const handleError = (error, context = '') => {
  const message = error?.message || error || 'Unknown error'
  const errorMessage = context ? `[${context}] ${message}` : message

  if (process.env.NODE_ENV === 'development') {
    console.error(errorMessage, error)
  }

  return message
}
