<script setup lang="ts">
import UserAvatar from '@/components/UserAvatar.vue'
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
</script>

<template>
  <div>
    <div class="flex items-center gap-2">
      <UserAvatar
        :avatar-path="row.original.jobOrder.creator.account?.avatar"
        :fallback="row.original.jobOrder.creator.fullName"
        class="size-9"
      />
      <div>
        <div class="text-xs font-medium">
          {{ row.original.jobOrder.creator?.fullName }}
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
