<template>
  <AppLayout title="Archived Items">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-1">Archived</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">All archived items data will be restored from again.</p>
      </div>

      <!-- Filter Bar -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
        <div class="p-4 space-y-4">
          <!-- Filter Bar -->
          <div class="flex items-center justify-between">
            <!-- Left side - Search and Filters -->
            <div class="flex items-center gap-3">
              <!-- Search -->
              <div class="relative">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <input v-model="searchValue" @input="debouncedSearch" type="text" placeholder="Search archived items..."
                  class="h-9 w-64 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 pl-9 pr-3 text-sm text-gray-900 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-400" />
              </div>

              <!-- Filter Dropdown -->
              <div class="relative" v-click-outside="() => showFilterDropdown = false">
                <button @click="showFilterDropdown = !showFilterDropdown"
                  class="flex items-center gap-2 h-9 px-3 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                  <Filter class="h-4 w-4" />
                  Filter
                  <ChevronDown class="h-4 w-4" />
                </button>

                <div v-if="showFilterDropdown"
                  class="absolute top-full left-0 mt-1 w-96 bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 shadow-lg z-10">
                  <div class="p-4 space-y-4">
                    <!-- Types Section -->
                    <div>
                      <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Types</div>
                      <div class="grid grid-cols-2 gap-2">
                        <label v-for="option in typeFilterOptions" :key="option.value"
                          class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 p-2 rounded cursor-pointer">
                          <input type="checkbox" :value="option.value" v-model="selectedTypes"
                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                          {{ option.label }}
                        </label>
                      </div>
                    </div>

                    <!-- Date Archived Section -->
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                      <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Date Archived</div>
                      <div class="space-y-3">
                        <!-- From Date -->
                        <div class="flex items-center gap-2">
                          <label class="text-sm text-gray-600 dark:text-gray-400 w-12">From</label>
                          <div class="relative flex-1">
                            <input type="date" v-model="dateFilters.from"
                              class="w-full h-9 px-3 pr-10 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-400" />
                            <Calendar
                              class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400 pointer-events-none" />
                          </div>
                        </div>

                        <!-- To Date -->
                        <div class="flex items-center gap-2">
                          <label class="text-sm text-gray-600 dark:text-gray-400 w-12">To</label>
                          <div class="relative flex-1">
                            <input type="date" v-model="dateFilters.to"
                              class="w-full h-9 px-3 pr-10 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-400" />
                            <Calendar
                              class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400 pointer-events-none" />
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Filter Actions -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-600">
                      <button @click="clearAllFilters"
                        class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                        Clear
                      </button>
                      <button @click="applyAllFilters"
                        class="px-4 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                        Apply Filter
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Sort Dropdown -->
              <div class="relative" v-click-outside="() => showSortDropdown = false">
                <button @click="showSortDropdown = !showSortDropdown"
                  class="flex items-center gap-2 h-9 px-3 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                  <ArrowUpDown class="h-4 w-4" />
                  Sort
                  <ChevronDown class="h-4 w-4" />
                </button>

                <div v-if="showSortDropdown"
                  class="absolute top-full left-0 mt-1 w-48 bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 shadow-lg z-10">
                  <div class="p-2 space-y-1">
                    <button v-for="option in sortOptions" :key="option.value" @click="handleSortChange(option.value)"
                      class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                      :class="{ 'bg-gray-100 dark:bg-gray-700': currentSort === option.value }">
                      {{ option.label }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right side - Actions -->
            <div class="flex items-center gap-3">
              <!-- Selected Actions -->
              <ActionBar :selected-count="selectedItems.length" :is-loading="isLoading" @restore="handleBulkRestore"
                @force-delete="handleBulkDelete" />

              <!-- Export Button -->
              <button @click="handleExport" data-export
                class="h-9 px-3 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 flex items-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed">
                <Download class="h-4 w-4" />
                Export
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full">
          <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <tr>
              <th class="w-12 px-4 py-3">
                <input type="checkbox" :checked="allSelected && archivedItems.data?.length > 0"
                  :indeterminate="selectedItems.length > 0 && !allSelected" @change="toggleSelectAll"
                  class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
              </th>
              <th
                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Record Name
              </th>
              <th
                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Archived From
              </th>
              <th
                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Archived by
              </th>
              <th
                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Date & Time Archived
              </th>
              <th
                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
            <template v-if="isLoading">
              <tr v-for="i in 5" :key="i" class="animate-pulse">
                <td class="px-4 py-3">
                  <div class="h-4 w-4 bg-gray-200 dark:bg-gray-600 rounded"></div>
                </td>
                <td class="px-4 py-3">
                  <div class="space-y-2">
                    <div class="h-4 w-32 bg-gray-200 dark:bg-gray-600 rounded"></div>
                    <div class="h-3 w-24 bg-gray-200 dark:bg-gray-600 rounded"></div>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="h-4 w-24 bg-gray-200 dark:bg-gray-600 rounded"></div>
                </td>
                <td class="px-4 py-3">
                  <div class="space-y-1">
                    <div class="h-4 w-20 bg-gray-200 dark:bg-gray-600 rounded"></div>
                    <div class="h-3 w-16 bg-gray-200 dark:bg-gray-600 rounded"></div>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="h-4 w-32 bg-gray-200 dark:bg-gray-600 rounded"></div>
                </td>
                <td class="px-4 py-3">
                  <div class="h-4 w-8 bg-gray-200 dark:bg-gray-600 rounded"></div>
                </td>
              </tr>
            </template>

            <template v-else>
              <tr v-for="item in archivedItems.data" :key="`${item.type}-${item.id}`"
                class="hover:bg-gray-50 dark:hover:bg-gray-700"
                :class="{ 'bg-blue-50 dark:bg-blue-900/20': isSelected(item.id, item.type) }">
                <td class="px-4 py-3">
                  <input type="checkbox" :checked="isSelected(item.id, item.type)"
                    @change="() => toggleSelection(item.id, item.type)"
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                </td>

                <!-- Record Name -->
                <td class="px-4 py-3">
                  <div>
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                      {{ item.display_name }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                      {{ item.name }}
                    </div>
                  </div>
                </td>

                <!-- Archived From -->
                <td class="px-4 py-3">
                  <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                    :class="getTypeBadgeClass(item.type)">
                    {{ getTypeDisplay(item.type) }}
                  </span>
                </td>

                <!-- Archived by -->
                <td class="px-4 py-3">
                  <div>
                    <div class="text-sm text-gray-900 dark:text-gray-100">
                      {{ item.archived_by_name }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                      {{ item.archived_by_position }}
                    </div>
                  </div>
                </td>

                <!-- Date & Time -->
                <td class="px-4 py-3">
                  <div class="text-sm text-gray-900 dark:text-gray-100">
                    {{ safeFormatDateOnly(item.archived_at) }}
                  </div>
                  <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ safeFormatTimeOnly(item.archived_at) }}
                  </div>
                </td>

                <!-- Actions -->
                <td class="px-4 py-3">
                  <div class="relative">
                    <button @click="toggleActionDropdown(`${item.type}-${item.id}`)"
                      class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                      <MoreHorizontal class="h-4 w-4" />
                    </button>

                    <!-- Action Dropdown -->
                    <div v-if="activeDropdown === `${item.type}-${item.id}`"
                      class="absolute right-0 top-full mt-1 w-40 bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 shadow-lg z-20">
                      <div class="p-1">
                        <button @click="showSingleRestoreModal(item.id, item.type, item.display_name)"
                          class="w-full flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                          <RotateCcw class="h-4 w-4 text-green-600 dark:text-green-400" />
                          Restore
                        </button>
                        <button @click="showSingleDeleteModal(item.id, item.type, item.display_name)"
                          class="w-full flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                          <Trash2 class="h-4 w-4 text-red-600 dark:text-red-400" />
                          Delete Forever
                        </button>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-if="!archivedItems.data?.length">
                <td colspan="6" class="px-4 py-12 text-center">
                  <div class="flex flex-col items-center gap-3">
                    <Archive class="h-12 w-12 text-gray-300 dark:text-gray-600" />
                    <div>
                      <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">No archived items</h3>
                      <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ filters.search ? 'No items match your search' : 'No items have been archived yet' }}
                      </p>
                    </div>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination :pagination="pagination" :selected-count="selectedItems.length" :filtered-count="archivedItems.total"
        @page-change="handlePageChange" @per-page-change="handlePerPageChange" />

      <!-- Single Item Confirmation Modals -->
      <ConfirmationModal :is-open="singleActionModal.show && singleActionModal.type === 'restore'" type="restore"
        :item-count="1" :is-loading="isLoading" @confirm="handleSingleRestoreConfirm" @cancel="hideSingleActionModal" />

      <ConfirmationModal :is-open="singleActionModal.show && singleActionModal.type === 'delete'" type="delete"
        :item-count="1" :is-loading="isLoading" @confirm="handleSingleDeleteConfirm" @cancel="hideSingleActionModal" />
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import {
  Search,
  Filter,
  ChevronDown,
  ArrowUpDown,
  Download,
  RotateCcw,
  Trash2,
  MoreHorizontal,
  Archive,
  Calendar
} from 'lucide-vue-next'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Pagination from '../components/Pagination.vue'
import ActionBar from '../components/ActionBar.vue'
import ConfirmationModal from '../components/ConfirmationModal.vue'
import { useArchivedItems } from '../hooks/useArchivedItems'

