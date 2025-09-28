<script setup lang="ts">
import { JobOrder } from '@/types'
import { Row } from '@tanstack/vue-table'
import { formatDistanceToNow } from 'date-fns'
import UserAvatar from './UserAvatar.vue'

interface CreatorAndTimestampProps {
  row: Row<JobOrder>
}

defineProps<CreatorAndTimestampProps>()
</script>

<template>
  <div>
    <div class="flex items-center gap-2">
      <UserAvatar
        :avatar-path="row.original.creator?.account?.avatar"
        :fallback="row.original.creator.fullName"
        class="size-9"
      />
      <div>
        <div class="text-xs font-medium">
          {{ row.original.creator?.fullName }}
        </div>
        <div class="text-[11px] text-muted-foreground">
          {{
            `created ${formatDistanceToNow(row.original.createdAt, { addSuffix: true })}`
          }}
        </div>
      </div>
    </div>
  </div>
</template>
