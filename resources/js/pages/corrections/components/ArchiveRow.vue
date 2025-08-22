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
import { JobOrderCorrection } from '@/types'
import { router } from '@inertiajs/vue3'
import { Archive, LoaderCircle, TriangleAlert } from 'lucide-vue-next'
import { VisuallyHidden } from 'radix-vue'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

interface ArchiveRowProps {
  correction: JobOrderCorrection
}

const props = defineProps<ArchiveRowProps>()

const isArchiveModalOpen = ref<boolean>(false)
const isLoading = ref<boolean>(false)

const handleRowArchival = () => {
  router.delete(route('job_order.correction.destroy', props.correction.id), {
    replace: true,
    showProgress: false,
    onStart: () => (isLoading.value = true),
    onSuccess: (page: any) => {
      isArchiveModalOpen.value = false
      isLoading.value = false
      toast.success(page.props.flash.message, {
        position: 'top-center',
      })
    },
  })
}
</script>

<template>
  <Dialog v-model:open="isArchiveModalOpen">
    <DialogTrigger>
      <Button
        variant="warning"
        type="icon"
        class="rounded-full p-2"
      >
        <Archive />
      </Button>
    </DialogTrigger>
    <DialogContent class="max-w-md">
      <VisuallyHidden as-child>
        <DialogTitle> Archiving Job Order </DialogTitle>
        <DialogDescription>
          <!---->
        </DialogDescription>
      </VisuallyHidden>
      <div class="flex flex-col items-center justify-center gap-2">
        <TriangleAlert
          class="h- h-32 w-32 fill-amber-500 stroke-amber-200 dark:fill-amber-700"
        />
        <div
          class="text-center text-2xl font-bold text-amber-500 dark:text-white"
        >
          Archiving Correction Request
        </div>
        <div class="px-5 text-center text-sm text-muted-foreground">
          Are you sure you want to archive correction request for
          <span class="font-bold">{{ correction.jobOrder.ticket }}</span>
          ?
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
            variant="warning"
            :disabled="isLoading"
            class="px-10"
            @click="handleRowArchival"
          >
            <LoaderCircle
              v-show="isLoading"
              class="mr-2 animate-spin"
            />
            Archive
          </Button>
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>
