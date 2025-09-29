<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
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
import type {
  EmployeeWithRating,
  Paginated,
} from '@/pages/ratings/types/rating'
import { BreadcrumbItem } from '@/types'
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
  employees: Paginated<EmployeeWithRating>
  allowedPositions: string[]
}>()

const search = ref('')
let searchTimeout: ReturnType<typeof setTimeout> | null = null

const filter = ref('')
const sort = ref('')

const urlParams = new URLSearchParams(window.location.search)
search.value = urlParams.get('search') || ''
filter.value = urlParams.get('filter') || ''
sort.value = urlParams.get('sort') || ''

const filteredEmployees = computed(() => {
  return props.employees.data
})

const selectedFilters = ref<string[]>(
  urlParams.get('filter') ? urlParams.get('filter')!.split(',') : [],
)

const selectedEvaluationStatus = ref<string[]>(
  urlParams.get('evaluation_status')
    ? urlParams.get('evaluation_status')!.split(',')
    : [],
)

const ratingFrom = ref<number | null>(
  urlParams.get('rating_from') ? Number(urlParams.get('rating_from')) : null,
)

const ratingTo = ref<number | null>(
  urlParams.get('rating_to') ? Number(urlParams.get('rating_to')) : null,
)

const isExporting = ref(false)

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

function applyFilterSort(newFilter?: string, newSort?: string) {
  const url = new URL(window.location.href)

  const filterVal = newFilter !== undefined ? newFilter : filter.value
  const sortVal = newSort !== undefined ? newSort : sort.value

  if (filterVal) url.searchParams.set('filter', filterVal)
  else url.searchParams.delete('filter')
  if (sortVal) url.searchParams.set('sort', sortVal)
  else url.searchParams.delete('sort')
  url.searchParams.set('page', '1')
  url.searchParams.set('per_page', props.employees.per_page.toString())
  router.visit(url.pathname + url.search)
}

function applyAllFilters() {
  const url = new URL(window.location.href)

  if (selectedFilters.value.length > 0) {
    url.searchParams.set('filter', selectedFilters.value.join(','))
  } else {
    url.searchParams.delete('filter')
  }

  if (selectedEvaluationStatus.value.length > 0) {
    url.searchParams.set(
      'evaluation_status',
      selectedEvaluationStatus.value.join(','),
    )
  } else {
    url.searchParams.delete('evaluation_status')
  }

  if (ratingFrom.value !== null) {
    url.searchParams.set('rating_from', ratingFrom.value.toString())
  } else {
    url.searchParams.delete('rating_from')
  }

  if (ratingTo.value !== null) {
    url.searchParams.set('rating_to', ratingTo.value.toString())
  } else {
    url.searchParams.delete('rating_to')
  }

  if (sort.value) url.searchParams.set('sort', sort.value)
  else url.searchParams.delete('sort')

  if (search.value.trim()) {
    url.searchParams.set('search', search.value.trim())
  }

  url.searchParams.set('page', '1')
  url.searchParams.set('per_page', props.employees.per_page.toString())
  router.visit(url.pathname + '?' + url.searchParams.toString())
}

function clearAllFilters() {
  search.value = ''
  selectedFilters.value = []
  selectedEvaluationStatus.value = []
  ratingFrom.value = null
  ratingTo.value = null
  sort.value = ''

  const url = new URL(window.location.href)
  url.searchParams.delete('filter')
  url.searchParams.delete('evaluation_status')
  url.searchParams.delete('rating_from')
  url.searchParams.delete('rating_to')
  url.searchParams.delete('sort')
  url.searchParams.delete('search')
  url.searchParams.set('page', '1')
  url.searchParams.set('per_page', props.employees.per_page.toString())
  router.visit(url.pathname + '?' + url.searchParams.toString())
}

