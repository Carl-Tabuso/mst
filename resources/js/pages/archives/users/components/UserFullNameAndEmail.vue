<script setup lang="ts">
import UserAvatar from '@/components/UserAvatar.vue'
import { User } from '@/types'

interface UserFullNameAndEmailProps {
  user: User
}

defineProps<UserFullNameAndEmailProps>()
</script>

<template>
  <div class="flex flex-row items-center gap-2">
    <UserAvatar
      :avatar-path="user?.avatar"
      :fallback="user.employee.fullName"
      class="h-9 w-9"
    />
    <div class="flex flex-col">
      <span class="truncate text-xs font-medium">
        {{ user.employee.fullName }}
      </span>
      <span class="text-xs text-muted-foreground">
        {{ user.email }}
      </span>
    </div>
  </div>
</template>
