export const formatTime = (totalSeconds) => {
  const seconds = Math.max(0, Math.round(Number(totalSeconds || 0)))
  const minutes = Math.floor(seconds / 60)
  const sec = seconds % 60
  return `${minutes}:${String(sec).padStart(2, '0')}`
}

export const calculateCompletionRate = (completed, attempted) => {
  const completedNum = Number(completed) || 0
  const attemptedNum = Number(attempted) || 0
  if (attemptedNum === 0) return '0%'
  const rate = Math.round((completedNum * 100) / attemptedNum)
  return isNaN(rate) ? '0%' : `${rate}%`
}

export const formatPercentage = (value) => {
  const num = Number(value) || 0
  const percentage = (num * 100).toFixed(0)
  return isNaN(percentage) ? '0%' : `${percentage}%`
}
