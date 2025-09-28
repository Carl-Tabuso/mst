<script setup lang="ts">
import { Employee } from '@/types'
import { Check } from 'lucide-vue-next'
import UserAvatar from './UserAvatar.vue'

interface EmployeePopoverSelectionProps {
  employee: Employee
  isSelected?: boolean
}

withDefaults(defineProps<EmployeePopoverSelectionProps>(), {
  isSelected: false,
})
</script>

<template>
  <div class="flex items-center space-x-3">
    <div
      :class="[
        'flex h-4 w-4 items-center justify-center rounded-sm border border-primary',
        isSelected
          ? 'bg-primary text-primary-foreground'
          : 'opacity-50 [&_svg]:invisible',
      ]"
    >
      <Check class="h-4 w-4" />
    </div>
    <UserAvatar
      :avatar-path="employee?.account?.avatar"
      :fallback="employee.fullName"
    />
    <div class="grid flex-1 text-left text-[13px] leading-tight">
      <span class="truncate">
        {{ employee.fullName }}
      </span>
    </div>
  </div>
</template>
