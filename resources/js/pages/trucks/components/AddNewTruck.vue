<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { usePermissions } from '@/composables/usePermissions'
import { useForm } from '@inertiajs/vue3'
import { LoaderCircle, Plus } from 'lucide-vue-next'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

const form = useForm({
  model: '',
  plate_no: '',
})

const isDialogOpen = ref<boolean>(false)

const onSubmit = () => {
  form.post(route('truck.store'), {
    onStart: () => form.clearErrors(),
    showProgress: false,
    preserveState: true,
    replace: true,
    onSuccess: (page: any) => {
      form.reset()
      isDialogOpen.value = false
      toast.success(page.props.flash.message, {
        position: 'top-center',
      })
    },
  })
}

const { hasRole } = usePermissions()

const isDispatcher = hasRole('dispatcher')
</script>

<template>
  <Dialog v-model:open="isDialogOpen">
    <DialogTrigger :disabled="!isDispatcher">
      <Button :class="{ 'cursor-not-allowed opacity-50': !isDispatcher }">
        <Plus />
        Add New Truck
      </Button>
    </DialogTrigger>
    <DialogContent @close-auto-focus="form.clearErrors()">
      <DialogHeader>
        <DialogTitle> Add New Truck </DialogTitle>
        <DialogDescription>
          Provide the model and plate number of the new truck.
        </DialogDescription>
      </DialogHeader>
      <form @submit.prevent="onSubmit">
        <div class="flex flex-col gap-4 py-5">
          <div class="grid grid-cols-4 items-start gap-4">
            <Label
              for="model"
              class="mt-4 text-left"
            >
              Model
            </Label>
            <div class="col-span-3 space-y-1">
              <Input
                id="model"
                type="text"
                required
                placeholder="Isuzu S Series SG5 Dump"
                v-model="form.model"
              />
              <InputError :message="form.errors.model" />
            </div>
          </div>
          <div class="grid grid-cols-4 items-start gap-4">
            <Label
              for="plateNumber"
              class="mt-4 text-left"
            >
              Plate Number
            </Label>
            <div class="col-span-3 space-y-1">
              <Input
                id="plateNumber"
                type="text"
                required
                placeholder="ABC-1234"
                v-model="form.plate_no"
              />
              <InputError :message="form.errors.plate_no" />
            </div>
          </div>
        </div>
        <DialogFooter>
          <Button
            type="submit"
            :disabled="form.processing"
          >
            <LoaderCircle
              v-show="form.processing"
              class="animate-spin"
            />
            Save
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
