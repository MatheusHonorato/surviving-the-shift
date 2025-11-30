# End-to-End (E2E) Tests

This directory contains the end-to-end tests for the project using Playwright.

## Test Structure

- **auth.spec.js** - Authentication tests (login, registration, validations)
- **navigation.spec.js** - Navigation and route protection tests
- **dashboard.spec.js** - Dashboard tests
- **game.spec.js** - Main game/board tests
- **accessibility.spec.js** - Accessibility tests

## Running Tests

### Installation

```bash
npm install
npx playwright install
```

### Run all tests

```bash
npm run test:e2e
```

### Run with UI mode

```bash
npm run test:e2e:ui
```

### Run in headed mode (visible browser)

```bash
npm run test:e2e:headed
```

### Run in debug mode

```bash
npm run test:e2e:debug
```

### Run specific tests

```bash
npx playwright test auth
npx playwright test navigation
```

## Configuration

Tests are configured to:

- Run on multiple browsers (Chromium, Firefox, WebKit)
- Automatically start the development server
- Generate screenshots on failure
- Generate trace for debugging on retries

## Notes

- Tests assume the API is running at `http://localhost:8000/api`
- Some tests use mock cookies to simulate authentication
- Tests are designed to be resilient to minor UI changes
