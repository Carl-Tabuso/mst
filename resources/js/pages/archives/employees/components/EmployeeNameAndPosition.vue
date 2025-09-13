<script setup lang="ts">
import UserAvatar from '@/components/UserAvatar.vue'
import { Employee, Position } from '@/types'

interface EmployeeNameAndPositionProps {
  employee: Employee & { position: Position }
}

defineProps<EmployeeNameAndPositionProps>()
</script>

<template>
  <div class="flex flex-row items-center gap-2">
    <UserAvatar
      :avatar-path="employee?.account?.avatar"
      :fallback="employee.fullName"
      class="h-9 w-9"
    />
    <div class="flex flex-col">
      <span class="truncate text-xs font-medium">
        {{ employee.fullName }}
      </span>
      <span class="text-xs text-muted-foreground">
        {{ employee.position.name }}
      </span>
    </div>
  </div>
</template>
