<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Download, Search } from 'lucide-vue-next'
import { useTruckTable } from '../composables/useTruckTable'
import FilterDispatcher from './FilterDispatcher.vue'

interface ToolbarProps {
  isEmpty: boolean
}

withDefaults(defineProps<ToolbarProps>(), {
  isEmpty: false,
})

const { table, onSearchInput } = useTruckTable()

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
        v-model="table.search"
        @update:model-value="onSearchInput"
      />
      <span
        class="absolute inset-y-0 start-0 flex items-center justify-center px-5"
      >
        <Search class="size-4 text-muted-foreground" />
      </span>
    </div>
    <FilterDispatcher />
    <Button
      variant="ghost"
      @click="onExport"
    >
      <Download class="mr-2" />
      Export All
    </Button>
  </div>
</template>
