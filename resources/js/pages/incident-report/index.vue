<script lang="ts" setup>
import Mail from '../../components/incident-report/components/Mail.vue'
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { ref } from 'vue'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Home',
    href: '/dashboard',
  },
  {
    title: 'Incident Report',
    href: '/incident-report',
  },
]

const isComposing = ref(false)


</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="container p-5">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold tracking-tight text-blue-900">Incident Report</h1>
        <Button @click="isComposing = !isComposing">
          {{ isComposing ? 'Cancel' : 'Compose New' }}
        </Button>
      </div>
      
      <div class="md:hidden">
        <!-- Mobile view placeholder -->
        <div class="p-4 border rounded-lg bg-background">
          <p class="text-sm text-muted-foreground">
            Incident reporting is optimized for desktop view. Please switch to a larger screen.
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