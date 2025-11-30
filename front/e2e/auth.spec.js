import { test, expect } from '@playwright/test'

test.describe('Authentication Flow', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/')
  })

  test('should redirect to login when not authenticated', async ({ page }) => {
    await expect(page).toHaveURL(/.*\/login/)
    await expect(page.locator('h1, h2')).toContainText(/login|Login/i)
  })

  test('should display login form with required fields', async ({ page }) => {
    await page.goto('/login')
    
    const emailInput = page.locator('input[type="email"], input[name="email"]')
    const passwordInput = page.locator('input[type="password"]')
    const submitButton = page.locator('button[type="submit"]')

    await expect(emailInput).toBeVisible()
    await expect(passwordInput).toBeVisible()
    await expect(submitButton).toBeVisible()
  })

  test('should navigate to register page', async ({ page }) => {
    await page.goto('/login')
    
    const registerLink = page.locator('a[href*="register"], button:has-text("Cadastro"), button:has-text("Register")').first()
    
    if (await registerLink.count() > 0) {
      await registerLink.click()
      await expect(page).toHaveURL(/.*\/register/)
    }
  })

  test('should display register form with required fields', async ({ page }) => {
    await page.goto('/register')
    
    const nameInput = page.locator('input[name="name"]').first()
    const emailInput = page.locator('input[type="email"], input[name="email"]').first()
    const passwordInput = page.locator('input[type="password"]').first()
    const submitButton = page.locator('button[type="submit"]').first()

    await expect(nameInput).toBeVisible()
    await expect(emailInput).toBeVisible()
    await expect(passwordInput).toBeVisible()
    await expect(submitButton).toBeVisible()
  })

  test('should show validation errors on empty form submission', async ({ page }) => {
    await page.goto('/login')
    
    const submitButton = page.locator('button[type="submit"]').first()
    await submitButton.click()
    
    await page.waitForTimeout(500)
    
    const errorMessages = page.locator('text=/erro|error|inválido|invalid/i')
    const hasErrors = await errorMessages.count() > 0 || 
                     await page.locator('[aria-invalid="true"]').count() > 0
    
    expect(hasErrors).toBeTruthy()
  })

  test('should handle invalid credentials gracefully', async ({ page }) => {
    await page.goto('/login')
    
    const emailInput = page.locator('input[type="email"], input[name="email"]').first()
    const passwordInput = page.locator('input[type="password"]').first()
    const submitButton = page.locator('button[type="submit"]').first()

    await emailInput.fill('invalid@example.com')
    await passwordInput.fill('wrongpassword')
    await submitButton.click()
    
    await page.waitForTimeout(1000)
    
    const errorMessage = page.locator('text=/credencial|credential|inválido|invalid|erro|error/i')
    const hasError = await errorMessage.count() > 0
    
    expect(hasError).toBeTruthy()
  })
})

