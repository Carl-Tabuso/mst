<script lang="ts" setup>
import MainContainer from '@/components/MainContainer.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import Incident from '../../components/incident-report/components/Incident.vue'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Home',
    href: '/',
  },
  {
    title: 'Incident Report',
    href: '/incidents',
  },
]
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
      <div class="container">
        <div class="mb-6 flex items-center justify-between">
          <h1 class="text-3xl font-bold tracking-tight text-primary">
            Incident Report
          </h1>
        </div>

        <div>
          <Incident :nav-collapsed-size="4" />
        </div>
      </div>
    </MainContainer>
  </AppLayout>
</template>
