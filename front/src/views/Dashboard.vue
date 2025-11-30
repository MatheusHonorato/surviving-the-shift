<script setup>
import { onMounted, ref } from 'vue'
import { dashboardApi } from '../services/api'
import { useLang } from '../composables/useLang'
import { useAuth } from '../composables/useAuth'
import { formatTime, calculateCompletionRate, formatPercentage } from '../utils/formatHelpers'
import { handleError } from '../utils/errorHandler'
import LoadingState from '../components/LoadingState.vue'
import GameHeader from '../components/GameHeader.vue'
import HeaderMenuItem from '../components/HeaderMenuItem.vue'

const loading = ref(true)
const error = ref('')
const patients = ref([])

const { t } = useLang()
const { handleLogout } = useAuth()

onMounted(async () => {
  loading.value = true
  error.value = ''
  try {
    patients.value = await dashboardApi.getDashboard()
  } catch (e) {
    error.value =
      handleError(e, 'Dashboard') ||
      t({ pt: 'Falha ao carregar estatísticas', en: 'Failed to load statistics' })
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <LoadingState
    v-if="loading"
    :title="t({ pt: 'Carregando Dashboard...', en: 'Loading Dashboard...' })"
    :description="
      t({ pt: 'Buscando estatísticas dos pacientes', en: 'Fetching patient statistics' })
    "
  />

  <div v-else class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <GameHeader :title="{ pt: 'Dashboard', en: 'Dashboard' }" :show-progress="false">
      <template #menu-items>
        <HeaderMenuItem type="link" to="/personal-report" variant="primary">
          {{ t({ pt: 'Meu Relatório', en: 'My Report' }) }}
        </HeaderMenuItem>
        <HeaderMenuItem type="button" variant="default" @click="handleLogout">
          {{ t({ pt: 'Sair', en: 'Logout' }) }}
        </HeaderMenuItem>
      </template>
    </GameHeader>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 mb-6">
        {{ error }}
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="item in patients"
          :key="item.patient_id"
          class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm"
        >
          <h2 class="text-lg font-semibold text-slate-800 truncate">
            {{ t({ pt: 'Paciente', en: 'Patient' }) + ' ' + item.patient_id }}
          </h2>
          <div class="mt-4">
            <div class="text-sm text-slate-600">
              {{ t({ pt: 'Usuários que responderam', en: 'Users who answered' }) }}
            </div>
            <div class="text-3xl font-bold text-green-600">
              {{ item.users_attempted || 0 }}
            </div>
            <div class="mt-4">
              <div class="text-sm text-slate-600">
                {{ t({ pt: 'Usuários que completaram', en: 'Users who completed' }) }}
              </div>
              <div class="text-2xl font-bold text-blue-600">
                {{ item.users_completed || 0 }}
              </div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-4">
              <div>
                <div class="text-xs text-slate-500">
                  {{ t({ pt: 'Taxa de conclusão', en: 'Completion rate' }) }}
                </div>
                <div class="text-lg font-semibold text-slate-800">
                  {{ calculateCompletionRate(item.users_completed, item.users_attempted) }}
                </div>
              </div>
              <div>
                <div class="text-xs text-slate-500">
                  {{ t({ pt: 'Taxa média de acerto', en: 'Average correct rate' }) }}
                </div>
                <div class="text-lg font-semibold text-slate-800">
                  {{ formatPercentage(item.avg_correct_rate) }}
                </div>
              </div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-4">
              <div>
                <div class="text-xs text-slate-500">
                  {{ t({ pt: 'Tempo méd. por passo', en: 'Average time per step' }) }}
                </div>
                <div class="text-lg font-semibold text-slate-800">
                  {{ formatTime(item.avg_step_time_sec) }}
                </div>
              </div>
              <div>
                <div class="text-xs text-slate-500">
                  {{ t({ pt: 'Passo mais difícil', en: 'Hardest step' }) }}
                </div>
                <div class="text-lg font-semibold text-slate-800">
                  {{ item.hardest_step_index ? '#' + item.hardest_step_index : '—' }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
