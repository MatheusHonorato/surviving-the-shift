<template>
  <header class="bg-white shadow-sm border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
          <img src="../../public/ppgmcs.png" alt="Logo" class="w-14" />
          <h1 class="text-3xl font-bold text-slate-900 cursor-default hidden md:block">
            {{ titleText }}
          </h1>
        </div>

        <div class="flex items-center space-x-6">
          <div ref="dropdownRef" class="relative language-dropdown" @click.stop>
            <button
              class="px-3 py-1.5 rounded-md border border-slate-300 text-slate-700 hover:bg-slate-50 transition cursor-pointer text-sm font-medium flex items-center gap-1.5"
              @click="showLangDropdown = !showLangDropdown"
            >
              <span>{{ lang.toUpperCase() }}</span>
              <IconChevronDown
                class="w-4 h-4 transition-transform"
                :class="showLangDropdown ? 'rotate-180' : ''"
              />
            </button>

            <div
              v-if="showLangDropdown"
              class="absolute right-0 mt-2 w-32 bg-white rounded-md shadow-lg border border-slate-200 py-1 z-50"
            >
              <button
                class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 transition flex items-center justify-between"
                :class="
                  lang === 'pt' ? 'text-slate-900 font-semibold bg-slate-50' : 'text-slate-600'
                "
                @click="selectLanguage('pt')"
              >
                <span>Português</span>
                <IconCheck v-if="lang === 'pt'" class="w-4 h-4 text-blue-600" />
              </button>
              <button
                class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 transition flex items-center justify-between"
                :class="
                  lang === 'en' ? 'text-slate-900 font-semibold bg-slate-50' : 'text-slate-600'
                "
                @click="selectLanguage('en')"
              >
                <span>English</span>
                <IconCheck v-if="lang === 'en'" class="w-4 h-4 text-blue-600" />
              </button>
            </div>
          </div>
          <div v-if="auth?.isAuthenticated" class="flex items-center space-x-3">
            <slot name="menu-items">
              <HeaderMenuItem
                v-for="item in menuItems"
                :key="item.key"
                :type="item.type"
                :to="item.to"
                :variant="item.variant"
                @click="item.action"
              >
                {{ t(item.label) }}
              </HeaderMenuItem>
            </slot>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { useAuthStore } from '../stores/authStore'
import { useRouter, useRoute } from 'vue-router'
import { useLang } from '../composables/useLang'
import { computed, ref } from 'vue'
import { useClickOutside } from '../composables/useClickOutside'
import HeaderMenuItem from './HeaderMenuItem.vue'
import IconChevronDown from '../icons/IconChevronDown.vue'
import IconCheck from '../icons/IconCheck.vue'

defineOptions({
  name: 'GameHeader',
})

const props = defineProps({
  title: {
    type: [String, Object],
    default: () => ({
      pt: 'Sobrevivendo ao plantão',
      en: 'Surviving the shift',
    }),
  },
  progressPercentage: {
    type: Number,
    default: 0,
  },
  showProgress: {
    type: Boolean,
    default: true,
  },
})

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()
const { t, lang, setLanguage } = useLang()
const titleText = computed(() => t(props.title))
const showLangDropdown = ref(false)
const dropdownRef = ref(null)

const menuItems = computed(() => {
  const items = []

  if (route.name !== 'personal-report' && route.name !== 'dashboard') {
    items.push({
      key: 'report',
      type: 'link',
      to: '/personal-report',
      label: { pt: 'Relatório', en: 'Report' },
      variant: 'primary',
    })
  }

  items.push({
    key: 'logout',
    type: 'button',
    action: async () => {
      await auth.logout()
      router.push({ name: 'login' })
    },
    label: { pt: 'Sair', en: 'Logout' },
    variant: 'default',
  })

  return items
})

const selectLanguage = (newLang) => {
  setLanguage(newLang)
  showLangDropdown.value = false
}

useClickOutside(dropdownRef, () => {
  showLangDropdown.value = false
})
</script>
