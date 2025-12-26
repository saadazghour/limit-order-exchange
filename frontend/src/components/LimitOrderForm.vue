<template>
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Place an Order</h3>
        <form @submit.prevent="placeOrder" class="mt-6 space-y-4">
            <div>
                <label for="symbol" class="block text-sm font-medium text-gray-700">Symbol</label>
                <select v-model="form.symbol" id="symbol" name="symbol"
                        class="block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option>BTC</option>
                    <option>ETH</option>
                </select>
            </div>
            <div>
                <label for="side" class="block text-sm font-medium text-gray-700">Side</label>
                <select v-model="form.side" id="side" name="side"
                        class="block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="buy">Buy</option>
                    <option value="sell">Sell</option>
                </select>
            </div>
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                <input v-model="form.price" id="price" name="price" type="number" step="0.01"
                       class="block w-full px-3 py-2 mt-1 placeholder-gray-400 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                <input v-model="form.amount" id="amount" name="amount" type="number" step="0.0001"
                       class="block w-full px-3 py-2 mt-1 placeholder-gray-400 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div v-if="error" class="text-sm text-red-600">
                {{ error }}
            </div>
            <div>
                <button type="submit"
                        class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Place Order
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { reactive, ref, defineEmits } from 'vue';
import apiClient from '@/services/api';

const form = reactive({
    symbol: 'BTC',
    side: 'buy',
    price: '',
    amount: ''
});

const error = ref<string | null>(null);
const emit = defineEmits(['orderPlaced']);

const placeOrder = async () => {
    error.value = null;
    try {
        await apiClient.post('/api/orders', form);
        // Reset form and emit event on success
        form.price = '';
        form.amount = '';
        emit('orderPlaced');
    } catch (err: any) {
        if (err.response && err.response.data.message) {
            error.value = err.response.data.message;
        } else {
            error.value = 'Failed to place order.';
        }
    }
};
</script>
