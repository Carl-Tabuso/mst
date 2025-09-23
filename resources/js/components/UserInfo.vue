<script setup lang="ts">
import { AuthUser } from '@/types'
import UserAvatar from './UserAvatar.vue'
import UserRoleBadge from './UserRoleBadge.vue'

interface Props {
  user: AuthUser
  showEmail?: boolean
  showRole?: boolean
}

withDefaults(defineProps<Props>(), {
  showEmail: false,
  showRole: false,
})
</script>

<template>
  <div class="flex items-center gap-2">
    <UserAvatar
      :avatar-path="user.avatar"
      :fallback="user.employee.full_name"
      class="size-8"
    />
    <div class="grid flex-1 text-left text-sm leading-tight">
      <span class="truncate font-medium">
        {{ user.employee.full_name }}
      </span>
      <span
        v-if="showEmail"
        class="truncate text-xs text-muted-foreground"
      >
        {{ user.email }}
      </span>
      <div
        v-if="showRole"
        class="truncate text-muted-foreground"
      >
        <UserRoleBadge use-auth-user />
      </div>
    </div>
  </div>
</template>
