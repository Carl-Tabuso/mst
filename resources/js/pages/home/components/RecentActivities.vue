<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import UserAvatar from '@/components/UserAvatar.vue'
import UserRoleBadge from '@/components/UserRoleBadge.vue'
import { Link } from '@inertiajs/vue3'
import { RecentActivities } from '..'

interface RecentActivitiesProps {
  data?: RecentActivities[]
}

defineProps<RecentActivitiesProps>()
</script>

<template>
  <Card class="w-full shadow">
    <CardHeader class="py-3">
      <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <CardTitle class="text-lg font-semibold text-primary">
            Recent Activities
          </CardTitle>
        </div>
        <Link :href="route('activity.index')">
          <Button variant="outline"> View All </Button>
        </Link>
      </div>
    </CardHeader>
    <Separator />
    <CardContent class="my-2 h-[403px] divide-y divide-border overflow-y-auto">
      <div
        v-for="activity in data"
        :key="activity.id"
        class="py-3"
      >
        <div class="flex items-start gap-3">
          <UserAvatar
            v-if="activity.causer"
            :avatar-path="activity.causer.avatar"
            :fallback="activity.causer.employee.fullName"
            class="size-8"
          />
          <div class="flex-1">
            <div class="flex items-start justify-between gap-2">
              <div
                v-if="activity.causer"
                class="flex flex-col leading-tight"
              >
                <span class="truncate text-xs font-semibold">
                  {{ activity.causer.employee.fullName }}
                </span>
                <UserRoleBadge
                  :role-name="activity.causer.roles[0].name"
                  small
                  class="w-fit text-xs"
                />
              </div>
              <div
                v-else
                class="flex flex-col leading-tight"
              >
                <span class="truncate text-xs font-semibold">
                  System Generated
                </span>
              </div>
              <span class="whitespace-nowrap text-xs text-muted-foreground">
                {{ activity.humanDiff }}
              </span>
            </div>
          </div>
        </div>
        <div class="mt-2 text-xs text-primary">
          {{ activity.description }}
        </div>
      </div>
    </CardContent>
  </Card>
</template>
