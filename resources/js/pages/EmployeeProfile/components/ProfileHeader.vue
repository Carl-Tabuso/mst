<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Camera, Edit, Mail, User } from 'lucide-vue-next'

interface Employee {
    id: number
    full_name: string
    first_name: string
    middle_name: string | null
    last_name: string
    suffix: string | null
    email?: string
    avatar_url?: string | null
    position: { name: string } | null
}

const props = defineProps<{
    employee: Employee
}>()

const isEditing = ref(false)
const imageInput = ref<HTMLInputElement>()

const form = useForm({
    first_name: props.employee.first_name || '',
    middle_name: props.employee.middle_name || '',
    last_name: props.employee.last_name || '',
    suffix: props.employee.suffix || '',
    avatar: null as File | null,
})

const previewImage = ref<string | null>(null)

const displayImage = computed(() => {
    if (previewImage.value) return previewImage.value
    if (props.employee.avatar_url) return props.employee.avatar_url
    return null
})

const fullNamePreview = computed(() => {
    if (!isEditing.value) return props.employee.full_name

    const parts = [
        form.first_name,
        form.middle_name,
        form.last_name,
        form.suffix
    ].filter(part => part && part.trim())

    return parts.join(' ')
})

const handleImageChange = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0]
    if (file) {
        // Validate file size (2MB max as per backend validation)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB')
            return
        }

        // Validate file type
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

const submit = () => {
    // Debug: Log form data before submission
    console.log('Form data being sent:', {
        first_name: form.first_name,
        middle_name: form.middle_name,
        last_name: form.last_name,
        suffix: form.suffix,
        avatar: form.avatar
    })

    // Check if required fields are filled
    if (!form.first_name?.trim() || !form.last_name?.trim()) {
        console.error('Required fields are empty:', {
            first_name: form.first_name,
            last_name: form.last_name
        })
        return
    }

    // Transform the form for submission
    form.transform((data) => ({
        ...data,
        _method: 'PATCH'
    }))

    // Always use POST with forceFormData for file uploads
    form.post(route('employees.profile.update', props.employee.id), {
        forceFormData: true,
        onBefore: () => {
            console.log('Form submission started')
        },
        onSuccess: (page) => {
            console.log('Update successful:', page)
            isEditing.value = false
            previewImage.value = null
        },
        onError: (errors) => {
            console.error('Update failed with errors:', errors)
            console.log('Current form state:', form.data())
        },
        onFinish: () => {
            console.log('Form submission finished')
        }
    })
}

const cancelEdit = () => {
    isEditing.value = false
    previewImage.value = null
    // Reset form to original values
    form.reset()
    form.clearErrors()

    // Reset to original values
    form.first_name = props.employee.first_name
    form.middle_name = props.employee.middle_name || ''
    form.last_name = props.employee.last_name
    form.suffix = props.employee.suffix || ''
    form.avatar = null
}

// Reset imageInput when canceling
const resetImageInput = () => {
    if (imageInput.value) {
        imageInput.value.value = ''
    }
}
</script>