function exportData() {
  if (isExporting.value) return
  isExporting.value = true

  try {
    const exportUrl = new URL('/ratings/table/export', window.location.origin)
    const currentUrl = new URL(window.location.href)

    const paramsToInclude = [
      'filter',
      'evaluation_status',
      'rating_from',
      'rating_to',
      'sort',
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
  goToPage(props.employees.last_page)
}

function goToPreviousPage() {
  if (props.employees.current_page > 1) {
    goToPage(props.employees.current_page - 1)
  }
}

function goToNextPage() {
  if (props.employees.current_page < props.employees.last_page) {
    goToPage(props.employees.current_page + 1)
  }
}

const canGoPrevious = computed(() => props.employees.current_page > 1)
const canGoNext = computed(
  () => props.employees.current_page < props.employees.last_page,
)

watch(
  () => props.employees,
  () => {
    const urlParams = new URLSearchParams(window.location.search)

    search.value = urlParams.get('search') || ''

    selectedFilters.value = urlParams.get('filter')
      ? urlParams.get('filter')!.split(',')
      : []
    selectedEvaluationStatus.value = urlParams.get('evaluation_status')
      ? urlParams.get('evaluation_status')!.split(',')
      : []
    ratingFrom.value = urlParams.get('rating_from')
      ? Number(urlParams.get('rating_from'))
      : null
    ratingTo.value = urlParams.get('rating_to')
      ? Number(urlParams.get('rating_to'))
      : null
    sort.value = urlParams.get('sort') || ''
  },
)

const activeFiltersCount = computed(() => {
  let count = 0
  if (search.value.trim()) count++
  if (sort.value) count++
  if (selectedEvaluationStatus.value.length > 0) count++
  if (ratingFrom.value !== null || ratingTo.value !== null) count++
  return count
})

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Performance Evaluation',
    href: '/ratings/table',
  },
]
</script>

