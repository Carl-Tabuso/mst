<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { User } from '@/types'
import { router } from '@inertiajs/vue3'
import { Ellipsis } from 'lucide-vue-next'

interface UserActionsProps {
  user: User
}

const props = defineProps<UserActionsProps>()

const handleEdit = () => {
  router.visit(route('users.settings', props.user.id))
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
      <DropdownMenuItem @click="handleEdit"> Edit </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
