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
import { JobOrder } from '@/types'
import { router } from '@inertiajs/vue3'
import type { Table } from '@tanstack/vue-table'
import { useDebounceFn } from '@vueuse/core'
import {
  ArchiveRestore,
  Download,
  LoaderCircle,
  Search,
  Settings2,
} from 'lucide-vue-next'
import { VisuallyHidden } from 'radix-vue'
import { computed, ref } from 'vue'
import { toast } from 'vue-sonner'
import FilterPopover from './FilterPopover.vue'

interface DataTableToolbarProps {
  table: Table<JobOrder>
  globalFilter: string | number
}

const props = defineProps<DataTableToolbarProps>()

const visibleColumnCount = computed(
  () =>
    props.table
      .getAllColumns()
      .filter((column) => column.getCanHide() && column.getIsVisible()).length,
)

const onSearch = useDebounceFn((value: string | number) => {
  console.log('hello')
  props.table.setGlobalFilter(value)
}, 500)

const selectedRowModels = computed(() => props.table.getSelectedRowModel().rows)

const jobOrderIds = computed(() => {
  return selectedRowModels.value.map((row) => row.original.id)
})

const onExport = () => {
  window.open(
    route('job_order.export', {
      jobOrderIds: jobOrderIds.value,
    }),
    '_blank',
  )

  props.table.resetRowSelection()
}

const isSubmitting = ref<boolean>(false)

const onBulkRestore = () => {
  router.patch(
    route('archive.job_order.bulk_restore'),
    {
      jobOrderIds: jobOrderIds.value,
    },
    {
      preserveScroll: true,
      onBefore: () => (isSubmitting.value = true),
      onSuccess: (page: any) => {
        toast.success(page.props.flash.message, {
          position: 'top-center',
        })
      },
      onFinish: () => {
        isSubmitting.value = false
        props.table.resetRowSelection()
      },
    },
  )
}
</script>

<template>
  <div class="flex flex-row items-center py-1">
    <div class="relative w-full max-w-xs items-center">
      <Input
        id="search"
        type="text"
        class="h-9 max-w-sm pl-12"
        placeholder="Search"
        :model-value="globalFilter"
        @update:model-value="onSearch"
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
          <Settings2 class="mr-2" />
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
    <Button
      @click="onExport"
      variant="ghost"
      :disabled="!selectedRowModels.length"
    >
      <Download class="mr-2" />
      Export
      <Badge
        v-if="selectedRowModels.length"
        variant="secondary"
        class="rounded-full font-extrabold"
      >
        {{ selectedRowModels.length }}
      </Badge>
    </Button>
    <div
      v-if="selectedRowModels.length"
      class="ml-auto"
    >
      <Dialog>
        <DialogTrigger as-child>
          <Button class="mx-3">
            <ArchiveRestore class="mr-2" />
            Restore
            <span class="hidden pl-3 font-extrabold lg:flex">
              {{ selectedRowModels.length }}
            </span>
          </Button>
        </DialogTrigger>
        <DialogContent class="w-fit">
          <VisuallyHidden as-child>
            <DialogTitle>Restoring Job Orders</DialogTitle>
            <DialogDescription>
              <!---->
            </DialogDescription>
          </VisuallyHidden>
          <div class="flex flex-col items-center justify-center gap-2">
            <ArchiveRestore class="h-28 w-28" />
            <div class="text-3xl font-bold">
              Restoring {{ selectedRowModels.length }} Job Order(s)
            </div>
            <div class="text-sm text-muted-foreground">
              Are you sure you want to restore the following?
            </div>
            <div class="max-h-40 w-full overflow-y-auto">
              <ul class="grid gap-2">
                <li
                  v-for="row in selectedRowModels"
                  :key="row.id"
                  class="rounded-sm bg-muted px-3 py-2 text-xs font-medium shadow-sm"
                >
                  {{ row.getValue('ticket') }}
                </li>
              </ul>
            </div>
            <div class="mt-4 flex flex-row items-center gap-4">
              <DialogClose>
                <Button
                  variant="outline"
                  class="px-10"
                >
                  Cancel
                </Button>
              </DialogClose>
              <Button
                :disabled="isSubmitting"
                @click="onBulkRestore"
                class="px-10"
              >
                <LoaderCircle
                  v-if="isSubmitting"
                  class="mr-2 animate-spin"
                />
                Confirm
              </Button>
            </div>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  </div>
</template>
