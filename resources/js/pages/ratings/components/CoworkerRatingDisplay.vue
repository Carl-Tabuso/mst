<template>
  <div
    class="flex flex-col gap-2 border-b border-gray-100 bg-white p-4 last:border-b-0 dark:border-gray-700 dark:bg-zinc-900"
  >
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img
          v-if="avatar"
          :src="`/storage/${avatar}`"
          class="h-10 w-10 rounded-full object-cover"
        />
        <img
          v-else
          src="/images/default-avatar.png"
          class="h-10 w-10 rounded-full object-cover"
        />
        <div>
          <div class="flex items-center gap-2">
            <span class="font-medium text-gray-900 dark:text-gray-100">{{
              name
            }}</span>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{
              role
            }}</span>
          </div>
          <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ department }}
          </div>
        </div>
      </div>

      <div class="flex flex-col items-start gap-1">
        <div class="flex items-center gap-3">
          <div class="flex gap-1">
            <span
              v-for="n in 5"
              :key="n"
              class="text-xl"
              :class="
                n <= rating
                  ? 'text-lime-500 dark:text-lime-400'
                  : 'text-gray-300 dark:text-gray-600'
              "
            >
              ★
            </span>
          </div>
          <span
            class="min-w-[2rem] text-sm font-medium text-gray-700 dark:text-gray-300"
          >
            <span class="text-lime-500 dark:text-lime-400">{{
              rating || 0
            }}</span
            >/5
          </span>
        </div>

        <div
          v-if="description"
          class="px-1 text-sm text-gray-700 dark:text-gray-200"
        >
          {{ description }}
        </div>
        <div
          v-else
          class="px-1 text-sm italic text-gray-400 dark:text-gray-500"
        >
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
  avatar?: string
  description?: string
}>()
</script>
