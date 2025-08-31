<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { router } from '@inertiajs/vue3'
import { useDebounceFn } from '@vueuse/core'
import { Download, Search } from 'lucide-vue-next'
import { useTruckTable } from '../composables/useTruckTable'

interface ToolbarProps {
  isEmpty: boolean
}

withDefaults(defineProps<ToolbarProps>(), {
  isEmpty: false,
})

const { table } = useTruckTable()

const onSearchInput = useDebounceFn(() => {
  router.get(route('truck.index'), table.value, {
    preserveState: true,
    replace: true,
  })
}, 500)

const onExport = () => {
  window.open(route('truck.export'), '__blank')
}
</script>

<template>
  <div class="flex flex-1 flex-row items-center gap-1">
    <div class="relative w-full max-w-sm items-center">
      <Input
        id="search"
        type="text"
        class="h-9 max-w-sm pl-12"
        placeholder="Search model or plate number"
        :disabled="isEmpty"
        v-model="table.search"
        @update:model-value="onSearchInput"
      />
      <span
        class="absolute inset-y-0 start-0 flex items-center justify-center px-5"
      >
        <Search class="size-4 text-muted-foreground" />
      </span>
    </div>
    <!-- <FilterDispatcher /> -->
    <Button
      variant="ghost"
      @click="onExport"
      :disabled="isEmpty"
    >
      <Download class="mr-2" />
      Export All
    </Button>
  </div>
</template>
