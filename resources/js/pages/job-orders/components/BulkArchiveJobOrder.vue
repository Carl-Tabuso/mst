<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { JobOrder } from '@/types'
import { router } from '@inertiajs/vue3'
import type { Table } from '@tanstack/vue-table'
import { Archive, LoaderCircle, TriangleAlert } from 'lucide-vue-next'
import { VisuallyHidden } from 'radix-vue'
import { computed, ref } from 'vue'
import { toast } from 'vue-sonner'

interface DataTableToolbarProps {
  table: Table<JobOrder>
}

const props = defineProps<DataTableToolbarProps>()

const jobOrderIds = computed(() => {
  return props.table.getSelectedRowModel().rows.map((row) => {
    return row.original.id
  })
})

const isLoading = ref<boolean>(false)

const onBulkArchive = () => {
  router.visit(route('job_order.bulk_destroy'), {
    method: 'delete',
    data: { jobOrderIds: jobOrderIds.value },
    preserveScroll: true,
    onBefore: () => (isLoading.value = true),
    onSuccess: (page: any) => {
      isLoading.value = false
      toast.success(page.props.flash.message, {
        position: 'top-center',
      })
    },
  })
}
</script>

<template>
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
          <div class="text-3xl font-bold text-amber-500 dark:text-white">
            Archiving {{ table.getSelectedRowModel().rows.length }} Job Order(s)
          </div>
          <div class="text-sm text-muted-foreground">
            Are you sure you want to archive the following?
          </div>
        </div>
        <div class="max-h-40 w-full overflow-y-auto">
          <ul class="grid gap-2">
            <li
              v-for="row in table.getSelectedRowModel().rows"
              :key="row.id"
              class="rounded-sm bg-muted px-3 py-2 text-xs font-medium shadow-sm"
            >
              {{ row.getValue('ticket') }}
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
            @click="onBulkArchive"
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
</template>
