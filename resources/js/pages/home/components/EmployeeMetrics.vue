<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import { UserRoundMinus, UserRoundX, UsersRound } from 'lucide-vue-next'
import { FunctionalComponent } from 'vue'
import { Status } from '..'
import { EmployeeStatistics } from '..'

interface EmployeeMetricsProps {
  data?: EmployeeStatistics[]
}

defineProps<EmployeeMetricsProps>()

const statusBindings: Record<
  Status,
  {
    bgClass: string
    textClass: string
    icon: FunctionalComponent
  }
> = {
  Active: {
    bgClass: 'bg-tertiary/75',
    textClass: 'text-tertiary',
    icon: UsersRound,
  },
  Inactive: {
    bgClass: 'bg-destructive/75',
    textClass: 'text-destructive',
    icon: UserRoundMinus,
  },
  'No Account': {
    bgClass: 'bg-zinc-700/75',
    textClass: 'text-muted-foreground',
    icon: UserRoundX,
  },
}

const getIcon = (status: Status) => statusBindings[status].icon
</script>

<template>
  <div class="flex flex-col gap-4">
    <div class="flex items-center gap-2">
      <div class="text-sm font-medium">Employee Metrics</div>
      <Separator class="flex-1" />
    </div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
      <Card
        v-for="(employeeStat, index) in data"
        :key="index"
        class="group shadow transition-all hover:translate-y-[-2px] hover:shadow-lg"
      >
        <CardContent class="flex flex-row items-center gap-4 p-5">
          <div
            :class="[
              'flex h-12 w-12 items-center justify-center rounded-full text-white',
              statusBindings[employeeStat.status].bgClass,
            ]"
          >
            <component
              :is="getIcon(employeeStat.status)"
              class="size-6"
            />
          </div>
          <div>
            <p class="text-sm font-medium text-muted-foreground">
              {{ employeeStat.status }}
            </p>
            <p
              :class="[
                'text-2xl font-bold',
                statusBindings[employeeStat.status].textClass,
              ]"
            >
              {{ employeeStat.total }}
            </p>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
