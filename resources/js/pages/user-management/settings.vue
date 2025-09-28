<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Separator } from '@/components/ui/separator'
import UserRoleBadge from '@/components/UserRoleBadge.vue'
import { userRoles, type UserRoleType } from '@/constants/user-role'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem, User } from '@/types'
import { router } from '@inertiajs/vue3'
import { format } from 'date-fns'
import { computed, ref } from 'vue'

interface UserSettingsProps {
  user: User
}

const props = defineProps<UserSettingsProps>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Home',
    href: '/',
  },
  {
    title: 'User Management',
    href: '/users',
  },
  {
    title: 'Settings',
    href: `/users/${props.user.id}/settings`,
  },
]

const isEditingProfile = ref(false)
const isEditingRole = ref(false)
const showDeactivateModal = ref(false)
const showDeleteModal = ref(false)
const deactivateConfirmation = ref('')
const deleteConfirmation = ref('')

const localUser = ref({
  firstName: props.user.employee.firstName,
  lastName: props.user.employee.lastName,
  email: props.user.email,
  role: props.user.roles?.[0]?.name as UserRoleType | null,
})

const statusVariant = computed(() =>
  props.user.deletedAt ? 'destructive' : 'default',
)

const statusText = computed(() =>
  props.user.deletedAt ? 'Deactivated' : 'Active',
)

const resetForm = () => {
  localUser.value = {
    firstName: props.user.employee.firstName,
    lastName: props.user.employee.lastName,
    email: props.user.email,
    role: props.user.roles?.[0]?.name as UserRoleType | null,
  }
}

const saveProfile = async () => {
  try {
    await router.patch(route('users.update', props.user.id), {
      first_name: localUser.value.firstName,
      last_name: localUser.value.lastName,
      email: localUser.value.email,
    })
    isEditingProfile.value = false
  } catch (error) {
    console.error('Error updating profile:', error)
  }
}

const saveRole = async () => {
  try {
    await router.patch(route('users.role.update', props.user.id), {
      role: localUser.value.role,
    })
    isEditingRole.value = false
  } catch (error) {
    console.error('Error updating role:', error)
  }
}

const deactivateAccount = async () => {
  if (deactivateConfirmation.value === 'DEACTIVATE') {
    try {
      await router.delete(route('users.deactivate', props.user.id))
      showDeactivateModal.value = false
      deactivateConfirmation.value = ''
    } catch (error) {
      console.error('Error deactivating account:', error)
    }
  }
}

const deleteAccount = async () => {
  if (deleteConfirmation.value === 'DELETE') {
    try {
      await router.delete(route('users.destroy', props.user.id))
      showDeleteModal.value = false
      deleteConfirmation.value = ''
    } catch (error) {
      console.error('Error deleting account:', error)
    }
  }
}

