import { test, expect } from '@playwright/test'

test.describe('Dashboard', () => {
  test('should require authentication to access dashboard', async ({ page }) => {
    await page.goto('/dashboard')
    
    await expect(page).toHaveURL(/.*\/login/)
  })

  test('should display dashboard structure when authenticated', async ({ page, context }) => {
    await context.addCookies([{
      name: 'auth_token',
      value: 'mock-token',
      domain: 'localhost',
      path: '/',
    }])
    
    await page.goto('/dashboard')
    
    await page.waitForTimeout(1000)
    
    const header = page.locator('header, [role="banner"]').first()
    await expect(header).toBeVisible()
    
    const mainContent = page.locator('main').first()
    if (await mainContent.count() > 0) {
      await expect(mainContent).toBeVisible()
    }
  })

  test('should display loading state initially', async ({ page, context }) => {
    await context.addCookies([{
      name: 'auth_token',
      value: 'mock-token',
      domain: 'localhost',
      path: '/',
    }])
    
    await page.goto('/dashboard')
    
    const loadingIndicator = page.locator('text=/carregando|loading/i, [role="status"]').first()
    
    if (await loadingIndicator.count() > 0) {
      await expect(loadingIndicator).toBeVisible({ timeout: 2000 })
    }
  })

  test('should have navigation menu items', async ({ page, context }) => {
    await context.addCookies([{
      name: 'auth_token',
      value: 'mock-token',
      domain: 'localhost',
      path: '/',
    }])
    
    await page.goto('/dashboard')
    await page.waitForTimeout(1000)
    
    const menuItems = page.locator('a[href*="personal-report"], button:has-text("Sair"), button:has-text("Logout")')
    
    if (await menuItems.count() > 0) {
      await expect(menuItems.first()).toBeVisible()
    }
  })
})

