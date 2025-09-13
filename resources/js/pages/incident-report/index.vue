<script lang="ts" setup>
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { ref } from 'vue'
import Incident from '../../components/incident-report/components/Incident.vue'
import MainContainer from '@/components/MainContainer.vue'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Home',
    href: '/',
  },
  {
    title: 'Incident Report',
    href: '/incident-report',
  },
]

const isComposing = ref(false)

const toggleCompose = () => {
  isComposing.value = !isComposing.value
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
    <div class="container p-5">
      <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold tracking-tight text-blue-900">
          Incident Report
        </h1>

      </div>

      <div class="md:hidden">
        <!-- Mobile view placeholder -->
        <div class="rounded-lg border bg-background p-4">
          <p class="text-sm text-muted-foreground">
            Incident reporting is optimized for desktop view. Please switch to a
            larger screen.
          </p>
        </div>
      </div>

      <div class="hidden flex-col md:flex">
        <Incident
          :nav-collapsed-size="4"
        />
      </div>
    </div>
    </MainContainer>
  </AppLayout>
</template>
