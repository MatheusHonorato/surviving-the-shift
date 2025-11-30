<template>
  <router-link v-if="type === 'link'" :to="to" :class="classNames">
    <slot />
  </router-link>
  <button v-else :class="classNames" @click="handleClick">
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue'

defineOptions({
  name: 'HeaderMenuItem',
})

const props = defineProps({
  type: {
    type: String,
    required: true,
    validator: (value) => ['link', 'button'].includes(value),
  },
  to: {
    type: String,
    default: null,
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'primary', 'secondary'].includes(value),
  },
})

const emit = defineEmits(['click'])

const handleClick = () => {
  emit('click')
}

const classNames = computed(() => {
  const base = 'px-4 py-2 rounded-md border transition cursor-pointer font-medium text-sm'

  const variants = {
    default: 'border-slate-300 text-slate-700 hover:bg-slate-100',
    primary: 'border-blue-300 text-blue-700 hover:bg-blue-50',
    secondary: 'border-slate-300 text-slate-600 hover:bg-slate-50',
  }

  return `${base} ${variants[props.variant]}`
})
</script>
