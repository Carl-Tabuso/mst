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
import { router } from '@inertiajs/vue3'
import type { Table } from '@tanstack/vue-table'
import { Archive, Download, Search, Settings2 } from 'lucide-vue-next'
import { computed } from 'vue'
import FilterPopover from './FilterPopover.vue'

interface DataTableToolbarProps {
  table: Table<TData>
  globalFilter: string | number
}

const props = defineProps<DataTableToolbarProps>()

const handleOnSearch = (value: string | number) => {
  props.table.setGlobalFilter(value)
}

const hasRowSelection = computed(
  () => props.table.getSelectedRowModel().rows.length > 0,
)

const visibleColumnCount = computed(
  () =>
    props.table
      .getAllColumns()
      .filter((column) => column.getCanHide() && column.getIsVisible()).length,
)

const handleExport = () => {
  window.open(route('job_order.export'), '_blank')
}

const handlePageSizeArchival = () => {
  const jobOrderIds = props.table
    .getSelectedRowModel()
    .rows.map((row) => row.original.id)

  router.visit(route('job_order.destroy'), {
    method: 'delete',
    data: { jobOrderIds },
    preserveScroll: true,
    onBefore: () => confirm('Are you sure you want to archive?'),
    onSuccess: () => console.log(), // show feedback
  })
}
</script>

<template>
  <div class="flex items-center py-1">
    <div class="relative w-full max-w-xs items-center">
      <Input
        id="search"
        type="text"
        class="h-9 max-w-sm pl-12"
        placeholder="Search"
        :model-value="globalFilter"
        @update:model-value="handleOnSearch"
      />
      <span
        class="absolute inset-y-0 start-0 flex items-center justify-center px-5"
      >
        <Search class="size-4 text-muted-foreground" />
      </span>
    </div>

    <FilterPopover />

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

    <Button
      @click="handleExport"
      variant="ghost"
    >
      <Download
        class="mr-2"
        :stroke-width="1"
      />
      Export
    </Button>

    <div class="ml-auto">
      <Button
        :disabled="!hasRowSelection"
        :variant="hasRowSelection ? 'destructive' : 'secondary'"
        class="mx-3"
        @click="handlePageSizeArchival"
      >
        <Archive class="mr-2" />
        Archive
        <template v-if="hasRowSelection">
          <div class="hidden lg:flex">
            <Badge
              variant="destructive"
              class="rounded-full font-normal"
              >{{ table.getSelectedRowModel().rows.length }}
            </Badge>
          </div>
        </template>
      </Button>
    </div>
  </div>
</template>
