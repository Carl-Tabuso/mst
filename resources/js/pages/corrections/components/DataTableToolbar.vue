<script setup lang="ts">
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
import { JobOrderCorrection } from '@/types'
import { router } from '@inertiajs/vue3'
import type { Table } from '@tanstack/vue-table'
import { useDebounceFn } from '@vueuse/core'
import {
  Archive,
  Download,
  LoaderCircle,
  Search,
  Settings2,
  TriangleAlert,
} from 'lucide-vue-next'
import { VisuallyHidden } from 'radix-vue'
import { computed, ref } from 'vue'
import { toast } from 'vue-sonner'
import FilterStatus from './FilterStatus.vue'

interface DataTableToolbarProps {
  table: Table<JobOrderCorrection>
  globalFilter: string | number
}

const props = defineProps<DataTableToolbarProps>()

const handleOnSearch = (value: string | number) => {
  debounceGlobalFilter(value)
}

const debounceGlobalFilter = useDebounceFn((value) => {
  props.table.setGlobalFilter(value)
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

const correctionIds = computed(() => {
  return props.table.getSelectedRowModel().rows.map((row) => {
    return row.original.id
  })
})

const handleExport = () => {
  window.open(
    route('job_order.correction.export', {
      correctionIds: correctionIds.value,
    }),
    '_blank',
  )

  props.table.resetRowSelection()
}

const isLoading = ref<boolean>(false)

const handlePageSizeArchival = () => {
  router.visit(route('job_order.correction.bulk_destroy'), {
    method: 'delete',
    data: { correctionIds: correctionIds.value },
    preserveScroll: true,
    showProgress: false,
    replace: true,
    onBefore: () => (isLoading.value = true),
    onSuccess: (page: any) => {
      isLoading.value = false
      toast.success(page.props.flash.message, {
        position: 'top-center',
      })
    },
  })
}

const { can } = usePermissions()
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

    <FilterStatus />

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
      @click="handleExport"
      variant="ghost"
      :disabled="!hasRowSelection"
    >
      <Download class="mr-2" />
      Export
      <div
        v-show="hasRowSelection"
        class="hidden space-x-1 lg:flex"
      >
        <Badge
          variant="secondary"
          class="rounded-full font-extrabold"
        >
          {{ table.getSelectedRowModel().rows.length }}
        </Badge>
      </div>
    </Button>

    <div
      v-if="hasRowSelection && can('update:job_order_correction')"
      class="ml-auto"
    >
      <Dialog>
        <DialogTrigger>
          <Button
            variant="warning"
            class="mx-3"
          >
            <Archive class="mr-2" />
            Archive
            <div class="hidden pl-3 font-extrabold lg:flex">
              {{ table.getSelectedRowModel().rows.length }}
            </div>
          </Button>
        </DialogTrigger>
        <DialogContent class="w-fit">
          <VisuallyHidden as-child>
            <DialogTitle> Archiving Multiple Job Orders </DialogTitle>
            <DialogDescription>
              <!---->
            </DialogDescription>
          </VisuallyHidden>
          <div class="flex flex-col items-center justify-center gap-2">
            <TriangleAlert
              class="h-32 w-32 fill-amber-500 stroke-amber-200 dark:fill-amber-700"
            />
            <div class="mb-4 flex flex-col">
              <div class="text-center text-3xl font-bold text-amber-500 dark:text-white">
                Archiving {{ table.getSelectedRowModel().rows.length }} Corrections
              </div>
              <div class="text-sm text-center text-muted-foreground">
                Are you sure you want to archive the following request of correction for these tickets?
              </div>
            </div>
            <div class="max-h-40 w-full overflow-y-auto">
              <ul class="grid gap-2">
                <li
                  v-for="row in table.getSelectedRowModel().rows"
                  :key="row.id"
                  class="rounded-sm bg-muted px-3 py-2 text-xs font-medium shadow-sm"
                >
                  {{ row.original.jobOrder.ticket }}
                </li>
              </ul>
            </div>
            <div class="mt-4 flex items-center gap-4">
              <DialogClose>
                <Button
                  variant="outline"
                  class="px-10"
                >
                  Cancel
                </Button>
              </DialogClose>
              <Button
                variant="warning"
                class="px-10"
                :disabled="isLoading"
                @click="handlePageSizeArchival"
              >
                <LoaderCircle
                  v-show="isLoading"
                  class="mr-2 animate-spin"
                />
                Continue
              </Button>
            </div>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  </div>
</template>
