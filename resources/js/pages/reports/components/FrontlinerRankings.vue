<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import UserAvatar from '@/components/UserAvatar.vue'
import { Employee } from '@/types'
import { computed } from 'vue'

interface FrontlinerRankingsProps {
  frontliners: {
    employee: Employee
    createdJobOrdersCount: number
    rank: number
  }[]
}

const props = defineProps<FrontlinerRankingsProps>()

// so the arrangement would be 2 | 1 | 3
const top3 = computed(() => props.frontliners.slice(0, 3))
const temp = top3.value.toSpliced(0, 1)
const top3Arrangement = computed(() => temp.toSpliced(1, 0, top3.value[0]))
</script>

<template>
  <Card class="shadow">
    <CardHeader class="item-center mb-3 flex flex-col justify-between">
      <CardTitle class="text-lg font-semibold leading-[11px]">
        Frontliner Rankings
      </CardTitle>
      <p class="text-sm text-muted-foreground">
        Based on the total job orders created.
      </p>
    </CardHeader>
    <CardContent class="flex flex-col gap-3">
      <div class="mx-auto flex items-end justify-center gap-1">
        <div
          v-if="frontliners.length"
          v-for="(top, index) in top3Arrangement"
          :key="index"
          class="relative flex flex-col items-center"
        >
          <div
            class="absolute -top-5 flex h-7 w-7 items-center justify-center rounded-full border-2 border-white bg-primary text-xs font-bold text-white dark:text-primary-foreground"
          >
            {{ top.rank }}
          </div>
          <UserAvatar
            :avatar-path="top.employee.account?.avatar"
            :fallback="top.employee.fullName"
            :class="[index === 1 ? 'h-16 w-16 text-2xl' : 'h-10 w-10 text-xs']"
          />
          <div
            class="mt-2 min-h-[28px] w-[60px] truncate break-words text-center text-xs font-medium leading-tight text-muted-foreground"
          >
            {{ top.employee.fullName }}
          </div>
          <!-- <div class="text-lg font-bold text-primary">
            {{ top.createdJobOrdersCount }}
          </div> -->
        </div>
      </div>
      <div
        class="max-h-52 w-full divide-y divide-border overflow-y-auto rounded-md"
      >
        <div
          v-for="frontliner in frontliners"
          :key="frontliner.employee.id"
          class="flex items-center justify-between px-3 py-2 transition-colors hover:bg-muted/50"
        >
          <div class="flex items-center gap-3">
            <div
              class="flex h-5 w-5 items-center justify-center rounded-full bg-primary text-xs font-bold text-white dark:text-primary-foreground"
            >
              {{ frontliner.rank }}
            </div>
            <div
              class="max-w-[150px] truncate text-xs font-medium text-muted-foreground"
            >
              {{ frontliner.employee.fullName }}
            </div>
          </div>
          <div class="text-xs font-bold text-primary">
            {{ frontliner.createdJobOrdersCount }}
          </div>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
