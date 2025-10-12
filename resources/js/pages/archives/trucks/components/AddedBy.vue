<script setup lang="ts">
import UserAvatar from '@/components/UserAvatar.vue'
import { Employee } from '@/types'

interface AddedByProps {
  dispatcher: Employee | null
}

defineProps<AddedByProps>()
</script>

<template>
  <div
    v-if="dispatcher"
    class="flex flex-row items-center gap-2"
  >
    <UserAvatar
      :avatar-path="dispatcher.account?.avatar"
      :fallback="dispatcher.fullName"
      class="h-7 w-7"
    />
    <span class="truncate text-xs font-medium">
      {{ dispatcher.fullName }}
    </span>
  </div>
  <span
    v-else
    class="text-xs text-muted-foreground"
  >
    Unknown Employee
  </span>
</template>
