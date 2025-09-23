<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import UserAvatar from '@/components/UserAvatar.vue'
import { JobOrderCorrection } from '@/types'
import { format } from 'date-fns'

interface ReasonCardProps {
  correction: JobOrderCorrection
}

defineProps<ReasonCardProps>()
</script>

<template>
  <Card>
    <CardContent class="px-3 py-3">
      <div class="flex items-start gap-3">
        <UserAvatar
          :avatar-path="correction.jobOrder.creator?.account?.avatar"
          :fallback="correction.jobOrder.creator.fullName"
          class="size-10"
        />
        <div class="flex flex-col">
          <div class="flex items-center gap-2 text-sm text-muted-foreground">
            <span>
              {{ correction.jobOrder.creator.fullName }}
            </span>
            <span>•</span>
            <span>
              {{ format(correction.createdAt, "MMMM d, yyyy 'at' h:mm a") }}
            </span>
          </div>
          <p class="mt-1 text-sm font-medium text-foreground">
            {{ correction.reason }}
          </p>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
