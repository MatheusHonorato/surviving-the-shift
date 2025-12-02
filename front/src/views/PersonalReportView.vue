<template>
  <LoadingState
    v-if="loading"
    :title="t({ pt: 'Carregando Relatório...', en: 'Loading Report...' })"
    :description="t({ pt: 'Analisando seu desempenho', en: 'Analyzing your performance' })"
  />

  <div v-else class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <GameHeader :title="{ pt: 'Relatório', en: 'Report' }" :show-progress="false">
      <template #menu-items>
        <HeaderMenuItem type="button" variant="default" @click="handleLogout">
          {{ t({ pt: 'Sair', en: 'Logout' }) }}
        </HeaderMenuItem>
      </template>
    </GameHeader>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 mb-6">
        {{ error }}
      </div>

      <div v-else-if="report" class="space-y-6">
        <section class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
          <h2 class="text-2xl font-bold text-slate-800 mb-4">
            {{ t({ pt: 'Resumo Geral', en: 'General Summary' }) }}
          </h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="border border-green-200 bg-green-50 rounded-lg p-4 text-green-700">
              <div class="text-sm font-medium opacity-80 mb-1">
                {{ t({ pt: 'Taxa de acerto', en: 'Accuracy rate' }) }}
              </div>
              <div class="text-2xl font-bold">{{ report.summary?.accuracy_rate || 0 }}%</div>
              <div class="text-xs opacity-70 mt-1">
                {{ report.summary?.correct_answers || 0 }} /
                {{ report.summary?.total_answers || 0 }}
              </div>
            </div>
            <div class="border border-blue-200 bg-blue-50 rounded-lg p-4 text-blue-700">
              <div class="text-sm font-medium opacity-80 mb-1">
                {{ t({ pt: 'Pacientes completados', en: 'Completed patients' }) }}
              </div>
              <div class="text-2xl font-bold">
                {{ report.summary?.completed_patients || 0 }} /
                {{ report.summary?.total_patients || 0 }}
              </div>
              <div class="text-xs opacity-70 mt-1">{{ report.summary?.completion_rate || 0 }}%</div>
            </div>
            <div class="border border-purple-200 bg-purple-50 rounded-lg p-4 text-purple-700">
              <div class="text-sm font-medium opacity-80 mb-1">
                {{ t({ pt: 'Tempo médio por passo', en: 'Avg time per step' }) }}
              </div>
              <div class="text-2xl font-bold">
                {{ formatTime(report.summary?.avg_time_seconds || 0) }}
              </div>
            </div>
            <div class="border border-orange-200 bg-orange-50 rounded-lg p-4 text-orange-700">
              <div class="text-sm font-medium opacity-80 mb-1">
                {{ t({ pt: 'Total de tentativas', en: 'Total attempts' }) }}
              </div>
              <div class="text-2xl font-bold">{{ report.summary?.total_attempts || 0 }}</div>
              <div class="text-xs opacity-70 mt-1">
                {{ t({ pt: 'Pacientes tentados', en: 'Attempted patients' }) }}:
                {{ report.summary?.attempted_patients || 0 }}
              </div>
            </div>
          </div>
        </section>

        <section class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
          <h2 class="text-2xl font-bold text-slate-800 mb-4">
            {{ t({ pt: 'Análise por Paciente', en: 'Patients Analysis' }) }}
          </h2>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                  <th class="px-4 py-3 text-left font-semibold text-slate-700">
                    {{ t({ pt: 'Paciente', en: 'Patient' }) }}
                  </th>
                  <th class="px-4 py-3 text-center font-semibold text-slate-700">
                    {{ t({ pt: 'Status', en: 'Status' }) }}
                  </th>
                  <th class="px-4 py-3 text-center font-semibold text-slate-700">
                    {{ t({ pt: 'Tentativas', en: 'Attempts' }) }}
                  </th>
                  <th class="px-4 py-3 text-center font-semibold text-slate-700">
                    {{ t({ pt: 'Taxa de Acerto', en: 'Accuracy' }) }}
                  </th>
                  <th class="px-4 py-3 text-center font-semibold text-slate-700">
                    {{ t({ pt: 'Tempo', en: 'Time' }) }}
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr
                  v-for="patient in report.patients"
                  :key="patient.patient_id"
                  class="hover:bg-slate-50"
                >
                  <td class="px-4 py-3">
                    <div class="font-medium text-slate-800">
                      {{ t({ pt: 'Paciente', en: 'Patient' }) }} {{ patient.patient_id }}
                    </div>
                    <div class="text-xs text-slate-500">
                      {{ patient.total_steps }} {{ t({ pt: 'passos', en: 'steps' }) }}
                    </div>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="
                        patient.is_completed
                          ? 'bg-green-100 text-green-800'
                          : 'bg-yellow-100 text-yellow-800'
                      "
                    >
                      {{
                        patient.is_completed
                          ? t({ pt: 'Completo', en: 'Completed' })
                          : t({ pt: 'Incompleto', en: 'Incomplete' })
                      }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center text-slate-600">{{ patient.total_attempts }}</td>
                  <td class="px-4 py-3 text-center">
                    <span
                      class="font-medium"
                      :class="
                        patient.accuracy_rate >= 80
                          ? 'text-green-600'
                          : patient.accuracy_rate >= 50
                            ? 'text-yellow-600'
                            : 'text-red-600'
                      "
                    >
                      {{ patient.accuracy_rate }}%
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center text-slate-600">
                    {{ formatTime(patient.best_attempt_time_seconds || 0) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Gráfico de evolução ao longo do tempo -->
        <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
          <section
            data-section="completion-chart"
            class="bg-white rounded-xl shadow-sm border border-slate-200 p-6"
          >
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <IconChart class="w-6 h-6 text-indigo-500" />
                {{ t({ pt: 'Evolução da Taxa de Conclusão', en: 'Completion Rate Over Time' }) }}
              </h2>
              <div v-if="paginatedChartData.length > 0" class="text-sm text-slate-600">
                {{
                  t({
                    pt: `Exibindo ${chartStartIndex + 1}-${chartEndIndex} de ${totalChartItems}`,
                    en: `Showing ${chartStartIndex + 1}-${chartEndIndex} of ${totalChartItems}`,
                  })
                }}
              </div>
            </div>
            <div v-if="paginatedChartData.length" class="space-y-4">
              <div
                v-for="point in paginatedChartData"
                :key="point.index"
                class="space-y-1"
              >
                <div class="flex justify-between text-xs text-slate-700">
                  <span class="font-medium">
                    {{ t({ pt: 'Tentativa', en: 'Attempt' }) }} {{ point.attempt }}
                  </span>
                  <span class="text-slate-500">
                    {{ point.completed }} /
                    {{ report.summary?.total_patients || 0 }}
                  </span>
                </div>
                <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                  <div
                    class="h-2 rounded-full bg-indigo-500"
                    :style="{ width: `${Math.max(point.percent, 0)}%` }"
                  ></div>
                </div>
              </div>
              <p class="text-xs text-slate-500">
                {{
                  t({
                    pt: 'Cada barra mostra quantos pacientes foram completados em cada tentativa, em relação ao total de pacientes.',
                    en: 'Each bar shows how many patients were completed in each attempt, relative to the total number of patients.',
                  })
                }}
              </p>
            </div>
            <div v-else-if="accuracyOverAttempts.length === 0" class="text-slate-500 text-sm">
              {{
                t({
                  pt: 'Você ainda não possui tentativas suficientes para gerar este gráfico.',
                  en: 'You do not have enough attempts yet to generate this chart.',
                })
              }}
            </div>
            <!-- Controles de paginação do gráfico -->
            <div
              v-if="totalChartPages > 1"
              class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-4"
            >
              <div class="flex items-center gap-2">
                <button
                  :disabled="currentChartPage === 1"
                  class="px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                  @click="goToChartPage(currentChartPage - 1)"
                >
                  {{ t({ pt: 'Anterior', en: 'Previous' }) }}
                </button>
                <div class="flex items-center gap-1">
                  <button
                    v-for="page in visibleChartPages"
                    :key="page"
                    class="px-3 py-2 text-sm font-medium rounded-lg transition-colors min-w-[40px]"
                    :class="
                      page === currentChartPage
                        ? 'bg-indigo-600 text-white'
                        : 'text-slate-700 bg-white border border-slate-300 hover:bg-slate-50'
                    "
                    @click="goToChartPage(page)"
                  >
                    {{ page }}
                  </button>
                </div>
                <button
                  :disabled="currentChartPage === totalChartPages"
                  class="px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                  @click="goToChartPage(currentChartPage + 1)"
                >
                  {{ t({ pt: 'Próxima', en: 'Next' }) }}
                </button>
              </div>
              <div class="text-sm text-slate-600">
                {{
                  t({
                    pt: `Página ${currentChartPage} de ${totalChartPages}`,
                    en: `Page ${currentChartPage} of ${totalChartPages}`,
                  })
                }}
              </div>
            </div>
          </section>
        </div>

        <!-- Blocos de pontos fortes / áreas de melhoria removidos a pedido do usuário -->

        <section
          data-section="attempts-history"
          class="bg-white rounded-xl shadow-sm border border-slate-200 p-6"
        >
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-slate-800">
              {{ t({ pt: 'Histórico de Tentativas', en: 'Attempts History' }) }}
            </h2>
            <div v-if="paginatedAttempts.length > 0" class="text-sm text-slate-600">
              {{
                t({
                  pt: `Exibindo ${startIndex + 1}-${endIndex} de ${totalAttempts}`,
                  en: `Showing ${startIndex + 1}-${endIndex} of ${totalAttempts}`,
                })
              }}
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                  <th class="px-4 py-3 text-left font-semibold text-slate-700">
                    {{ t({ pt: 'Paciente', en: 'Patient' }) }}
                  </th>
                  <th class="px-4 py-3 text-center font-semibold text-slate-700">
                    {{ t({ pt: 'Tentativa', en: 'Attempt' }) }}
                  </th>
                  <th class="px-4 py-3 text-center font-semibold text-slate-700">
                    {{ t({ pt: 'Status', en: 'Status' }) }}
                  </th>
                  <th class="px-4 py-3 text-center font-semibold text-slate-700">
                    {{ t({ pt: 'Acertos', en: 'Correct' }) }}
                  </th>
                  <th class="px-4 py-3 text-center font-semibold text-slate-700">
                    {{ t({ pt: 'Tempo', en: 'Time' }) }}
                  </th>
                  <th class="px-4 py-3 text-center font-semibold text-slate-700">
                    {{ t({ pt: 'Data', en: 'Date' }) }}
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr
                  v-for="(attempt, idx) in paginatedAttempts"
                  :key="idx"
                  class="hover:bg-slate-50"
                >
                  <td class="px-4 py-3">
                    <div class="font-medium text-slate-800">
                      {{ t({ pt: 'Paciente', en: 'Patient' }) }} {{ attempt.patient_id }}
                    </div>
                  </td>
                  <td class="px-4 py-3 text-center text-slate-600">#{{ attempt.attempt }}</td>
                  <td class="px-4 py-3 text-center">
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="
                        attempt.is_complete
                          ? 'bg-green-100 text-green-800'
                          : 'bg-yellow-100 text-yellow-800'
                      "
                    >
                      {{
                        attempt.is_complete
                          ? t({ pt: 'Completo', en: 'Complete' })
                          : t({ pt: 'Incompleto', en: 'Incomplete' })
                      }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span class="font-medium text-slate-800">
                      {{ attempt.correct_answers }}/{{ attempt.total_answers }}
                    </span>
                    <div class="text-xs text-slate-500">{{ attempt.completion_rate }}%</div>
                  </td>
                  <td class="px-4 py-3 text-center text-slate-600">
                    {{ formatTime(attempt.duration_seconds || 0) }}
                  </td>
                  <td class="px-4 py-3 text-center text-slate-600 text-xs">
                    {{ formatDate(attempt.started_at) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- Controles de paginação -->
          <div
            v-if="totalPages > 1"
            class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-4"
          >
            <div class="flex items-center gap-2">
              <button
                :disabled="currentPage === 1"
                class="px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                @click="goToPage(currentPage - 1)"
              >
                {{ t({ pt: 'Anterior', en: 'Previous' }) }}
              </button>
              <div class="flex items-center gap-1">
                <button
                  v-for="page in visiblePages"
                  :key="page"
                  class="px-3 py-2 text-sm font-medium rounded-lg transition-colors min-w-[40px]"
                  :class="
                    page === currentPage
                      ? 'bg-indigo-600 text-white'
                      : 'text-slate-700 bg-white border border-slate-300 hover:bg-slate-50'
                  "
                  @click="goToPage(page)"
                >
                  {{ page }}
                </button>
              </div>
              <button
                :disabled="currentPage === totalPages"
                class="px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                @click="goToPage(currentPage + 1)"
              >
                {{ t({ pt: 'Próxima', en: 'Next' }) }}
              </button>
            </div>
            <div class="text-sm text-slate-600">
              {{
                t({
                  pt: `Página ${currentPage} de ${totalPages}`,
                  en: `Page ${currentPage} of ${totalPages}`,
                })
              }}
            </div>
          </div>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup>
import { onMounted, ref, computed, watch } from 'vue'
import { personalReportApi } from '../services/api'
import { useLang } from '../composables/useLang'
import { useAuth } from '../composables/useAuth'
import { formatTime } from '../utils/formatHelpers'
import LoadingState from '../components/LoadingState.vue'
import GameHeader from '../components/GameHeader.vue'
import HeaderMenuItem from '../components/HeaderMenuItem.vue'
import IconChart from '../icons/IconChart.vue'

const { t, lang } = useLang()
const { handleLogout } = useAuth()
const loading = ref(true)
const error = ref('')
const report = ref(null)

// Paginação - Histórico de Tentativas
const currentPage = ref(1)
const itemsPerPage = ref(10)

// Paginação - Evolução da Taxa de Conclusão
const currentChartPage = ref(1)
const chartItemsPerPage = ref(10)

const formatDate = (dateString) => {
  if (!dateString) return '—'
  const date = new Date(dateString)
  return date.toLocaleDateString(lang.value === 'pt' ? 'pt-BR' : 'en-US', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const sortedAttempts = computed(() => {
  const attempts = report.value?.attempts
  if (!Array.isArray(attempts)) return []

  return [...attempts].sort((a, b) => {
    // Ordenar por data (mais recente primeiro) - ordem decrescente
    const aDate = a?.started_at ? new Date(a.started_at).getTime() : 0
    const bDate = b?.started_at ? new Date(b.started_at).getTime() : 0
    
    // Se as datas forem diferentes, ordenar por data (decrescente)
    if (aDate !== bDate) {
      return bDate - aDate // Decrescente: mais recente primeiro
    }
    
    // Se as datas forem iguais, ordenar por tentativa (decrescente)
    const aAttempt = Number(a?.attempt ?? 0)
    const bAttempt = Number(b?.attempt ?? 0)
    if (aAttempt !== bAttempt) return bAttempt - aAttempt
    
    // Por último, ordenar por paciente (decrescente)
    const aPatient = Number(a?.patient_id ?? 0)
    const bPatient = Number(b?.patient_id ?? 0)
    return bPatient - aPatient
  })
})

const totalAttempts = computed(() => sortedAttempts.value.length)

const totalPages = computed(() => Math.ceil(totalAttempts.value / itemsPerPage.value))

const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value)

const endIndex = computed(() =>
  Math.min(startIndex.value + itemsPerPage.value, totalAttempts.value)
)

const paginatedAttempts = computed(() => {
  return sortedAttempts.value.slice(startIndex.value, endIndex.value)
})

const visiblePages = computed(() => {
  const pages = []
  const maxVisible = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)

  if (end - start < maxVisible - 1) {
    start = Math.max(1, end - maxVisible + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    // Scroll suave para o topo da seção
    const section = document.querySelector('[data-section="attempts-history"]')
    if (section) {
      section.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
  }
}

// Paginação do gráfico de evolução
const totalChartItems = computed(() => accuracyOverAttempts.value.length)

const totalChartPages = computed(() => Math.ceil(totalChartItems.value / chartItemsPerPage.value))

const chartStartIndex = computed(() => (currentChartPage.value - 1) * chartItemsPerPage.value)

const chartEndIndex = computed(() =>
  Math.min(chartStartIndex.value + chartItemsPerPage.value, totalChartItems.value)
)

const paginatedChartData = computed(() => {
  return accuracyOverAttempts.value.slice(chartStartIndex.value, chartEndIndex.value)
})

const visibleChartPages = computed(() => {
  const pages = []
  const maxVisible = 5
  let start = Math.max(1, currentChartPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalChartPages.value, start + maxVisible - 1)

  if (end - start < maxVisible - 1) {
    start = Math.max(1, end - maxVisible + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

const goToChartPage = (page) => {
  if (page >= 1 && page <= totalChartPages.value) {
    currentChartPage.value = page
    // Scroll suave para o topo da seção
    const section = document.querySelector('[data-section="completion-chart"]')
    if (section) {
      section.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
  }
}

const accuracyOverAttempts = computed(() => {
  const attempts = report.value?.attempts
  if (!Array.isArray(attempts) || attempts.length === 0) return []

  const totalPatients = Number(report.value?.summary?.total_patients ?? 0)

  const groups = new Map()

  attempts.forEach((attempt) => {
    const key = Number(attempt?.attempt ?? 0)
    if (!Number.isFinite(key) || key <= 0) return

    const isComplete = Boolean(attempt?.is_complete)

    if (!groups.has(key)) {
      groups.set(key, { completed: 0 })
    }

    const agg = groups.get(key)
    if (isComplete) {
      agg.completed += 1
    }
  })

  return Array.from(groups.entries())
    .sort(([a], [b]) => b - a) // Ordem decrescente: tentativas mais recentes primeiro
    .map(([attemptNumber, agg], index) => {
      const completed = agg.completed
      const percent =
        totalPatients > 0 ? Math.max(0, Math.min((completed / totalPatients) * 100, 100)) : 0

      return {
        attempt: attemptNumber,
        completed,
        percent,
        index,
      }
    })
})

// Resetar páginas quando o relatório mudar
watch(
  () => report.value?.attempts,
  () => {
    currentPage.value = 1
    currentChartPage.value = 1
  }
)

onMounted(async () => {
  loading.value = true
  error.value = ''
  try {
    report.value = await personalReportApi.getReport()
  } catch (e) {
    error.value =
      e?.message || t({ pt: 'Falha ao carregar relatório', en: 'Failed to load report' })
  } finally {
    loading.value = false
  }
})
</script>
