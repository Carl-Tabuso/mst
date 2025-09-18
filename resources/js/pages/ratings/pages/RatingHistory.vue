<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Input } from '@/components/ui/input'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import AppLayout from '@/layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'
import {
  ChevronDown,
  ChevronLeft,
  ChevronRight,
  ChevronsLeft,
  ChevronsRight,
  Download,
  Filter,
  Search,
  SortAsc,
} from 'lucide-vue-next'
import { computed, ref, watch } from 'vue'

const props = defineProps<{
  employee: { id: number; full_name: string; position: string }
  received: {
    data: Array<{
      job_order_id: number
      from: string
      from_position: string
      scale: string
      description: string
      date: string
    }>
    meta: {
      current_page: number
      last_page: number
      per_page: number
      total: number
    }
  }
  filters: {
    sort: string | null
    scale_from: number | null
    scale_to: number | null
    date_from?: string | null
    date_to?: string | null
    search?: string | null // Add search to filters prop type
  }
}>()

// Initialize search with the value from filters to persist it after navigation
const search = ref(props.filters.search || '')
const sort = ref(props.filters.sort || '')
const ratingFrom = ref<string | number>(props.filters.scale_from ?? '')
const ratingTo = ref<string | number>(props.filters.scale_to ?? '')
const dateFrom = ref<string | null>(props.filters.date_from || null)
const dateTo = ref<string | null>(props.filters.date_to || null)
const isExporting = ref(false)

let searchTimeout: ReturnType<typeof setTimeout> | null = null

const filteredReceived = computed(() => {
  return props.received.data
})

watch(search, () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applySearch()
  }, 400)
})

function applySearch() {
  const url = new URL(window.location.href)

  if (search.value.trim()) {
    url.searchParams.set('search', search.value.trim())
  } else {
    url.searchParams.delete('search')
  }

  url.searchParams.set('page', '1')
  router.visit(url.pathname + '?' + url.searchParams.toString(), {
    preserveScroll: true,
  })
}

function applyFilterSort() {
  const url = new URL(window.location.href)

  if (sort.value) url.searchParams.set('sort', sort.value)
  else url.searchParams.delete('sort')

  if (ratingFrom.value !== '')
    url.searchParams.set('scale_from', ratingFrom.value.toString())
  else url.searchParams.delete('scale_from')

  if (ratingTo.value !== '')
    url.searchParams.set('scale_to', ratingTo.value.toString())
  else url.searchParams.delete('scale_to')

  if (dateFrom.value) url.searchParams.set('date_from', dateFrom.value)
  else url.searchParams.delete('date_from')

  if (dateTo.value) url.searchParams.set('date_to', dateTo.value)
  else url.searchParams.delete('date_to')

  if (search.value.trim()) {
    url.searchParams.set('search', search.value.trim())
  }

  url.searchParams.set('page', '1')
  router.visit(url.pathname + '?' + url.searchParams.toString())
}

function clearFilters() {
  search.value = ''
  sort.value = ''
  ratingFrom.value = ''
  ratingTo.value = ''
  dateFrom.value = null
  dateTo.value = null
  applyFilterSort()
}

function exportData() {
  if (isExporting.value) return
  isExporting.value = true

  try {
    const exportUrl = new URL(
      `/employee-ratings/${props.employee.id}/history-page/export`,
      window.location.origin,
    )

    const currentUrl = new URL(window.location.href)
    const paramsToInclude = [
      'sort',
      'scale_from',
      'scale_to',
      'date_from',
      'date_to',
      'search',
    ]

    paramsToInclude.forEach((param) => {
      const value = currentUrl.searchParams.get(param)
      if (value) {
        exportUrl.searchParams.set(param, value)
      }
    })

    if (search.value.trim() && !exportUrl.searchParams.get('search')) {
      exportUrl.searchParams.set('search', search.value.trim())
    }

    window.location.href = exportUrl.toString()
  } catch (error) {
    alert('Export failed. Please try again.')
  } finally {
    setTimeout(() => {
      isExporting.value = false
    }, 2000)
  }
}

const perPageOptions = [10, 25, 50, 100]

function changePerPage(value: string) {
  const url = new URL(window.location.href)
  url.searchParams.set('per_page', value)
  url.searchParams.set('page', '1')
  router.visit(url.pathname + '?' + url.searchParams.toString())
}

function goToPage(page: number) {
  const url = new URL(window.location.href)
  url.searchParams.set('page', page.toString())
  router.visit(url.pathname + '?' + url.searchParams.toString(), {
    preserveScroll: true,
  })
}

function goToFirstPage() {
  goToPage(1)
}

function goToLastPage() {
  goToPage(props.received.meta.last_page)
}

