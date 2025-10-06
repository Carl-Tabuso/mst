<script setup lang="ts">
import { HTMLAttributes } from 'vue'
import {
  Avatar,
  AvatarFallback,
  AvatarImage,
  AvatarVariants,
} from './ui/avatar'
import { getInitials } from '@/composables/useInitials'

interface UserAvatarProps {
  avatarPath?: string | null
  fallback: string
  class?: HTMLAttributes['class']
  size?: AvatarVariants['size']
  shape?: AvatarVariants['shape']
}

const props = defineProps<UserAvatarProps>()

const toForward = {
  size: props.size,
  shape: props.shape,
}
</script>

<template>
  <Avatar
    class="h-7 w-7 shrink-0 overflow-hidden border border-primary"
    :class="class"
    v-bind="toForward"
  >
    <AvatarImage
      v-if="avatarPath"
      :src="avatarPath"
      :alt="fallback"
    />
    <AvatarFallback>
      {{ getInitials(fallback) }}
    </AvatarFallback>
  </Avatar>
</template>
