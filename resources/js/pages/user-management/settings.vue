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
import AppLayout from '@/layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

import type { BreadcrumbItem } from '@/types'

interface UserSettingsProps {
  user: {
    id: number
    email: string
    created_at: string
    deleted_at: string | null
    employee: {
      first_name: string
      last_name: string
      position: string
      position_id: number
    }
  }
  positions: Array<{ id: number; name: string }>
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
const showReactivateModal = ref(false)
const deactivateConfirmation = ref('')
const deleteConfirmation = ref('')
const reactivateConfirmation = ref('')

// Local state for editing
const localUser = ref({
  first_name: props.user.employee.first_name,
  last_name: props.user.employee.last_name,
  email: props.user.email,
  position_id: props.user.employee.position_id
})

const statusVariant = computed(() =>
  props.user.deleted_at ? 'destructive' : 'default',
)

const statusText = computed(() =>
  props.user.deleted_at ? 'Deactivated' : 'Active',
)

const resetForm = () => {
  localUser.value = {
    first_name: props.user.employee.first_name,
    last_name: props.user.employee.last_name,
    email: props.user.email,
    position_id: props.user.employee.position_id
  }
}

const saveProfile = async () => {
  try {
    await router.patch(route('users.update', props.user.id), {
      first_name: localUser.value.first_name,
      last_name: localUser.value.last_name,
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
      position_id: localUser.value.position_id,
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
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="max-w-4xl space-y-8 p-6">
      <h1 class="text-2xl font-bold text-blue-800">User Account Settings</h1>

      <div class="space-y-6">
        <!-- Profile Section -->
        <div class="space-y-4">
          <div class="grid grid-cols-5 items-center gap-2">
            <Label class="col-span-1 text-left">First Name:</Label>
            <Input
              v-if="isEditingProfile"
              v-model="localUser.first_name"
              class="col-span-4"
            />
            <p
              v-else
              class="col-span-4 text-muted-foreground"
            >
              {{ user.employee.first_name }}
            </p>
          </div>

          <div class="grid grid-cols-5 items-center gap-4">
            <Label class="col-span-1 text-left">Last Name:</Label>
            <Input
              v-if="isEditingProfile"
              v-model="localUser.last_name"
              class="col-span-4"
            />
            <p
              v-else
              class="col-span-4 text-muted-foreground"
            >
              {{ user.employee.last_name }}
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
              {{ user.created_at }}
            </p>
          </div>

          <div class="grid grid-cols-5 items-center gap-4">
            <Label class="col-span-1 text-left">Account Status:</Label>
            <div class="col-span-4 flex items-center gap-2">
              <Badge :variant="statusVariant">
                {{ statusText }}
              </Badge>
              <Button
                v-if="!user.deleted_at"
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

        <!-- Role and Permissions -->
        <div class="space-y-4">
          <div class="grid grid-cols-5 items-center gap-4">
            <Label class="col-span-1 text-left">Role:</Label>
            <div
              v-if="isEditingRole"
              class="col-span-4"
            >
              <Select v-model="localUser.position_id">
                <SelectTrigger class="w-full">
                  <SelectValue placeholder="Select role" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem
                    v-for="position in positions"
                    :key="position.id"
                    :value="position.id"
                  >
                    {{ position.name }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>
            <p
              v-else
              class="col-span-4 text-muted-foreground"
            >
              {{ user.employee.position }}
            </p>
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
<!-- 
        <Separator /> -->

        <!-- Delete Account -->
        <!-- <div class="space-y-6">
          <div class="grid grid-cols-5 gap-4">
            <div class="col-span-4 space-y-4">
              <h2 class="text-2xl font-bold text-red-600">Delete Account</h2>
              <p class="text-sm text-muted-foreground">
                <b>Attention! </b>You are about to delete an account. Once you
                delete this account, all data will be lost.
              </p>
              <div class="flex justify-start pt-2">
                <Button
                  variant="destructive"
                  @click="showDeleteModal = true"
                >
                  Delete Account
                </Button>
              </div>
            </div>
          </div>
        </div> -->
      </div>
    </div>

    <!-- Deactivate Account Modal -->
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

    <!-- Delete Account Modal -->
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