const {
  archivedItems,
  selectedItems,
  isSelected,
  toggleSelection,
  toggleSelectAll,
  allSelected,
  formatDateOnly,
  formatTimeOnly,
  filters,
  handleSearch,
  handleTypeFilter,
  handleRestore,
  handleForceDelete,
  handleSingleForceDelete,
  handleSingleRestore: hookHandleSingleRestore,
  pagination,
  handleSort,
  handlePerPageChange,
  isLoading,
  handleRefresh,
  getTypeDisplay,
  getTypeBadgeClass
} = useArchivedItems()

const searchValue = ref(filters.value.search || '')
const showFilterDropdown = ref(false)
const showSortDropdown = ref(false)
const currentSort = ref('archived_at_desc')
const activeDropdown = ref<string | null>(null)

// Multi-select type filter state
const selectedTypes = ref<string[]>([])

// Date filter state
const dateFilters = ref({
  from: '',
  to: ''
})

// Single action modal state
const singleActionModal = ref({
  show: false,
  type: 'restore' as 'restore' | 'delete',
  itemId: null as number | null,
  itemType: null as string | null,
  itemName: ''
})

// Filter options (removed "All Types" option since we're using checkboxes)
const typeFilterOptions = [
  { value: 'job_order', label: 'Job Orders' },
  { value: 'user', label: 'Users' },
  { value: 'employee', label: 'Employees' },
  { value: 'correction', label: 'Corrections' },
]

