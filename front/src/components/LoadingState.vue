<template>
  <div class="min-h-screen flex items-center justify-center">
    <div class="text-center">
      <div class="relative">
        <div
          class="w-16 h-16 border-4 border-blue-200 border-t-blue-500 rounded-full animate-spin mx-auto mb-4"
        ></div>
        <div
          class="absolute inset-0 w-16 h-16 border-4 border-transparent border-r-blue-300 rounded-full animate-pulse mx-auto"
        ></div>
      </div>
      <h2 class="text-2xl font-bold text-slate-700 mb-2">{{ translatedTitle }}</h2>
      <div v-if="showProgress" class="w-48 bg-slate-200 rounded-full h-2 mx-auto">
        <div
          class="bg-blue-500 h-2 rounded-full transition-all duration-300"
          :style="`width: ${progress}%`"
        ></div>
      </div>
      <p class="text-slate-600 mt-2">{{ translatedDescription }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useLang } from '../composables/useLang'

defineOptions({
  name: 'LoadingState',
})

const props = defineProps({
  title: {
    type: [String, Object],
    default: () => ({ pt: 'Carregando...', en: 'Loading...' }),
  },
  description: {
    type: [String, Object],
    default: () => ({ pt: 'Aguarde um momento', en: 'Please wait a moment' }),
  },
  progress: {
    type: Number,
    default: 0,
  },
  showProgress: {
    type: Boolean,
    default: false,
  },
})

const { t } = useLang()

const translatedTitle = computed(() => t(props.title))
const translatedDescription = computed(() => t(props.description))
</script>
