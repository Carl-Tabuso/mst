<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import TicketHeader from '@/pages/job-orders/components/TicketHeader.vue';
import { BreadcrumbItem, JobOrder, JobOrderCorrection } from '@/types';

interface ShowProps {
    data: JobOrderCorrection
}

defineProps<ShowProps>()

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
            <TicketHeader :job-order="data.jobOrder" />
            Waste Management
        </div>
    </AppLayout>
</template>
