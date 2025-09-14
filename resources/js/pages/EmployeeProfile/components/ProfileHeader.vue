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

const submit = () => {

    if (!form.first_name?.trim() || !form.last_name?.trim()) {
        return
    }

    form.transform((data) => ({
        ...data,
        _method: 'PATCH'
    }))

    form.post(route('employees.profile.update', props.employee.id), {
        forceFormData: true,
        onBefore: () => {
        },
        onSuccess: (page) => {
            isEditing.value = false
            previewImage.value = null
        },
        onFinish: () => {
        }
    })
}

const cancelEdit = () => {
    isEditing.value = false
    previewImage.value = null
    form.reset()
    form.clearErrors()

    form.first_name = props.employee.first_name
    form.middle_name = props.employee.middle_name || ''
    form.last_name = props.employee.last_name
    form.suffix = props.employee.suffix || ''
    form.avatar = null
}

const resetImageInput = () => {
    if (imageInput.value) {
        imageInput.value.value = ''
    }
}
</script>

<template>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm p-4 sm:p-6 mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-sky-900 dark:text-white mb-6">Profile</h1>

        <form @submit.prevent="submit">
            <div class="flex flex-col lg:flex-row lg:items-start gap-6">
                <!-- Profile Image -->
                <div class="flex justify-center lg:justify-start">
                    <div class="relative">
                        <div
                            class="w-20 h-20 rounded-full overflow-hidden bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center">
                            <img v-if="displayImage" :src="displayImage" alt="Profile"
                                class="w-full h-full object-cover" />
                            <User v-else class="w-10 h-10 text-white" />
                        </div>

                        <button v-if="isEditing" type="button" @click="triggerImageUpload"
                            class="absolute -bottom-1 -right-1 bg-blue-600 dark:bg-blue-500 text-white rounded-full p-1 shadow-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                            <Camera class="w-3 h-3" />
                        </button>

                        <input ref="imageInput" type="file" accept="image/jpeg,image/png,image/jpg,image/gif"
                            @change="handleImageChange" class="hidden" />
                    </div>
                </div>

                <!-- Profile Info -->
                <div class="flex-1 space-y-4">
                    <!-- Name Display/Edit -->
                    <div v-if="isEditing" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <input v-model="form.first_name" type="text" placeholder="First Name"
                                    class="w-full text-xl font-bold border-b-2 border-blue-500 dark:border-blue-400 bg-transparent dark:text-white dark:placeholder-gray-300 focus:outline-none px-1 py-2"
                                    :class="{ 'border-red-500 dark:border-red-400': form.errors.first_name }"
                                    required />
                                <span v-if="form.errors.first_name"
                                    class="text-red-500 dark:text-red-400 text-xs mt-1 block">
                                    {{ form.errors.first_name }}
                                </span>
                            </div>

                            <div>
                                <input v-model="form.middle_name" type="text" placeholder="Middle Name"
                                    class="w-full text-xl font-bold border-b-2 border-blue-500 dark:border-blue-400 bg-transparent dark:text-white dark:placeholder-gray-300 focus:outline-none px-1 py-2" />
                            </div>

                            <div>
                                <input v-model="form.last_name" type="text" placeholder="Last Name"
                                    class="w-full text-xl font-bold border-b-2 border-blue-500 dark:border-blue-400 bg-transparent dark:text-white dark:placeholder-gray-300 focus:outline-none px-1 py-2"
                                    :class="{ 'border-red-500 dark:border-red-400': form.errors.last_name }" required />
                                <span v-if="form.errors.last_name"
                                    class="text-red-500 dark:text-red-400 text-xs mt-1 block">
                                    {{ form.errors.last_name }}
                                </span>
                            </div>

                            <div>
                                <input v-model="form.suffix" type="text" placeholder="Suffix (Jr., Sr., etc.)"
                                    class="w-full text-xl font-bold border-b-2 border-blue-500 dark:border-blue-400 bg-transparent dark:text-white dark:placeholder-gray-300 focus:outline-none px-1 py-2" />
                            </div>
                        </div>

                        <div
                            class="text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 rounded px-3 py-2">
                            Preview: {{ fullNamePreview }}
                        </div>
                    </div>

                    <div v-else class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <h2 class="text-xl sm:text-2xl font-bold text-sky-900 dark:text-white">
                            {{ props.employee.full_name }}
                        </h2>

                        <span
                            class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-3 py-1 rounded-full text-sm font-medium self-start sm:self-center">
                            {{ props.employee.position?.name || 'No Position' }}
                        </span>
                    </div>

                    <!-- Avatar error -->
                    <div v-if="form.errors.avatar"
                        class="text-red-500 dark:text-red-400 text-sm flex items-center gap-2">
                        <span>⚠</span>
                        <span>{{ form.errors.avatar }}</span>
                    </div>

                    <!-- Email -->
                    <div v-if="props.employee.email" class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <Mail class="w-4 h-4" />
                        <span>{{ props.employee.email }}</span>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-2">
                        <button v-if="!isEditing" type="button" @click="isEditing = true"
                            class="bg-blue-900 dark:bg-blue-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-800 dark:hover:bg-blue-600 transition-colors flex items-center justify-center gap-2">
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

                    <div v-if="form.processing"
                        class="flex items-center gap-2 text-blue-600 dark:text-blue-400 text-sm">
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