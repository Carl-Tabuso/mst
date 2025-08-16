<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { getInitials } from '@/composables/useInitials'
import { JobOrderCorrection } from '@/types'
import { Row } from '@tanstack/vue-table'
import { computed } from 'vue'

interface RequestingFrontlinerProps {
  row: Row<JobOrderCorrection>
}

const props = defineProps<RequestingFrontlinerProps>()

const changesCount = computed(() => {
  return Object.keys(props.row.original.properties.after).length
})

const { jobOrder } = props.row.original
</script>

<template>
  <div>
    <div class="flex items-center gap-2">
      <Avatar class="h-9 w-9 shrink-0">
        <AvatarImage
          v-if="jobOrder.creator.account?.avatar"
          :src="jobOrder.creator.account.avatar"
          :alt="jobOrder.creator.fullName"
        />
        <AvatarFallback>
          {{ getInitials(jobOrder.creator.fullName) }}
        </AvatarFallback>
      </Avatar>
      <div>
        <div class="text-xs font-medium">
          {{ jobOrder.creator?.fullName }}
        </div>
        <div class="text-[11px] text-muted-foreground">
          Made
          <span class="font-bold">{{ changesCount }}</span>
          changes
        </div>
      </div>
    </div>
  </div>
</template>
