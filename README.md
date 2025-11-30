# 🏥 Surviving the Shift

Educational web application for simulating clinical cases in a hospital environment. Students practice medical decision-making through a gamified system that presents realistic scenarios and tracks performance.

## 🎯 Project Overview

**Surviving the Shift** is a full-stack application designed to help medical students and professionals practice clinical decision-making in a safe, controlled environment. The system presents patients with various symptoms and conditions, requiring users to make diagnostic and treatment decisions at each step of the clinical case.

### Key Features

- **Clinical Case Simulation** - Realistic hospital scenarios with multiple decision points
- **Gamified Learning** - Score tracking and performance metrics
- **Bilingual Support** - Portuguese and English interface
- **Performance Analytics** - Dashboard and personal reports with detailed insights
- **Multiple Attempts** - Students can retry cases to improve their performance

## 🛠 Tech Stack

### Frontend
- **Vue.js 3.5** - Reactive JavaScript framework
- **Pinia** - State management
- **Tailwind CSS** - Utility-first styling
- **Vite** - Build tool and dev server
- **Axios** - HTTP client

### Backend
- **Laravel 12** - PHP framework
- **MySQL 8.0** - Relational database
- **Laravel Sanctum** - Token-based authentication
- **Laravel Sail** - Docker development environment

## 🏗 Architecture

The project follows a **client-server architecture** with clear separation between frontend and backend:

```
┌─────────────────┐         ┌─────────────────┐
│   Frontend      │         │   Backend       │
│   (Vue.js 3)    │ ◄─────► │   (Laravel 12)  │
│                 │  HTTP   │                 │
│   - Pinia       │  REST   │   - Controllers │
│   - Components  │         │   - Services    │
│   - Services    │         │   - Models      │
└─────────────────┘         └─────────────────┘
                                      │
                                      ▼
                              ┌──────────────┐
                              │    MySQL     │
                              │  Database    │
                              └──────────────┘
```

### Architecture Principles

- **Separation of Concerns** - Clear boundaries between frontend and backend
- **RESTful API** - Standard HTTP methods and status codes
- **Service Layer** - Business logic isolated in service classes
- **State Management** - Centralized state with Pinia stores
- **Token Authentication** - Secure API access with Laravel Sanctum

## 📁 Project Structure

```
/
├── api/              # Laravel backend API
│   ├── app/         # Application code
│   ├── routes/      # API routes
│   ├── database/    # Migrations and seeders
│   └── README.md    # API documentation
│
├── front/           # Vue.js frontend
│   ├── src/         # Source code
│   ├── public/      # Static assets
│   └── README.md    # Frontend documentation
│
└── README.md        # This file
```

## 🚀 Quick Start

### Prerequisites

- **PHP 8.2+** with Composer
- **Node.js 20+** with npm
- **Docker & Docker Compose** (for Laravel Sail)
- **MySQL 8.0** (if not using Docker)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd dissertacao
   ```

2. **Setup Backend (API)**
   ```bash
   cd api
   composer install
   cp .env.example .env
   php artisan key:generate
   
   # With Docker (recommended)
   ./vendor/bin/sail up -d
   ./vendor/bin/sail artisan migrate:fresh --seed
   
   # Or without Docker
   php artisan migrate:fresh --seed
   php artisan serve
   ```

3. **Setup Frontend**
   ```bash
   cd front
   npm install
   cp .env.example .env
   # Edit .env and set VITE_API_URL=http://localhost:8000/api
   npm run dev
   ```

4. **Access the application**
   - Frontend: `http://localhost:5173`
   - API: `http://localhost:8000` (or `http://localhost` with Sail)

## 📚 Documentation

For detailed documentation on each part of the project, refer to:

- **[API Documentation](./api/README.md)** - Backend API setup, architecture, and endpoints
- **[Frontend Documentation](./front/README.md)** - Frontend setup, architecture, and components

## 🔄 Development Workflow

1. **Backend Development**
   - API runs on Laravel Sail (Docker) or native PHP
   - Database migrations and seeders for data setup
   - Token-based authentication for API access

2. **Frontend Development**
   - Hot-reload development server with Vite
   - State management with Pinia stores
   - API integration via Axios

3. **Testing**
   - Backend: PHPUnit tests (`composer test`)
   - Frontend: Playwright E2E tests (`npm run test:e2e`)

## 🗄️ Database

The application uses MySQL with the following main entities:

- **users** - System users (students/professionals)
- **patients** - Clinical cases/scenarios
- **steps** - Decision points in each case
- **alternatives** - Answer options for each step
- **answers** - User responses and attempts
- **answer_keys** - Correct answers for evaluation

See the [API README](./api/README.md) for the complete database schema.

## 🔐 Authentication

The application uses **Laravel Sanctum** for API authentication:

1. Users register/login via `/api/auth/register` or `/api/auth/login`
2. Backend returns a Bearer token
3. Frontend includes token in `Authorization` header for protected routes
4. API validates token on each request

## 📝 License

CC BY-NC-ND 4.0