<template>
  <Head title="Performance Evaluation" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
      <div class="min-h-screen">
        <div class="flex flex-col">
          <h3 class="scroll-m-20 text-3xl font-bold text-primary">
            Performance Evaluation
          </h3>
          <p class="text-muted-foreground">
            Track and view ratings of all employees evaluations here.
          </p>
        </div>
        <div class="mx-auto mt-3 max-w-7xl">
          <div class="py-6">
            <div class="flex flex-col justify-between gap-3 lg:flex-row">
              <div class="flex flex-col flex-wrap gap-3 lg:flex-row">
                <div class="relative max-w-md flex-1">
                  <Search
                    class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 transform text-gray-400 dark:text-gray-500"
                  />
                  <Input
                    v-model="search"
                    placeholder="Search employees..."
                    class="h-10 bg-white pl-10 text-gray-900 placeholder-gray-500 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-400"
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
                          class="ml-1 rounded-full bg-blue-100 px-2 py-0.5 text-xs text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                        >
                          {{ activeFiltersCount }}
                        </span>
                        <ChevronDown class="ml-2 h-4 w-4" />
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                      align="end"
                      class="min-w-[280px] border-gray-200 bg-white p-0 dark:border-gray-700 dark:bg-gray-800"
                    >
                      <div class="space-y-4 p-4">
                        <div>
                          <h4
                            class="mb-3 text-sm font-medium text-gray-900 dark:text-gray-100"
                          >
                            Evaluation Status
                          </h4>
                          <div class="space-y-2">
                            <div class="flex items-center gap-3">
                              <input
                                type="checkbox"
                                id="no-ratings"
                                v-model="selectedEvaluationStatus"
                                value="no_ratings"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-blue-400"
                              />
                              <label
                                for="no-ratings"
                                class="cursor-pointer text-sm text-gray-700 dark:text-gray-300"
                              >
                                No ratings yet
                              </label>
                            </div>
                          </div>
                        </div>

                        <div
                          class="border-t border-gray-200 pt-4 dark:border-gray-700"
                        >
                          <h4
                            class="mb-3 text-sm font-medium text-gray-900 dark:text-gray-100"
                          >
                            Average Rating
                          </h4>
                          <div class="space-y-3">
                            <div>
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
                            <div>
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
                          class="flex justify-between gap-3 border-t border-gray-200 pt-4 dark:border-gray-700"
                        >
                          <Button
                            variant="outline"
                            class="h-9 flex-1 border-gray-300 bg-white text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                            @click="clearAllFilters"
                          >
                            Clear
                          </Button>
                          <Button
                            class="h-9 flex-1 bg-sky-900 text-sm text-white hover:bg-sky-800 dark:bg-sky-900 dark:hover:bg-sky-800"
                            @click="applyAllFilters"
                          >
                            Apply Filter
                          </Button>
                        </div>
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
                            sort = 'name_asc'
                            applyFilterSort(undefined, 'name_asc')
                          }
                        "
                        class="text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700"
                      >
                        Name (A - Z)
                      </DropdownMenuItem>
                      <DropdownMenuItem
                        @click="
                          () => {
                            sort = 'name_desc'
                            applyFilterSort(undefined, 'name_desc')
                          }
                        "
                        class="text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700"
                      >
                        Name (Z - A)
                      </DropdownMenuItem>
                      <DropdownMenuItem
                        @click="
                          () => {
                            sort = 'rating_desc'
                            applyFilterSort(undefined, 'rating_desc')
                          }
                        "
                        class="text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700"
                      >
                        Highest Rated
                      </DropdownMenuItem>
                      <DropdownMenuItem
                        @click="
                          () => {
                            sort = 'rating_asc'
                            applyFilterSort(undefined, 'rating_asc')
                          }
                        "
                        class="text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700"
                      >
                        Lowest Rated
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
              v-if="filteredEmployees.length === 0"
              class="py-16 text-center"
            >
              <div class="text-lg text-gray-400 dark:text-gray-500">
                No employees found
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
                      >Name</TableHead
                    >
                    <TableHead
                      class="hidden px-3 py-4 font-bold text-gray-900 dark:text-gray-100 sm:table-cell sm:px-6"
                    >
                      Position
                    </TableHead>
                    <TableHead
                      class="px-3 py-4 font-bold text-gray-900 dark:text-gray-100 sm:px-6"
                      >Average Rating
                    </TableHead>
                    <TableHead
                      class="px-3 py-4 font-bold text-gray-900 dark:text-gray-100 sm:px-6"
                      >Action</TableHead
                    >
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow
                    v-for="emp in filteredEmployees"
                    :key="emp.id"
                    class="border-b border-gray-100 bg-white transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-zinc-900 dark:hover:bg-gray-800"
                  >
                    <TableCell
                      class="px-3 py-4 text-gray-900 dark:text-gray-100 sm:px-6"
                    >
                      <div class="space-y-1">
                        <div
                          class="font-medium text-gray-900 dark:text-gray-100"
                        >
                          {{ emp.full_name }}
                        </div>
                        <div
                          class="text-sm text-gray-500 dark:text-gray-400 sm:hidden"
                        >
                          {{ emp.position }}
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
                          {{ emp.position }}
                        </div>
                      </div>
                    </TableCell>
                    <TableCell class="px-3 py-4 sm:px-6">
                      <span
                        v-if="emp.average_rating !== null"
                        class="inline-flex items-center rounded-full px-2 py-1 text-sm font-medium"
                        :class="{
                          'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200':
                            emp.average_rating >= 4,
                          'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200':
                            emp.average_rating >= 3 && emp.average_rating < 4,
                          'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200':
                            emp.average_rating < 3,
                        }"
                      >
                        {{ emp.average_rating }}
                      </span>
                      <span
                        v-else
                        class="text-sm text-gray-400 dark:text-gray-500"
                        >No ratings yet</span
                      >
                    </TableCell>
                    <TableCell
                      class="px-3 py-4 text-gray-500 dark:text-gray-400 sm:px-6"
                    >
                      <a
                        :href="`/employee-ratings/${emp.id}/history-page`"
                        class="text-sm font-medium text-zinc-500 underline hover:text-blue-800 dark:text-zinc-400 dark:hover:text-blue-400"
                      >
                        View History
                      </a>
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
                  Showing
                  {{
                    props.employees.total === 0
                      ? 0
                      : (props.employees.current_page - 1) *
                          props.employees.per_page +
                        1
                  }}
                  -
                  {{
                    Math.min(
                      props.employees.current_page * props.employees.per_page,
                      props.employees.total,
                    )
                  }}
                  of {{ props.employees.total }} employees
                </div>

                <div class="flex flex-col items-center gap-4 sm:flex-row">
                  <div class="flex items-center gap-2">
                    <p
                      class="text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                      Rows per page
                    </p>
                    <Select
                      :model-value="props.employees.per_page.toString()"
                      @update:model-value="changePerPage"
                    >
                      <SelectTrigger
                        class="h-8 w-[70px] dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                      >
                        <SelectValue
                          :placeholder="props.employees.per_page.toString()"
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
                      Page {{ props.employees.current_page }} of
                      {{ props.employees.last_page }}
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
    </MainContainer>
  </AppLayout>
</template>
