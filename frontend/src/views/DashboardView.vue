<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">Limit-Order Exchange</h1>
                    </div>
                    <div class="flex items-center">
                         <span v-if="profile" class="mr-4 text-sm text-gray-600">Welcome, {{ profile.name }}!</span>
                        <button @click="logout" class="px-3 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="py-10">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
                    <!-- Left Column -->
                    <div class="space-y-8 lg:col-span-1">
                        <WalletOverview :profile="profile" />
                        <OrdersList :refresh-trigger="refreshTrigger" title="My Order History" />
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-8 lg:col-span-2">
                        <LimitOrderForm @order-placed="refreshData" />
                        <OrderBook :symbol="'BTC/USD'" :refresh-trigger="refreshTrigger" />
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'; // Keep ref, remove onMounted as it's not directly used here after cleanup
import { useRouter } from 'vue-router';
import apiClient from '@/services/api';
import LimitOrderForm from '@/components/LimitOrderForm.vue';
import WalletOverview from '@/components/WalletOverview.vue';
import OrdersList from '@/components/OrdersList.vue';
import OrderBook from '@/components/OrderBook.vue';

interface Asset {
    symbol: string;
    amount: string;
    locked_amount: string;
}

interface Profile {
    name: string;
    balance: string;
    assets: Asset[];
}

const profile = ref<Profile | null>(null);
const error = ref<string | null>(null);
const refreshTrigger = ref(0);
const router = useRouter();

const fetchProfile = async () => {
    error.value = null;
    try {
        const response = await apiClient.get('/api/profile');
        profile.value = response.data.data;
    } catch (err) {
        error.value = 'Failed to fetch profile data.';
        console.error(err);
    }
};

const refreshData = () => {
    console.log('Refreshing data...');
    fetchProfile();
    refreshTrigger.value++;
};

const logout = async () => {
    try {
        await apiClient.post('/logout');
    } finally {
        localStorage.removeItem('authToken');
        router.push({ name: 'login' });
    }
};

onMounted(fetchProfile);
</script>
