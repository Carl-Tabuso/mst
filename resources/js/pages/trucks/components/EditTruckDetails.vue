<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Alert, AlertDescription } from '@/components/ui/alert'
import { Button } from '@/components/ui/button'
import {
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { usePermissions } from '@/composables/usePermissions'
import { Truck } from '@/types'
import { useForm } from '@inertiajs/vue3'
import { LoaderCircle, TriangleAlert } from 'lucide-vue-next'
import { toast } from 'vue-sonner'

interface TruckDetailsProps {
  truck: Truck
  additionalInfo: string
}

interface TruckDetailsEmits {
  (e: 'onTruckDetailsUpdate'): void
}

const props = defineProps<TruckDetailsProps>()

const emit = defineEmits<TruckDetailsEmits>()

const form = useForm({
  model: props.truck.model,
  plate_no: props.truck.plateNo,
})

const onSubmit = () => {
  form.patch(route('truck.update', props.truck.id), {
    onStart: () => form.clearErrors(),
    preserveState: true,
    replace: true,
    onSuccess: (page: any) => {
      emit('onTruckDetailsUpdate')
      form.reset()
      toast.success(page.props.flash.message, {
        position: 'top-center',
      })
    },
  })
}

const { hasRole } = usePermissions()
</script>

<template>
  <DialogHeader>
    <DialogTitle> Truck Details </DialogTitle>
    <DialogDescription>
      You can change this truck information as needed.
    </DialogDescription>
  </DialogHeader>
  <form @submit.prevent="onSubmit">
    <div class="flex flex-col gap-4 py-4">
      <div class="grid grid-cols-4 items-start gap-4">
        <Label
          for="model"
          class="mt-4"
        >
          Model
        </Label>
        <div class="col-span-3 space-y-1">
          <Input
            id="model"
            type="text"
            required
            v-model="form.model"
          />
          <InputError :message="form.errors.model" />
        </div>
      </div>
      <div class="grid grid-cols-4 items-start gap-4">
        <Label
          for="plateNumber"
          class="mt-4"
        >
          Plate Number
        </Label>
        <div class="col-span-3 space-y-1">
          <Input
            id="plateNumber"
            type="text"
            required
            v-model="form.plate_no"
          />
          <InputError :message="form.errors.plate_no" />
        </div>
      </div>
      <Alert
        variant="warning"
        class="mt-4 py-2"
      >
        <div class="flex items-center gap-3">
          <TriangleAlert class="h-4 w-4" />
          <AlertDescription>
            Changes will cascade through every record.
          </AlertDescription>
        </div>
      </Alert>
    </div>
    <div class="flex justify-between">
      <div class="mt-2 flex items-center text-xs text-muted-foreground">
        {{ additionalInfo }}
      </div>
      <DialogFooter>
        <Button
          type="submit"
          :disabled="form.processing || !hasRole('dispatcher')"
        >
          <LoaderCircle
            v-show="form.processing"
            class="animate-spin"
          />
          Save Changes
        </Button>
      </DialogFooter>
    </div>
  </form>
</template>
