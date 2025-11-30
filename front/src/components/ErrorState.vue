<template>
  <div class="min-h-screen flex items-center justify-center p-6">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full text-center">
      <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <IconWarning class="w-8 h-8 text-red-500" />
      </div>
      <h2 class="text-2xl font-bold text-red-800 mb-4">{{ translatedTitle }}</h2>
      <p class="text-red-700 mb-6 leading-relaxed">{{ message }}</p>
      <button class="btn-primary w-full" @click="onRetry">
        <IconRetry class="w-4 h-4 mr-2" />
        {{ translatedRetryText }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import IconWarning from '../icons/IconWarning.vue'
import IconRetry from '../icons/IconRetry.vue'
import { useLang } from '../composables/useLang'

defineOptions({
  name: 'ErrorState',
})

const props = defineProps({
  title: {
    type: [String, Object],
    default: () => ({ pt: 'Oops! Algo deu errado', en: 'Oops! Something went wrong' }),
  },
  message: {
    type: String,
    required: true,
  },
  retryText: {
    type: [String, Object],
    default: () => ({ pt: 'Tentar Novamente', en: 'Try Again' }),
  },
})

const emit = defineEmits(['retry'])

const { t } = useLang()

const translatedTitle = computed(() => t(props.title))
const translatedRetryText = computed(() => t(props.retryText))

const onRetry = () => {
  emit('retry')
}
</script>