<template>
    <div class= "dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <h1 class="text-2xl font-bold text-sky-900 dark:text-white mb-6">Profile</h1>

        <form @submit.prevent="submit" class="flex items-start gap-6">
            <!-- Profile Image -->
            <div class="relative">
                <div
                    class="w-20 h-20 rounded-full overflow-hidden bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center">
                    <img v-if="displayImage" :src="displayImage" alt="Profile" class="w-full h-full object-cover" />
                    <User v-else class="w-10 h-10 text-white" />
                </div>

                <button v-if="isEditing" type="button" @click="triggerImageUpload"
                    class="absolute -bottom-1 -right-1 bg-blue-600 dark:bg-blue-500 text-white rounded-full p-1 shadow-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                    <Camera class="w-3 h-3" />
                </button>

                <input ref="imageInput" type="file" accept="image/jpeg,image/png,image/jpg,image/gif"
                    @change="handleImageChange" class="hidden" />
            </div>

            <!-- Profile Info -->
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <div v-if="isEditing" class="flex gap-2 flex-wrap items-center">
                        <div class="flex flex-col">
                            <input v-model="form.first_name" type="text" placeholder="First Name"
                                class="text-xl font-bold border-b-2 border-blue-500 dark:border-blue-400 bg-transparent dark:bg-transparent dark:text-white dark:placeholder-gray-300 focus:outline-none min-w-[120px] px-1 py-1"
                                :class="{ 'border-red-500 dark:border-red-400': form.errors.first_name }" required />
                            <span v-if="form.errors.first_name" class="text-red-500 dark:text-red-400 text-xs mt-1">
                                {{ form.errors.first_name }}
                            </span>
                        </div>

                        <div class="flex flex-col">
                            <input v-model="form.middle_name" type="text" placeholder="Middle Name"
                                class="text-xl font-bold border-b-2 border-blue-500 dark:border-blue-400 bg-transparent dark:bg-transparent dark:text-white dark:placeholder-gray-300 focus:outline-none min-w-[120px] px-1 py-1" />
                        </div>

                        <div class="flex flex-col">
                            <input v-model="form.last_name" type="text" placeholder="Last Name"
                                class="text-xl font-bold border-b-2 border-blue-500 dark:border-blue-400 bg-transparent dark:bg-transparent dark:text-white dark:placeholder-gray-300 focus:outline-none min-w-[120px] px-1 py-1"
                                :class="{ 'border-red-500 dark:border-red-400': form.errors.last_name }" required />
                            <span v-if="form.errors.last_name" class="text-red-500 dark:text-red-400 text-xs mt-1">
                                {{ form.errors.last_name }}
                            </span>
                        </div>

                        <div class="flex flex-col">
                            <input v-model="form.suffix" type="text" placeholder="Suffix (Jr., Sr., etc.)"
                                class="text-xl font-bold border-b-2 border-blue-500 dark:border-blue-400 bg-transparent dark:bg-transparent dark:text-white dark:placeholder-gray-300 focus:outline-none w-24 px-1 py-1" />
                        </div>

                        <!-- Live preview of full name -->
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-2 w-full">
                            Preview: {{ fullNamePreview }}
                        </div>
                    </div>

                    <h2 v-else class="text-xl font-bold text-sky-900 dark:text-white">
                        {{ props.employee.full_name }}
                    </h2>

                    <span
                        class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded-full text-xs font-medium">
                        {{ props.employee.position?.name || 'No Position' }}
                    </span>
                </div>

                <!-- Avatar error display -->
                <div v-if="form.errors.avatar"
                    class="text-red-500 dark:text-red-400 text-sm mb-2 flex items-center gap-1">
                    <span class="w-4 h-4 text-red-500 dark:text-red-400">⚠</span>
                    {{ form.errors.avatar }}
                </div>

                <div class="space-y-1 text-gray-600 dark:text-gray-300">
                    <div v-if="props.employee.email" class="flex items-center gap-2">
                        <Mail class="w-4 h-4" />
                        <span>{{ props.employee.email }}</span>
                    </div>
                </div>

                <div class="mt-3 flex gap-2">
                    <button v-if="!isEditing" type="button" @click="isEditing = true"
                        class="bg-blue-900 dark:bg-blue-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-800 dark:hover:bg-blue-600 transition-colors flex items-center gap-2">
                        <Edit class="w-4 h-4" />
                        Edit Profile
                    </button>

                    <template v-else>
                        <button type="submit" :disabled="form.processing || !form.first_name || !form.last_name"
                            class="bg-blue-600 dark:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ form.processing ? 'Saving...' : 'Save Profile' }}
                        </button>

                        <button type="button" @click="cancelEdit" :disabled="form.processing"
                            class="bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors disabled:opacity-50">
                            Cancel
                        </button>
                    </template>
                </div>

                <!-- Processing indicator -->
                <div v-if="form.processing" class="mt-2">
                    <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400 text-sm">
                        <div
                            class="w-4 h-4 border-2 border-blue-600 dark:border-blue-400 border-t-transparent rounded-full animate-spin">
                        </div>
                        Updating profile...
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>