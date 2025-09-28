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
import { Input } from '@/components/ui/input'
import type { Table } from '@tanstack/vue-table'
import { Search, Settings2 } from 'lucide-vue-next'
import { computed } from 'vue'
import FilterPopover from './FilterPopover.vue'

interface DataTableToolbarProps {
  table: Table<TData>
  globalFilter: string | number
  routeName: string
  filters?: {
    search?: string
    statuses?: string[]
    fromDateOfService?: string
    toDateOfService?: string
  }
}

const props = defineProps<DataTableToolbarProps>()

const handleOnSearch = (value: string | number) => {
  props.table.setGlobalFilter(value)
}

const visibleColumnCount = computed(
  () =>
    props.table
      .getAllColumns()
      .filter((column) => column.getCanHide() && column.getIsVisible()).length,
)
</script>

<template>
  <div class="flex items-center py-1">
    <div class="relative w-full max-w-xs items-center">
      <Input
        id="search"
        type="text"
        class="h-9 max-w-sm pl-12"
        placeholder="Search job orders..."
        :model-value="globalFilter"
        @update:model-value="handleOnSearch"
      />
      <span
        class="absolute inset-y-0 start-0 flex items-center justify-center px-5"
      >
        <Search class="size-4 text-muted-foreground" />
      </span>
    </div>

    <FilterPopover
      :routeName="routeName"
      :filters="filters"
    />

    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <Button
          variant="ghost"
          class="mx-1"
        >
          <Settings2
            class="mr-2"
            :stroke-width="1"
          />
          View
          <div class="hidden space-x-1 lg:flex">
            <Badge
              variant="secondary"
              class="rounded-full font-normal"
            >
              {{ visibleColumnCount }}
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
  </div>
</template>
