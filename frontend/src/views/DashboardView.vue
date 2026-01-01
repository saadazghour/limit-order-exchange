<template>
  <div class="min-h-screen bg-gray-100 text-gray-800">
    <header class="bg-white shadow-sm sticky top-0 z-20">
      <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center gap-4">
            <svg
              class="w-8 h-8 text-indigo-600"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h18"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M16.5 3L21 7.5m0 0L16.5 12M21 7.5H3"
              />
            </svg>
            <h1 class="text-xl font-semibold text-gray-900">
              Limit-Order Exchange
            </h1>
          </div>
          <div>
            <button
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
              @click="logout"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </header>

    <main class="py-12 bg-gradient-to-b from-gray-50 to-gray-100 min-h-screen">
      <div
        class="mx-auto w-full px-4 sm:px-6 lg:px-8"
        style="max-width: 1280px"
      >
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Top-Left: Order Book -->
          <OrderBook :symbol="'BTC/USD'" :refresh-trigger="refreshTrigger" />

          <!-- Top-Right: Wallet Overview -->
          <WalletOverview :profile="profile" />

          <!-- Bottom-Left: Limit Order Form -->
          <LimitOrderForm @order-placed="refreshData" />

          <!-- Bottom-Right: Order History -->
          <OrdersList
            :refresh-trigger="refreshTrigger"
            title="My Order History"
          />
        </div>
      </div>
      <!-- Toast Notification -->
      <div
        v-if="showToast"
        class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg transition-opacity duration-500"
        :class="{ 'opacity-100': showToast, 'opacity-0': !showToast }"
      >
        {{ toastMessage }}
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from "vue"
import { useRouter } from "vue-router"
import apiClient from "@/services/api"
import echo from "@/services/echo"
import LimitOrderForm from "@/components/LimitOrderForm.vue"
import WalletOverview from "@/components/WalletOverview.vue"
import OrdersList from "@/components/OrdersList.vue"
import OrderBook from "@/components/OrderBook.vue"

interface Asset {
  symbol: string
  amount: string
  locked_amount: string
}

interface Profile {
  id: number
  name: string
  balance: string
  assets: Asset[]
}

const profile = ref<Profile | null>(null)
const router = useRouter()
const refreshTrigger = ref(0)
const showToast = ref(false)
const toastMessage = ref("")
let toastTimeout: number | undefined

const fetchProfile = async () => {
  try {
    const response = await apiClient.get("/api/profile")
    profile.value = response.data.data
    if (profile.value) setupRealtimeListener(profile.value.id)
  } catch (err) {
    console.error("Failed to fetch profile data:", err)
  }
}

const refreshData = () => {
  fetchProfile()
  refreshTrigger.value++
}

const displayToast = (message: string) => {
  if (toastTimeout) clearTimeout(toastTimeout) // Clear any existing timeout

  toastMessage.value = message
  showToast.value = true

  toastTimeout = setTimeout(() => {
    showToast.value = false
    toastTimeout = undefined
  }, 3000) // Hide after 3 seconds
}

let realtimeTimeout: ReturnType<typeof setTimeout> | null = null

const setupRealtimeListener = (userId: number) => {
  if (realtimeTimeout) clearTimeout(realtimeTimeout)
  echo.private(`user.${userId}`).listen(".OrderMatched", () => {
    realtimeTimeout = setTimeout(() => {
      refreshData()
      displayToast("Order matched successfully!") // Display toast
      realtimeTimeout = null
    }, 500)
  })
}

const logout = async () => {
  if (profile.value) echo.leave(`user.${profile.value.id}`)
  try {
    await apiClient.post("/logout")
  } finally {
    localStorage.removeItem("authToken")
    router.push({ name: "login" })
  }
}

onMounted(fetchProfile)
onBeforeUnmount(() => {
  if (profile.value) echo.leave(`user.${profile.value.id}`)
  if (toastTimeout) clearTimeout(toastTimeout) // Cleanup on unmount
  if (realtimeTimeout) clearTimeout(realtimeTimeout)
})
</script>
