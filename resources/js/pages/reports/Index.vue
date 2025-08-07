<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectLabel,
  SelectSeparator,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem, Employee } from '@/types'
import { router } from '@inertiajs/vue3'
import { useUrlSearchParams } from '@vueuse/core'
import { Download } from 'lucide-vue-next'
import { ref } from 'vue'
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
    totalJobOrders: number
    trends: {
      name: string
      total: number
    }[]
    completion: {
      name: string
      Completed: number
      Cancelled: number
    }[]
    year: number
  }
  availableYears: number[]
}

const urlParam = useUrlSearchParams('history')

const props = defineProps<IndexProps>()

const selectedYear = ref<number>(
  Number(urlParam.year) || props.availableYears[0],
)

const onYearSelect = (year: number) => {
  router.get(
    route('report.index'),
    {
      year: year,
    },
    {
      preserveState: true,
      replace: true,
      onSuccess: () => (selectedYear.value = year),
    },
  )
}

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
        <div class="flex items-center gap-1 sm:flex-col lg:flex-row">
          <Select
            :model-value="selectedYear"
            @update:model-value="(value: any) => onYearSelect(value)"
          >
            <SelectTrigger class="ml-auto h-9 w-28">
              <SelectValue :placeholder="String(selectedYear)" />
            </SelectTrigger>
            <SelectContent align="end">
              <SelectLabel> Filter by year </SelectLabel>
              <SelectSeparator />
              <SelectItem
                v-for="(year, index) in availableYears"
                :key="index"
                :value="year"
              >
                {{ year }}
              </SelectItem>
            </SelectContent>
          </Select>
          <Button
            variant="ghost"
            type="icon"
            class="rounded-md"
          >
            <Download />
            <!-- Export -->
          </Button>
        </div>
      </div>
      <div class="mt-6 flex flex-col gap-4">
        <JobOrderStatsGrid
          :total="data.totalJobOrders"
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
            <ServiceTypeTrends :data="data.trends" />
          </div>
          <div>
            <ServiceTypeCompletion :data="data.completion" />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
