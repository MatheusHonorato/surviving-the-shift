import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

export const ROUTE_NAMES = {
  LOGIN: 'login',
  REGISTER: 'register',
  GAME: 'game',
  DASHBOARD: 'dashboard',
  PERSONAL_REPORT: 'personal-report',
}

export const ROUTE_PATHS = {
  LOGIN: '/login',
  REGISTER: '/register',
  GAME: '/',
  DASHBOARD: '/dashboard',
  PERSONAL_REPORT: '/personal-report',
}

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: ROUTE_PATHS.LOGIN,
      name: ROUTE_NAMES.LOGIN,
      component: () => import('../views/LoginView.vue'),
      meta: {
        title: { pt: 'Login', en: 'Login' },
        requiresAuth: false,
      },
    },
    {
      path: ROUTE_PATHS.REGISTER,
      name: ROUTE_NAMES.REGISTER,
      component: () => import('../views/RegisterView.vue'),
      meta: {
        title: { pt: 'Cadastro', en: 'Register' },
        requiresAuth: false,
      },
    },
    {
      path: ROUTE_PATHS.GAME,
      name: ROUTE_NAMES.GAME,
      component: () => import('../views/GameBoard.vue'),
      meta: {
        title: { pt: 'Jogo', en: 'Game' },
        requiresAuth: true,
      },
    },
    {
      path: ROUTE_PATHS.DASHBOARD,
      name: ROUTE_NAMES.DASHBOARD,
      component: () => import('../views/Dashboard.vue'),
      meta: {
        title: { pt: 'Dashboard', en: 'Dashboard' },
        requiresAuth: true,
      },
    },
    {
      path: ROUTE_PATHS.PERSONAL_REPORT,
      name: ROUTE_NAMES.PERSONAL_REPORT,
      component: () => import('../views/PersonalReportView.vue'),
      meta: {
        title: { pt: 'Relatório', en: 'Report' },
        requiresAuth: true,
      },
    },
  ],
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }
    return { top: 0 }
  },
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  const authPages = [ROUTE_NAMES.LOGIN, ROUTE_NAMES.REGISTER]

  if (authPages.includes(to.name) && auth.isAuthenticated) {
    return next({ name: ROUTE_NAMES.GAME })
  }

  if (to.meta?.requiresAuth && !auth.isAuthenticated) {
    return next({
      name: ROUTE_NAMES.LOGIN,
      query: { redirect: to.fullPath },
    })
  }

  return next()
})

export default router
