<template>
  <div>
    <div class="flex items-center justify-end mb-2">
      <span v-if="alternatives && alternatives.length > 0" class="text-xs text-slate-500">
        {{ alternatives.length }} {{ t({ pt: 'alternativas', en: 'alternatives' }) }}
      </span>
    </div>

    <div
      v-if="alternatives && alternatives.length > 0"
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 max-h-[400px] overflow-y-auto"
    >
      <button
        v-for="alternative in alternatives"
        :key="alternative.id"
        :disabled="disabled || isSelecting"
        :aria-label="t(alternative.text)"
        class="text-left p-2.5 rounded-lg border border-slate-200 hover:border-blue-400 hover:bg-blue-50 active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:border-slate-200 disabled:hover:bg-transparent group focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
        @click="$emit('select', alternative)"
      >
        <div class="flex items-start gap-2">
          <div
            class="w-4 h-4 rounded-full border-2 flex-shrink-0 mt-0.5 transition-colors"
            :class="
              isSelecting
                ? 'border-blue-400 bg-blue-100'
                : 'border-slate-300 group-hover:border-blue-500 group-hover:bg-blue-500'
            "
          ></div>
          <span class="text-xs text-slate-700 group-hover:text-blue-700 leading-snug">
            {{ t(alternative.text) }}
          </span>
        </div>
      </button>
    </div>

    <div v-else-if="!loading" class="text-center py-6">
      <p class="text-sm text-slate-500">
        {{ t({ pt: 'Nenhuma opção disponível', en: 'No options available' }) }}
      </p>
    </div>

    <div v-if="loading" class="text-center py-3">
      <div class="inline-flex items-center gap-2">
        <div
          class="w-4 h-4 border-2 border-blue-200 border-t-blue-500 rounded-full animate-spin"
        ></div>
        <span class="text-xs text-slate-600">{{
          t({ pt: 'Carregando alternativas...', en: 'Loading alternatives...' })
        }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useLang } from '../composables/useLang'

defineOptions({
  name: 'AlternativeList',
})

defineProps({
  alternatives: {
    type: Array,
    default: () => [],
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  isSelecting: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['select'])

const { t } = useLang()
</script>

<style scoped>
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background-color: rgb(203 213 225);
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background-color: rgb(148 163 184);
}
</style>
