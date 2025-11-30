import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { patientsApi, answerKeysApi, stepsApi, answersApi } from '../services/api.js'
import { useLang } from '../composables/useLang.js'
import { useToastStore } from './toastStore.js'
import {
  TIMER_ENVIRONMENT_NAMES,
  ENVIRONMENT_SUCCESS_VALUES,
  TOAST_DURATIONS,
} from '../constants/game.js'
import { MESSAGES } from '../constants/messages.js'
import { handleError } from '../utils/errorHandler.js'
import { getText, getTextLowercase } from '../utils/textHelpers.js'

export const useGameStore = defineStore('game', () => {
  const { lang, t } = useLang()
  const toastStore = useToastStore()
  const loading = ref(true)
  const error = ref(null)
  const patients = ref([])
  const currentPatientIndex = ref(0)
  const currentStepIndex = ref(0)
  const selectedSteps = ref([])
  const feedback = ref('')
  const feedbackType = ref('')
  const score = ref(0)
  const gameCompleted = ref(false)
  const patientCompleted = ref(false)
  const showVideoModal = ref(false)
  const errorMessage = ref('')
  const isTimeoutModal = ref(false)
  const currentStep = ref(null)
  const currentStepLoading = ref(false)
  const shouldCloseStepModal = ref(false)

  const timeRemaining = ref(0)
  const timerInterval = ref(null)
  const timerStarted = ref(false)

  const gameStarted = ref(false)

  let loadingPromise = null

  const progressPercentage = computed(() => {
    if (gameCompleted.value || patients.value.length === 0) return 100
    const totalSteps = patients.value.reduce((sum, q) => sum + (q.steps?.length || 0), 0)
    const completedSteps =
      currentPatientIndex.value * (currentPatient.value?.steps?.length || 0) +
      currentStepIndex.value
    return Math.min((completedSteps / totalSteps) * 100, 100)
  })

  const formattedTimeRemaining = computed(() => {
    const minutes = Math.floor(timeRemaining.value / 60)
    const seconds = timeRemaining.value % 60
    return `${minutes}:${seconds.toString().padStart(2, '0')}`
  })

  const isTimeCritical = computed(() => {
    return timeRemaining.value <= 30 && timeRemaining.value > 0
  })

  const currentPatient = computed(() => {
    return patients.value[currentPatientIndex.value] || null
  })

  const currentStepAlternatives = computed(() => {
    return currentStep.value?.alternatives || []
  })

  const parseTimeToSeconds = (timeString) => {
    if (!timeString || typeof timeString !== 'string') return 0
    const [minutes, seconds] = timeString.split(':').map(Number)
    return (minutes || 0) * 60 + (seconds || 0)
  }

  const findTimerEnvironment = (environments) => {
    if (!Array.isArray(environments)) return null
    return environments.find((env) => {
      if (!env?.name) return false
      const name = getTextLowercase(env.name, lang.value)
      return TIMER_ENVIRONMENT_NAMES.has(name)
    })
  }

  const startTimer = () => {
    if (timerInterval.value || patientCompleted.value || timerStarted.value) return

    const cronometroEnv = findTimerEnvironment(currentPatient.value?.environments)

    if (!cronometroEnv || !cronometroEnv.value) return

    const rawTimerValue = getText(cronometroEnv.value, lang.value, '')

    timeRemaining.value = parseTimeToSeconds(rawTimerValue)
    timerStarted.value = true

    timerInterval.value = setInterval(() => {
      if (timeRemaining.value > 0) {
        timeRemaining.value--
      } else {
        handleTimeUp()
      }
    }, 1000)
  }

  const stopTimer = () => {
    if (timerInterval.value) {
      clearInterval(timerInterval.value)
      timerInterval.value = null
    }
    timerStarted.value = false
  }

  const resetTimer = () => {
    stopTimer()
    timeRemaining.value = 0
    timerStarted.value = false
  }

  const handleTimeUp = async () => {
    stopTimer()

    try {
      const stepId = currentStep.value?.id
      if (stepId) {
        await answersApi.finish({
          stepId,
          alternativeId: null,
        })
      }

      shouldCloseStepModal.value = true
      setTimeout(() => {
        shouldCloseStepModal.value = false
      }, 100)
    } catch (timeoutErr) {
      handleError(timeoutErr, 'handleTimeUp')
    }

    errorMessage.value = t(MESSAGES.TIME_UP)
    isTimeoutModal.value = true
    showVideoModal.value = true
    patientCompleted.value = true
  }

  const initializeGame = async () => {
    loading.value = true
    error.value = null

    try {
      patients.value = (await patientsApi.getAll()).map((q) => ({
        ...q,
        environments: Array.isArray(q?.environments) ? q.environments : [],
      }))

      if (!patients.value || patients.value.length === 0) {
        throw new Error(t(MESSAGES.ERRORS.NO_PATIENTS_FOUND))
      }

      await loadCurrentStep()
    } catch (err) {
      error.value = handleError(err, 'initializeGame') || t(MESSAGES.ERRORS.UNKNOWN_INIT_ERROR)
    } finally {
      loading.value = false
    }
  }

  const loadCurrentStep = async () => {
    if (loadingPromise) {
      await loadingPromise
      return
    }

    if (currentStepLoading.value) return

    loadingPromise = (async () => {
      currentStepLoading.value = true
      try {
        const patient = patients.value[currentPatientIndex.value]
        if (!patient) {
          handleError(`Patient not found at index ${currentPatientIndex.value}`, 'loadCurrentStep')
          return
        }

        const steps = patient.steps
        if (!Array.isArray(steps)) {
          handleError('Steps is not an array', 'loadCurrentStep')
          return
        }

        const step = steps[currentStepIndex.value]
        if (!step) {
          handleError(`Step not found at index ${currentStepIndex.value}`, 'loadCurrentStep')
          return
        }

        try {
          const fullStep = await stepsApi.getById(step.id)
          let baseAlternatives = Array.isArray(fullStep.alternatives)
            ? fullStep.alternatives.map((a) => ({ id: a.id, text: a.text }))
            : []

          if (fullStep.type === 'medication') {
            baseAlternatives = baseAlternatives.sort((a, b) => {
              const textA = getTextLowercase(a.text, lang.value)
              const textB = getTextLowercase(b.text, lang.value)
              return textA.localeCompare(textB, lang.value)
            })
          }

          const correctAlternativeIds = await answerKeysApi.getByStepId(step.id)
          const alternativesWithCorrectFlag = baseAlternatives.map((alt) => ({
            ...alt,
            isCorrect: correctAlternativeIds.includes(alt.id),
          }))

          currentStep.value = {
            ...step,
            alternatives: alternativesWithCorrectFlag,
          }
        } catch (err) {
          handleError(err, 'loadCurrentStep:fetchStep')
          currentStep.value = { ...step, alternatives: [] }
        }
      } catch (err) {
        handleError(err, 'loadCurrentStep')
      } finally {
        currentStepLoading.value = false
        loadingPromise = null
      }
    })()

    await loadingPromise
  }

  const selectAlternative = async (alternative) => {
    if (patientCompleted.value || currentStepLoading.value) return

    try {
      const isCorrect = alternative.isCorrect === true

      try {
        const stepId = currentStep.value?.id
        const alternativeId = alternative?.id
        if (stepId && alternativeId) {
          await answersApi.finish({ stepId, alternativeId, isCorrect })
        }
      } catch (postErr) {
        handleError(postErr, 'selectAlternative:finish')
      }

      if (isCorrect) {
        selectedSteps.value.push(alternative)
        currentStepIndex.value++

        const patient = patients.value[currentPatientIndex.value]
        const totalSteps = patient?.steps?.length || 0
        const isLastStep = currentStepIndex.value >= totalSteps

        if (isLastStep) {
          stopTimer()
          patientCompleted.value = true
          score.value++
          feedback.value = t(MESSAGES.SUCCESS.COMPLETE)
          feedbackType.value = 'success'
          updateEnvironmentsOnSuccess()
        } else {
          toastStore.success(t(MESSAGES.SUCCESS.CORRECT), TOAST_DURATIONS.MEDIUM)
          await loadCurrentStep()
          await startAnswerAttempt()
        }
      } else {
        stopTimer()
        const altText = getText(alternative.text, lang.value, '')
        errorMessage.value = `${t(MESSAGES.SELECTED)} "${altText}".`
        isTimeoutModal.value = false
        showVideoModal.value = true
        patientCompleted.value = true
      }
    } catch (err) {
      handleError(err, 'selectAlternative')
    }
  }

  const clearPatientState = () => {
    currentStepIndex.value = 0
    selectedSteps.value = []
    feedback.value = ''
    feedbackType.value = ''
    patientCompleted.value = false
    showVideoModal.value = false
    errorMessage.value = ''
    isTimeoutModal.value = false
    gameStarted.value = false
    timerStarted.value = false
  }

  const nextPatient = async () => {
    try {
      if (currentPatientIndex.value < patients.value.length - 1) {
        stopTimer()
        currentPatientIndex.value++
        await resetCurrentPatient()
        await loadCurrentStep()
      } else {
        stopTimer()
        gameCompleted.value = true
      }
    } catch (err) {
      handleError(err, 'nextPatient')
    }
  }

  const resetCurrentPatient = async () => {
    try {
      resetTimer()
      clearPatientState()
      await loadCurrentStep()
    } catch (err) {
      handleError(err, 'resetCurrentPatient')
    }
  }

  const resetCurrentStep = async () => {
    if (loadingPromise) await loadingPromise
    if (currentStepLoading.value) return

    loadingPromise = (async () => {
      currentStepLoading.value = true
      try {
        clearPatientState()
        await loadCurrentStep()
      } catch (stepErr) {
        handleError(stepErr, 'resetCurrentStep')
      } finally {
        currentStepLoading.value = false
        loadingPromise = null
      }
    })()
  }

  const resetGame = async () => {
    try {
      currentPatientIndex.value = 0
      score.value = 0
      gameCompleted.value = false
      gameStarted.value = false
      await resetCurrentPatient()
    } catch (err) {
      handleError(err, 'resetGame')
    }
  }

  const closeVideoModal = () => {
    showVideoModal.value = false
  }

  const resetAndStartAttempt = async () => {
    try {
      await resetCurrentPatient()
      await startAnswerAttempt()
    } catch (err) {
      handleError(err, 'resetAndStartAttempt')
    }
  }

  const startAnswerAttempt = async () => {
    try {
      const stepId = currentStep.value?.id
      if (stepId) await answersApi.start({ stepId })
      gameStarted.value = true
      if (currentStepIndex.value === 0 && !timerStarted.value && !timerInterval.value) {
        startTimer()
      }
    } catch (startErr) {
      handleError(startErr, 'startAnswerAttempt')
    }
  }

  const updateEnvironmentsOnSuccess = () => {
    const patient = patients.value[currentPatientIndex.value]
    if (!patient || !Array.isArray(patient.environments)) return

    patient.environments = patient.environments.map((env) => {
      if (!env?.name) return env

      const envNamePt = getTextLowercase(env.name, 'pt')
      const envNameEn = getTextLowercase(env.name, 'en')
      const successValue =
        ENVIRONMENT_SUCCESS_VALUES[envNamePt] || ENVIRONMENT_SUCCESS_VALUES[envNameEn]

      return successValue ? { ...env, value: successValue } : env
    })
  }

  return {
    loading,
    error,
    patients,
    currentPatientIndex,
    currentStepIndex,
    selectedSteps,
    feedback,
    feedbackType,
    score,
    gameCompleted,
    patientCompleted,
    showVideoModal,
    errorMessage,
    isTimeoutModal,
    currentStep,
    currentStepLoading,
    timeRemaining,
    formattedTimeRemaining,
    isTimeCritical,
    shouldCloseStepModal,
    gameStarted,

    currentPatient,
    currentStepAlternatives,
    progressPercentage,

    initializeGame,
    loadCurrentStep,
    selectAlternative,
    nextPatient,
    resetCurrentPatient,
    resetCurrentStep,
    resetGame,
    startAnswerAttempt,
    resetAndStartAttempt,
    closeVideoModal,
    startTimer,
    stopTimer,
    resetTimer,
    handleTimeUp,
  }
})
