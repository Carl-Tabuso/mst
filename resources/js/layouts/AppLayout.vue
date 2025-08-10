<script setup lang="ts">
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import type { BreadcrumbItem, SharedData } from '@/types'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

interface Props {
  breadcrumbs?: BreadcrumbItem[]
}

const page = usePage<SharedData>()
const userRole = computed(() => page.props.auth.user.roles[0])

const AppLayout = computed(() =>
  userRole.value.name === 'head frontliner'
    ? AppSidebarLayout
    : AppHeaderLayout,
)

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
})
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <slot />
  </AppLayout>
</template>
