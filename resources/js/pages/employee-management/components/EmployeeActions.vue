<script setup lang="ts">
import { Employee } from '@/types'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { Button } from '@/components/ui/button'
import { MoreVertical, Edit, Archive, User, UserPlus, UserX } from 'lucide-vue-next' 
import { usePermissions } from '@/composables/usePermissions'
import { router } from '@inertiajs/vue3'

interface Props {
  employee: Employee
}

defineProps<Props>()

const { can } = usePermissions()

const getEditRoute = (employee: Employee) => {
  try {
    return route('employee-management.edit', employee.id)
  } catch (error) {
    console.error('Error generating edit route:', error)
    return '#'
  }
}

const getShowRoute = (employee: Employee) => {
  try {
    return route('employee-management.show', employee.id)
  } catch (error) {
    console.error('Error generating show route:', error)
    return '#'
  }
}

const isAccountDeactivated = (employee: Employee) => {
  return employee.account?.deleted_at !== null && employee.account?.deleted_at !== undefined
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
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="h-8 w-8 p-0">
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
      
      <!-- Account management actions -->
      <DropdownMenuItem 
        v-if="!hasAccount(employee) && can('create:user_account')" 
        @click="() => {}" 
      >
        <UserPlus class="mr-2 h-4 w-4" />
        Create Account
      </DropdownMenuItem>
      
      <DropdownMenuItem 
        v-if="hasAccount(employee) && !isAccountDeactivated(employee) && can('deactivate:user_account')" 
        @click="() => {}" 
        class="text-destructive"
      >
        <UserX class="mr-2 h-4 w-4" />
        Deactivate Account
      </DropdownMenuItem>
      
      <DropdownMenuItem 
        v-if="hasAccount(employee) && isAccountDeactivated(employee) && can('activate:user_account')" 
        @click="() => {}" 
      >
        <User class="mr-2 h-4 w-4" />
        Reactivate Account
      </DropdownMenuItem>
      
      <DropdownMenuItem 
        v-if="can('archive:employee')" 
        @click="() => {}" 
        class="text-destructive"
      >
        <Archive class="mr-2 h-4 w-4" />
        Archive Employee
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>