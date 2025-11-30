<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <LoadingState
      v-if="gameStore.loading"
      :title="t({ pt: 'Carregando Jogo...', en: 'Loading Game...' })"
      :description="
        t({
          pt: 'Preparando sua experiência de aprendizado',
          en: 'Preparing your learning experience',
        })
      "
      :progress="gameStore.progressPercentage"
      :show-progress="true"
    />

    <ErrorState
      v-else-if="gameStore.error"
      :message="gameStore.error"
      @retry="gameStore.initializeGame"
    />

    <VideoModal
      v-else-if="gameStore.showVideoModal"
      :error-message="gameStore.errorMessage"
      :video-url="gameStore.currentPatient?.video_url"
      :title="
        gameStore.isTimeoutModal
          ? MESSAGES.VIDEO_MODAL.TIMEOUT_TITLE
          : MESSAGES.VIDEO_MODAL.INCORRECT_TITLE
      "
      :subtitle="
        gameStore.isTimeoutModal
          ? MESSAGES.VIDEO_MODAL.TIMEOUT_SUBTITLE
          : MESSAGES.VIDEO_MODAL.INCORRECT_SUBTITLE
      "
      @close="handleVideoModalClose"
      @retry="handleVideoModalRetry"
    />

    <div
      v-else-if="gameStore.gameCompleted"
      class="min-h-screen flex items-center justify-center p-6"
    >
      <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full text-center">
        <div
          class="w-20 h-20 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6"
        >
          <IconCheckCircle class="w-10 h-10 text-white" />
        </div>
        <h2 class="text-3xl font-bold text-slate-800 mb-4">
          🎉 {{ lang === 'pt' ? 'Parabéns!' : 'Congratulations!' }}
        </h2>
        <p class="text-lg text-slate-600 mb-6">
          {{
            lang === 'pt'
              ? 'Você completou todas as situações com sucesso!'
              : 'You completed all the patients successfully!'
          }}<br />
          {{ lang === 'pt' ? 'Pontuação:' : 'Score:' }} {{ gameStore.score }} /
          {{ gameStore.patients.length }}
        </p>
        <button class="btn-primary w-full" @click="gameStore.resetGame">
          <IconRetry class="w-5 h-5 mr-2" />
          {{ lang === 'pt' ? 'Jogar Novamente' : 'Play Again' }}
        </button>
      </div>
    </div>

    <div v-else class="min-h-screen">
      <GameHeader :progress-percentage="gameStore.progressPercentage">
        <template #menu-items>
          <HeaderMenuItem type="link" to="/personal-report" variant="primary">
            {{ lang === 'pt' ? 'Meu Relatório' : 'My Report' }}
          </HeaderMenuItem>
          <HeaderMenuItem type="button" variant="default" @click="handleLogout">
            {{ lang === 'pt' ? 'Sair' : 'Logout' }}
          </HeaderMenuItem>
        </template>
      </GameHeader>

      <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <section
          v-if="gameStore.selectedSteps && gameStore.selectedSteps.length > 0"
          class="bg-white/80 backdrop-blur-sm rounded-xl shadow px-3 sm:px-4 py-3 mb-5 border border-slate-100"
        >
          <div class="flex items-center justify-between mb-2">
            <h2
              class="text-xs font-semibold text-slate-600 flex items-center uppercase tracking-wide"
            >
              <IconClipboard class="w-3.5 h-3.5 mr-1.5 text-indigo-500" />
              {{ lang === 'pt' ? 'Evolução dos passos' : 'Steps progression' }}
            </h2>
            <span class="text-[11px] text-slate-400">
              {{ gameStore.currentStepIndex }} / {{ gameStore.selectedSteps.length }}
            </span>
          </div>

          <div class="overflow-x-auto scrollbar-thin">
            <div class="flex items-center gap-1.5 min-w-max">
              <div
                v-for="(step, index) in gameStore.selectedSteps"
                :key="index"
                class="flex items-center flex-shrink-0"
              >
                <div class="flex flex-col items-center min-w-[72px] sm:min-w-[80px]">
                  <div
                    class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-semibold transition-all"
                    :class="{
                      'bg-green-500 text-white': index < gameStore.currentStepIndex,
                      'bg-blue-500 text-white ring-1 ring-blue-300':
                        index === gameStore.currentStepIndex,
                      'bg-slate-200 text-slate-400': index > gameStore.currentStepIndex,
                    }"
                  >
                    <span v-if="index < gameStore.currentStepIndex">
                      <IconCheck class="w-4 h-4" />
                    </span>
                    <span v-else>
                      {{ index + 1 }}
                    </span>
                  </div>
                  <p
                    class="mt-1 text-[11px] text-center px-1 leading-tight max-w-[70px] sm:max-w-[80px] truncate"
                    :class="{
                      'text-green-600 font-medium': index < gameStore.currentStepIndex,
                      'text-blue-600 font-semibold': index === gameStore.currentStepIndex,
                      'text-slate-400': index > gameStore.currentStepIndex,
                    }"
                  >
                    {{ t(step.text) }}
                  </p>
                </div>
                <div v-if="index < gameStore.selectedSteps.length - 1" class="mx-1">
                  <div
                    class="h-px w-8 transition-colors"
                    :class="index < gameStore.currentStepIndex ? 'bg-green-400' : 'bg-slate-200'"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <div
          v-if="!hasStarted"
          class="mb-8 pb-12 min-h-[calc(100vh-96px)] flex items-center justify-center"
        >
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex flex-col gap-4 items-center justify-center flex-1">
              <div
                class="w-24 h-24 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center shadow-inner"
              >
                <IconPlay class="w-16 h-16 text-indigo-600" />
              </div>

              <span class="text-sm text-slate-600 text-center px-2">
                {{
                  lang === 'pt'
                    ? 'Você precisa iniciar o jogo para começar a responder as questões.'
                    : 'You need to start the game to start answering the questions.'
                }}
              </span>

              <button class="btn-primary w-full group" @click="openPatientModal">
                {{ lang === 'pt' ? 'Iniciar resolução' : 'Start solving' }}
              </button>

              <div class="w-full mt-2 border-t border-slate-100">
                <p
                  class="text-xs font-semibold text-slate-500 mb-2 text-center uppercase tracking-wide"
                >
                  {{ lang === 'pt' ? 'Como funciona' : 'How it works' }}
                </p>
                <div class="space-y-1.5 text-xs text-slate-500">
                  <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <span>{{
                      lang === 'pt'
                        ? 'Analise o caso e os parâmetros do paciente.'
                        : 'Review the case and patient parameters.'
                    }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>{{
                      lang === 'pt'
                        ? 'Clique em \"Iniciar resolução\" para iniciar o jogo.'
                        : 'Click \"Start solving\" to start the game.'
                    }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 items-stretch">
          <div
            class="bg-white rounded-2xl shadow-lg overflow-hidden lg:col-span-1 h-full flex flex-col"
          >
            <div class="relative h-40 sm:h-44">
              <img
                :src="gameStore.currentPatient?.image_url || './hospital.jpg'"
                alt="hospital"
                class="w-full h-full object-cover"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-slate-900/30 to-transparent"></div>
            </div>

            <div class="p-6 flex-1 flex flex-col">
              <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                <IconDocument class="w-5 h-5 mr-2 text-blue-500" />
                {{ lang === 'pt' ? 'Parâmetros' : 'Parameters' }}
              </h3>
              <div class="space-y-3">
                <div v-for="env in filteredEnvironments" :key="env.id">
                  <div class="flex justify-between py-2 border-b border-slate-100 last:border-b-0">
                    <span class="text-sm font-medium text-slate-600 capitalize"
                      >{{ t(env.name) }}:</span
                    >
                    <span class="text-sm text-slate-800 font-medium">{{ t(env.value) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl shadow-lg p-6 lg:col-span-2 h-full flex flex-col">
            <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
              <IconClipboard class="w-5 h-5 mr-2 text-indigo-500" />
              {{ lang === 'pt' ? 'Passos' : 'Steps' }}
            </h3>

            <StepPanel @patient-completed="onPatientCompleted" />
          </div>
        </div>
      </main>
    </div>

    <PatientProgressBar
      v-if="hasStarted && gameStore.patients && gameStore.patients.length > 0"
      :total="gameStore.patients.length"
      :current-index="gameStore.currentPatientIndex"
    />
  </div>
</template>

<script setup>
import { onMounted, computed, defineAsyncComponent } from 'vue'
import { useGameStore } from '../stores/gameStore.js'
import { useLang } from '../composables/useLang'
import { useAuth } from '../composables/useAuth'
import GameHeader from '../components/GameHeader.vue'
import HeaderMenuItem from '../components/HeaderMenuItem.vue'
import StepPanel from '../components/StepPanel.vue'
import PatientProgressBar from '../components/PatientProgressBar.vue'
import IconCheckCircle from '../icons/IconCheckCircle.vue'
import IconRetry from '../icons/IconRetry.vue'
import IconClipboard from '../icons/IconClipboard.vue'
import IconCheck from '../icons/IconCheck.vue'
import IconPlay from '../icons/IconPlay.vue'
import IconDocument from '../icons/IconDocument.vue'
import { TIMER_ENVIRONMENT_NAMES } from '../constants/game.js'
import { MESSAGES } from '../constants/messages.js'
import { getTextLowercase } from '../utils/textHelpers.js'

const LoadingState = defineAsyncComponent(() => import('../components/LoadingState.vue'))
const ErrorState = defineAsyncComponent(() => import('../components/ErrorState.vue'))
const VideoModal = defineAsyncComponent(() => import('../components/VideoModal.vue'))

defineOptions({
  name: 'GameBoard',
})

const gameStore = useGameStore()
const { handleLogout } = useAuth()
const { t, lang } = useLang()

const hasStarted = computed(() => gameStore.gameStarted)

const filteredEnvironments = computed(() => {
  const envs = gameStore.currentPatient?.environments
  if (!Array.isArray(envs)) return []
  return envs.filter((env) => {
    if (!env?.name) return false
    const name = getTextLowercase(env.name, lang.value)
    return !TIMER_ENVIRONMENT_NAMES.has(name)
  })
})

const openPatientModal = async () => {
  const hasProgress =
    gameStore.currentStepIndex > 0 ||
    gameStore.selectedSteps.length > 0 ||
    gameStore.patientCompleted ||
    gameStore.timeRemaining > 0

  if (hasProgress) await gameStore.resetGame()
  await gameStore.startAnswerAttempt()
}

const onPatientCompleted = () => {
  gameStore.nextPatient()
}

const handleVideoModalClose = async () => {
  await gameStore.resetGame()
}

const handleVideoModalRetry = async () => {
  await gameStore.resetGame()
}

onMounted(() => {
  gameStore.initializeGame()
})
</script>

<style scoped>
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-in {
  animation: slideIn 0.3s ease-out;
}
</style>
