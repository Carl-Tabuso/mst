<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import { Employee } from '@/types'
import { useForm } from '@inertiajs/vue3'
import { Mail } from 'lucide-vue-next'

interface UserCreationModalProps {
  open: boolean
  employees: Employee[]
}

const props = defineProps<UserCreationModalProps>()
const emit = defineEmits(['close'])

const form = useForm({
  employee_id: null as number | null,
  email: '',
})

const handleSubmit = () => {
  form.post(route('users.store'), {
    preserveScroll: true,
    onSuccess: () => {
      resetForm()
      emit('close')
    },
  })
}

const resetForm = () => {
  form.reset()
}

const handleClose = () => {
  resetForm()
  emit('close')
}

// Helpers
const getEmail = (employee: Employee): string =>
  employee.email ||
  `${employee.first_name.toLowerCase()}.${employee.last_name.toLowerCase()}@example.com`

const getFullName = (employee: Employee): string =>
  `${employee.first_name} ${employee.last_name}`

const getPositionName = (employee: Employee): string =>
  employee.position?.name || 'No position'

const getDisplayValue = (employee: Employee): string =>
  `${getFullName(employee)} - ${getPositionName(employee)}`
</script>

<template>
  <Dialog
    :open="open"
    @update:open="(val) => !val && handleClose()"
  >
    <DialogContent class="sm:max-w-[600px]">
      <DialogHeader>
        <DialogTitle>Create User Account</DialogTitle>
        <DialogDescription>
          Select an employee to create a user account. Credentials will be sent
          via email.
        </DialogDescription>
      </DialogHeader>

      <div class="grid gap-6 py-4">
        <!-- Employee Select -->
        <div class="grid grid-cols-4 items-center gap-4">
          <Label
            for="employee"
            class="text-right"
            >Employee *</Label
          >
          <div class="col-span-3">
            <select
              id="employee"
              v-model="form.employee_id"
              class="w-full rounded-md border px-3 py-2"
              @change="
                (e) => {
                  const emp = props.employees.find(
                    (emp) => emp.id === Number(e.target.value),
                  )
                  if (emp) form.email = getEmail(emp)
                }
              "
            >
              <option
                disabled
                value=""
              >
                Select employee...
              </option>
              <option
                v-for="employee in employees"
                :key="employee.id"
                :value="employee.id"
              >
                {{ getDisplayValue(employee) }} — {{ getEmail(employee) }}
              </option>
            </select>
            <div
              v-if="form.errors.employee_id"
              class="mt-1 text-sm text-red-500"
            >
              {{ form.errors.employee_id }}
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-end gap-3">
        <Button
          variant="outline"
          @click="handleClose"
          >Cancel</Button
        >
        <Button
          :disabled="!form.employee_id || form.processing"
          @click="handleSubmit"
        >
          <Mail class="mr-2 h-4 w-4" />
          Create & Send Email
        </Button>
      </div>
    </DialogContent>
  </Dialog>
</template>
