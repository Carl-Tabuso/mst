<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { getInitials } from '@/composables/useInitials'
import { Employee } from '@/types'
import { Check } from 'lucide-vue-next'

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
    <Avatar class="h-7 w-7 overflow-hidden rounded-full">
      <AvatarImage
        v-if="employee?.account?.avatar"
        :src="employee?.account?.avatar"
        :alt="employee.fullName"
      />
      <AvatarFallback class="rounded-full">
        {{ getInitials(employee.fullName) }}
      </AvatarFallback>
    </Avatar>
    <div class="grid flex-1 text-left text-[13px] leading-tight">
      <span class="truncate">
        {{ employee.fullName }}
      </span>
    </div>
  </div>
</template>
