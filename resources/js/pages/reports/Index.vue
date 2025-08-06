<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem, Employee } from '@/types'
import FrontlinerRankings from './components/FrontlinerRankings.vue'
import JobOrderStatsGrid from './components/JobOrderStatsGrid.vue'
import MonthlyJobOrderTrends from './components/MonthlyJobOrderTrends.vue'
import ServiceTypeCompletion from './components/ServiceTypeCompletion.vue'
import ServiceTypeTrends from './components/ServiceTypeTrends.vue'

interface IndexProps {
  data: {
    metrics: {
      month: string
      Completed: number
      Cancelled: number
    }[]
    top: {
      client: string
      month: string
      service: string
    }
    frontliners: {
      employee: Employee
      createdJobOrdersCount: number
      rank: number
    }[]
    total: number,
    services: {
      trends: {
        name: string,
        total: number,        
      }[]
      completion: {
        name: string,
        Completed: number,
        Cancelled: number,
      }[]
    }
  }
}

defineProps<IndexProps>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Reports and Analytics',
    href: route('activity.index'),
  },
]
</script>

<template>
  <Head title="Reports and Analytics" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-6 mb-6 mt-3">
      <div class="flex items-start justify-between">
        <div class="flex flex-col">
          <h1 class="scroll-m-20 text-3xl font-bold text-primary">
            Reports and Analytics
          </h1>
          <p class="text-muted-foreground">
            Track annual performance, trends, and key metrics across the system.
          </p>
        </div>
        <div>
          <!--Export and year filter-->
          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <Button variant="outline"> Select Year </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent> Hello </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </div>
      <div class="mt-6 flex flex-col gap-4">
        <JobOrderStatsGrid
          :total="data.total"
          :top="data.top"
        />
        <div class="flex flex-col gap-4 lg:flex-row">
          <FrontlinerRankings :frontliners="data.frontliners" />
          <div class="flex-1">
            <MonthlyJobOrderTrends :metrics="data.metrics" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <ServiceTypeTrends :data="data.services.trends" />
          </div>
          <div>
            <ServiceTypeCompletion />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
