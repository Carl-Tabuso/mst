<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { LineChart } from '@/components/ui/chart-line'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { getInitials } from '@/composables/useInitials'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem, Employee } from '@/types'
import { CurveType } from '@unovis/ts'
import { computed } from 'vue'

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
    }
    frontliners: {
      employee: Employee
      createdJobOrdersCount: number
      rank: number
    }[]
  }
}

const props = defineProps<IndexProps>()

// so the arrangement would be 2 | 1 | 3
const top3 = computed(() => props.data.frontliners.slice(0, 3))
const temp = top3.value.toSpliced(0, 1)
const top3Arrangement = computed(() => temp.toSpliced(1, 0, top3.value[0]))

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
    <div class="m-6">
      <div class="flex items-center justify-between">
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
      <div class="mt-10">
        <!-- <div class="text-xl font-semibold text-primary mb-6">
          Job Order Metrics
        </div> -->
        <div class="flex flex-col gap-4 lg:flex-row">
          <div
            class="flex flex-col items-center gap-5 rounded-md border p-6 lg:w-1/3 lg:items-start"
          >
            <div class="mb-3">
              <div class="text-lg font-semibold leading-5">
                Frontliner Rankings
              </div>
              <div class="text-sm text-muted-foreground">
                Ranking is based on the total job orders created.
              </div>
            </div>
            <div class="mx-auto flex items-end justify-center gap-1">
              <div
                v-if="data.frontliners.length"
                v-for="(top, index) in top3Arrangement"
                :key="index"
                class="relative flex flex-col items-center"
              >
                <div
                  class="absolute -top-5 flex h-7 w-7 items-center justify-center rounded-full border-2 border-white bg-primary text-xs font-bold text-white dark:text-primary-foreground"
                >
                  {{ top.rank }}
                </div>
                <Avatar
                  :src="top.employee.account?.avatar"
                  :alt="top.employee.fullName"
                  :size="index === 1 ? 'base' : 'sm'"
                >
                  <AvatarFallback>
                    {{ getInitials(top.employee.fullName) }}
                  </AvatarFallback>
                </Avatar>
                <div
                  class="mt-2 min-h-[28px] max-w-[80px] break-words text-center text-xs font-medium leading-tight text-muted-foreground"
                >
                  {{ top.employee.fullName }}
                </div>
                <div class="text-lg font-bold text-primary">
                  {{ top.createdJobOrdersCount }}
                </div>
              </div>
            </div>
            <div
              class="max-h-60 w-full divide-y divide-border overflow-y-auto rounded-md"
            >
              <div
                v-for="frontliner in data.frontliners"
                :key="frontliner.employee.id"
                class="flex items-center justify-between px-3 py-2 transition-colors hover:bg-muted/50"
              >
                <div class="flex items-center gap-3">
                  <div
                    class="flex h-5 w-5 items-center justify-center rounded-full bg-primary text-xs font-bold text-white dark:text-primary-foreground"
                  >
                    {{ frontliner.rank }}
                  </div>
                  <div class="max-w-[150px] truncate text-xs">
                    {{ frontliner.employee.fullName }}
                  </div>
                </div>
                <div class="text-xs font-bold text-primary">
                  {{ frontliner.createdJobOrdersCount }}
                </div>
              </div>
            </div>
          </div>
          <div class="flex-1 rounded-lg border p-6">
            <div class="mb-3">
              <div class="text-lg font-semibold leading-5">
                Monthly Job Order Trends
              </div>
              <div class="text-sm text-muted-foreground">
                Distribution between completed and cancelled job orders by
                month.
              </div>
            </div>
            <LineChart
              :data="data.metrics"
              index="month"
              :categories="['Completed', 'Cancelled']"
              :curve-type="CurveType.Linear"
            />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
