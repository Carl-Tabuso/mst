<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

// defineProps();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Job Orders',
    href: '/job-orders'
  },
  {
    title: 'List',
    href: '/job-orders',
  },
  {
    title: 'Create',
    href: '#'
  }
]
</script>

<template>
    <Head title="Create Job Order" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div>Hello nigga</div>
    </AppLayout>
</template>
