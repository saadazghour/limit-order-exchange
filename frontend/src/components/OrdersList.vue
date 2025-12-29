<template>
  <div
    class="relative flex flex-col w-full h-full text-gray-700 bg-white shadow-xl rounded-xl border border-gray-200"
  >
    <div
      class="relative p-4 mx-4 -mt-6 overflow-hidden text-white shadow-lg bg-clip-border rounded-xl bg-gradient-to-tr from-gray-900 to-gray-800"
    >
      <h3 class="text-lg font-semibold">{{ title }}</h3>
    </div>
    <div class="p-0 overflow-x-auto">
      <table class="w-full text-left table-auto min-w-max">
        <thead>
          <tr>
            <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
              <p
                class="font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70"
              >
                Order Details
              </p>
            </th>
            <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
              <p
                class="font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70"
              >
                Date
              </p>
            </th>
            <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
              <p
                class="font-sans text-sm antialiased font-normal leading-none text-center text-blue-gray-900 opacity-70"
              >
                Status
              </p>
            </th>
            <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
              <p
                class="font-sans text-sm antialiased font-normal leading-none text-right text-blue-gray-900 opacity-70"
              >
                Total Value
              </p>
            </th>
            <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
              <p
                class="font-sans text-sm antialiased font-normal leading-none text-right text-blue-gray-900 opacity-70"
              >
                Action
              </p>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
            <td class="p-4 border-b border-blue-gray-50">
              <div class="flex flex-col">
                <p
                  class="font-sans text-sm antialiased font-bold leading-normal text-blue-gray-900"
                >
                  {{ order.symbol }}/USD
                </p>
                <p
                  class="font-sans text-xs antialiased font-normal"
                  :class="
                    order.side === 'buy' ? 'text-green-600' : 'text-red-600'
                  "
                >
                  {{ order.side.toUpperCase() }} |
                  {{ parseFloat(order.amount).toFixed(6) }} @ ${{
                    parseFloat(order.price).toFixed(2)
                  }}
                </p>
              </div>
            </td>
            <td class="p-4 border-b border-blue-gray-50">
              <p
                class="font-sans text-sm antialiased font-normal text-blue-gray-900"
              >
                {{ formatDate(order.created_at) }}
              </p>
            </td>
            <td class="p-4 border-b border-blue-gray-50 text-center">
              <span
                class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full"
                :class="getStatusClass(order.status)"
              >
                {{ getStatusText(order.status) }}
              </span>
            </td>
            <td class="p-4 border-b border-blue-gray-50 text-right">
              <p
                class="font-sans text-sm font-medium text-blue-gray-900 font-mono"
              >
                ${{
                  (parseFloat(order.price) * parseFloat(order.amount)).toFixed(
                    2,
                  )
                }}
              </p>
            </td>
            <td class="p-4 border-b border-blue-gray-50 text-right">
              <button
                v-if="order.status === 1"
                @click="cancelOrder(order.id)"
                class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm"
              >
                Cancel
              </button>
            </td>
          </tr>
          <tr v-if="orders.length === 0">
            <td
              colspan="5"
              class="p-4 py-16 text-sm text-center text-gray-500 border-b border-blue-gray-50"
            >
              You have no order history.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from "vue";
import apiClient from "@/services/api.ts";

interface Order {
  id: number;
  symbol: string;
  side: string;
  price: string;
  amount: string;
  status: number;
  created_at: string;
}

const props = defineProps<{
  refreshTrigger: number;
  title: string;
}>();

const orders = ref<Order[]>([]);

const fetchOrders = async () => {
  try {
    const response = await apiClient.get("/api/orders", {
      params: { symbol: "ALL" },
    });
    orders.value = response.data.data;
  } catch (err) {
    console.error("Failed to fetch orders:", err);
  }
};

const cancelOrder = async (orderId: number) => {
  if (!confirm("Are you sure you want to cancel this order?")) return;
  try {
    await apiClient.post(`/api/orders/${orderId}/cancel`);
    await fetchOrders();
  } catch (err) {
    alert("Failed to cancel order.");
  }
};

const getStatusText = (status: number) => {
  switch (status) {
    case 1:
      return "Open";
    case 2:
      return "Filled";
    case 3:
      return "Cancelled";
    default:
      return "Unknown";
  }
};

const getStatusClass = (status: number) => {
  switch (status) {
    case 1:
      return "bg-blue-100 text-blue-800";
    case 2:
      return "bg-green-100 text-green-800";
    case 3:
      return "bg-gray-200 text-gray-800";
    default:
      return "bg-yellow-100 text-yellow-800";
  }
};

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString(undefined, {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
};

onMounted(fetchOrders);
watch(() => props.refreshTrigger, fetchOrders);
</script>
