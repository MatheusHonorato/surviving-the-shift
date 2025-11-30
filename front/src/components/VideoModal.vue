<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <div
      class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
    >
      <div class="fixed inset-0 bg-black/50 transition-opacity z-0" @click="onClose"></div>

      <div
        class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full relative z-10"
      >
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <IconInfo class="h-6 w-6 text-white" />
              </div>
              <div class="ml-3">
                <h3 class="text-lg leading-6 font-medium text-white">{{ t(title) }}</h3>
                <p class="text-red-100">{{ t(subtitle) }}</p>
              </div>
            </div>
            <button class="text-red-100 hover:text-white" @click="onRetry">
              <IconX class="h-6 w-6" />
            </button>
          </div>
        </div>

        <div class="px-6 py-4">
          <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
              <IconError class="h-5 w-5 text-red-400 mt-0.5" />
              <div class="ml-3">
                <h4 class="text-sm font-medium text-red-800">
                  {{ lang === 'pt' ? 'Sua resposta' : 'Your answer' }}
                </h4>
                <p class="text-sm text-red-700 mt-1">{{ errorMessage }}</p>
              </div>
            </div>
          </div>

          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex">
              <IconInfo class="h-5 w-5 text-blue-400 mt-0.5" />
              <div class="ml-3">
                <h4 class="text-sm font-medium text-blue-800">
                  {{ lang === 'pt' ? 'Aprenda mais' : 'Learn more' }}
                </h4>
                <p class="text-sm text-blue-700 mt-1">
                  {{
                    lang === 'pt'
                      ? 'Assista ao vídeo abaixo para entender melhor:'
                      : 'Watch the video below to better understand:'
                  }}
                </p>
              </div>
            </div>
          </div>

          <div class="aspect-video mb-6">
            <div
              v-if="!videoUrl"
              class="w-full h-full flex items-center justify-center bg-slate-100 rounded-lg border-2 border-slate-200"
            >
              <div class="text-center p-6">
                <IconVideo class="w-16 h-16 mx-auto text-slate-400 mb-3" />
                <p class="text-slate-500 font-medium">
                  {{ lang === 'pt' ? 'Vídeo não disponível' : 'Video unavailable' }}
                </p>
                <p class="text-sm text-slate-400 mt-1">
                  {{
                    lang === 'pt'
                      ? 'O vídeo explicativo não pôde ser carregado'
                      : 'The explainer video could not be loaded'
                  }}
                </p>
              </div>
            </div>
            <iframe
              v-else
              :src="videoUrl"
              class="w-full h-full rounded-lg border-2 border-slate-200"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
            >
            </iframe>
          </div>

          <div class="flex flex-col sm:flex-row gap-3">
            <button class="btn-primary flex-1 group" @click="onRetry">
              <IconRetry
                class="w-5 h-5 mr-2 transition-transform duration-200 group-hover:rotate-180"
              />
              {{ t(retryText) }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useLang } from '../composables/useLang'
import IconInfo from '../icons/IconInfo.vue'
import IconX from '../icons/IconX.vue'
import IconError from '../icons/IconError.vue'
import IconVideo from '../icons/IconVideo.vue'
import IconRetry from '../icons/IconRetry.vue'

defineOptions({
  name: 'VideoModal',
})

defineProps({
  title: {
    type: [String, Object],
    default: () => ({ pt: 'Resposta Incorreta', en: 'Incorrect Answer' }),
  },
  subtitle: {
    type: [String, Object],
    default: () => ({ pt: 'Vamos aprender juntos!', en: "Let's learn together!" }),
  },
  errorMessage: {
    type: String,
    required: true,
  },
  videoUrl: {
    type: String,
    default: '',
  },
  retryText: {
    type: [String, Object],
    default: () => ({ pt: 'Tentar Novamente', en: 'Try Again' }),
  },
})

const emit = defineEmits(['close', 'retry'])

const { t, lang } = useLang()

const onClose = () => {
  emit('close')
}

const onRetry = () => {
  emit('retry')
}
</script>
