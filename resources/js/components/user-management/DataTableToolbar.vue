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
import { User } from '@/types'
import { usePage } from '@inertiajs/vue3'
import type { Table } from '@tanstack/vue-table'
import { useDebounceFn } from '@vueuse/core'
import { Filter, Search, Settings2 } from 'lucide-vue-next'
import { computed } from 'vue'
import FilterPopover from './FilterPopover.vue'

interface DataTableToolbarProps {
  table: Table<User>
  globalFilter: string | number
  routeName?: string
}

const props = withDefaults(defineProps<DataTableToolbarProps>(), {
  routeName: 'users.index',
})

const emit = defineEmits<{
  (e: 'filter-change', filters: any): void
}>()

const page = usePage()

const roles = computed(() => (page.props as any).roles || [])

const currentFilters = computed(() => (page.props as any).filters ?? {})

const handleOnSearch = (value: string | number) => {
  debounceSearch(value)
}

const debounceSearch = useDebounceFn((value) => {
  emit('filter-change', { search: value })
}, 500)

const visibleColumnCount = computed(
  () =>
    props.table
      .getAllColumns()
      .filter((column) => column.getCanHide() && column.getIsVisible()).length,
)

const handleFilterChange = (newFilters: any) => {
  emit('filter-change', newFilters)
}

const handleClearFilters = () => {
  emit('filter-change', {
    search: '',
    role: [],
    fromDateCreated: '',
    toDateCreated: ''
  })
}

const activeFilterCount = computed(() => {
  const filters = (page.props as any).filters ?? {}
  let count = 0
  
  if (filters.search && filters.search.trim() !== '') {
    count++
  }
  
  if (filters.role) {
    if (Array.isArray(filters.role)) {
      count += filters.role.filter(role => 
        role !== null && role !== undefined && role !== ''
      ).length
    } else if (typeof filters.role === 'string') {
      const roles = filters.role.split(',').filter(r => r.trim() !== '')
      count += roles.length
    }
  }
  
  if (filters.fromDateCreated && filters.fromDateCreated !== '') {
    count++
  }
  
  if (filters.toDateCreated && filters.toDateCreated !== '') {
    count++
  }
  
  return count
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
      :roles="roles" 
      :route-name="props.routeName"
      :current-filters="currentFilters"
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