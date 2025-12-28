<template>
  <div
    class="relative flex flex-col w-full h-96 text-gray-700 bg-white shadow-md rounded-xl border border-gray-200"
  >
    <div
      class="relative p-4 mx-4 -mt-6 overflow-hidden text-white shadow-lg bg-clip-border rounded-xl bg-gradient-to-tr from-gray-900 to-gray-800"
    >
      <h3 class="text-lg font-semibold">{{ symbol }} Order Book</h3>
    </div>
    <div class="grid grid-cols-2 flex-grow p-4 overflow-hidden">
      <!-- Asks -->
      <div class="flex flex-col pr-2 border-r border-gray-200">
        <h4 class="text-sm font-semibold text-center text-red-500 mb-2">
          Asks
        </h4>
        <div class="overflow-y-auto">
          <table class="w-full text-xs text-left">
            <thead>
              <tr class="text-gray-500">
                <th class="py-1 font-normal">Price (USD)</th>
                <th class="py-1 font-normal text-right">Amount</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="order in asks" :key="order.id">
                <td class="py-1.5 text-red-500 font-mono">
                  {{ formatPrice(order.price) }}
                </td>
                <td class="py-1.5 text-right font-mono text-gray-700">
                  {{ parseFloat(order.amount).toFixed(6) }}
                </td>
              </tr>
            </tbody>
          </table>
          <div
            v-if="asks.length === 0"
            class="flex items-center justify-center h-full text-sm text-center text-gray-400 py-16"
          >
            No sell orders.
          </div>
        </div>
      </div>
      <!-- Bids -->
      <div class="flex flex-col pl-2">
        <h4 class="text-sm font-semibold text-center text-green-500 mb-2">
          Bids
        </h4>
        <div class="overflow-y-auto">
          <table class="w-full text-xs text-left">
            <thead>
              <tr class="text-gray-500">
                <th class="py-1 font-normal">Price (USD)</th>
                <th class="py-1 font-normal text-right">Amount</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="order in bids" :key="order.id">
                <td class="py-1.5 text-green-500 font-mono">
                  {{ formatPrice(order.price) }}
                </td>
                <td class="py-1.5 text-right font-mono text-gray-700">
                  {{ parseFloat(order.amount).toFixed(6) }}
                </td>
              </tr>
            </tbody>
          </table>
          <div
            v-if="bids.length === 0"
            class="flex items-center justify-center h-full text-sm text-center text-gray-400 py-16"
          >
            No buy orders.
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from "vue";
import apiClient from "@/services/api";

interface Order {
  id: number;
  price: string;
  amount: string;
  side: "buy" | "sell";
}

const props = defineProps<{ symbol: string; refreshTrigger: number }>();
const orders = ref<Order[]>([]);

const bids = computed(() =>
  orders.value
    .filter((o) => o.side === "buy")
    .sort((a, b) => parseFloat(b.price) - parseFloat(a.price)),
);
const asks = computed(() =>
  orders.value
    .filter((o) => o.side === "sell")
    .sort((a, b) => parseFloat(a.price) - parseFloat(b.price)),
);

const fetchOrderBook = async () => {
  try {
    const response = await apiClient.get("/api/orders", {
      params: { symbol: props.symbol.split("/")[0] },
    });
    orders.value = response.data.data;
  } catch (error) {
    console.error("Failed to fetch order book:", error);
  }
};

const formatPrice = (price: string) =>
  parseFloat(price).toLocaleString("en-US", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });

onMounted(fetchOrderBook);
watch(() => props.refreshTrigger, fetchOrderBook);
watch(() => props.symbol, fetchOrderBook);
</script>
