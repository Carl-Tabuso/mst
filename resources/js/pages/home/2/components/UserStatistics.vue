<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import {
  UserRoundCheck,
  UserRoundMinus,
  UserRoundX,
  UsersRound,
} from 'lucide-vue-next'
import { UserStatisticsCard } from '..'

interface UserStatisticsProps {
  data?: UserStatisticsCard[]
}

const props = defineProps<UserStatisticsProps>()

console.log(props.data)

const statBindings: Record<any, any> = {
  'Total Users': {
    textClass: 'text-sky-900',
    bgClass: 'bg-sky-900/75',
    icon: UsersRound,
  },
  'Employee w No Account': {
    textClass: 'text-destructive',
    bgClass: 'bg-destructive/75',
    icon: UserRoundX,
  },
  'Active Users': {
    textClass: 'text-tertiary',
    bgClass: 'bg-tertiary/75',
    icon: UserRoundCheck,
  },
  'Inactive Users': {
    textClass: 'text-warning',
    bgClass: 'bg-warning',
    icon: UserRoundMinus,
  },
}
</script>

<template>
  <div class="grid gap-4 sm:grid-cols-1 lg:grid-cols-2">
    <Card
      v-for="(statistic, index) in data"
      :key="index"
      class="group shadow transition-all hover:translate-y-[-2px] hover:shadow-lg"
    >
      <CardContent class="flex flex-row items-center gap-4 p-5">
        <div
          :class="[
            'flex h-12 w-12 items-center justify-center rounded-full text-white',
            statBindings[statistic.label].bgClass,
          ]"
        >
          <component
            :is="statBindings[statistic.label].icon"
            class="size-6"
          />
        </div>
        <div>
          <p class="text-sm font-medium text-muted-foreground">
            {{ statistic.label }}
          </p>
          <p
            :class="[
              'text-2xl font-bold',
              statBindings[statistic.label].textClass,
            ]"
          >
            {{ statistic.total }}
          </p>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
