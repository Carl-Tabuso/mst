<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Camera, Mail, User } from 'lucide-vue-next'
import { computed, ref } from 'vue'

interface Employee {
  id: number
  full_name: string
  first_name: string
  middle_name: string | null
  last_name: string
  suffix: string | null
  email?: string
  avatar?: string | null
  position: { name: string } | null
}

const props = defineProps<{
  employee: Employee
}>()

const isEditingAvatar = ref(false)
const imageInput = ref<HTMLInputElement>()

const form = useForm({
  avatar: null as File | null,
})

const previewImage = ref<string | null>(null)

const displayImage = computed(() => {
  if (previewImage.value) return previewImage.value
  if (props.employee.avatar) return props.employee.avatar
  return null
})

const handleImageChange = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (file) {
    if (file.size > 2 * 1024 * 1024) {
      alert('File size must be less than 2MB')
      return
    }

    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']
    if (!allowedTypes.includes(file.type)) {
      alert('Only JPEG, PNG, JPG and GIF files are allowed')
      return
    }

    form.avatar = file
    const reader = new FileReader()
    reader.onload = (e) => {
      previewImage.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
  }
}

const triggerImageUpload = () => {
  imageInput.value?.click()
}

const submitAvatar = () => {
  if (!form.avatar) {
    return
  }

  form.transform((data) => ({
    ...data,
    _method: 'PATCH',
  }))

  form.post(route('employees.profile.update', props.employee.id), {
    forceFormData: true,
    onSuccess: (page) => {
      isEditingAvatar.value = false
      previewImage.value = null
      form.avatar = null
    },
    onFinish: () => {
      // Reset form after request
    },
  })
}

const cancelAvatarEdit = () => {
  isEditingAvatar.value = false
  previewImage.value = null
  form.reset()
  form.clearErrors()
  form.avatar = null

  // Reset file input
  if (imageInput.value) {
    imageInput.value.value = ''
  }
}
</script>

<template>
  <div class="mb-6 rounded-lg bg-white p-4 shadow-sm dark:bg-gray-900 sm:p-6">
    <h1 class="mb-6 text-xl font-bold text-sky-900 dark:text-white sm:text-2xl">
      Profile
    </h1>

    <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
      <!-- Profile Image -->
      <div class="flex justify-center lg:justify-start">
        <div class="relative">
          <div
            class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-purple-500 to-blue-500"
          >
            <img
              v-if="displayImage"
              :src="displayImage"
              alt="Profile"
              class="h-full w-full object-cover"
            />
            <User
              v-else
              class="h-10 w-10 text-white"
            />
          </div>

          <!-- Always show camera button for avatar editing -->
          <button
            type="button"
            @click="triggerImageUpload"
            class="absolute -bottom-1 -right-1 rounded-full bg-blue-600 p-1 text-white shadow-lg transition-colors hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
            :disabled="form.processing"
          >
            <Camera class="h-3 w-3" />
          </button>

          <input
            ref="imageInput"
            type="file"
            accept="image/jpeg,image/png,image/jpg,image/gif"
            @change="handleImageChange"
            class="hidden"
          />
        </div>
      </div>

      <!-- Profile Info -->
      <div class="flex-1 space-y-4">
        <!-- Name Display (Read-only) -->
        <div
          class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
          <div class="space-y-1">
            <h2
              class="text-xl font-bold text-sky-900 dark:text-white sm:text-2xl"
            >
              {{ props.employee.full_name }}
            </h2>
          </div>

          <span
            class="self-start rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 sm:self-center"
          >
            {{ props.employee.position?.name || 'No Position' }}
          </span>
        </div>

        <!-- Avatar Upload Actions (shown when file is selected) -->
        <div
          v-if="previewImage"
          class="flex flex-col gap-2 sm:flex-row"
        >
          <button
            @click="submitAvatar"
            :disabled="form.processing || !form.avatar"
            class="rounded-lg bg-sky-700 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-sky-900 dark:hover:bg-green-600"
          >
            {{ form.processing ? 'Saving...' : 'Save New Photo' }}
          </button>

          <button
            @click="cancelAvatarEdit"
            :disabled="form.processing"
            class="rounded-lg bg-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-400 disabled:opacity-50 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500"
          >
            Cancel
          </button>
        </div>

        <!-- Avatar error -->
        <div
          v-if="form.errors.avatar"
          class="flex items-center gap-2 text-sm text-red-500 dark:text-red-400"
        >
          <span>⚠</span>
          <span>{{ form.errors.avatar }}</span>
        </div>

        <!-- Email -->
        <div
          v-if="props.employee.email"
          class="flex items-center gap-2 text-gray-600 dark:text-gray-300"
        >
          <Mail class="h-4 w-4" />
          <span>{{ props.employee.email }}</span>
        </div>

        <!-- Processing indicator -->
        <div
          v-if="form.processing"
          class="flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400"
        >
          <div
            class="h-4 w-4 animate-spin rounded-full border-2 border-blue-600 border-t-transparent dark:border-blue-400"
          ></div>
          Updating profile photo...
        </div>
      </div>
    </div>
  </div>
</template>