// Computed property for type filter display
const getSelectedTypesDisplay = () => {
  if (selectedTypes.value.length === 0) {
    return 'All Types'
  }
  if (selectedTypes.value.length === 1) {
    const option = typeFilterOptions.find(opt => opt.value === selectedTypes.value[0])
    return option?.label || 'All Types'
  }
  return `${selectedTypes.value.length} Types Selected`
}

// Sort options
const sortOptions = [
  { value: 'archived_at_desc', label: 'Recently Archived' },
  { value: 'archived_at_asc', label: 'Oldest Archived' },
  { value: 'name_asc', label: 'Name A-Z' },
  { value: 'name_desc', label: 'Name Z-A' },
  { value: 'type_asc', label: 'Type A-Z' },
  { value: 'type_desc', label: 'Type Z-A' }
]

// Safe date formatting functions
const safeFormatDateOnly = (date: string | null): string => {
  if (!date) {
    console.warn('Date is null or undefined')
    return 'No date'
  }

  try {
    const parsedDate = new Date(date)
    if (isNaN(parsedDate.getTime())) {
      console.warn('Invalid date:', date)
      return 'Invalid date'
    }

    return parsedDate.toLocaleDateString('en-US', {
      day: '2-digit',
      month: 'short',
      year: 'numeric'
    })
  } catch (error) {
    console.error('Error formatting date:', error, 'Date value:', date)
    return 'Error'
  }
}

const safeFormatTimeOnly = (date: string | null): string => {
  if (!date) return ''

  try {
    const parsedDate = new Date(date)
    if (isNaN(parsedDate.getTime())) {
      return ''
    }

    return parsedDate.toLocaleTimeString('en-US', {
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch (error) {
    console.error('Error formatting time:', error)
    return ''
  }
}

// Debounced search
let searchTimeout: ReturnType<typeof setTimeout> | null = null
const debouncedSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    handleSearch(searchValue.value)
  }, 500)
}

const applyAllFilters = () => {
  const params = {
    ...filters.value,
    page: 1,
    date_from: dateFilters.value.from || undefined,
    date_to: dateFilters.value.to || undefined
  }

  // Handle multiple type selection properly
  if (selectedTypes.value.length > 0) {
    // Send as array instead of comma-separated string
    params.type = selectedTypes.value
  } else {
    // Remove type filter if no types selected
    delete params.type
  }

  router.get(route('archives.index'), params, {
    preserveState: true,
    preserveScroll: true
  })

  showFilterDropdown.value = false
}

const clearAllFilters = () => {
  selectedTypes.value = []
  dateFilters.value.from = ''
  dateFilters.value.to = ''
}

// Other event handlers
const handleSortChange = (sortValue: string) => {
  currentSort.value = sortValue
  handleSort(sortValue)
  showSortDropdown.value = false
}

