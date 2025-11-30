<template>
  <div v-if="timeRemaining > 0" class="flex items-center gap-2">
    <div
      class="font-mono text-lg font-bold px-3 py-1.5 rounded-md border transition-all duration-300"
      :class="
        isTimeCritical
          ? 'text-red-700 bg-red-50 border-red-300 animate-pulse'
          : 'text-blue-700 bg-blue-50 border-blue-300'
      "
    >
      {{ formattedTime }}
    </div>
    <span v-if="isTimeCritical" class="text-xs text-red-500 font-medium">⚠️</span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

defineOptions({
  name: 'TimerDisplay',
})

const props = defineProps({
  timeRemaining: {
    type: Number,
    required: true,
  },
})

const isTimeCritical = computed(() => props.timeRemaining <= 30 && props.timeRemaining > 0)

const formattedTime = computed(() => {
  const minutes = Math.floor(props.timeRemaining / 60)
  const seconds = props.timeRemaining % 60
  return `${minutes}:${seconds.toString().padStart(2, '0')}`
})
</script>
