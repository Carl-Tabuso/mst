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

const avatarSrc = computed(() => {
  return props.user.avatar ? `/storage/${props.user.avatar}` : null
})

const fallbackInitials = computed(() =>
  getInitials(props.user.employee?.full_name || props.user.email || 'User')
)

const fullName = computed(() => props.user.employee?.full_name || props.user.email || 'User')
</script>

<template>
  <div class="flex items-center gap-2">
    <Avatar class="h-8 w-8 overflow-hidden rounded-full">
      <AvatarImage v-if="avatarSrc" :src="avatarSrc" :alt="fullName" />
      <AvatarFallback class="rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
        {{ fallbackInitials }}
      </AvatarFallback>
    </Avatar>

    <div class="grid flex-1 text-left text-sm leading-tight">
      <span class="truncate font-medium">{{ fullName }}</span>
      <span v-if="showEmail" class="truncate text-xs text-muted-foreground">
        {{ user.email }}
      </span>
      <div v-if="showRole" class="truncate text-muted-foreground">
        <UserRoleBadge use-auth-user />
      </div>
    </div>
  </div>
</template>
