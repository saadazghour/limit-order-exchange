<template>
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h3 class="text-lg font-medium leading-6 text-gray-900">{{ symbol }} Order Book</h3>
        <div class="grid grid-cols-2 gap-6 mt-4">
            <!-- Bids (Buy Orders) -->
            <div>
                <h4 class="font-semibold text-center text-green-600">Bids</h4>
                <div class="mt-2 text-xs text-gray-500">
                    <div class="flex justify-between p-1 border-b">
                        <span class="w-1/2 text-left">Price (USD)</span>
                        <span class="w-1/2 text-right">Amount ({{ symbol.split('/')[0] }})</span>
                    </div>
                    <div v-for="order in bids" :key="order.id" class="flex justify-between p-1 hover:bg-green-50">
                        <span class="w-1/2 text-left text-green-700">{{ formatPrice(order.price) }}</span>
                        <span class="w-1/2 text-right">{{ order.amount }}</span>
                    </div>
                     <div v-if="bids.length === 0" class="p-4 text-center">No buy orders.</div>
                </div>
            </div>
            <!-- Asks (Sell Orders) -->
            <div>
                <h4 class="font-semibold text-center text-red-600">Asks</h4>
                 <div class="mt-2 text-xs text-gray-500">
                    <div class="flex justify-between p-1 border-b">
                        <span class="w-1/2 text-left">Price (USD)</span>
                        <span class="w-1/2 text-right">Amount ({{ symbol.split('/')[0] }})</span>
                    </div>
                    <div v-for="order in asks" :key="order.id" class="flex justify-between p-1 hover:bg-red-50">
                        <span class="w-1/2 text-left text-red-700">{{ formatPrice(order.price) }}</span>
                        <span class="w-1/2 text-right">{{ order.amount }}</span>
                    </div>
                    <div v-if="asks.length === 0" class="p-4 text-center">No sell orders.</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import apiClient from '@/services/api';

interface Order {
    id: number;
    price: string;
    amount: string;
    side: 'buy' | 'sell';
}

const props = defineProps<{
    symbol: string;
    refreshTrigger: number;
}>();

const orders = ref<Order[]>([]);

const bids = computed(() => 
    orders.value
        .filter(o => o.side === 'buy')
        .sort((a, b) => parseFloat(b.price) - parseFloat(a.price)) // Highest price first
);

const asks = computed(() => 
    orders.value
        .filter(o => o.side === 'sell')
        .sort((a, b) => parseFloat(a.price) - parseFloat(b.price)) // Lowest price first
);

const fetchOrderBook = async () => {
    try {
        const response = await apiClient.get('/api/orders', { params: { symbol: props.symbol.split('/')[0] } });
        orders.value = response.data.data;
    } catch (error) {
        console.error('Failed to fetch order book:', error);
    }
};

const formatPrice = (price: string) => {
    return parseFloat(price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

onMounted(fetchOrderBook);
watch(() => props.refreshTrigger, fetchOrderBook);
watch(() => props.symbol, fetchOrderBook);
</script>
