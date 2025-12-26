<template>
    <div class="min-h-screen bg-gray-100">
        <div class="container p-4 mx-auto sm:p-6 lg:p-8">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p v-if="profile" class="mt-2 text-lg text-gray-600">Welcome back, {{ profile.name }}!</p>
            </header>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Left Column: Order Form and Wallet -->
                <div class="space-y-8 lg:col-span-1">
                    <LimitOrderForm @order-placed="refreshData" />
                    <WalletOverview :profile="profile" />
                </div>

                <!-- Right Column: Orders List -->
                <div class="lg:col-span-2">
                    <OrdersList :refresh-trigger="refreshTrigger" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import apiClient from '@/services/api';
import LimitOrderForm from '@/components/LimitOrderForm.vue';
import WalletOverview from '@/components/WalletOverview.vue';
import OrdersList from '@/components/OrdersList.vue';

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

const fetchProfile = async () => {
    error.value = null;
    try {
        const response = await apiClient.get('/api/profile');
        profile.value = response.data;
    } catch (err) {
        error.value = 'Failed to fetch profile data.';
    }
};

const refreshData = () => {
    fetchProfile();
    refreshTrigger.value++;
};

onMounted(fetchProfile);
</script>
