<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Job Order Corrections',
        href: '/job-orders/corrections',
    }
]
</script>

<template>
    <Head title="{Ticket} - Job Order Correction" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto mb-6 mt-3 w-full max-w-screen-xl px-6">
            IT Service
        </div>
    </AppLayout>
</template>
