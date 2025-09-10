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
import {
  Tooltip,
  TooltipContent,
  TooltipTrigger,
} from '@/components/ui/tooltip'
import { JobOrder } from '@/types'
import { router } from '@inertiajs/vue3'
import { ArchiveRestore, CircleAlert, LoaderCircle } from 'lucide-vue-next'
import { VisuallyHidden } from 'radix-vue'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

interface RestoreJobOrderProps {
  jobOrder: JobOrder
}

const props = defineProps<RestoreJobOrderProps>()

const isForceDestroyModalOpen = ref<boolean>(false)
const isSubmitting = ref<boolean>(false)

const onForceDelete = () => {
  router.delete(route('archive.job_order.destroy', props.jobOrder.id), {
    onStart: () => (isSubmitting.value = true),
    replace: true,
    showProgress: false,
    onSuccess: (page: any) => {
      toast.success(page.props.flash.message, {
        position: 'top-center',
      })
    },
    onFinish: () => {
      isSubmitting.value = false
      isForceDestroyModalOpen.value = false
    },
  })
}
</script>

<template>
  <Dialog v-model:open="isForceDestroyModalOpen">
    <Tooltip>
      <TooltipTrigger as-child>
        <DialogTrigger as-child>
          <Button
            variant="destructive"
            type="icon"
            class="rounded-full p-[10px]"
          >
            <ArchiveRestore />
          </Button>
        </DialogTrigger>
      </TooltipTrigger>
      <TooltipContent> Delete Permanently </TooltipContent>
    </Tooltip>
    <DialogContent class="w-fit">
      <VisuallyHidden as-child>
        <DialogTitle> Deleting Job Order </DialogTitle>
        <DialogDescription>
          <!---->
        </DialogDescription>
      </VisuallyHidden>
      <div class="flex flex-col items-center justify-center gap-2">
        <CircleAlert class="h-28 w-28 fill-destructive stroke-white" />
        <div class="text-3xl font-bold text-destructive">
          Deleting Job Order
        </div>
        <div class="text-sm text-muted-foreground">
          Are you sure you want to permanently delete
          <span class="font-bold">{{ jobOrder.ticket }}?</span>
        </div>
        <div class="mt-6 flex items-center gap-4">
          <DialogClose>
            <Button
              variant="outline"
              class="px-10"
            >
              Cancel
            </Button>
          </DialogClose>
          <Button
            variant="destructive"
            :disabled="isSubmitting"
            class="px-10"
            @click="onForceDelete"
          >
            <LoaderCircle
              v-if="isSubmitting"
              class="animate-spin"
            />
            Confirm
          </Button>
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>
