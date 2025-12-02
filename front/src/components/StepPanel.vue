<template>
  <div class="space-y-4">
    <div
      v-if="gameStore.timeRemaining > 0"
      class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4"
    >
      <!-- Timer oculto em mobile, visível em desktop -->
      <div class="hidden sm:block">
        <TimerDisplay :time-remaining="gameStore.timeRemaining" />
      </div>

      <p
        v-if="!gameStore.patientCompleted"
        class="text-xs text-slate-600 leading-relaxed sm:text-right"
      >
        {{ instructionMessage }}
      </p>
    </div>

    <div
      v-if="gameStore.feedback && gameStore.feedbackType === 'error'"
      class="rounded-lg p-2.5 border text-sm bg-red-50 border-red-200 text-red-700"
      role="status"
    >
      <div class="flex items-center gap-2">
        <IconError class="w-4 h-4 text-red-500 flex-shrink-0" />
        <span>{{ gameStore.feedback }}</span>
      </div>
    </div>

    <div v-if="!gameStore.patientCompleted">
      <AlternativeList
        :alternatives="gameStore.currentStepAlternatives"
        :disabled="gameStore.patientCompleted"
        :loading="gameStore.currentStepLoading"
        :is-selecting="isSelecting"
        @select="selectAlternative"
      />
    </div>

    <div
      v-if="gameStore.patientCompleted && gameStore.feedbackType === 'success'"
      class="bg-blue-50 border border-blue-200 rounded-lg p-3"
    >
      <div class="grid grid-cols-2 gap-2 text-xs">
        <div v-for="env in successEnvironments" :key="env.name" class="flex justify-between">
          <span class="text-blue-700">{{ t(env.name) }}:</span>
          <span class="text-blue-900 font-medium">{{ t(env.value) }}</span>
        </div>
      </div>
    </div>

    <div v-if="gameStore.patientCompleted" class="flex flex-col gap-3">
      <button
        v-if="gameStore.feedbackType === 'success'"
        class="btn-next w-full group"
        @click="handleNextOrFinish"
      >
        <span>{{
          isLastPatient ? t(MESSAGES.BUTTONS.FINISH) : t(MESSAGES.BUTTONS.NEXT_PATIENT)
        }}</span>
        <IconArrowRight
          v-if="!isLastPatient"
          class="w-5 h-5 ml-2 transition-transform duration-200 group-hover:translate-x-1"
        />
        <IconCheckCircle
          v-else
          class="w-5 h-5 ml-2 transition-transform duration-200 group-hover:translate-x-1"
        />
      </button>
    </div>

    <div
      v-if="gameStore.currentStepLoading && !gameStore.currentStepAlternatives"
      class="text-center py-8"
    >
      <div class="inline-flex items-center">
        <div
          class="w-6 h-6 border-2 border-blue-200 border-t-blue-500 rounded-full animate-spin mr-3"
        ></div>
        <span class="text-sm text-slate-600">{{
          lang === 'pt' ? 'Carregando questão...' : 'Loading question...'
        }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useGameStore } from '../stores/gameStore.js'
import { useLang } from '../composables/useLang'
import { MESSAGES } from '../constants/messages.js'
import TimerDisplay from './TimerDisplay.vue'
import AlternativeList from './AlternativeList.vue'
import IconError from '../icons/IconError.vue'
import IconArrowRight from '../icons/IconArrowRight.vue'
import IconCheckCircle from '../icons/IconCheckCircle.vue'

defineOptions({
  name: 'StepPanel',
})

const emit = defineEmits(['patient-completed'])

const gameStore = useGameStore()
const { t, lang } = useLang()

const isSelecting = ref(false)

const isLastPatient = computed(() => {
  return gameStore.currentPatientIndex >= gameStore.patients.length - 1
})

const instructionMessage = computed(() => {
  return gameStore.currentStepIndex === 0
    ? t(MESSAGES.INSTRUCTIONS.FIRST_STEP)
    : t(MESSAGES.INSTRUCTIONS.NEXT_STEP)
})

const successEnvironments = computed(() => {
  const patient = gameStore.currentPatient
  if (!patient?.environments) return []
  return patient.environments.filter((env) => {
    if (!env?.name || !env?.value) return false
    const name =
      typeof env.name === 'object'
        ? (env.name.pt || env.name.en || '').toString().toLowerCase()
        : String(env.name).toLowerCase()
    return !['cronômetro', 'cronometro', 'timer'].includes(name)
  })
})

const selectAlternative = async (alternative) => {
  if (gameStore.patientCompleted || gameStore.currentStepLoading || isSelecting.value) return

  isSelecting.value = true
  try {
    await gameStore.selectAlternative(alternative)
  } finally {
    isSelecting.value = false
  }
}

const handleNextOrFinish = async () => {
  if (isLastPatient.value) {
    emit('patient-completed')
  } else {
    await gameStore.nextPatient()
    await gameStore.startAnswerAttempt()
  }
}

watch(
  () => gameStore.currentPatientIndex,
  async () => {
    if (!gameStore.currentStep) {
      await gameStore.loadCurrentStep()
    }
  },
  { immediate: true },
)
</script>
