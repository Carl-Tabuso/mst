<template>
  <div class="flex w-full gap-3 lg:max-w-2xl">
    <div class="relative flex-1">
      <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400 dark:text-gray-500" />
      <Input v-model="searchInput" placeholder="Search by client, contact person, address..."
        class="h-11 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 pl-10 pr-4 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400" />
      <div v-if="searchInput" class="absolute right-3 top-1/2 -translate-y-1/2">
        <Button variant="ghost" size="sm"
          class="h-6 w-6 p-0 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300"
          @click="clearSearch">
          ×
        </Button>
      </div>
    </div>

    <DropdownMenuRoot>
      <DropdownMenuTrigger as-child>
        <Button variant="outline"
          class="h-11 min-w-[140px] border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 hover:bg-gray-50 dark:hover:bg-gray-700">
          <SortAsc class="mr-2 h-4 w-4" />
          {{ currentSortLabel }}
          <ChevronDown class="ml-2 h-4 w-4" />
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent
        class="w-48 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 shadow-lg dark:shadow-gray-900/20">
        <div class="px-2 py-1.5 text-xs font-semibold text-gray-500 dark:text-gray-400">SORT BY</div>
        <DropdownMenuItem v-for="option in sortOptions" :key="option.value" @click="handleSortChange(option.value)"
          class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-gray-100 px-2 py-2 rounded">
          <component :is="option.icon" class="h-4 w-4" />
          {{ option.label }}
          <div v-if="currentSort === option.value" class="ml-auto h-2 w-2 rounded-full bg-blue-600 dark:bg-blue-400">
          </div>
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenuRoot>

    <TooltipProvider>
      <TooltipRoot>
        <TooltipTrigger as-child>
          <Button variant="outline" size="icon"
            class="h-11 w-11 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
            :disabled="isLoading" @click="$emit('refresh')">
            <RefreshCw :class="['h-4 w-4 text-gray-600 dark:text-gray-400', isLoading && 'animate-spin']" />
          </Button>
        </TooltipTrigger>
        <TooltipContent class="bg-gray-900 dark:bg-gray-700 text-white dark:text-gray-200 px-2 py-1 rounded text-sm">
          Refresh data
        </TooltipContent>
      </TooltipRoot>
    </TooltipProvider>
  </div>
</template>

<script setup lang="ts">
import {
  DropdownMenuRoot,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
  TooltipRoot,
  TooltipTrigger,
  TooltipContent,
  TooltipProvider,
} from 'radix-vue'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import {
  ChevronDown,
  Search,
  SortAsc,
  RefreshCw,
  Calendar,
  User,
  Building,
} from 'lucide-vue-next'
import { ref, computed, watch } from 'vue'

interface FilterBarProps {
  search: string
  currentSort: string
  isLoading?: boolean
}

const props = withDefaults(defineProps<FilterBarProps>(), {
  isLoading: false
})

const emit = defineEmits<{
  search: [value: string]
  sort: [value: string]
  refresh: []
}>()

const searchInput = ref(props.search || '')
const currentSort = ref(props.currentSort || 'archived_at_desc')

const sortOptions = [
  { value: 'archived_at_desc', label: 'Recently Deleted', icon: Calendar },
  { value: 'archived_at_asc', label: 'Oldest Deleted', icon: Calendar },
  { value: 'client_asc', label: 'Client A→Z', icon: Building },
  { value: 'client_desc', label: 'Client Z→A', icon: Building },
  { value: 'contact_person_asc', label: 'Contact A→Z', icon: User },
  { value: 'contact_person_desc', label: 'Contact Z→A', icon: User },
]

const currentSortLabel = computed(() => {
  return sortOptions.find(option => option.value === currentSort.value)?.label || 'Sort'
})

// Debounced search
let searchTimeout: NodeJS.Timeout
const debouncedSearch = (value: string) => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    emit('search', value)
  }, 300)
}

watch(searchInput, (newValue) => {
  debouncedSearch(newValue)
})

const handleSortChange = (sortValue: string) => {
  currentSort.value = sortValue
  emit('sort', sortValue)
}

const clearSearch = () => {
  searchInput.value = ''
}

// Watch props to sync with parent
watch(() => props.search, (newValue) => {
  searchInput.value = newValue || ''
})

watch(() => props.currentSort, (newValue) => {
  currentSort.value = newValue || 'archived_at_desc'
})
</script>