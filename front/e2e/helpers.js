/**
 * Helper functions for E2E tests
 */

/**
 * Wait for API response or timeout
 * @param {import('@playwright/test').Page} page
 * @param {string} urlPattern
 * @param {number} timeout
 */
export async function waitForApiResponse(page, urlPattern, timeout = 5000) {
  try {
    await page.waitForResponse(
      (response) => response.url().includes(urlPattern) && response.status() === 200,
      { timeout }
    )
  } catch {
    // Ignore timeout errors - API might not be available in test environment
  }
}

/**
 * Mock authentication by setting localStorage
 * @param {import('@playwright/test').Page} page
 * @param {string} token
 */
export async function mockAuth(page, token = 'mock-token') {
  await page.addInitScript((token) => {
    localStorage.setItem('auth_token', token)
  }, token)
}

/**
 * Clear all storage
 * @param {import('@playwright/test').Page} page
 */
export async function clearStorage(page) {
  await page.evaluate(() => {
    localStorage.clear()
    sessionStorage.clear()
  })
}

/**
 * Wait for element to be visible with retry
 * @param {import('@playwright/test').Locator} locator
 * @param {number} timeout
 */
export async function waitForVisible(locator, timeout = 5000) {
  try {
    await locator.waitFor({ state: 'visible', timeout })
    return true
  } catch {
    return false
  }
}

