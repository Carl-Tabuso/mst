<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import UserRoleBadge from '@/components/UserRoleBadge.vue'
import { getInitials } from '@/composables/useInitials'
import { Link } from '@inertiajs/vue3'
import { RecentActivitiesCard } from '..'

interface RecentActivitiesProps {
  data?: RecentActivitiesCard[]
}

defineProps<RecentActivitiesProps>()
</script>

<template>
  <Card class="w-full shadow">
    <CardHeader class="py-4">
      <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <CardTitle class="text-lg font-semibold text-primary">
            Recent Activities
          </CardTitle>
        </div>
        <Link :href="route('activity.index')">
          <Button variant="outline"> View </Button>
        </Link>
      </div>
    </CardHeader>
    <Separator />
    <CardContent
      class="my-2 max-h-[394px] divide-y divide-border overflow-y-auto"
    >
      <div
        v-for="activity in data"
        :key="activity.id"
        class="py-3"
      >
        <div class="flex items-start gap-3">
          <Avatar class="h-8 w-8 flex-shrink-0">
            <AvatarImage
              v-if="activity.causer.employee?.account?.avatar"
              :src="activity.causer.employee.account.avatar"
              :alt="activity.causer.employee.fullName"
            />
            <AvatarFallback>
              {{ getInitials(activity.causer.employee.fullName) }}
            </AvatarFallback>
          </Avatar>
          <div class="flex-1">
            <div class="flex items-start justify-between gap-2">
              <div class="flex flex-col leading-tight">
                <span class="truncate text-xs font-semibold">
                  {{ activity.causer.employee.fullName }}
                </span>
                <UserRoleBadge
                  :role-name="activity.causer.roles[0].name"
                  small
                  class="w-fit text-xs"
                />
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
