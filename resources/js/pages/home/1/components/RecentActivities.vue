<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import UserRoleBadge from '@/components/UserRoleBadge.vue'
import { getInitials } from '@/composables/useInitials'
import { Activity } from 'lucide-vue-next'

const activities = [
  {
    id: 1,
    frontliner: 'John Doe II',
    date: '11:45 PM',
  },
  {
    id: 2,
    frontliner: 'Jane Smith',
    date: '12:45 PM',
  },
  {
    id: 3,
    frontliner: 'Carlos Dela Cruz',
    date: '1:56 AM',
  },
  {
    id: 4,
    frontliner: 'Maria Santos',
    date: '8:00 AM',
  },
  {
    id: 5,
    frontliner: 'Robert Johnson',
    date: '10:36 AM',
  },
]
</script>

<template>
  <Card class="w-full max-w-sm shadow">
    <CardHeader class="py-4">
      <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <Activity class="stroke-2 text-primary" />
          <CardTitle class="text-lg font-semibold text-primary">
            Recent Activities
          </CardTitle>
        </div>
        <Button
          variant="outline"
          @click="() => void 0"
        >
          View all
        </Button>
      </div>
    </CardHeader>
    <Separator />
    <CardContent class="my-2 divide-y divide-border">
      <div
        v-for="activity in activities"
        :key="activity.id"
        class="py-3"
      >
        <div class="flex items-start gap-3">
          <Avatar class="h-8 w-8 flex-shrink-0">
            <AvatarFallback>
              {{ getInitials(activity.frontliner) }}
            </AvatarFallback>
          </Avatar>
          <div class="flex-1">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="text-xs font-semibold">
                  {{ activity.frontliner }}
                </span>
                <UserRoleBadge
                  :role-name="'frontliner'"
                  small
                  class="text-xs"
                />
              </div>
              <span class="whitespace-nowrap text-xs text-muted-foreground">
                {{ activity.date }}
              </span>
            </div>
            <div class="text-xs text-muted-foreground">
              sample.mail@yahoo.com
            </div>
          </div>
        </div>
        <div class="mt-2 text-xs text-primary">
          {{ activity.frontliner }} created a new Job Order
        </div>
      </div>
    </CardContent>
  </Card>
</template>
