<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { usePermissions } from '@/composables/usePermissions'
import { Employee } from '@/types'
import { router } from '@inertiajs/vue3'
import { Edit, MoreVertical, User, UserX } from 'lucide-vue-next'

interface Props {
  employee: Employee
}

defineProps<Props>()

const { can } = usePermissions()

const isAccountDeactivated = (employee: Employee) => {
  return (
    employee.account?.deleted_at !== null &&
    employee.account?.deleted_at !== undefined
  )
}

const hasAccount = (employee: Employee) => {
  return employee.account !== null && employee.account !== undefined
}

const navigateToEdit = (employee: Employee) => {
  try {
    router.visit(route('employee-management.edit', employee.id))
  } catch (error) {
    console.error('Error navigating to edit:', error)
  }
}

const navigateToShow = (employee: Employee) => {
  try {
    router.visit(route('employee-management.show', employee.id))
  } catch (error) {
    console.error('Error navigating to show:', error)
  }
}

const archiveEmployee = (employee: Employee) => {
  if (!confirm('Are you sure you want to archive this employee?')) {
    return
  }

  try {
    router.delete(route('employee-management.destroy', employee.id), {
      onSuccess: () => {
        console.log('Employee archived successfully')
      },
      onError: (errors) => {
        console.error('Error archiving employee:', errors)
        alert('Failed to archive employee. Please try again.')
      },
    })
  } catch (error) {
    console.error('Error archiving employee:', error)
    alert('An unexpected error occurred.')
  }
}
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button
        variant="ghost"
        class="h-8 w-8 p-0"
      >
        <span class="sr-only">Open menu</span>
        <MoreVertical class="h-4 w-4" />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuItem @click="navigateToShow(employee)">
        <User class="mr-2 h-4 w-4" />
        View Details
      </DropdownMenuItem>

      <DropdownMenuItem
        v-if="can('manage:employees')"
        @click="navigateToEdit(employee)"
      >
        <Edit class="mr-2 h-4 w-4" />
        Edit
      </DropdownMenuItem>

      <DropdownMenuItem
        v-if="
          hasAccount(employee) &&
          !isAccountDeactivated(employee) &&
          can('manage:employees')
        "
        @click="archiveEmployee(employee)"
        class="text-destructive"
      >
        <UserX class="mr-2 h-4 w-4" />
        Archive Employee
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
