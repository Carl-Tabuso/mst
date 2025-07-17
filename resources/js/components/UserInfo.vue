<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { useInitials } from '@/composables/useInitials'
import { computed } from 'vue'
import { Badge } from './ui/badge'

interface User {
  avatar: string
  email: string
  employee: {
    full_name: string
  }
  roles: {
    name: string
    permissions: {
      name: string
    }[]
  }[]
}

interface Props {
  user: User
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
  <Avatar class="h-9 w-9 overflow-hidden rounded-full">
    <AvatarImage
      v-if="showAvatar"
      :src="user.avatar"
      :alt="user.employee.full_name"
    />
    <AvatarFallback class="rounded-full text-black dark:text-white">
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
      <Badge
        variant="outline"
        class="inline-flex w-fit items-center gap-x-1 bg-sky-100 px-2 py-0.5"
      >
        <span class="mr-1 h-[10px] w-[10px] rounded-full bg-sky-700"></span>
        <span class="text-xs font-semibold text-sky-700">{{
          `${user.roles[0].name.charAt(0).toUpperCase()}${user.roles[0].name.slice(1)}`
        }}</span>
      </Badge>
    </div>
  </div>
</template>
