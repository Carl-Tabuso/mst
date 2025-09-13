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
import { usePermissions } from '@/composables/usePermissions'
import { User } from '@/types'
import { router, usePage } from '@inertiajs/vue3'
import type { Table } from '@tanstack/vue-table'
import { useDebounceFn } from '@vueuse/core'
import { Filter, Search, Settings2 } from 'lucide-vue-next'
import { computed, ref } from 'vue'
import FilterPopover from './FilterPopover.vue'

interface DataTableToolbarProps {
  table: Table<User>
  globalFilter: string | number
  routeName?: string
}

const props = withDefaults(defineProps<DataTableToolbarProps>(), {
  routeName: 'users.index',
})

const page = usePage()
const { can } = usePermissions()

const handleOnSearch = (value: string | number) => {
  debounceSearch(value)
}

const debounceSearch = useDebounceFn((value) => {
  const currentFilters = (page.props as any).filters ?? {}
  router.get(
    route(props.routeName),
    {
      filters: {
        ...currentFilters,
        search: value,
      },
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    },
  )
}, 500)

const visibleColumnCount = computed(
  () =>
    props.table
      .getAllColumns()
      .filter((column) => column.getCanHide() && column.getIsVisible()).length,
)

// Handle filter changes from FilterPopover
const handleFilterChange = (newFilters: any) => {
  const currentFilters = (page.props as any).filters ?? {}
  router.get(
    route(props.routeName),
    {
      filters: {
        ...currentFilters,
        ...newFilters,
        search: currentFilters.search, // Preserve search
      },
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    },
  )
}

// Handle clear filters from FilterPopover
const handleClearFilters = () => {
  const currentFilters = (page.props as any).filters ?? {}
  router.get(
    route(props.routeName),
    {
      filters: {
        search: currentFilters.search, // Only preserve search
      },
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    },
  )
}

// Count active filters (excluding search)
const activeFilterCount = computed(() => {
  const filters = (page.props as any).filters ?? {}
  return Object.keys(filters).filter(key => 
    key !== 'search' && 
    filters[key] !== null && 
    filters[key] !== undefined && 
    filters[key] !== ''
  ).length
})
</script>

<template>
  <div class="flex items-center gap-2 py-1">
    <div class="relative w-full max-w-xs items-center">
      <Input
        id="search"
        type="text"
        class="h-9 max-w-sm pl-12"
        placeholder="Search users..."
        :model-value="globalFilter"
        @update:model-value="handleOnSearch"
      />
      <span
        class="absolute inset-y-0 start-0 flex items-center justify-center px-5"
      >
        <Search class="size-4 text-muted-foreground" />
      </span>
    </div>

    <!-- Filter Popover -->
    <FilterPopover
      :route-name="props.routeName"
      @filter-change="handleFilterChange"
      @clear-filters="handleClearFilters"
    >
      <Button variant="outline" class="mx-1">
        <Filter class="mr-2 size-4" />
        Filter
        <Badge
          v-if="activeFilterCount > 0"
          variant="secondary"
          class="ml-2 rounded-full px-1 font-extrabold"
        >
          {{ activeFilterCount }}
        </Badge>
      </Button>
    </FilterPopover>

    <!-- Column Visibility Dropdown -->
    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <Button
          variant="ghost"
          class="mx-1"
        >
          <Settings2 class="mr-2 size-4" />
          View
          <div class="hidden space-x-1 lg:flex">
            <Badge
              variant="secondary"
              class="rounded-full font-extrabold"
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