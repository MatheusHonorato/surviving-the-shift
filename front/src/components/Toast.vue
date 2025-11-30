<template>
  <Teleport to="body">
    <div class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 pointer-events-none">
      <TransitionGroup name="toast" tag="div" class="flex flex-col gap-2">
        <div
          v-for="toast in toastStore.toasts"
          :key="toast.id"
          :class="getToastClasses(toast.type)"
          class="pointer-events-auto min-w-[300px] max-w-md rounded-lg shadow-lg p-4 flex items-start gap-3 animate-slide-in"
        >
          <div class="flex-shrink-0">
            <IconSuccess v-if="toast.type === 'success'" class="w-5 h-5" />
            <IconError v-else-if="toast.type === 'error'" class="w-5 h-5" />
            <IconWarning v-else-if="toast.type === 'warning'" class="w-5 h-5" />
            <IconInfo v-else class="w-5 h-5" />
          </div>
          <div class="flex-1">
            <p class="text-sm font-medium">{{ toast.message }}</p>
          </div>
          <button
            class="flex-shrink-0 text-current opacity-50 hover:opacity-100 transition-opacity"
            aria-label="Fechar"
            @click="toastStore.removeToast(toast.id)"
          >
            <IconX class="w-4 h-4" />
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { useToastStore } from '../stores/toastStore'
import IconSuccess from '../icons/IconSuccess.vue'
import IconError from '../icons/IconError.vue'
import IconWarning from '../icons/IconWarning.vue'
import IconInfo from '../icons/IconInfo.vue'
import IconX from '../icons/IconX.vue'

const toastStore = useToastStore()

const getToastClasses = (type) => {
  const baseClasses = 'border'
  const typeClasses = {
    success: 'bg-green-50 border-green-200 text-green-800',
    error: 'bg-red-50 border-red-200 text-red-800',
    warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
    info: 'bg-blue-50 border-blue-200 text-blue-800',
  }
  return `${baseClasses} ${typeClasses[type] || typeClasses.info}`
}
</script>

<style scoped>
.toast-enter-active {
  transition: all 0.3s ease-out;
}

.toast-leave-active {
  transition: all 0.3s ease-in;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

.toast-move {
  transition: transform 0.3s ease;
}

@keyframes slide-in {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.animate-slide-in {
  animation: slide-in 0.3s ease-out;
}
</style>
