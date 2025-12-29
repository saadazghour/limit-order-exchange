<template>
  <div
    class="relative flex flex-col text-gray-700 bg-white shadow-lg rounded-xl border border-gray-200"
  >
    <div
      class="relative p-4 mx-4 -mt-6 overflow-hidden text-white shadow-lg bg-clip-border rounded-xl bg-gradient-to-tr from-gray-900 to-gray-800"
    >
      <h3 class="text-lg font-semibold">Wallet</h3>
    </div>
    <div class="p-6">
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <CurrencyDollarIcon class="h-6 w-6 text-green-500" />
            <div>
              <p class="text-sm font-semibold text-gray-900">USD Balance</p>
            </div>
          </div>
          <div class="text-right">
            <p class="text-sm font-medium text-gray-900 font-mono">
              ${{
                profile
                  ? parseFloat(profile.balance).toLocaleString("en-US", {
                      minimumFractionDigits: 2,
                      maximumFractionDigits: 2,
                    })
                  : "0.00"
              }}
            </p>
          </div>
        </div>

        <div
          v-if="profile && profile.assets.length > 0"
          class="border-t border-gray-100 !my-4"
        ></div>

        <div v-if="profile && profile.assets.length" class="space-y-3">
          <div
            v-for="asset in profile.assets"
            :key="asset.symbol"
            class="flex items-center justify-between"
          >
            <div class="flex items-center gap-4">
              <component
                :is="getAssetIcon(asset.symbol)"
                class="h-6 w-6"
                :class="getAssetColor(asset.symbol)"
              />
              <div>
                <p class="text-sm font-semibold text-gray-900">
                  {{ asset.symbol }}
                </p>
              </div>
            </div>
            <div class="text-right font-mono text-sm">
              <p
                class="font-medium text-gray-900"
                :title="`Available: ${asset.amount}`"
              >
                {{ parseFloat(asset.amount).toFixed(6) }}
              </p>
              <p
                v-if="parseFloat(asset.locked_amount) > 0"
                class="text-xs text-gray-500"
                :title="`Locked: ${asset.locked_amount}`"
              >
                Locked: {{ parseFloat(asset.locked_amount).toFixed(6) }}
              </p>
            </div>
          </div>
        </div>
        <div v-else class="text-sm text-center text-gray-400 py-8">
          You have no crypto assets.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {
  CurrencyDollarIcon,
  BoltIcon,
  CpuChipIcon,
} from "@heroicons/vue/24/solid"

const assetIcons: { [key: string]: any } = {
  BTC: BoltIcon,
  ETH: CpuChipIcon,
}
const assetColors: { [key: string]: any } = {
  BTC: { bg: "bg-yellow-100", text: "text-yellow-500" },
  ETH: { bg: "bg-blue-100", text: "text-blue-500" },
  DEFAULT: { bg: "bg-gray-100", text: "text-gray-500" },
}

const getAssetIcon = (symbol: string) => assetIcons[symbol] || CpuChipIcon
const getAssetColor = (symbol: string) =>
  assetColors[symbol] || assetColors["DEFAULT"]

interface Asset {
  symbol: string
  amount: string
  locked_amount: string
}
interface Profile {
  balance: string
  assets: Asset[]
}

defineProps<{
  profile: Profile | null
}>()
</script>
