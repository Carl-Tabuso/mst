<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { useInitials } from '@/composables/useInitials'
import { AuthUser } from '@/types'
import { computed } from 'vue'
import UserRoleBadge from './UserRoleBadge.vue'

interface Props {
  user: AuthUser
  showEmail?: boolean
  showRole?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  showEmail: false,
  showRole: false,
})

const { getInitials } = useInitials()

// Compute whether we should show the avatar image
const showAvatar = computed(() => props.user.avatar && props.user.avatar !== '')
</script>

<template>
  <Avatar class="h-8 w-8 overflow-hidden">
    <AvatarImage
      v-if="showAvatar"
      :src="user.avatar"
      :alt="user.employee.full_name"
    />
    <AvatarFallback class="text-black dark:text-white">
      {{ getInitials(user.employee.full_name) }}
    </AvatarFallback>
  </Avatar>

  <div class="grid flex-1 text-left text-sm leading-tight">
    <span class="truncate font-medium">{{ user.employee.full_name }}</span>
    <span
      v-if="showEmail"
      class="truncate text-xs text-muted-foreground"
      >{{ user.email }}</span
    >
    <div
      v-if="showRole"
      class="text truncate text-muted-foreground"
    >
      <UserRoleBadge use-auth-user />
    </div>
  </div>
</template>
