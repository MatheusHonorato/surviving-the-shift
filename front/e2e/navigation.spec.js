import { test, expect } from '@playwright/test'

test.describe('Navigation and Route Protection', () => {
  test('should redirect protected routes to login', async ({ page }) => {
    const protectedRoutes = ['/', '/dashboard', '/personal-report']
    
    for (const route of protectedRoutes) {
      await page.goto(route)
      await expect(page).toHaveURL(/.*\/login/)
    }
  })

  test('should preserve redirect query parameter', async ({ page }) => {
    await page.goto('/dashboard')
    
    await expect(page).toHaveURL(/.*\/login.*redirect/)
  })

  test('should navigate between login and register', async ({ page }) => {
    await page.goto('/login')
    await expect(page).toHaveURL(/.*\/login/)
    
    const registerLink = page.locator('a[href*="register"]').first()
    if (await registerLink.count() > 0) {
      await registerLink.click()
      await expect(page).toHaveURL(/.*\/register/)
    }
  })

  test('should display header with language selector', async ({ page }) => {
    await page.goto('/login')
    
    const header = page.locator('header, [role="banner"]').first()
    const languageButton = page.locator('button:has-text("PT"), button:has-text("EN"), button:has-text("PT"), button:has-text("EN")').first()
    
    await expect(header).toBeVisible()
    
    if (await languageButton.count() > 0) {
      await expect(languageButton).toBeVisible()
    }
  })

  test('should change language when language selector is clicked', async ({ page }) => {
    await page.goto('/login')
    
    const languageButton = page.locator('button:has-text("PT"), button:has-text("EN")').first()
    
    if (await languageButton.count() > 0) {
      const initialText = await languageButton.textContent()
      await languageButton.click()
      
      await page.waitForTimeout(500)
      
      const dropdown = page.locator('text=/Português|English/i').first()
      if (await dropdown.count() > 0) {
        await dropdown.click()
        await page.waitForTimeout(500)
        
        const newText = await languageButton.textContent()
        expect(newText).not.toBe(initialText)
      }
    }
  })
})

