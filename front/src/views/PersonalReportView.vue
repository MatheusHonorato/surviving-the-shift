<template>
  <LoadingState
    v-if="loading"
    :title="t({ pt: 'Carregando Relatório...', en: 'Loading Report...' })"
    :description="t({ pt: 'Analisando seu desempenho', en: 'Analyzing your performance' })"
  />

  <div v-else class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <GameHeader :title="{ pt: 'Meu Relatório', en: 'My Report' }" :show-progress="false">
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
              <div class="text-xs opacity-70 mt-1">
                {{ t({ pt: 'Total', en: 'Total' }) }}:
                {{ formatTime(report.summary?.total_time_seconds || 0) }}
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <section class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
              <IconSuccess class="w-6 h-6 text-green-500" />
              {{ t({ pt: 'Pontos Fortes', en: 'Strengths' }) }}
            </h2>
            <div v-if="report.insights?.strong_steps?.length > 0" class="space-y-3">
              <div
                v-for="(step, idx) in report.insights.strong_steps"
                :key="idx"
                class="bg-green-50 border border-green-200 rounded-lg p-3"
              >
                <div class="font-medium text-green-800">
                  {{ t({ pt: 'Paciente', en: 'Patient' }) }} {{ step.patient_id }} -
                  {{ t({ pt: 'Passo', en: 'Step' }) }} {{ step.step_index }}
                </div>
                <div class="text-sm text-green-600 mt-1">
                  {{ step.accuracy_rate }}% {{ t({ pt: 'de acerto', en: 'accuracy' }) }} ({{
                    step.correct_answers
                  }}/{{ step.total_answers }})
                </div>
              </div>
            </div>
            <div v-else class="text-slate-500 text-sm">
              {{
                t({
                  pt: 'Continue praticando para identificar seus pontos fortes!',
                  en: 'Keep practicing to identify your strengths!',
                })
              }}
            </div>
          </section>

          <section class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
              <IconError class="w-6 h-6 text-red-500" />
              {{ t({ pt: 'Áreas de Melhoria', en: 'Areas for Improvement' }) }}
            </h2>
            <div v-if="report.insights?.weak_steps?.length > 0" class="space-y-3">
              <div
                v-for="(step, idx) in report.insights.weak_steps"
                :key="idx"
                class="bg-red-50 border border-red-200 rounded-lg p-3"
              >
                <div class="font-medium text-red-800">
                  {{ t({ pt: 'Paciente', en: 'Patient' }) }} {{ step.patient_id }} -
                  {{ t({ pt: 'Passo', en: 'Step' }) }} {{ step.step_index }}
                </div>
                <div class="text-sm text-red-600 mt-1">
                  {{ step.accuracy_rate }}% {{ t({ pt: 'de acerto', en: 'accuracy' }) }} ({{
                    step.incorrect_answers
                  }}
                  {{ t({ pt: 'erros', en: 'errors' }) }})
                </div>
                <div v-if="step.attempts_until_correct" class="text-xs text-red-500 mt-1">
                  {{ t({ pt: 'Precisou de', en: 'Needed' }) }} {{ step.attempts_until_correct }}
                  {{ t({ pt: 'tentativas para acertar', en: 'attempts to get it right' }) }}
                </div>
              </div>
            </div>
            <div v-else class="text-slate-500 text-sm">
              {{
                t({
                  pt: 'Parabéns! Você está indo muito bem!',
                  en: 'Congratulations! You are doing great!',
                })
              }}
            </div>
          </section>
        </div>

        <section class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
          <h2 class="text-2xl font-bold text-slate-800 mb-4">
            {{ t({ pt: 'Histórico de Tentativas', en: 'Attempts History' }) }}
          </h2>
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
                <tr v-for="(attempt, idx) in report.attempts" :key="idx" class="hover:bg-slate-50">
                  <td class="px-4 py-3">
                    <div class="font-medium text-slate-800">
                      {{ `Paciente ${attempt.patient_id}` }}
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
        </section>
      </div>
    </main>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { personalReportApi } from '../services/api'
import { useLang } from '../composables/useLang'
import { useAuth } from '../composables/useAuth'
import { formatTime } from '../utils/formatHelpers'
import LoadingState from '../components/LoadingState.vue'
import GameHeader from '../components/GameHeader.vue'
import HeaderMenuItem from '../components/HeaderMenuItem.vue'
import IconSuccess from '../icons/IconSuccess.vue'
import IconError from '../icons/IconError.vue'

const { t, lang } = useLang()
const { handleLogout } = useAuth()
const loading = ref(true)
const error = ref('')
const report = ref(null)

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
