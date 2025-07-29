<template>
  <div
    class="flex flex-col gap-2 border-b border-gray-100 p-4 last:border-b-0 hover:bg-gray-50"
  >
    <!-- Top row: Avatar and Info -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img
          :src="`https://ui-avatars.com/api/?name=${name}&background=random`"
          :alt="name"
          class="h-10 w-10 rounded-full object-cover"
        />
        <div>
          <div class="flex items-center gap-2">
            <span class="font-medium text-gray-900">{{ name }}</span>
            <span class="text-sm text-gray-500">{{ role }}</span>
          </div>
          <div class="text-sm text-gray-500">{{ department }}</div>
        </div>
      </div>

      <div class="flex flex-col gap-2">
        <div class="flex items-center gap-3">
          <!-- Stars -->
          <div class="flex gap-1">
            <button
              v-for="n in 5"
              :key="n"
              @click="$emit('update:rating', n)"
              class="text-xl transition-colors hover:scale-110"
              :class="n <= rating ? 'text-lime-500' : 'text-gray-300'"
            >
              ★
            </button>
          </div>

          <!-- Rating Text -->
          <span class="min-w-[2rem] text-sm font-medium text-gray-700">
            <span class="text-lime-500">{{ rating || 0 }}</span
            >/5
          </span>
        </div>

        <!-- Description Field -->
        <textarea
          :value="description"
          @input="
            $emit(
              'update:description',
              ($event.target as HTMLTextAreaElement).value,
            )
          "
          class="w-full resize-none border-0 border-b border-gray-300 bg-transparent px-0 py-1 text-sm text-gray-700 placeholder-gray-400 focus:border-blue-500 focus:ring-0"
          placeholder="Add feedback..."
          rows="1"
        ></textarea>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  name: string
  role: string
  department: string
  rating: number | ''
  description?: string
}>()

defineEmits(['update:rating', 'update:description'])
</script>
