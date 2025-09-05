<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { EloquentCollection } from '@/types'
import { Link } from '@inertiajs/vue3'

interface TruckListPaginationProps {
  meta: EloquentCollection
}

defineProps<TruckListPaginationProps>()
</script>

<template>
  <div class="flex flex-row items-center gap-2">
    <template
      v-for="(link, index) in meta.links"
      :key="index"
    >
      <Link
        :href="link.url ?? '#'"
        preserve-scroll
        preserve-state
        :class="{ 'pointer-events-none': link.active || !link.url }"
      >
        <Button
          variant="outline"
          v-html="link.label"
          :disabled="link.active || !link.url"
          size="sm"
          class="text-[13px]"
        />
      </Link>
    </template>
  </div>
</template>
