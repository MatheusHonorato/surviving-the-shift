# 🎮 Surviving the Shift - Frontend

Educational web application built with Vue.js 3 for simulating clinical cases in a hospital environment. Students practice medical decision-making through a gamified system.

## 📋 Table of Contents

- [Tech Stack](#-tech-stack)
- [Architecture](#-architecture)
- [Getting Started](#-getting-started)

## 🛠 Tech Stack

### Core

- **Vue.js 3.5.18** - Reactive JavaScript framework
- **Pinia 3.0.3** - State management
- **Vue Router 4.5.1** - SPA routing

### Styling

- **Tailwind CSS 4.1.12** - Utility-first CSS framework
- **@tailwindcss/vite** - Tailwind integration with Vite

### Build & Dev Tools

- **Vite 7.0.6** - Build tool and dev server
- **ESLint 9.31.0** - Code quality linter
- **Prettier 3.6.2** - Code formatter
- **Vue DevTools** - Development tools

### Testing

- **Playwright 1.48.0** - End-to-end testing framework

### HTTP Client

- **Axios 1.11.0** - HTTP client for API requests

### Requirements

- **Node.js**: ^20.19.0 or >=22.12.0
- **npm** or **yarn** package manager
- **Backend API** running (Laravel)

## 🏗 Architecture

### Project Structure

```
src/
├── components/      # Reusable Vue components
├── composables/    # Reusable composition functions
│   ├── useAuth.js  # Authentication logic
│   └── useLang.js  # Internationalization logic
├── constants/      # Application constants
│   ├── game.js     # Game constants
│   └── storage.js  # Storage keys
├── css/           # Global styles
├── icons/         # SVG icon components
├── router/        # Route configuration
├── services/      # API services
│   ├── api.js     # Axios client and endpoints
│   └── errorHelpers.js
├── stores/        # Pinia stores
│   ├── authStore.js
│   ├── gameStore.js
│   ├── localeStore.js
│   └── toastStore.js
├── utils/         # Utility functions
│   ├── errorHandler.js
│   ├── formatHelpers.js
│   └── textHelpers.js
└── views/         # Page components
```

### Architecture Pattern

The project follows a modular and scalable architecture:

1. **Composition API** - All stores and components use Composition API
2. **Separation of Concerns** - Clear separation of responsibilities
3. **DRY Principle** - Reusable code through composables and utils
4. **Single Source of Truth** - Pinia stores as the single source of truth

### Data Flow

```
View → Composable/Store → Service → API Backend
  ↑                                    ↓
  └─────────── Response ──────────────┘
```

### State Management (Pinia)

#### `authStore`

Manages user authentication:

- JWT token
- User data
- Login/Logout/Register
- Loading and error states

#### `gameStore`

Manages all game logic:

- Patient list
- Current patient and step
- Timer/chronometer
- Selected answers
- Progress and score
- Loading states

#### `localeStore`

Manages application language:

- Current language (pt/en)
- localStorage persistence
- Language switching methods

#### `toastStore`

Manages toast notifications:

- Notification queue
- Types (success, error, warning, info)
- Auto-removal after duration

### Composables

#### `useAuth()`

Composable for authentication functionality:

```javascript
const { auth, handleLogout, isAuthenticated } = useAuth()
```

#### `useLang()`

Composable for internationalization:

```javascript
const { lang, t, setLanguage, toggleLanguage } = useLang()
// t() translates objects { pt: '...', en: '...' }
```

### Utilities

#### `errorHandler.js`

Centralized error handling:

```javascript
import { handleError } from '@/utils/errorHandler'
handleError(error, 'context')
```

#### `textHelpers.js`

Helpers for multilingual text:

```javascript
import { getText, getTextLowercase } from '@/utils/textHelpers'
getText(textObject, language, fallback)
```

#### `formatHelpers.js`

Data formatting:

```javascript
import { formatTime, formatPercentage, calculateCompletionRate } from '@/utils/formatHelpers'
```

### Routing

Router configured in `src/router/index.js` with:

- **Lazy Loading** - Components loaded on demand
- **Route Guards** - Authentication protection
- **Meta Tags** - Translated titles per route
- **Scroll Behavior** - Scroll behavior between routes

Available routes:

- `/login` - Login page
- `/register` - Registration page
- `/` - Main game
- `/dashboard` - Statistics dashboard
- `/personal-report` - Personal report

## 🚀 Getting Started

### Installation

1. **Navigate to the project directory**:

```bash
cd front
```

2. **Install dependencies**:

```bash
npm install
```

3. **Configure environment variables**:
   Create a `.env` file in the `front/` root:

```env
VITE_API_URL=http://localhost:8000/api
```

### Running the Application

#### Development

```bash
npm run dev
```

Starts the development server with hot-reload at `http://localhost:5173`

#### Production Build

```bash
npm run build
```

Generates optimized files in the `dist/` folder.

#### Preview Production Build

```bash
npm run preview
```

Preview the production build locally.

#### Linting

```bash
npm run lint
```

Runs ESLint and automatically fixes issues.

#### Formatting

```bash
npm run format
```

Formats code with Prettier.

#### End-to-End Testing

```bash
# Install Playwright browsers (first time only)
npx playwright install

# Run all E2E tests
npm run test:e2e

# Run with UI mode
npm run test:e2e:ui

# Run in headed mode (visible browser)
npm run test:e2e:headed

# Run in debug mode
npm run test:e2e:debug
```

See [e2e/README.md](./e2e/README.md) for more details about E2E tests.

### Available Scripts

| Script                | Description                        |
| --------------------- | ---------------------------------- |
| `npm run dev`         | Start development server           |
| `npm run build`        | Build for production               |
| `npm run preview`     | Preview production build           |
| `npm run lint`         | Run ESLint and fix issues          |
| `npm run format`       | Format code with Prettier          |
| `npm run test:e2e`     | Run end-to-end tests               |
| `npm run test:e2e:ui`  | Run E2E tests with UI mode         |
| `npm run test:e2e:headed` | Run E2E tests with visible browser |
| `npm run test:e2e:debug` | Run E2E tests in debug mode        |

---

**Built with ❤️ using Vue.js 3 + Pinia + TailwindCSS**
