<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { usePermissions } from '@/composables/usePermissions'
import { User } from '@/types'
import { router } from '@inertiajs/vue3'
import { Ellipsis } from 'lucide-vue-next'
import { toast } from 'vue-sonner'

interface UserActionsProps {
  user: User
}

const props = defineProps<UserActionsProps>()
const { can } = usePermissions()

const handleEdit = () => {
  router.visit(route('users.settings', props.user.id))
}

const handleToggleStatus = () => {
  if (props.user.deletedAt) {
    router.visit(route('users.restore', props.user.id), {
      method: 'post',
      preserveScroll: true,
      onSuccess: () => {
        toast.success('User activated successfully')
      },
      onError: () => {
        toast.error('Failed to activate user')
      },
    })
  } else {
    router.visit(route('user-management.deactivate', props.user.id), {
      method: 'post',
      preserveScroll: true,
      onSuccess: () => {
        toast.success('User deactivated successfully')
      },
      onError: () => {
        toast.error('Failed to deactivate user')
      },
    })
  }
}

const handleDelete = () => {
  if (confirm('Are you sure you want to permanently delete this user? This action cannot be undone.')) {
    router.visit(route('user-management.destroy', props.user.id), {
      method: 'delete',
      preserveScroll: true,
      onSuccess: () => {
        toast.success('User permanently deleted')
      },
      onError: () => {
        toast.error('Failed to delete user')
      },
    })
  }
}
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button
        variant="ghost"
        class="flex h-8 w-8 p-0 data-[state=open]:bg-muted"
      >
        <Ellipsis class="h-4 w-4" />
        <span class="sr-only">Open menu</span>
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent
      align="end"
      class="w-[160px]"
    >
      <DropdownMenuItem @click="handleEdit">
        Edit
      </DropdownMenuItem>

      <DropdownMenuItem
        v-if="!props.user.deletedAt"
        @click="handleToggleStatus"
      >
        Deactivate
      </DropdownMenuItem>

      <DropdownMenuItem
        v-else-if="props.user.deletedAt"
        @click="handleToggleStatus"
      >
        Activate
      </DropdownMenuItem>

      <DropdownMenuItem
        v-if="can('delete:user')"
        class="text-red-600"
        @click="handleDelete"
      >
        Delete
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
