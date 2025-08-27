<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import { HTMLAttributes } from 'vue'
import { MyRecentActivites } from '..'

interface RecentActivitiesProps {
  data?: MyRecentActivites[]
  contentHeight?: HTMLAttributes['class']
}

withDefaults(defineProps<RecentActivitiesProps>(), {
  contentHeight: 'h-[370px]',
})
</script>

<template>
  <Card class="w-full shadow">
    <CardHeader class="py-4">
      <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <CardTitle class="text-lg font-semibold text-primary">
            Your Recent Activities
          </CardTitle>
        </div>
        <!-- <Link :href="route('activity.index')">
          <Button variant="outline"> View </Button>
        </Link> -->
      </div>
    </CardHeader>
    <Separator />
    <CardContent
      :class="['my-2 divide-y divide-border overflow-y-auto', contentHeight]"
    >
      <div
        v-for="activity in data"
        :key="activity.id"
        class="py-3"
      >
        <div class="flex flex-row items-start justify-between gap-4">
          <div class="flex flex-col">
            <p class="text-xs font-medium text-foreground">
              {{ activity.description }}
            </p>
            <div class="mt-auto flex flex-row items-center gap-2">
              <p class="text-[11px] text-muted-foreground">
                {{ `${activity.platform} - ${activity.browser}` }}
              </p>
              <span class="text-muted-foreground"> • </span>
              <p class="text-[11px] text-muted-foreground">
                {{ activity.ipAddress }}
              </p>
            </div>
          </div>
          <span class="whitespace-nowrap text-xs text-muted-foreground">
            {{ activity.timestamp }}
          </span>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
