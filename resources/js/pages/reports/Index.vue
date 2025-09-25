<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import ReportsAnalyticsPlaholder from '@/components/placeholders/ReportsAnalyticsPlaholder.vue'
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
import { onMounted, ref } from 'vue'
import FrontlinerRankings from './components/FrontlinerRankings.vue'
import JobOrderStatsGrid from './components/JobOrderStatsGrid.vue'
import MonthlyJobOrderTrends from './components/MonthlyJobOrderTrends.vue'
import ServiceTypeCompletion from './components/ServiceTypeCompletion.vue'
import ServiceTypeTrends from './components/ServiceTypeTrends.vue'

interface IndexProps {
  data?: {
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
  availableYears?: number[]
}

const urlParam = useUrlSearchParams('history')

defineProps<IndexProps>()

const selectedYear = ref<number>(
  Number(urlParam.year) || new Date().getFullYear(),
)

const isLoading = ref(true)

const onYearSelect = (year: number) => {
  isLoading.value = true
  router.get(
    route('report.index'),
    {
      year: year,
    },
    {
      only: ['data'],
      showProgress: false,
      preserveState: true,
      replace: true,
      onSuccess: () => (selectedYear.value = year),
      onFinish: () => (isLoading.value = false),
    },
  )
}

onMounted(() => {
  router.reload({
    only: ['data', 'availableYears'],
    onFinish: () => (isLoading.value = false),
  })
})

const onExport = () => {
  window.open(
    route('report.export', {
      year: selectedYear.value,
    }),
    '__blank',
  )
}

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Home',
    href: route('home'),
  },
  {
    title: 'Reports and Analytics',
    href: route('report.index'),
  },
]
</script>

<template>
  <Head title="Reports and Analytics" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
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
            @click="onExport"
            class="rounded-md"
          >
            <Download />
          </Button>
        </div>
      </div>
      <ReportsAnalyticsPlaholder v-if="isLoading" />
      <div
        v-else
        class="mt-6 flex flex-col gap-4"
      >
        <JobOrderStatsGrid
          :total="data!.totalJobOrders"
          :top="data!.top"
        />
        <div class="sm-grid-cols-1 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <FrontlinerRankings
            :frontliners="data!.frontliners"
            class="col-span-1"
          />
          <MonthlyJobOrderTrends
            :metrics="data!.metrics"
            class="md:col-span-1 lg:col-span-2"
          />
        </div>
        <div class="grid gap-4 sm:grid-cols-1 lg:grid-cols-2">
          <ServiceTypeTrends :data="data!.trends" />
          <ServiceTypeCompletion :data="data!.completion" />
        </div>
      </div>
    </MainContainer>
  </AppLayout>
</template>
