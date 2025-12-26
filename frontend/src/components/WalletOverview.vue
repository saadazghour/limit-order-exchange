<template>
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Wallet Overview</h3>
        <div class="mt-5 border-t border-gray-200">
            <dl class="divide-y divide-gray-200">
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">USD Balance</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">${{ profile?.balance }}</dd>
                </div>
                <div v-for="asset in profile?.assets" :key="asset.symbol" class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">{{ asset.symbol }} Balance</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        Available: {{ asset.amount }} <br>
                        Locked: {{ asset.locked_amount }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</template>

<script setup lang="ts">
// In newer Vue 3 versions, defineProps is a compiler macro and doesn't need explicit import
// import { defineProps } from 'vue';

interface Asset {
    symbol: string;
    amount: string;
    locked_amount: string;
}

interface Profile {
    balance: string;
    assets: Asset[];
}

const props = defineProps<{
    profile: Profile | null;
}>();
</script>
