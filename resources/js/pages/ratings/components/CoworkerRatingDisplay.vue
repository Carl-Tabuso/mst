<template>
    <div class="flex flex-col gap-2 p-4 border-b border-gray-100 last:border-b-0">
        <!-- Top row: Avatar and Info -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img :src="`https://ui-avatars.com/api/?name=${name}&background=random`" :alt="name"
                    class="w-10 h-10 rounded-full object-cover" />
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-900">{{ name }}</span>
                        <span class="text-sm text-gray-500">{{ role }}</span>
                    </div>
                    <div class="text-sm text-gray-500">{{ department }}</div>
                </div>
            </div>

            <!-- Read-only Rating Block -->
            <div class="flex flex-col items-start gap-1">
                <!-- Stars + Rating -->
                <div class="flex items-center gap-3">
                    <div class="flex gap-1">
                        <span v-for="n in 5" :key="n" class="text-xl"
                            :class="n <= rating ? 'text-lime-500' : 'text-gray-300'">
                            ★
                        </span>
                    </div>
                    <span class="text-sm font-medium text-gray-700 min-w-[2rem]">
                        <span class="text-lime-500">{{ rating || 0 }}</span>/5
                    </span>
                </div>

                <!-- Feedback placed below -->
                <div v-if="description" class="text-sm text-gray-700 px-1">
                    {{ description }}
                </div>
                <div v-else class="text-sm italic text-gray-400 px-1">
                    No feedback provided.
                </div>
            </div>
        </div>

    </div>
</template>

<script setup lang="ts">
defineProps<{
    name: string
    role: string
    department: string
    rating: number
    description?: string
}>()
</script>
  
