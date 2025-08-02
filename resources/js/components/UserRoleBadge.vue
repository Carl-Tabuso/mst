<script setup lang="ts">
import { userRoles, UserRoleType } from '@/constants/user-role'
import { SharedData } from '@/types'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

interface UserRoleBadgeProps {
  useAuthUser?: boolean
  roleName?: UserRoleType
  small?: boolean
}

const props = withDefaults(defineProps<UserRoleBadgeProps>(), {
  useAuthUser: false,
})

const userRole = computed(() => {
  if (props.roleName) {
    return userRoles.find((role) => role.id === props.roleName)
  } else {
    const page = usePage<SharedData>()
    return userRoles.find((role) => {
      return role.id === page.props.auth.user.roles[0].name
    })
  }
})
</script>

<template>
  <div
    v-if="userRole"
    :class="[
      'inline-flex items-center rounded-lg border text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
      userRole.class.badge,
      props.small ? 'px-1.5 py-0' : 'gap-x-1 px-2.5 py-0.5',
    ]"
  >
    <span
      :class="[
        'mr-1 rounded-full',
        userRole.class.dot,
        props.small ? 'h-[7px] w-[7px]' : 'h-[10px] w-[10px]',
      ]"
    />
    <span
      :class="[
        'font-semibold',
        userRole.class.text,
        props.small ? 'text-[11px]' : 'text-xs',
      ]"
    >
      {{ userRole.label }}
    </span>
  </div>
</template>
