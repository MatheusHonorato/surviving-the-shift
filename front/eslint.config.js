import { defineConfig, globalIgnores } from 'eslint/config'
import globals from 'globals'
import js from '@eslint/js'
import pluginVue from 'eslint-plugin-vue'
import skipFormatting from '@vue/eslint-config-prettier/skip-formatting'

/**
 * ESLint configuration
 * Uses Vue 3 recommended rules with Prettier integration
 */
export default defineConfig([
  {
    name: 'app/files-to-lint',
    files: ['**/*.{js,mjs,jsx,vue}'],
  },

  globalIgnores(['**/dist/**', '**/dist-ssr/**', '**/coverage/**', '**/node_modules/**']),

  {
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.node,
      },
      ecmaVersion: 'latest',
      sourceType: 'module',
    },
  },

  js.configs.recommended,
  ...pluginVue.configs['flat/recommended'],
  {
    rules: {
      // Vue specific rules
      'vue/multi-word-component-names': [
        'warn',
        {
          ignores: ['Toast', 'Dashboard']
        }
      ],
      'vue/no-unused-vars': 'warn',
      'vue/no-unused-components': 'warn',
      // General JavaScript rules
      'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
      'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'warn',
      'no-unused-vars': ['warn', { argsIgnorePattern: '^_' }],
      'prefer-const': 'warn',
    },
  },
  skipFormatting,
])
