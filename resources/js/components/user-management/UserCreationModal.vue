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
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { userRoles, type UserRoleType } from '@/constants/user-role'
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
  role: null as UserRoleType | null,
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
  form.role = null
}

const handleClose = () => {
  resetForm()
  emit('close')
}

const getEmail = (employee: Employee): string => employee.email || 'No email'
const getFullName = (employee: Employee): string =>
  employee.fullName || 'No name'
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
          Select an employee and assign a role to create a user account.
          Credentials will be sent via email.
        </DialogDescription>
      </DialogHeader>

      <div class="grid gap-6 py-4">
        <div class="grid grid-cols-4 items-center gap-4">
          <Label
            for="employee"
            class="text-right"
            >Employee *</Label
          >
          <div class="col-span-3">
            <Select
              v-model="form.employee_id"
              @update:modelValue="
                (val) => {
                  const emp = props.employees.find(
                    (emp) => emp.id === Number(val),
                  )
                  if (emp) form.email = getEmail(emp)
                }
              "
            >
              <SelectTrigger
                id="employee"
                class="w-full"
              >
                <SelectValue placeholder="Select employee..." />
              </SelectTrigger>
              <SelectContent>
                <SelectItem
                  v-for="employee in employees"
                  :key="employee.id"
                  :value="employee.id"
                >
                  {{ getDisplayValue(employee) }} — {{ getEmail(employee) }}
                </SelectItem>
              </SelectContent>
            </Select>

            <div
              v-if="form.errors.employee_id"
              class="mt-1 text-sm text-red-500"
            >
              {{ form.errors.employee_id }}
            </div>
          </div>
        </div>

        <div class="grid grid-cols-4 items-center gap-4">
          <Label
            for="role"
            class="text-right"
            >Role *</Label
          >
          <div class="col-span-3">
            <Select v-model="form.role">
              <SelectTrigger
                id="role"
                class="w-full"
              >
                <SelectValue placeholder="Select role..." />
              </SelectTrigger>
              <SelectContent>
                <SelectItem
                  v-for="role in userRoles"
                  :key="role.id"
                  :value="role.id"
                >
                  {{ role.label }}
                </SelectItem>
              </SelectContent>
            </Select>

            <div
              v-if="form.errors.role"
              class="mt-1 text-sm text-red-500"
            >
              {{ form.errors.role }}
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
          :disabled="!form.employee_id || !form.role || form.processing"
          @click="handleSubmit"
        >
          <Mail class="mr-2 h-4 w-4" />
          Create & Send Email
        </Button>
      </div>
    </DialogContent>
  </Dialog>
</template>
