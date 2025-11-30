<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import { useLang } from '../composables/useLang'

const router = useRouter()
const auth = useAuthStore()
const { t, lang, setLanguage } = useLang()

const email = ref('')
const password = ref('')

onMounted(() => {
  auth.clearError()
})

const onSubmit = async () => {
  const ok = await auth.login(email.value, password.value)
  if (ok) {
    router.push({ name: 'game' })
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 p-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-6">
      <div class="flex items-center justify-center">
        <img
          src="../../public/ppgmcs.png"
          :alt="t({ pt: 'Logo do aplicativo', en: 'Application logo' })"
          class="w-24"
        />
      </div>

      <div
        class="flex items-center justify-center space-x-2 mt-4"
        role="group"
        aria-label="Language selector"
      >
        <button
          type="button"
          :aria-pressed="lang === 'pt'"
          :aria-label="t({ pt: 'Português', en: 'Portuguese' })"
          class="px-3 py-1 rounded-md border transition cursor-pointer text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
          :class="
            lang === 'pt'
              ? 'border-slate-900 text-slate-900 font-semibold bg-slate-50'
              : 'border-slate-300 text-slate-500 hover:text-slate-700 hover:border-slate-400'
          "
          @click="setLanguage('pt')"
        >
          PT
        </button>
        <button
          type="button"
          :aria-pressed="lang === 'en'"
          :aria-label="t({ pt: 'Inglês', en: 'English' })"
          class="px-3 py-1 rounded-md border transition cursor-pointer text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
          :class="
            lang === 'en'
              ? 'border-slate-900 text-slate-900 font-semibold bg-slate-50'
              : 'border-slate-300 text-slate-500 hover:text-slate-700 hover:border-slate-400'
          "
          @click="setLanguage('en')"
        >
          EN
        </button>
      </div>

      <h1 class="text-2xl font-semibold text-center mb-4 mt-6">
        {{ t({ pt: 'Sobrevivendo ao plantão', en: 'Surviving the shift' }) }}
      </h1>

      <form class="space-y-4" novalidate @submit.prevent="onSubmit">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
            {{ t({ pt: 'E-mail', en: 'E-mail' }) }}
          </label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            autocomplete="email"
            :aria-invalid="!!auth.error"
            :aria-describedby="auth.error ? 'email-error' : undefined"
            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed"
            :class="auth.error ? 'border-red-300' : ''"
          />
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
            {{ t({ pt: 'Senha', en: 'Password' }) }}
          </label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            autocomplete="current-password"
            :aria-invalid="!!auth.error"
            :aria-describedby="auth.error ? 'password-error' : undefined"
            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed"
            :class="auth.error ? 'border-red-300' : ''"
          />
        </div>

        <div
          v-if="auth.error"
          id="email-error password-error"
          role="alert"
          class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-md p-2"
        >
          {{ auth.error }}
        </div>

        <button
          :disabled="auth.loading"
          type="submit"
          class="btn-primary w-full disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{
            auth.loading
              ? t({ pt: 'Entrando...', en: 'Signing in...' })
              : t({ pt: 'Entrar', en: 'Sign in' })
          }}
        </button>
      </form>

      <p class="mt-4 text-center text-sm text-gray-600">
        {{ t({ pt: 'Não tem conta?', en: "Don't have an account?" }) }}
        <router-link
          to="/register"
          class="text-blue-600 hover:underline focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded"
        >
          {{ t({ pt: 'Cadastre-se', en: 'Register' }) }}
        </router-link>
      </p>
    </div>
  </div>
</template>
