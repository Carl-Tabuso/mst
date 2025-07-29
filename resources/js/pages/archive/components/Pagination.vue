<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps<{ pagination: any }>()
function goTo(page: number) {
  router.get(
    route('archives.index'),
    { page },
    { preserveState: true, preserveScroll: true },
  )
}

const paginationPages = computed(() => {
  const pages: (number | string)[] = []
  const total = props.pagination.last_page
  const current = props.pagination.current_page

  for (let i = 1; i <= total; i++) {
    if (i === 1 || i === total || Math.abs(i - current) <= 1) {
      pages.push(i)
    } else if (
      (i === current - 2 && i !== 1) ||
      (i === current + 2 && i !== total)
    ) {
      pages.push('...')
    }
  }

  return [...new Set(pages)]
})
</script>

<template>
  <div
    class="mt-6 flex flex-col gap-4 px-4 sm:flex-row sm:items-center sm:justify-between"
  >
    <div class="text-sm text-gray-700">
      <span class="font-medium">
        {{
          props.pagination.total === 0
            ? 0
            : (props.pagination.current_page - 1) * props.pagination.per_page +
              1
        }}
      </span>
      –
      <span class="font-medium">
        {{
          Math.min(
            props.pagination.current_page * props.pagination.per_page,
            props.pagination.total,
          )
        }}
      </span>
      of <span class="font-medium">{{ props.pagination.total }}</span> results
    </div>

    <div class="flex items-center gap-2">
      <button
        class="rounded border px-3 py-1 text-sm hover:bg-gray-100 disabled:opacity-50"
        :disabled="props.pagination.current_page <= 1"
        @click="goTo(props.pagination.current_page - 1)"
      >
        Previous
      </button>

      <template
        v-for="page in paginationPages"
        :key="page"
      >
        <span
          v-if="page === '...'"
          class="px-2 text-gray-400"
          >…</span
        >
        <button
          v-else
          class="rounded px-3 py-1 text-sm font-medium"
          :class="{
            'bg-blue-600 text-white': page === props.pagination.current_page,
            'text-gray-700 hover:bg-gray-100':
              page !== props.pagination.current_page,
          }"
          @click="goTo(page as number)"
          :disabled="page === props.pagination.current_page"
        >
          {{ page }}
        </button>
      </template>

      <button
        class="rounded border px-3 py-1 text-sm hover:bg-gray-100 disabled:opacity-50"
        :disabled="props.pagination.current_page >= props.pagination.last_page"
        @click="goTo(props.pagination.current_page + 1)"
      >
        Next
      </button>
    </div>
  </div>
</template>
