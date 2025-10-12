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
import { usePermissions } from '@/composables/usePermissions'
import { Truck } from '@/types'
import { router } from '@inertiajs/vue3'
import { Archive, LoaderCircle, TriangleAlert } from 'lucide-vue-next'
import { VisuallyHidden } from 'radix-vue'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

interface ArchiveTruckProps {
  truck: Truck
}

const props = defineProps<ArchiveTruckProps>()

const isArchiveModalOpen = ref<boolean>(false)
const isLoading = ref<boolean>(false)

const onArchive = () => {
  router.delete(route('truck.destroy', props.truck.id), {
    replace: true,
    showProgress: false,
    onStart: () => (isLoading.value = true),
    onSuccess: (page: any) => {
      toast.success(page.props.flash.message, {
        position: 'top-center',
      })
    },
    onFinish: () => {
      isArchiveModalOpen.value = false
      isLoading.value = false
    },
  })
}

const canArchiveTruck = usePermissions().can('archive:truck')
</script>

<template>
  <Dialog v-model:open="isArchiveModalOpen">
    <Tooltip>
      <TooltipTrigger as-child>
        <div>
          <DialogTrigger>
            <Button
              variant="warning"
              type="icon"
              class="rounded-full p-[10px]"
              :disabled="!canArchiveTruck"
            >
              <Archive />
            </Button>
          </DialogTrigger>
        </div>
      </TooltipTrigger>
      <TooltipContent>
        {{ canArchiveTruck ? 'Archive' : 'Unauthorized' }}
      </TooltipContent>
    </Tooltip>
    <DialogContent class="w-[350px]">
      <VisuallyHidden as-child>
        <DialogTitle> Archiving Truck </DialogTitle>
        <DialogDescription>
          <!---->
        </DialogDescription>
      </VisuallyHidden>
      <div class="flex flex-col items-center justify-center gap-2">
        <TriangleAlert
          class="h-32 w-32 fill-amber-500 stroke-amber-200 dark:fill-amber-700"
        />
        <div
          class="text-2xl font-bold text-amber-500 dark:text-white sm:text-3xl"
        >
          Archiving Truck
        </div>
        <div class="space-y-2 text-center text-sm text-muted-foreground">
          <p>Are you sure you want to archive this truck?</p>
          <div class="mt-1">
            <p>
              <span class="font-medium text-foreground">Model: </span>
              <span class="font-semibold">{{ truck.model }}</span>
            </p>
            <p>
              <span class="font-medium text-foreground">Plate Number: </span>
              <span class="font-semibold">{{ truck.plateNo }}</span>
            </p>
          </div>
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
            @click="onArchive"
          >
            <LoaderCircle
              v-show="isLoading"
              class="animate-spin"
            />
            Archive
          </Button>
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>