const toggleActionDropdown = (key: string) => {
  activeDropdown.value = activeDropdown.value === key ? null : key
}

// Bulk action handlers
const handleBulkRestore = async () => {
  try {
    await handleRestore()
  } catch (error) {
    console.error('Bulk restore failed:', error)
  }
}

const handleBulkDelete = async () => {
  try {
    await handleForceDelete()
  } catch (error) {
    console.error('Bulk delete failed:', error)
  }
}

// Single action modal handlers
const showSingleRestoreModal = (id: number, type: string, name: string) => {
  activeDropdown.value = null
  singleActionModal.value = {
    show: true,
    type: 'restore',
    itemId: id,
    itemType: type,
    itemName: name
  }
}

const showSingleDeleteModal = (id: number, type: string, name: string) => {
  activeDropdown.value = null
  singleActionModal.value = {
    show: true,
    type: 'delete',
    itemId: id,
    itemType: type,
    itemName: name
  }
}

const hideSingleActionModal = () => {
  singleActionModal.value.show = false
}

const handleSingleRestoreConfirm = async () => {
  if (singleActionModal.value.itemId && singleActionModal.value.itemType) {
    try {
      await hookHandleSingleRestore(singleActionModal.value.itemId, singleActionModal.value.itemType)
      hideSingleActionModal()
    } catch (error) {
      console.error('Single restore failed:', error)
    }
  }
}

const handleSingleDeleteConfirm = async () => {
  if (singleActionModal.value.itemId && singleActionModal.value.itemType) {
    try {
      await handleSingleForceDelete(singleActionModal.value.itemId, singleActionModal.value.itemType)
      hideSingleActionModal()
    } catch (error) {
      console.error('Single delete failed:', error)
    }
  }
}

const handlePageChange = (page: number) => {
  const params = {
    ...filters.value,
    page
  }

  // Preserve the multiple type selection on pagination
  if (selectedTypes.value.length > 0) {
    params.type = selectedTypes.value
  }

  router.get(route('archives.index'), params, {
    preserveState: true,
    preserveScroll: true
  })
}

const handleExport = () => {
  const exportButton = document.querySelector('button[data-export]') as HTMLButtonElement
  if (exportButton) {
    exportButton.disabled = true
    exportButton.innerHTML = `
      <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      Exporting...
    `
  }

  const exportParams = {
    search: filters.value.search || undefined,
    sort: filters.value.sort || 'archived_at_desc',
    date_from: dateFilters.value.from || undefined,
    date_to: dateFilters.value.to || undefined
  }

  if (selectedTypes.value.length > 0) {
    exportParams.type = selectedTypes.value
  }

  const url = route('archives.export')
  const queryString = new URLSearchParams()

  Object.entries(exportParams).forEach(([key, value]) => {
    if (value !== undefined) {
      if (Array.isArray(value)) {
        value.forEach(item => {
          queryString.append(`${key}[]`, item)
        })
      } else {
        queryString.append(key, value.toString())
      }
    }
  })

  const exportUrl = `${url}?${queryString.toString()}`

  const link = document.createElement('a')
  link.href = exportUrl
  link.style.display = 'none'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)

  // Reset button state after a short delay
  setTimeout(() => {
    if (exportButton) {
      exportButton.disabled = false
      exportButton.innerHTML = `
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Export
      `
    }
  }, 2000)
}

// Global click handler to close dropdowns
const handleGlobalClick = (event: Event) => {
  const target = event.target as HTMLElement
  if (!target.closest('.relative')) {
    activeDropdown.value = null
    showFilterDropdown.value = false
    showSortDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleGlobalClick)

  // Initialize selected types from current filters
  if (filters.value.type) {
    // Handle both string (comma-separated) and array formats
    if (typeof filters.value.type === 'string') {
      selectedTypes.value = filters.value.type.split(',').filter(Boolean)
    } else if (Array.isArray(filters.value.type)) {
      selectedTypes.value = filters.value.type
    }
  }

  const urlParams = new URLSearchParams(window.location.search)
  if (urlParams.get('date_from')) {
    dateFilters.value.from = urlParams.get('date_from') || ''
  }
  if (urlParams.get('date_to')) {
    dateFilters.value.to = urlParams.get('date_to') || ''
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleGlobalClick)
})

// Click outside directive
const vClickOutside = {
  beforeMount(el: any, binding: any) {
    el.clickOutsideEvent = function (event: Event) {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event, el)
      }
    }
    document.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el: any) {
    document.removeEventListener('click', el.clickOutsideEvent)
  }
}
</script>