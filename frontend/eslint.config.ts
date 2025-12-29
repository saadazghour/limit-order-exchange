import globals from "globals"
import vueEslintConfigTypescript from "@vue/eslint-config-typescript" // This exports an array of configs
import vueEslintConfigPrettier from "@vue/eslint-config-prettier" // This exports an object
import pluginJs from "@eslint/js"
import tseslint from "typescript-eslint"
import vueParser from "vue-eslint-parser" // Explicitly import vue-eslint-parser

export default [
  {
    languageOptions: {
      globals: globals.browser, // Default for browser environment
    },
    ignores: ["dist/**", "node_modules/**"],
  },
  // Base JS recommended rules (single object)
  pluginJs.configs.recommended,
  // TypeScript recommended (array -> spread)
  ...tseslint.configs.recommended,
  // Vue 3 + TypeScript config (array -> spread)
  ...vueEslintConfigTypescript(),
  // Vue Prettier config (single object)
  vueEslintConfigPrettier,

  {
    // Override for Vue files to apply TypeScript parser
    files: ["**/*.vue"],
    languageOptions: {
      parser: vueParser,
      parserOptions: {
        parser: tseslint.parser, // Specify @typescript-eslint/parser for <script lang="ts">
        ecmaVersion: "latest",
        sourceType: "module",
      },
    },
    rules: {
      "vue/multi-word-component-names": "off",
    },
  },
  {
    files: ["vite.config.ts", "tailwind.config.js", "eslint.config.ts"],
    languageOptions: {
      globals: globals.node, // Node.js globals for config files
    },
  },
]
