<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import {
  Card,
  CardContent,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import UserAvatar from '@/components/UserAvatar.vue'
import { useForm } from '@inertiajs/vue3'
import { Camera, Mail } from 'lucide-vue-next'
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
    onSuccess: () => {
      isEditingAvatar.value = false
      previewImage.value = null
      form.avatar = null
    },
    onFinish: () => {
      form.reset()
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
  <Card>
    <CardContent class="p-4 sm:p-6">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-center">
        <div class="flex justify-center lg:justify-start">
          <div class="relative">
            <UserAvatar
              :avatar-path="displayImage"
              :fallback="employee.full_name"
              class="flex h-32 w-32 text-5xl"
            />
            <Button
              type="button"
              size="icon"
              @click="triggerImageUpload"
              class="absolute -right-1 top-24 h-7 w-7 rounded-full"
              :disabled="form.processing"
            >
              <Camera class="h-3 w-3" />
            </Button>

            <input
              ref="imageInput"
              type="file"
              accept="image/jpeg,image/png,image/jpg,image/gif"
              @change="handleImageChange"
              class="hidden"
            />
          </div>
        </div>
        <div class="flex-1 space-y-4">
          <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between"
          >
            <div class="space-y-1">
              <CardHeader class="p-0">
                <CardTitle
                  class="flex items-center text-xl font-bold text-foreground sm:text-2xl"
                >
                  {{ props.employee.full_name }}
                </CardTitle>
                <div
                  v-if="props.employee.position?.name"
                  class="font-medium text-muted-foreground"
                >
                  {{ props.employee.position.name }}
                </div>
              </CardHeader>
            </div>
          </div>
          <CardFooter
            v-if="previewImage"
            class="flex flex-col gap-2 p-0 sm:flex-row"
          >
            <Button
              @click="submitAvatar"
              :disabled="form.processing || !form.avatar"
            >
              {{ form.processing ? 'Saving...' : 'Save New Photo' }}
            </Button>
            <Button
              variant="secondary"
              @click="cancelAvatarEdit"
              :disabled="form.processing"
            >
              Cancel
            </Button>
          </CardFooter>
          <InputError :message="form.errors.avatar" />
          <div
            v-if="props.employee.email"
            class="flex items-center gap-2 text-sm"
          >
            <Mail class="h-4 w-4" />
            <span>{{ props.employee.email }}</span>
          </div>
        </div>
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
    </CardContent>
  </Card>
</template>
