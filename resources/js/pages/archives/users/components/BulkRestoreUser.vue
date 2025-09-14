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
import { User } from '@/types'
import { router } from '@inertiajs/vue3'
import type { Table } from '@tanstack/vue-table'
import { ArchiveRestore, LoaderCircle } from 'lucide-vue-next'
import { VisuallyHidden } from 'radix-vue'
import { computed, ref } from 'vue'
import { toast } from 'vue-sonner'

interface BulkRestoreUserProps {
  table: Table<User>
}

const props = defineProps<BulkRestoreUserProps>()

const selectedRowModels = computed(() => props.table.getSelectedRowModel().rows)

const isSubmitting = ref<boolean>(false)

const onBulkRestore = () => {
  router.patch(
    route('archive.user.bulk_restore'),
    {
      userIds: selectedRowModels.value.map((row) => row.original.id),
    },
    {
      preserveScroll: true,
      showProgress: false,
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
        <DialogTitle>Restoring Users</DialogTitle>
        <DialogDescription>
          <!---->
        </DialogDescription>
      </VisuallyHidden>
      <div class="flex flex-col items-center justify-center gap-2">
        <ArchiveRestore class="h-28 w-28" />
        <div class="text-3xl font-bold">
          Restoring {{ selectedRowModels.length }} User(s)
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
              {{ row.original.email }}
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
</template>
