<template>
    <div class="p-6 mt-8 bg-white rounded-lg shadow-md">
        <h3 class="text-lg font-medium leading-6 text-gray-900">My Orders</h3>
        <div class="mt-4">
            <!-- Order filtering controls can be added here in the future -->
        </div>
        <div class="mt-4 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Symbol</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Side</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Price</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Amount</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Cancel</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="order in orders" :key="order.id">
                                <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 whitespace-nowrap sm:pl-6">{{ order.symbol }}</td>
                                <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{ order.side }}</td>
                                <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{ order.price }}</td>
                                <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{ order.amount }}</td>
                                <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{ getStatusText(order.status) }}</td>
                                <td class="relative py-4 pl-3 pr-4 text-sm font-medium text-right whitespace-nowrap sm:pr-6">
                                    <button v-if="order.status === 1" @click="cancelOrder(order.id)"
                                            class="text-indigo-600 hover:text-indigo-900">Cancel</button>
                                </td>
                            </tr>
                            <tr v-if="orders.length === 0">
                                <td colspan="6" class="px-3 py-4 text-sm text-center text-gray-500">No orders found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, defineProps, watch } from 'vue';
import apiClient from '@/services/api';

interface Order {
    id: number;
    symbol: string;
    side: string;
    price: string;
    amount: string;
    status: number;
}

const props = defineProps<{
    refreshTrigger: number;
}>();

const orders = ref<Order[]>([]);
const error = ref<string | null>(null);

const fetchOrders = async () => {
    error.value = null;
    try {
        // Fetching all orders for now. A symbol filter can be added.
        const response = await apiClient.get('/api/orders', { params: { symbol: 'ALL' } }); // Adjust API if needed
        orders.value = response.data.data;
    } catch (err) {
        error.value = 'Failed to fetch orders.';
    }
};

const cancelOrder = async (orderId: number) => {
    try {
        await apiClient.post(`/api/orders/${orderId}/cancel`);
        await fetchOrders(); // Re-fetch orders after cancellation
    } catch (err) {
        // Handle error
    }
};

const getStatusText = (status: number) => {
    switch (status) {
        case 1: return 'Open';
        case 2: return 'Filled';
        case 3: return 'Cancelled';
        default: return 'Unknown';
    }
};

onMounted(fetchOrders);
watch(() => props.refreshTrigger, fetchOrders);
</script>