function goToPreviousPage() {
  if (props.received.meta.current_page > 1) {
    goToPage(props.received.meta.current_page - 1)
  }
}

function goToNextPage() {
  if (props.received.meta.current_page < props.received.meta.last_page) {
    goToPage(props.received.meta.current_page + 1)
  }
}

const canGoPrevious = computed(() => props.received.meta.current_page > 1)
const canGoNext = computed(
  () => props.received.meta.current_page < props.received.meta.last_page,
)

// Update the watcher to include search from filters
watch(
  () => props.filters,
  (newFilters) => {
    sort.value = newFilters.sort || ''
    ratingFrom.value = newFilters.scale_from ?? ''
    ratingTo.value = newFilters.scale_to ?? ''
    dateFrom.value = newFilters.date_from || null
    dateTo.value = newFilters.date_to || null
    // Only update search if it's different to avoid clearing user input
    if (newFilters.search !== undefined && newFilters.search !== search.value) {
      search.value = newFilters.search || ''
    }
  },
  { immediate: true },
)

const activeFiltersCount = computed(() => {
  let count = 0
  if (search.value.trim()) count++
  if (sort.value) count++
  if (ratingFrom.value !== '' || ratingTo.value !== '') count++
  if (dateFrom.value || dateTo.value) count++
  return count
})

function getTicketDisplay(row: any): string {
  return row.job_order_ticket || `JO-${row.job_order_id}`
}
</script>

