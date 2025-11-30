<template>
  <div v-if="steps && steps.length > 0" class="bg-slate-50 rounded-lg p-3 border border-slate-200">
    <div
      class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-thin"
      role="region"
      :aria-label="t({ pt: 'Progresso dos passos', en: 'Steps progress' })"
    >
      <div
        v-for="(step, index) in steps"
        :key="index"
        class="flex items-center flex-shrink-0"
        :aria-label="`${t({ pt: 'Passo', en: 'Step' })} ${index + 1}: ${t(step.text)}`"
      >
        <div class="flex flex-col items-center min-w-[90px] sm:min-w-[100px]">
          <div
            class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all"
            :class="getStepClass(index)"
            role="status"
            :aria-label="getStepAriaLabel(index)"
          >
            <IconCheck v-if="index < currentIndex" class="w-4 h-4" aria-hidden="true" />
            <span v-else aria-hidden="true">{{ index + 1 }}</span>
          </div>
          <p
            class="mt-1 text-xs text-center px-1 leading-tight max-w-[85px] sm:max-w-none truncate"
            :class="getStepTextClass(index)"
          >
            {{ t(step.text) }}
          </p>
        </div>
        <div v-if="index < steps.length - 1" class="mx-1.5" aria-hidden="true">
          <div
            class="h-px w-10 transition-colors"
            :class="index < currentIndex ? 'bg-green-400' : 'bg-slate-200'"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useLang } from '../composables/useLang'
import IconCheck from '../icons/IconCheck.vue'

defineOptions({
  name: 'StepProgress',
})

const props = defineProps({
  steps: {
    type: Array,
    required: true,
  },
  currentIndex: {
    type: Number,
    required: true,
  },
})

const { t } = useLang()

const getStepClass = (index) => {
  if (index === props.currentIndex) {
    return 'bg-blue-500 text-white ring-1 ring-blue-300'
  }
  if (index < props.currentIndex) {
    return 'bg-green-500 text-white'
  }
  return 'bg-slate-200 text-slate-400'
}

const getStepTextClass = (index) => {
  if (index < props.currentIndex) {
    return 'text-green-600 font-medium'
  }
  if (index === props.currentIndex) {
    return 'text-blue-600 font-semibold'
  }
  return 'text-slate-400'
}

const getStepAriaLabel = (index) => {
  if (index < props.currentIndex) {
    return t({ pt: 'Concluído', en: 'Completed' })
  }
  if (index === props.currentIndex) {
    return t({ pt: 'Em execução', en: 'In progress' })
  }
  return t({ pt: 'Pendente', en: 'Pending' })
}
</script>

<style scoped>
.scrollbar-thin::-webkit-scrollbar {
  height: 3px;
}

.scrollbar-thin::-webkit-scrollbar-track {
  background: transparent;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
  background-color: rgb(203 213 225);
  border-radius: 3px;
}
</style>
