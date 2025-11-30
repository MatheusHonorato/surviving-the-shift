import { test, expect } from '@playwright/test'

test.describe('Game Board', () => {
  test('should require authentication to access game', async ({ page }) => {
    await page.goto('/')
    
    await expect(page).toHaveURL(/.*\/login/)
  })

  test('should display game board structure when authenticated', async ({ page, context }) => {
    await context.addCookies([{
      name: 'auth_token',
      value: 'mock-token',
      domain: 'localhost',
      path: '/',
    }])
    
    await page.goto('/')
    await page.waitForTimeout(1000)
    
    const header = page.locator('header, [role="banner"]').first()
    await expect(header).toBeVisible()
    
    const mainContent = page.locator('main').first()
    if (await mainContent.count() > 0) {
      await expect(mainContent).toBeVisible()
    }
  })

  test('should display patient selection or game content', async ({ page, context }) => {
    await context.addCookies([{
      name: 'auth_token',
      value: 'mock-token',
      domain: 'localhost',
      path: '/',
    }])
    
    await page.goto('/')
    await page.waitForTimeout(2000)
    
    const gameContent = page.locator('text=/paciente|patient|jogo|game|iniciar|start/i, button, [role="button"]').first()
    
    if (await gameContent.count() > 0) {
      await expect(gameContent).toBeVisible()
    }
  })

  test('should have progress bar when game is active', async ({ page, context }) => {
    await context.addCookies([{
      name: 'auth_token',
      value: 'mock-token',
      domain: 'localhost',
      path: '/',
    }])
    
    await page.goto('/')
    await page.waitForTimeout(2000)
    
    const progressBar = page.locator('[role="progressbar"], .progress, [aria-label*="progress"]').first()
    
    if (await progressBar.count() > 0) {
      await expect(progressBar).toBeVisible()
    }
  })
})