const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A'
  try {
    return format(new Date(dateString), 'MMMM dd, yyyy')
  } catch {
    return dateString
  }
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="max-w-4xl space-y-8 p-6">
      <h1 class="text-2xl font-bold text-blue-800">User Account Settings</h1>

      <div class="space-y-6">
        <div class="space-y-4">
          <div class="grid grid-cols-5 items-center gap-2">
            <Label class="col-span-1 text-left">First Name:</Label>
            <Input
              v-if="isEditingProfile"
              v-model="localUser.firstName"
              class="col-span-4"
            />
            <p
              v-else
              class="col-span-4 text-muted-foreground"
            >
              {{ user.employee.firstName }}
            </p>
          </div>

          <div class="grid grid-cols-5 items-center gap-4">
            <Label class="col-span-1 text-left">Last Name:</Label>
            <Input
              v-if="isEditingProfile"
              v-model="localUser.lastName"
              class="col-span-4"
            />
            <p
              v-else
              class="col-span-4 text-muted-foreground"
            >
              {{ user.employee.lastName }}
            </p>
          </div>

          <div class="grid grid-cols-5 items-center gap-4">
            <Label class="col-span-1 text-left">Email:</Label>
            <Input
              v-if="isEditingProfile"
              v-model="localUser.email"
              type="email"
              class="col-span-4"
            />
            <p
              v-else
              class="col-span-4 text-muted-foreground"
            >
              {{ user.email }}
            </p>
          </div>

          <div class="grid grid-cols-5 items-center gap-4">
            <Label class="col-span-1 text-left">Created:</Label>
            <p class="col-span-4 text-muted-foreground">
              {{ formatDate(user.createdAt) }}
            </p>
          </div>

          <div class="grid grid-cols-5 items-center gap-4">
            <Label class="col-span-1 text-left">Account Status:</Label>
            <div class="col-span-4 flex items-center gap-2">
              <Badge :variant="statusVariant">
                {{ statusText }}
              </Badge>
              <Button
                v-if="!user.deletedAt"
                variant="link"
                class="col-span-1 border-destructive text-gray-500 underline hover:bg-destructive/10"
                @click="showDeactivateModal = true"
              >
                Deactivate Account
              </Button>
              <span
                v-if="statusText === 'Deactivated'"
                class="text-sm text-muted-foreground"
              >
                Deactivated Account
              </span>
            </div>
          </div>

          <div class="grid grid-cols-5 gap-4 pt-2">
            <div class="col-span-4 flex justify-start gap-2">
              <template v-if="isEditingProfile">
                <Button
                  variant="outline"
                  @click="
                    () => {
                      resetForm()
                      isEditingProfile = false
                    }
                  "
                >
                  Cancel
                </Button>
                <Button @click="saveProfile"> Save Changes </Button>
              </template>
              <Button
                v-else
                variant="outline"
                @click="isEditingProfile = true"
              >
                Edit Profile
              </Button>
            </div>
          </div>
        </div>

        <Separator />

        <div class="space-y-4">
          <div class="grid grid-cols-5 items-center gap-4">
            <Label class="col-span-1 text-left">Role:</Label>
            <div
              v-if="isEditingRole"
              class="col-span-4"
            >
              <Select v-model="localUser.role">
                <SelectTrigger class="w-full">
                  <SelectValue placeholder="Select role"> </SelectValue>
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
            </div>
            <div
              v-else
              class="col-span-4"
            >
              <UserRoleBadge
                v-if="user.roles && user.roles.length > 0"
                :role-name="user.roles[0].name"
              />
              <span
                v-else
                class="text-sm text-muted-foreground"
              >
                No role assigned
              </span>
            </div>
          </div>

          <div class="grid grid-cols-5 gap-4 pt-2">
            <div class="col-span-4 flex justify-start gap-2">
              <template v-if="isEditingRole">
                <Button
                  variant="outline"
                  @click="
                    () => {
                      resetForm()
                      isEditingRole = false
                    }
                  "
                >
                  Cancel
                </Button>
                <Button @click="saveRole"> Save Role </Button>
              </template>
              <Button
                v-else
                variant="outline"
                @click="isEditingRole = true"
              >
                Edit Role
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Dialog v-model:open="showDeactivateModal">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Deactivate Account?</DialogTitle>
          <DialogDescription>
            This will temporarily disable access. Type
            <span class="font-bold">DEACTIVATE</span> to confirm.
          </DialogDescription>
        </DialogHeader>
        <div class="py-4">
          <Input
            v-model="deactivateConfirmation"
            placeholder="Type DEACTIVATE to proceed"
            class="w-full"
          />
        </div>
        <DialogFooter>
          <Button
            variant="outline"
            @click="showDeactivateModal = false"
          >
            Cancel
          </Button>
          <Button
            variant="destructive"
            :disabled="deactivateConfirmation !== 'DEACTIVATE'"
            @click="deactivateAccount"
          >
            Confirm & Deactivate
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <Dialog v-model:open="showDeleteModal">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Delete Account?</DialogTitle>
          <DialogDescription>
            This action cannot be undone. Type
            <span class="font-bold">DELETE</span> to confirm permanent deletion.
          </DialogDescription>
        </DialogHeader>
        <div class="py-4">
          <Input
            v-model="deleteConfirmation"
            placeholder="Type DELETE to proceed"
            class="w-full"
          />
        </div>
        <DialogFooter>
          <Button
            variant="outline"
            @click="showDeleteModal = false"
          >
            Cancel
          </Button>
          <Button
            variant="destructive"
            :disabled="deleteConfirmation !== 'DELETE'"
            @click="deleteAccount"
          >
            Confirm & Delete
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>
