<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
      <h2 class="text-2xl font-bold text-center text-gray-900">
        Login to your account
      </h2>
      <form class="space-y-6" @submit.prevent="handleLogin">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700"
            >Email address</label
          >
          <input
            id="email"
            v-model="form.email"
            name="email"
            type="email"
            autocomplete="email"
            required
            class="block w-full px-3 py-2 mt-1 placeholder-gray-400 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700"
            >Password</label
          >
          <input
            id="password"
            v-model="form.password"
            name="password"
            type="password"
            autocomplete="current-password"
            required
            class="block w-full px-3 py-2 mt-1 placeholder-gray-400 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div v-if="error" class="text-sm text-red-600">
          {{ error }}
        </div>
        <div>
          <button
            type="submit"
            class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Sign in
          </button>
        </div>
      </form>
      <p class="mt-4 text-sm text-center text-gray-600">
        Don't have an account?
        <router-link
          to="/register"
          class="font-medium text-indigo-600 hover:text-indigo-500"
        >
          Register here
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from "vue"
import { useRouter } from "vue-router"
import apiClient from "@/services/api"

const form = reactive({
  email: "",
  password: "",
})

const error = ref<string | null>(null)
const router = useRouter()

const handleLogin = async () => {
  error.value = null
  try {
    // First, get the CSRF cookie
    await apiClient.get("/sanctum/csrf-cookie")

    // Then, attempt to log in
    const response = await apiClient.post("/login", form)

    if (response.data.token) {
      localStorage.setItem("authToken", response.data.token)
      // Redirect to the dashboard
      await router.push({ name: "dashboard" })
    } else {
      error.value = "Login failed: No token received."
    }
  } catch (err: any) {
    if (err.response && err.response.data.message) {
      error.value = err.response.data.message
    } else {
      error.value = "An unexpected error occurred."
    }
  }
}
</script>
