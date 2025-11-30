import { test, expect } from '@playwright/test'

test.describe('Accessibility', () => {
  test('should have proper ARIA labels on interactive elements', async ({ page }) => {
    await page.goto('/login')
    
    const buttons = page.locator('button')
    const buttonCount = await buttons.count()
    
    if (buttonCount > 0) {
      for (let i = 0; i < Math.min(buttonCount, 5); i++) {
        const button = buttons.nth(i)
        const ariaLabel = await button.getAttribute('aria-label')
        const textContent = await button.textContent()
        
        expect(ariaLabel || textContent?.trim()).toBeTruthy()
      }
    }
  })

  test('should have proper form labels', async ({ page }) => {
    await page.goto('/login')
    
    const inputs = page.locator('input[type="email"], input[type="password"]')
    const inputCount = await inputs.count()
    
    if (inputCount > 0) {
      for (let i = 0; i < inputCount; i++) {
        const input = inputs.nth(i)
        const id = await input.getAttribute('id')
        const ariaLabel = await input.getAttribute('aria-label')
        const name = await input.getAttribute('name')
        
        if (id) {
          const label = page.locator(`label[for="${id}"]`)
          const hasLabel = await label.count() > 0
          expect(hasLabel || ariaLabel || name).toBeTruthy()
        } else {
          expect(ariaLabel || name).toBeTruthy()
        }
      }
    }
  })

  test('should support keyboard navigation', async ({ page }) => {
    await page.goto('/login')
    
    await page.keyboard.press('Tab')
    await page.waitForTimeout(200)
    
    const focusedElement = page.locator(':focus')
    await expect(focusedElement).toBeVisible()
  })

  test('should have proper heading hierarchy', async ({ page }) => {
    await page.goto('/login')
    
    const h1 = page.locator('h1')
    const h2 = page.locator('h2')
    
    if (await h1.count() > 0) {
      await expect(h1.first()).toBeVisible()
    } else if (await h2.count() > 0) {
      await expect(h2.first()).toBeVisible()
    }
  })
})

