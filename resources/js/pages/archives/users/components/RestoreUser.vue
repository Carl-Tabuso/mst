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
import { User } from '@/types'
import { router } from '@inertiajs/vue3'
import { ArchiveRestore, LoaderCircle } from 'lucide-vue-next'
import { VisuallyHidden } from 'radix-vue'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

interface RestoreUserProps {
  user: User
}

const props = defineProps<RestoreUserProps>()

const isRestoreModalOpen = ref<boolean>(false)
const isSubmitting = ref<boolean>(false)

const onRestore = () => {
  router.patch(route('archive.user.update', props.user.id), undefined, {
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
      isRestoreModalOpen.value = false
    },
  })
}

const canRestoreArchivedUser = usePermissions().can('manage:users')
</script>

<template>
  <Dialog v-model:open="isRestoreModalOpen">
    <Tooltip>
      <TooltipTrigger as-child>
        <div>
          <DialogTrigger
            as-child
            :disabled="!canRestoreArchivedUser"
          >
            <Button
              type="icon"
              class="rounded-full p-[10px]"
            >
              <ArchiveRestore />
            </Button>
          </DialogTrigger>
        </div>
      </TooltipTrigger>
      <TooltipContent>
        {{ canRestoreArchivedUser ? 'Restore' : 'Unauthorized' }}
      </TooltipContent>
    </Tooltip>
    <DialogContent class="w-fit">
      <VisuallyHidden as-child>
        <DialogTitle> Restoring User </DialogTitle>
        <DialogDescription>
          <!---->
        </DialogDescription>
      </VisuallyHidden>
      <div class="flex flex-col items-center justify-center gap-2">
        <ArchiveRestore class="h-28 w-28" />
        <div class="text-3xl font-bold">Restoring User</div>
        <div class="text-sm text-muted-foreground">
          Are you sure you want to restore
          <span class="font-bold">{{ user.employee.fullName }}?</span>
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
            :disabled="isSubmitting"
            class="px-10"
            @click="onRestore"
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
