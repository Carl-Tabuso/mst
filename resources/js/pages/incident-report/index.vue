<script lang="ts" setup>
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { ref } from 'vue'
import Mail from '../../components/incident-report/components/Mail.vue'

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
    <div class="container p-5">
      <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold tracking-tight text-blue-900">
          Incident Report
        </h1>
        <Button @click="toggleCompose">
          {{ isComposing ? 'Cancel' : 'Compose New' }}
        </Button>
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
        <Mail
          :nav-collapsed-size="4"
          :is-composing="isComposing"
          @cancel-compose="isComposing = false"
        />
      </div>
    </div>
  </AppLayout>
</template>
