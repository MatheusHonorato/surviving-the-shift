<template>
  <div class="fixed bottom-0 inset-x-0 z-40">
    <div class="bg-white shadow-sm border-b border-slate-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
        <div class="flex items-center gap-3">
          <div class="text-xs text-slate-600 whitespace-nowrap">
            {{
              t({ pt: 'Paciente', en: 'Patient' }) +
              ' ' +
              (currentIndex + 1) +
              ' ' +
              t({ pt: 'de', en: 'of' }) +
              ' ' +
              total
            }}
          </div>
          <div class="flex-1 flex items-center gap-2">
            <div
              v-for="(n, i) in total"
              :key="i"
              class="h-2.5 rounded-full flex-1 transition-all duration-300 ring-1 ring-white/10"
              :class="segmentClass(i)"
              :aria-label="t({ pt: 'Paciente', en: 'Patient' }) + ' ' + (i + 1)"
              role="progressbar"
              :aria-valuemin="1"
              :aria-valuemax="total"
              :aria-valuenow="currentIndex + 1"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useLang } from '../composables/useLang'

defineOptions({
  name: 'PatientProgressBar',
})

const props = defineProps({
  total: {
    type: Number,
    required: true,
  },
  currentIndex: {
    type: Number,
    required: true,
  },
})

const { t } = useLang()

const segmentClass = (i) => {
  if (i < props.currentIndex) {
    return 'bg-blue-500 opacity-80'
  }
  if (i === props.currentIndex) {
    return 'bg-blue-500 opacity-100'
  }
  return 'bg-blue-500 opacity-35'
}
</script>

<style scoped></style>
