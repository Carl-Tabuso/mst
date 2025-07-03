<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
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
import axios from 'axios'
import { onMounted, ref } from 'vue'

const isOpen = ref(false)
const positions = ref([])
const isLoading = ref(false)

const form = ref({
  first_name: '',
  middle_name: '',
  last_name: '',
  suffix: '',
  position_id: '',

  email: '',
})

const errors = ref({
  first_name: '',
  last_name: '',
  position_id: '',
  email: '',
})

const fetchPositions = async () => {
  try {
    const response = await axios.get('/data/positions')
    positions.value = response.data
  } catch (error) {
    console.error('Error fetching positions:', error)
  }
}

// Form validation
const validateForm = () => {
  let valid = true
  errors.value = {
    first_name: '',
    last_name: '',
    position_id: '',
    email: '',
  }

  if (!form.value.first_name) {
    errors.value.first_name = 'First name is required'
    valid = false
  }
  if (!form.value.last_name) {
    errors.value.last_name = 'Last name is required'
    valid = false
  }
  if (!form.value.position_id) {
    errors.value.position_id = 'Position is required'
    valid = false
  }
  if (!form.value.email) {
    errors.value.email = 'Email is required'
    valid = false
  } else if (!/^\S+@\S+\.\S+$/.test(form.value.email)) {
    errors.value.email = 'Email is invalid'
    valid = false
  }

  return valid
}

const submitForm = async () => {
  if (!validateForm()) return

  isLoading.value = true
  try {
    const response = await axios.post('/employees-with-account', form.value)

    if (response.status === 200 || response.status === 201) {
      isOpen.value = false
      form.value = {
        first_name: '',
        middle_name: '',
        last_name: '',
        suffix: '',
        position_id: '',
        email: '',
      }
    }
  } catch (error) {
    if (axios.isAxiosError(error) && error.response) {
      if (error.response.status === 422 && error.response.data.errors) {
        errors.value = { ...errors.value, ...error.response.data.errors }
      }
    }
    console.error('Error creating employee with account:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(fetchPositions)
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogTrigger as-child>
      <Button variant="default"> Create New Employee with Account </Button>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>Create Employee with Account</DialogTitle>
      </DialogHeader>
      <div class="grid gap-4 py-4">
        <!-- Employee Name Fields -->
        <div class="grid grid-cols-4 items-center gap-4">
          <Label
            for="first_name"
            class="text-right"
          >
            First Name *
          </Label>
          <Input
            id="first_name"
            v-model="form.first_name"
            placeholder="First Name"
            class="col-span-3"
          />
          <small
            v-if="errors.first_name"
            class="col-span-3 col-start-2 text-sm text-red-500"
          >
            {{ errors.first_name }}
          </small>
        </div>

        <div class="grid grid-cols-4 items-center gap-4">
          <Label
            for="middle_name"
            class="text-right"
          >
            Middle Name
          </Label>
          <Input
            id="middle_name"
            v-model="form.middle_name"
            placeholder="Middle Name"
            class="col-span-3"
          />
        </div>

        <div class="grid grid-cols-4 items-center gap-4">
          <Label
            for="last_name"
            class="text-right"
          >
            Last Name *
          </Label>
          <Input
            id="last_name"
            v-model="form.last_name"
            placeholder="Last Name"
            class="col-span-3"
          />
          <small
            v-if="errors.last_name"
            class="col-span-3 col-start-2 text-sm text-red-500"
          >
            {{ errors.last_name }}
          </small>
        </div>

        <div class="grid grid-cols-4 items-center gap-4">
          <Label
            for="suffix"
            class="text-right"
          >
            Suffix
          </Label>
          <Input
            id="suffix"
            v-model="form.suffix"
            placeholder="Jr, Sr, III, etc."
            class="col-span-3"
          />
        </div>

        <!-- Position -->
        <div class="grid grid-cols-4 items-center gap-4">
          <Label
            for="position"
            class="text-right"
          >
            Position *
          </Label>
          <Select v-model="form.position_id">
            <SelectTrigger class="col-span-3">
              <SelectValue placeholder="Select Position" />
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
          <small
            v-if="errors.position_id"
            class="col-span-3 col-start-2 text-sm text-red-500"
          >
            {{ errors.position_id }}
          </small>
        </div>

        <!-- Email -->
        <div class="grid grid-cols-4 items-center gap-4">
          <Label
            for="email"
            class="text-right"
          >
            Email *
          </Label>
          <Input
            id="email"
            v-model="form.email"
            placeholder="Email Address"
            type="email"
            class="col-span-3"
          />
          <small
            v-if="errors.email"
            class="col-span-3 col-start-2 text-sm text-red-500"
          >
            {{ errors.email }}
          </small>
        </div>
      </div>
      <div class="flex justify-end gap-2">
        <Button
          variant="outline"
          @click="isOpen = false"
        >
          Cancel
        </Button>
        <Button
          type="submit"
          @click="submitForm"
          :disabled="isLoading"
        >
          <span v-if="isLoading">Creating...</span>
          <span v-else>Create Employee</span>
        </Button>
      </div>
    </DialogContent>
  </Dialog>
</template>
