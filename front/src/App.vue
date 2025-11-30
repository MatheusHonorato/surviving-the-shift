<script setup>
import Toast from './components/Toast.vue'

defineOptions({
  name: 'App',
})
</script>

<template>
  <router-view />
  <Toast />
</template>
