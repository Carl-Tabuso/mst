<script setup lang="ts">
import type { Row } from '@tanstack/vue-table'
import { router } from '@inertiajs/vue3'
import { Ellipsis } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'

interface DataTableRowActionsProps {
  row: Row<any>
}

const props = defineProps<DataTableRowActionsProps>()

const navigateToSettings = () => {
  router.get(`/users/${props.row.original.id}/settings`)
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
    <DropdownMenuContent align="end" class="w-[160px]">
      <DropdownMenuItem @click="navigateToSettings">
        Settings
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>