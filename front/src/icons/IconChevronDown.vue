<template>
  <svg
    :class="className"
    fill="none"
    stroke="currentColor"
    viewBox="0 0 24 24"
    xmlns="http://www.w3.org/2000/svg"
  >
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
  </svg>
</template>

<script setup>
defineOptions({
  name: 'IconChevronDown',
})

defineProps({
  className: {
    type: String,
    default: 'w-4 h-4',
  },
})
</script>
