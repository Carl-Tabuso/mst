<template>
  <div
    class="flex flex-col gap-2 border-b border-gray-100 bg-white p-4 last:border-b-0 hover:bg-gray-50 dark:border-gray-700 dark:bg-zinc-900 dark:hover:bg-gray-800"
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

      <div class="flex flex-col gap-2">
        <div class="flex items-center gap-3">
          <!-- Stars -->
          <div class="flex gap-1">
            <button
              v-for="n in 5"
              :key="n"
              @click="$emit('update:rating', n)"
              class="text-xl transition-colors hover:scale-110"
              :class="
                n <= Number(rating)
                  ? 'text-lime-500 dark:text-lime-400'
                  : 'text-gray-300 dark:text-gray-600'
              "
            >
              ★
            </button>
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

        <textarea
          :value="description"
          @input="
            $emit(
              'update:description',
              ($event.target as HTMLTextAreaElement).value,
            )
          "
          class="w-full resize-none border-0 border-b border-gray-300 bg-transparent px-0 py-2 text-sm text-gray-700 placeholder-gray-400 focus:border-blue-500 focus:ring-0 dark:border-gray-600 dark:text-gray-200 dark:placeholder-gray-500 dark:focus:border-blue-400 sm:text-base lg:text-sm xl:text-base"
          placeholder="Add feedback..."
          rows="1"
        />
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
  avatar?: string
  description?: string
}>()

defineEmits(['update:rating', 'update:description'])
</script>
