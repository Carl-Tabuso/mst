<script setup lang="ts" generic="TData">
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import type { Table } from '@tanstack/vue-table'
import { Settings2 } from 'lucide-vue-next'

interface ColumnViewToggleProps {
  table: Table<TData>
}

defineProps<ColumnViewToggleProps>()
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button
        variant="ghost"
        class="mx-1"
      >
        <Settings2 class="sm:mr-2" />
        <span class="hidden sm:inline"> View </span>
        <div class="flex space-x-1">
          <Badge
            variant="secondary"
            class="rounded-full font-extrabold"
          >
            {{
              table
                .getAllColumns()
                .filter(
                  (column) => column.getCanHide() && column.getIsVisible(),
                ).length
            }}
          </Badge>
        </div>
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent>
      <DropdownMenuLabel>Toggle columns</DropdownMenuLabel>
      <DropdownMenuSeparator />
      <DropdownMenuCheckboxItem
        v-for="column in table.getAllColumns().filter((c) => c.getCanHide())"
        :key="column.id"
        :checked="column.getIsVisible()"
        @update:checked="(value: boolean) => column.toggleVisibility(value)"
      >
        {{ column.columnDef.meta?.label }}
      </DropdownMenuCheckboxItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
