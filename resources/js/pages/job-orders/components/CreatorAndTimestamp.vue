<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { getInitials } from '@/composables/useInitials'
import { JobOrder } from '@/types'
import { Row } from '@tanstack/vue-table'
import { computed } from 'vue'

interface CreatorAndTimestampProps {
  row: Row<JobOrder>
}

const props = defineProps<CreatorAndTimestampProps>()

const formattedDateTime = computed(() => {
  return new Date(props.row.original.createdAt).toLocaleString('en-ph', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
})
</script>

<template>
  <div>
    <div class="flex items-center gap-2">
      <Avatar class="h-9 w-9 shrink-0 rounded-full">
        <AvatarImage
          v-if="row.original.creator.account?.avatar"
          :src="row.original.creator.account.avatar"
          :alt="row.original.creator.fullName"
        />
        <AvatarFallback>
          {{ getInitials(row.original.creator.fullName) }}
        </AvatarFallback>
      </Avatar>
      <div>
        <div class="text-xs font-semibold">
          {{ row.original.creator?.fullName }}
        </div>
        <div class="text-[11px] text-muted-foreground">
          {{ formattedDateTime }}
        </div>
      </div>
    </div>
  </div>
</template>