<template>
  <AppLayout>
    <div class="min-h-screen bg-white dark:bg-zinc-900">
      <div
        class="border-gray-200 bg-white px-4 pt-8 dark:border-gray-700 dark:bg-zinc-900 sm:px-6"
      >
        <div class="mx-auto max-w-7xl">
          <div class="space-y-2">
            <h1
              class="text-2xl font-semibold text-blue-900 dark:text-blue-400 sm:text-3xl lg:text-4xl"
            >
              Evaluation History
            </h1>
            <p class="text-base text-gray-500 dark:text-gray-400 sm:text-lg">
              Track and view ratings for {{ props.employee.full_name }} ({{
                props.employee.position
              }}).
            </p>
          </div>
        </div>
      </div>

      <div class="mx-auto max-w-7xl px-6 py-6">
        <div class="py-6">
          <div class="flex flex-col justify-between gap-3 lg:flex-row">
            <div class="flex flex-col flex-wrap gap-3 lg:flex-row">
              <div class="relative max-w-md flex-1">
                <Search
                  class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 transform text-gray-400 dark:text-gray-500"
                />
                <Input
                  v-model="search"
                  placeholder="Search evaluations..."
                  class="h-10 border-gray-300 bg-white pl-10 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-400 dark:focus:border-blue-400"
                />
              </div>
              <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button
                      variant="outline"
                      class="h-10 w-full border-gray-300 bg-white px-4 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 sm:w-auto"
                    >
                      <Filter class="mr-2 h-4 w-4" />
                      Filter
                      <span
                        v-if="activeFiltersCount > 0"
                        class="ml-1 rounded-full bg-sky-100 px-2 py-0.5 text-xs text-blue-800 dark:bg-sky-900 dark:text-blue-200"
                      >
                        {{ activeFiltersCount }}
                      </span>
                      <ChevronDown class="ml-2 h-4 w-4" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent
                    align="end"
                    class="w-100 max-w-[90vw] border-gray-200 bg-white p-0 dark:border-gray-700 dark:bg-gray-800"
                  >
                    <div class="p-4">
                      <h4
                        class="mb-3 text-sm font-medium text-gray-900 dark:text-gray-100"
                      >
                        Rating Range
                      </h4>
                      <div class="flex gap-4">
                        <div class="flex-1">
                          <label
                            class="mb-1 block text-xs text-gray-500 dark:text-gray-400"
                            >From</label
                          >
                          <input
                            type="number"
                            v-model="ratingFrom"
                            placeholder="Enter rating"
                            min="0"
                            max="5"
                            step="0.1"
                            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 dark:focus:border-blue-400"
                          />
                        </div>
                        <div class="flex-1">
                          <label
                            class="mb-1 block text-xs text-gray-500 dark:text-gray-400"
                            >To</label
                          >
                          <input
                            type="number"
                            v-model="ratingTo"
                            placeholder="Enter rating"
                            min="0"
                            max="5"
                            step="0.1"
                            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 dark:focus:border-blue-400"
                          />
                        </div>
                      </div>
                    </div>

                    <div
                      class="border-t border-gray-200 p-4 dark:border-gray-700"
                    >
                      <h4
                        class="mb-3 text-sm font-medium text-gray-900 dark:text-gray-100"
                      >
                        Date Range
                      </h4>
                      <div class="flex gap-4">
                        <div class="flex-1">
                          <label
                            class="mb-1 block text-xs text-gray-500 dark:text-gray-400"
                            >From</label
                          >
                          <input
                            type="date"
                            v-model="dateFrom"
                            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:focus:border-blue-400"
                          />
                        </div>
                        <div class="flex-1">
                          <label
                            class="mb-1 block text-xs text-gray-500 dark:text-gray-400"
                            >To</label
                          >
                          <input
                            type="date"
                            v-model="dateTo"
                            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:focus:border-blue-400"
                          />
                        </div>
                      </div>
                    </div>

                    <div
                      class="sticky bottom-0 flex justify-between gap-3 border-t border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800"
                    >
                      <Button
                        variant="outline"
                        class="h-9 flex-1 border-gray-300 bg-white text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                        @click="clearFilters"
                      >
                        Clear
                      </Button>
                      <Button
                        class="h-9 flex-1 bg-sky-900 text-sm text-white hover:bg-sky-800 dark:bg-sky-900 dark:hover:bg-sky-800"
                        @click="applyFilterSort"
                      >
                        Apply Filter
                      </Button>
                    </div>
                  </DropdownMenuContent>
                </DropdownMenu>

                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button
                      variant="outline"
                      class="h-10 w-full border-gray-300 bg-white px-4 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 sm:w-auto"
                    >
                      <SortAsc class="mr-2 h-4 w-4" />
                      Sort
                      <ChevronDown class="ml-2 h-4 w-4" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent
                    align="end"
                    class="border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800"
                  >
                    <DropdownMenuItem
                      @click="
                        () => {
                          sort = 'date_desc'
                          applyFilterSort()
                        }
                      "
                      class="text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                      Date (Newest)
                    </DropdownMenuItem>
                    <DropdownMenuItem
                      @click="
                        () => {
                          sort = 'date_asc'
                          applyFilterSort()
                        }
                      "
                      class="text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                      Date (Oldest)
                    </DropdownMenuItem>
                    <DropdownMenuItem
                      @click="
                        () => {
                          sort = 'scale_desc'
                          applyFilterSort()
                        }
                      "
                      class="text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                      Highest Rating
                    </DropdownMenuItem>
                    <DropdownMenuItem
                      @click="
                        () => {
                          sort = 'scale_asc'
                          applyFilterSort()
                        }
                      "
                      class="text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                      Lowest Rating
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <Button
                @click="exportData"
                :disabled="isExporting"
                class="h-10 w-full bg-sky-900 px-4 text-white hover:bg-sky-800 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-sky-900 dark:hover:bg-sky-800 sm:w-auto"
              >
                <Download class="mr-2 h-4 w-4" />
                {{ isExporting ? 'Exporting...' : 'Export' }}
              </Button>
            </div>
          </div>
        </div>

        <div class="overflow-hidden px-4 sm:px-0">
          <div
            v-if="filteredReceived.length === 0"
            class="py-16 text-center"
          >
            <div class="text-lg text-gray-400 dark:text-gray-500">
              No ratings found
            </div>
            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
              Try adjusting your search or filter criteria
            </div>
          </div>

          <div v-else>
            <Table>
              <TableHeader>
                <TableRow
                  class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800"
                >
                  <TableHead
                    class="px-3 py-4 font-bold text-gray-900 dark:text-gray-100 sm:px-6"
                    >Job Order</TableHead
                  >
                  <TableHead
                    class="px-3 py-4 font-bold text-gray-900 dark:text-gray-100 sm:px-6"
                    >Evaluator</TableHead
                  >
                  <TableHead
                    class="hidden px-3 py-4 font-bold text-gray-900 dark:text-gray-100 sm:table-cell sm:px-6"
                  >
                    Position</TableHead
                  >
                  <TableHead
                    class="px-3 py-4 font-bold text-gray-900 dark:text-gray-100 sm:px-6"
                    >Rating</TableHead
                  >
                  <TableHead
                    class="hidden px-3 py-4 font-bold text-gray-900 dark:text-gray-100 sm:px-6 lg:table-cell"
                  >
                    Remarks</TableHead
                  >
                  <TableHead
                    class="px-3 py-4 font-bold text-gray-900 dark:text-gray-100 sm:px-6"
                    >Date</TableHead
                  >
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow
                  v-for="(row, i) in filteredReceived"
                  :key="i"
                  class="border-b border-gray-100 bg-white transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-zinc-900 dark:hover:bg-gray-800"
                >
                  <TableCell
                    class="px-3 py-4 text-gray-900 dark:text-gray-100 sm:px-6"
                  >
                    <div class="font-medium">{{ getTicketDisplay(row) }}</div>
                  </TableCell>
                  <TableCell class="px-3 py-4 sm:px-6">
                    <div class="space-y-1">
                      <div class="font-medium text-gray-900 dark:text-gray-100">
                        {{ row.from }}
                      </div>
                      <div
                        class="text-sm text-gray-500 dark:text-gray-400 sm:hidden"
                      >
                        {{ row.from_position }}
                      </div>
                    </div>
                  </TableCell>
                  <TableCell
                    class="hidden px-3 py-4 text-gray-900 dark:text-gray-100 sm:table-cell sm:px-6"
                  >
                    <div class="space-y-1">
                      <div
                        class="text-sm font-medium text-gray-900 dark:text-gray-100"
                      >
                        Waste Management
                      </div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ row.from_position }}
                      </div>
                    </div>
                  </TableCell>
                  <TableCell class="px-3 py-4 sm:px-6">
                    <span
                      class="inline-flex items-center rounded-full px-2 py-1 text-sm font-medium"
                      :class="{
                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200':
                          parseFloat(row.scale) >= 4,
                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200':
                          parseFloat(row.scale) >= 3 &&
                          parseFloat(row.scale) < 4,
                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200':
                          parseFloat(row.scale) < 3,
                      }"
                    >
                      {{ row.scale }}
                    </span>
                  </TableCell>
                  <TableCell
                    class="hidden max-w-sm truncate px-3 py-4 text-gray-900 dark:text-gray-100 sm:px-6 lg:table-cell"
                    :title="row.description"
                  >
                    {{ row.description || 'No remarks' }}
                  </TableCell>
                  <TableCell
                    class="px-3 py-4 text-gray-500 dark:text-gray-400 sm:px-6"
                  >
                    <div class="text-sm">{{ row.date }}</div>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>

            <div
              class="flex flex-col items-center justify-between gap-4 border-t border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-zinc-900 sm:flex-row sm:gap-8"
            >
              <div
                class="mb-4 px-4 text-sm text-gray-600 dark:text-gray-400 sm:px-0"
              >
                Showing {{ props.received.data.length }} of
                {{ props.received.meta.total }} ratings
                <span
                  v-if="activeFiltersCount > 0"
                  class="text-blue-600 dark:text-blue-400"
                >
                  ({{ activeFiltersCount }} filters active)
                </span>
              </div>

              <div class="flex flex-col items-center gap-4 sm:flex-row">
                <div class="flex items-center gap-2">
                  <p
                    class="text-sm font-medium text-gray-700 dark:text-gray-300"
                  >
                    Rows per page
                  </p>
                  <Select
                    :model-value="props.received.meta.per_page.toString()"
                    @update:model-value="changePerPage"
                  >
                    <SelectTrigger
                      class="h-8 w-[70px] dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                    >
                      <SelectValue
                        :placeholder="props.received.meta.per_page.toString()"
                      />
                    </SelectTrigger>
                    <SelectContent
                      side="top"
                      class="dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                    >
                      <SelectItem
                        v-for="pageSize in perPageOptions"
                        :key="pageSize"
                        :value="pageSize.toString()"
                      >
                        {{ pageSize }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div class="flex items-center gap-4">
                  <div
                    class="text-sm font-medium text-gray-700 dark:text-gray-300"
                  >
                    Page {{ props.received.meta.current_page }} of
                    {{ props.received.meta.last_page }}
                  </div>
                  <div class="flex items-center gap-2">
                    <Button
                      variant="outline"
                      class="hidden h-8 w-8 p-0 dark:border-gray-700 dark:text-gray-200 sm:flex"
                      :disabled="!canGoPrevious"
                      @click="goToFirstPage"
                    >
                      <span class="sr-only">Go to first page</span>
                      <ChevronsLeft class="h-4 w-4" />
                    </Button>
                    <Button
                      variant="outline"
                      class="h-8 w-8 p-0 dark:border-gray-700 dark:text-gray-200"
                      :disabled="!canGoPrevious"
                      @click="goToPreviousPage"
                    >
                      <span class="sr-only">Go to previous page</span>
                      <ChevronLeft class="h-4 w-4" />
                    </Button>
                    <Button
                      variant="outline"
                      class="h-8 w-8 p-0 dark:border-gray-700 dark:text-gray-200"
                      :disabled="!canGoNext"
                      @click="goToNextPage"
                    >
                      <span class="sr-only">Go to next page</span>
                      <ChevronRight class="h-4 w-4" />
                    </Button>
                    <Button
                      variant="outline"
                      class="hidden h-8 w-8 p-0 dark:border-gray-700 dark:text-gray-200 sm:flex"
                      :disabled="!canGoNext"
                      @click="goToLastPage"
                    >
                      <span class="sr-only">Go to last page</span>
                      <ChevronsRight class="h-4 w-4" />
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
