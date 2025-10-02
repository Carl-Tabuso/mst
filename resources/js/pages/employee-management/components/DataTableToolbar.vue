<script setup lang="ts" generic="TData">
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
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
import { Employee } from '@/types'
import { router, usePage } from '@inertiajs/vue3'
import type { Table } from '@tanstack/vue-table'
import { useDebounceFn } from '@vueuse/core'
import {
  Archive,
  Download,
  LoaderCircle,
  Search,
  Settings2,
  TriangleAlert,
  UserPlus,
} from 'lucide-vue-next'
import { VisuallyHidden } from 'radix-vue'
import { computed, ref } from 'vue'
import { toast } from 'vue-sonner'
import FilterPopover from './FilterPopover.vue'

interface DataTableToolbarProps {
  table: Table<Employee>
  globalFilter: string | number
  routeName?: string
  positions?: Array<{ id: number; name: string }>
}

const props = withDefaults(defineProps<DataTableToolbarProps>(), {
  routeName: 'employee-management.index',
  positions: () => [],
})

const page = usePage()

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

const hasRowSelection = computed(
  () => props.table.getSelectedRowModel().rows.length > 0,
)

const visibleColumnCount = computed(
  () =>
    props.table
      .getAllColumns()
      .filter((column) => column.getCanHide() && column.getIsVisible()).length,
)

const employeeIds = computed(() => {
  return props.table.getSelectedRowModel().rows.map((row) => {
    return row.original.id
  })
})

const isLoading = ref(false)

const handleExport = () => {
  const currentFilters = (page.props as any).filters ?? {}

  const exportFilters = {
    positions: currentFilters.positions || [],
    search: currentFilters.search || '',
  }

  const query = new URLSearchParams()
  exportFilters.positions.forEach((pos: string | number) =>
    query.append('filters[positions][]', String(pos)),
  )
  if (exportFilters.search) {
    query.append('filters[search]', exportFilters.search)
  }

  const url = route('employee-management.export') + '?' + query.toString()

  // same tab
  window.location.href = url
}

const handleBulkArchive = () => {
  router.visit(route('employee-management.bulk-archive'), {
    method: 'post',
    data: { employeeIds: employeeIds.value },
    preserveScroll: true,
    onBefore: () => (isLoading.value = true),
    onSuccess: () => {
      isLoading.value = false
      props.table.resetRowSelection()
      toast.success('Successfully archived employees', {
        position: 'top-center',
      })
    },
    onError: (error) => {
      isLoading.value = false
      toast.error('Failed to archive employees' + error, {
        position: 'top-center',
      })
    },
  })
}

const handleBulkCreateAccounts = () => {
  router.visit(route('employee-management.bulk_create_accounts'), {
    method: 'post',
    data: { employeeIds: employeeIds.value },
    preserveScroll: true,
    onBefore: () => (isLoading.value = true),
    onSuccess: (page: any) => {
      isLoading.value = false
      props.table.resetRowSelection()
      toast.success(page.props.flash.message, { position: 'top-center' })
    },
    onError: () => {
      isLoading.value = false
      toast.error('Failed to create accounts', { position: 'top-center' })
    },
  })
}

const { can } = usePermissions()
</script>

<template>
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-2">
    <div class="relative w-full sm:max-w-xs">
      <Input
        id="search"
        type="text"
        class="h-9 w-full pl-10 sm:pl-9"
        placeholder="Search employees..."
        :model-value="globalFilter"
        @update:model-value="handleOnSearch"
      />
      <span
        class="absolute inset-y-0 start-0 flex items-center justify-center px-3"
      >
        <Search class="size-4 text-muted-foreground" />
      </span>
    </div>

    <div class="flex flex-wrap items-center gap-2">
      <FilterPopover
        :routeName="routeName"
        :positions="positions"
      />

      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <Button
            variant="ghost"
            size="sm"
            class="h-9"
          >
            <Settings2 class="size-4 sm:mr-2" />
            <span class="hidden sm:inline">View</span>
            <Badge
              variant="secondary"
              class="ml-1 hidden rounded-full font-extrabold sm:flex"
            >
              {{ visibleColumnCount }}
            </Badge>
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent
          align="end"
          class="w-[200px]"
        >
          <DropdownMenuLabel>Toggle columns</DropdownMenuLabel>
          <DropdownMenuSeparator />
          <DropdownMenuCheckboxItem
            v-for="column in table
              .getAllColumns()
              .filter((c) => c.getCanHide())"
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
        size="sm"
        class="h-9"
      >
        <Download class="size-4 sm:mr-2" />
        <span class="hidden sm:inline">Export</span>
      </Button>

      <Button
        v-if="hasRowSelection && can('create:user_account')"
        @click="handleBulkCreateAccounts"
        variant="ghost"
        size="sm"
        class="h-9"
        :disabled="isLoading"
      >
        <UserPlus class="size-4 sm:mr-2" />
        <span class="hidden sm:inline">Create Accounts</span>
        <Badge
          variant="secondary"
          class="ml-1 hidden rounded-full font-extrabold sm:flex"
        >
          {{ table.getSelectedRowModel().rows.length }}
        </Badge>
      </Button>

      <div
        v-if="hasRowSelection && can('manage:employees')"
        class="sm:ml-auto"
      >
        <Dialog>
          <DialogTrigger as-child>
            <Button
              variant="warning"
              size="sm"
              class="h-9 w-full sm:w-auto"
            >
              <Archive class="size-4 sm:mr-2" />
              <span class="hidden sm:inline">Archive</span>
              <Badge
                variant="secondary"
                class="ml-1 hidden rounded-full font-extrabold sm:flex"
              >
                {{ table.getSelectedRowModel().rows.length }}
              </Badge>
            </Button>
          </DialogTrigger>
          <DialogContent class="w-fit max-w-md">
            <VisuallyHidden as-child>
              <DialogTitle>Archiving Multiple Employees</DialogTitle>
              <DialogDescription>
                Confirm archiving of selected employees
              </DialogDescription>
            </VisuallyHidden>
            <div class="flex flex-col items-center justify-center gap-4">
              <TriangleAlert
                class="h-24 w-24 fill-amber-500 stroke-amber-200 dark:fill-amber-700"
              />
              <div class="mb-4 flex flex-col text-center">
                <div class="text-2xl font-bold text-amber-500 dark:text-white">
                  Archiving
                  {{ table.getSelectedRowModel().rows.length }} Employee(s)
                </div>
                <div class="mt-2 text-sm text-muted-foreground">
                  This action will archive the selected employees. Archived
                  employees can be restored later.
                </div>
              </div>
              <div class="max-h-40 w-full overflow-y-auto">
                <ul class="grid gap-2">
                  <li
                    v-for="row in table.getSelectedRowModel().rows"
                    :key="row.id"
                    class="rounded-sm bg-muted px-3 py-2 text-xs font-medium shadow-sm"
                  >
                    {{ row.getValue('fullName') }}
                  </li>
                </ul>
              </div>
              <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:gap-4">
                <DialogClose as-child>
                  <Button
                    variant="outline"
                    class="px-8"
                  >
                    Cancel
                  </Button>
                </DialogClose>
                <Button
                  variant="warning"
                  class="px-8"
                  :disabled="isLoading"
                  @click="handleBulkArchive"
                >
                  <LoaderCircle
                    v-show="isLoading"
                    class="mr-2 size-4 animate-spin"
                  />
                  Archive
                </Button>
              </div>
            </div>
          </DialogContent>
        </Dialog>
      </div>
    </div>
  </div>
</template>
