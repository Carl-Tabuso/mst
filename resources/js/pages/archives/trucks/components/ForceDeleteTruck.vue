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
import { CircleAlert, LoaderCircle, Trash2 } from 'lucide-vue-next'
import { VisuallyHidden } from 'radix-vue'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

interface ForceDeleteTruckProps {
  truck: Truck
}

const props = defineProps<ForceDeleteTruckProps>()

const isForceDestroyModalOpen = ref<boolean>(false)
const isSubmitting = ref<boolean>(false)

const onForceDelete = () => {
  router.delete(route('archive.truck.destroy', props.truck.id), {
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

const canForceDeleteTruck = usePermissions().can('force_delete:truck')
</script>

<template>
  <Dialog v-model:open="isForceDestroyModalOpen">
    <Tooltip>
      <TooltipTrigger as-child>
        <div>
          <DialogTrigger
            as-child
            :disabled="!canForceDeleteTruck"
          >
            <Button
              variant="destructive"
              type="icon"
              class="rounded-full p-[10px]"
            >
              <Trash2 />
            </Button>
          </DialogTrigger>
        </div>
      </TooltipTrigger>
      <TooltipContent>
        {{ canForceDeleteTruck ? 'Delete Permanently' : 'Unauthorized' }}
      </TooltipContent>
    </Tooltip>
    <DialogContent class="w-[350px]">
      <VisuallyHidden as-child>
        <DialogTitle> Deleting Truck </DialogTitle>
        <DialogDescription>
          <!---->
        </DialogDescription>
      </VisuallyHidden>
      <div class="flex flex-col items-center justify-center gap-2">
        <CircleAlert class="h-28 w-28 fill-destructive stroke-white" />
        <div class="text-3xl font-bold text-destructive">Deleting Truck</div>
        <div class="space-y-2 text-center text-sm text-muted-foreground">
          <p>Are you sure you want to permanently delete this truck?</p>
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
