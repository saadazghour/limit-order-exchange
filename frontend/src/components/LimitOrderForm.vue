<template>
  <div
    class="relative flex flex-col text-gray-700 bg-white shadow-lg rounded-xl border border-gray-200"
  >
    <div
      class="relative p-4 mx-4 -mt-6 overflow-hidden text-white shadow-lg bg-clip-border rounded-xl bg-gradient-to-tr from-gray-900 to-gray-800"
    >
      <h3 class="text-lg font-semibold">Place New Order</h3>
    </div>
    <form class="p-6" @submit.prevent="placeOrder">
      <div class="space-y-6">
        <fieldset>
          <div class="flex rounded-md">
            <label
              :class="
                form.side === 'buy'
                  ? 'bg-green-500 text-white border-green-500 z-10 ring-2 ring-green-400'
                  : 'text-gray-700 bg-white hover:bg-gray-50 border-gray-300'
              "
              class="relative flex-1 flex items-center justify-center px-3 py-2 text-sm font-medium border rounded-l-md cursor-pointer focus:outline-none"
            >
              <input v-model="form.side" type="radio" value="buy" class="sr-only" />
              <span>Buy</span>
            </label>
            <label
              :class="
                form.side === 'sell'
                  ? 'bg-red-500 text-white border-red-500 z-10 ring-2 ring-red-400'
                  : 'text-gray-700 bg-white hover:bg-gray-50 border-gray-300'
              "
              class="relative -ml-px flex-1 flex items-center justify-center px-3 py-2 text-sm font-medium border rounded-r-md cursor-pointer focus:outline-none"
            >
              <input v-model="form.side" type="radio" value="sell" class="sr-only" />
              <span>Sell</span>
            </label>
          </div>
        </fieldset>

        <div>
          <label for="symbol" class="block text-sm font-medium leading-6 text-gray-900"
            >Asset</label
          >
          <select
            id="symbol"
            v-model="form.symbol"
            class="mt-2 block w-full rounded-md border-0 py-2 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
          >
            <option>BTC</option>
            <option>ETH</option>
          </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="price" class="block text-sm font-medium leading-6 text-gray-900"
              >Price</label
            >
            <div class="mt-2">
              <input
                id="price"
                v-model="form.price"
                type="number"
                step="0.01"
                placeholder="Price (USD)"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
              />
            </div>
          </div>
          <div>
            <label for="amount" class="block text-sm font-medium leading-6 text-gray-900"
              >Amount</label
            >
            <div class="mt-2">
              <input
                id="amount"
                v-model="form.amount"
                type="number"
                step="0.0001"
                placeholder="Amount"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
              />
            </div>
          </div>
        </div>
      </div>

      <div v-if="error" class="mt-4 text-sm text-center text-red-500">
        {{ error }}
      </div>

      <div class="pt-6">
        <button
          type="submit"
          :class="
            form.side === 'buy'
              ? 'bg-gradient-to-tr from-green-600 to-green-400 text-white shadow-md shadow-green-500/20 hover:shadow-lg hover:shadow-green-500/40'
              : 'bg-gradient-to-tr from-red-600 to-red-400 text-white shadow-md shadow-red-500/20 hover:shadow-lg hover:shadow-red-500/40'
          "
          class="w-full select-none rounded-lg py-3 px-6 text-center align-middle font-sans text-xs font-bold uppercase transition-all focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none"
        >
          Place {{ form.side }} Order
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from "vue";
import apiClient from "@/services/api.ts";
const form = reactive({ symbol: "BTC", side: "buy", price: "", amount: "" });
const error = ref<string | null>(null);
const emit = defineEmits(["orderPlaced"]);

const placeOrder = async () => {
  error.value = null;
  try {
    await apiClient.post("/api/orders", form);
    form.price = "";
    form.amount = "";
    emit("orderPlaced");
  } catch (err: any) {
    if (err.response && err.response.data && err.response.data.message) {
      error.value = err.response.data.message;
    } else {
      error.value = "Failed to place order.";
    }
  }
};
</script>
