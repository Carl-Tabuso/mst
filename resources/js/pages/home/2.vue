<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types'

interface Home2Props {

}

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Home',
    href: '/',
  },
]
</script>

<template>
    <Head title="Home" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto mb-6 mt-3 w-full max-w-screen-xl px-6">
            Home 2
        </div>
    </AppLayout>
</template>
