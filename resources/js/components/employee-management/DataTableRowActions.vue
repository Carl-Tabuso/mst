<script setup lang="ts">
import { EllipsisVertical, Eye, Pencil, Trash2 } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'

defineProps<{
  row: any
}>()
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button
        variant="ghost"
        class="flex h-8 w-8 p-0 data-[state=open]:bg-muted"
      >
        <EllipsisVertical class="h-4 w-4" />
        <span class="sr-only">Open menu</span>
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end" class="w-[160px]">
      <DropdownMenuItem>
        <Eye class="mr-2 h-3.5 w-3.5 text-muted-foreground/70" />
        View
      </DropdownMenuItem>
      <DropdownMenuItem>
        <Pencil class="mr-2 h-3.5 w-3.5 text-muted-foreground/70" />
        Edit
      </DropdownMenuItem>
      <DropdownMenuItem>
        <Trash2 class="mr-2 h-3.5 w-3.5 text-muted-foreground/70" />
        Delete
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>