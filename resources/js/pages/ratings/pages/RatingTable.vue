<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import { ChevronDown, Search, Filter, SortAsc, Archive, Download, TriangleAlert } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import type { EmployeeWithRating, Paginated } from '@/pages/ratings/types/rating';

const props = defineProps<{
  employees: Paginated<EmployeeWithRating>;
  allowedPositions: string[];
}>();

const search = ref('');
let searchTimeout: ReturnType<typeof setTimeout> | null = null;

const filter = ref('');
const sort = ref('');

const urlParams = new URLSearchParams(window.location.search);
filter.value = urlParams.get('filter') || '';
sort.value = urlParams.get('sort') || '';

const filteredEmployees = computed(() => {
  return props.employees.data.filter(emp =>
    emp.full_name.toLowerCase().includes(search.value.toLowerCase()) ||
    emp.position.toLowerCase().includes(search.value.toLowerCase())
  );
});

function goToPage(url: string | null) {
  if (!url) return;
  const currentPerPage = props.employees.per_page;
  const urlObj = new URL(url, window.location.origin);
  urlObj.searchParams.set('per_page', currentPerPage);
  router.visit(urlObj.pathname + urlObj.search);
}

function goToPagePageNumber(page: number) {
  const url = new URL(window.location.href);
  url.searchParams.set('page', String(page));
  url.searchParams.set('per_page', props.employees.per_page);
  router.visit(url.pathname + url.search);
}

const paginationPages = computed(() => {
  const total = props.employees.last_page;
  const current = props.employees.current_page;
  const pages: (number | string)[] = [];

  if (total <= 7) {
    for (let i = 1; i <= total; i++) pages.push(i);
  } else {
    pages.push(1, 2, 3);

    if (current > 5) {
      pages.push('...');
    }

    if (current > 3 && current < total - 2) {
      pages.push(current);
    }

    if (current < total - 4) {
      pages.push('...');
    }

    pages.push(total - 2, total - 1, total);
  }

  return [...new Set(pages.filter(p => typeof p === 'string' || (p >= 1 && p <= total)))];
});

function changePerPage(e: Event) {
  const selected = (e.target as HTMLSelectElement).value;
  const url = new URL(window.location.href);

  url.searchParams.set('per_page', selected);
  url.searchParams.set('page', '1');

  router.visit(url.pathname + '?' + url.searchParams.toString(), {
    preserveScroll: true,
  });
}

function applyFilterSort(newFilter?: string, newSort?: string) {
  const url = new URL(window.location.href);

  const filterVal = newFilter !== undefined ? newFilter : filter.value;
  const sortVal = newSort !== undefined ? newSort : sort.value;

  if (filterVal) url.searchParams.set('filter', filterVal);
  else url.searchParams.delete('filter');
  if (sortVal) url.searchParams.set('sort', sortVal);
  else url.searchParams.delete('sort');
  url.searchParams.set('page', '1');
  url.searchParams.set('per_page', props.employees.per_page);
  router.visit(url.pathname + url.search);
}

const selectedFilters = ref<string[]>(
  urlParams.get('filter')
    ? urlParams.get('filter')!.split(',')
    : []
);

const selectedEvaluationStatus = ref<string[]>(
  urlParams.get('evaluation_status')
    ? urlParams.get('evaluation_status')!.split(',')
    : []
);

const ratingFrom = ref<number | null>(
  urlParams.get('rating_from') ? Number(urlParams.get('rating_from')) : null
);

const ratingTo = ref<number | null>(
  urlParams.get('rating_to') ? Number(urlParams.get('rating_to')) : null
);

function applyMultiFilter() {
  const url = new URL(window.location.href);
  if (selectedFilters.value.length > 0) {
    url.searchParams.set('filter', selectedFilters.value.join(','));
  } else {
    url.searchParams.delete('filter');
  }
  if (sort.value) url.searchParams.set('sort', sort.value);
  url.searchParams.set('page', '1');
  url.searchParams.set('per_page', props.employees.per_page);
  router.visit(url.pathname + url.search);
}

function applyAllFilters() {
  const url = new URL(window.location.href);

  // Position filters
  if (selectedFilters.value.length > 0) {
    url.searchParams.set('filter', selectedFilters.value.join(','));
  } else {
    url.searchParams.delete('filter');
  }

  // Evaluation status filters
  if (selectedEvaluationStatus.value.length > 0) {
    url.searchParams.set('evaluation_status', selectedEvaluationStatus.value.join(','));
  } else {
    url.searchParams.delete('evaluation_status');
  }

  // Rating range filters
  if (ratingFrom.value !== null) {
    url.searchParams.set('rating_from', ratingFrom.value.toString());
  } else {
    url.searchParams.delete('rating_from');
  }

  if (ratingTo.value !== null) {
    url.searchParams.set('rating_to', ratingTo.value.toString());
  } else {
    url.searchParams.delete('rating_to');
  }

  if (sort.value) url.searchParams.set('sort', sort.value);
  url.searchParams.set('page', '1');
  url.searchParams.set('per_page', props.employees.per_page);
  router.visit(url.pathname + url.search);
}

function clearAllFilters() {
  selectedFilters.value = [];
  selectedEvaluationStatus.value = [];
  ratingFrom.value = null;
  ratingTo.value = null;

  const url = new URL(window.location.href);
  url.searchParams.delete('filter');
  url.searchParams.delete('evaluation_status');
  url.searchParams.delete('rating_from');
  url.searchParams.delete('rating_to');
  url.searchParams.set('page', '1');
  url.searchParams.set('per_page', props.employees.per_page);
  router.visit(url.pathname + url.search);
}

watch(
  () => props.employees,
  () => {
    const urlParams = new URLSearchParams(window.location.search);
    selectedFilters.value = urlParams.get('filter')
      ? urlParams.get('filter')!.split(',')
      : [];
    selectedEvaluationStatus.value = urlParams.get('evaluation_status')
      ? urlParams.get('evaluation_status')!.split(',')
      : [];
    ratingFrom.value = urlParams.get('rating_from') ? Number(urlParams.get('rating_from')) : null;
    ratingTo.value = urlParams.get('rating_to') ? Number(urlParams.get('rating_to')) : null;
  }
);

watch(search, () => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
  }, 400); //debounce baby debounce
});

let filterTimeout: ReturnType<typeof setTimeout> | null = null;

watch(selectedFilters, () => {
  if (filterTimeout) clearTimeout(filterTimeout);
  filterTimeout = setTimeout(() => {
  }, 500);
});

function onFilterDropdownClose() {
  if (filterTimeout) clearTimeout(filterTimeout);
  applyMultiFilter();
}

const selectedIds = ref<number[]>([])

const isAllSelected = computed(() => selectedIds.value.length === filteredEmployees.value.length)

const toggleAll = (event: Event) => {
  const checked = (event.target as HTMLInputElement).checked
  selectedIds.value = checked ? filteredEmployees.value.map(emp => emp.id) : []
}

const showArchiveModal = ref(false);

const archiveSelected = () => {
  if (selectedIds.value.length === 0) return
  showArchiveModal.value = true;
}

const confirmArchive = () => {
  router.post(
    route('employee.ratings.archive'),
    { ids: selectedIds.value },
    {
      preserveScroll: true,
      onSuccess: () => {
        selectedIds.value = []
        showArchiveModal.value = false;
      },
      onError: (errors) => {
        console.error(errors)
        showArchiveModal.value = false;
      },
    }
  )
}

</script>

<template>
  <AppLayout>
    <div class="min-h-screen bg-white">
      <!-- Header Section -->
      <div class="bg-white border-gray-200 px-6 pt-8">
        <div class="max-w-7xl mx-auto">
          <div class="flex flex-col space-y-2">
            <h1 class="text-4xl font-semibold text-blue-900">Performance Evaluation</h1>
            <p class="text-lg text-gray-500">Track and view ratings of all employees evaluations here.</p>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="max-w-7xl mx-auto py-6">
        <div class="py-6">
          <div class="flex flex-col lg:flex-row justify-between gap-3">
            <!-- Left Section: Search, Filter, Sort -->
            <div class="flex flex-col lg:flex-row flex-wrap gap-3">
              <!-- Search -->
              <div class="relative flex-1 max-w-md">
                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
                <Input v-model="search" placeholder="Search employees..."
                  class="pl-10 h-10 border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
              </div>
              <div class="flex items-center gap-3">
                <!-- Filter Dropdown -->
                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button variant="outline" class="h-10 px-4 border-gray-300 hover:bg-gray-50">
                      <Filter class="w-4 h-4 mr-2" />
                      Filter
                      <ChevronDown class="w-4 h-4 ml-2" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end" class="min-w-[280px] p-0">
                    <div class="p-4 space-y-4">
                      <!-- Evaluation Status Section -->
                      <div>
                        <h4 class="font-medium text-gray-900 text-sm mb-3">Evaluation Status</h4>
                        <div class="space-y-2">
                          <div class="flex items-center gap-3">
                            <input type="checkbox" id="no-ratings" v-model="selectedEvaluationStatus" value="no_ratings"
                              class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                            <label for="no-ratings" class="text-sm text-gray-700 cursor-pointer">
                              No ratings yet
                            </label>
                          </div>
                        </div>
                      </div>

                      <!-- Average Rating Section -->
                      <div class="border-t border-gray-200 pt-4">
                        <h4 class="font-medium text-gray-900 text-sm mb-3">Average Rating</h4>
                        <div class="space-y-3">
                          <div>
                            <label class="block text-xs text-gray-500 mb-1">From</label>
                            <input type="number" v-model="ratingFrom" placeholder="Enter rating" min="0" max="5"
                              step="0.1"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                          </div>
                          <div>
                            <label class="block text-xs text-gray-500 mb-1">To</label>
                            <input type="number" v-model="ratingTo" placeholder="Enter rating" min="0" max="5"
                              step="0.1"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                          </div>
                        </div>
                      </div>

                      <!-- Action Buttons -->
                      <div class="border-t border-gray-200 pt-4 flex justify-between gap-3">
                        <Button variant="outline" class="flex-1 h-9 text-sm border-gray-300 hover:bg-gray-50"
                          @click="clearAllFilters">
                          Clear
                        </Button>
                        <Button class="flex-1 h-9 text-sm bg-blue-600 hover:bg-blue-700 text-white"
                          @click="applyAllFilters">
                          Apply Filter
                        </Button>
                      </div>
                    </div>
                  </DropdownMenuContent>
                </DropdownMenu>

                <!-- Sort Dropdown -->
                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button variant="outline" class="h-10 px-4 border-gray-300 hover:bg-gray-50">
                      <SortAsc class="w-4 h-4 mr-2" />
                      Sort
                      <ChevronDown class="w-4 h-4 ml-2" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end">
                    <DropdownMenuItem @click="applyFilterSort(undefined, 'name_asc')">
                      Name (A - Z)
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="applyFilterSort(undefined, 'name_desc')">
                      Name (Z - A)
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="applyFilterSort(undefined, 'rating_desc')">
                      Highest Rated
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="applyFilterSort(undefined, 'rating_asc')">
                      Lowest Rated
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </div>
            </div>
            <!-- Right Section: Archive and Export -->
            <div class="flex items-center gap-3">
              <button class="h-10 bg-yellow-500 text-white px-4 flex items-center gap-2 rounded disabled:opacity-50"
                :disabled="selectedIds.length === 0" @click="archiveSelected">
                <Archive class="w-4 h-4" />
                Archive<span v-if="selectedIds.length"> ({{ selectedIds.length }})</span>
              </button>

              <Button class="h-10 px-4 bg-blue-600 hover:bg-blue-700 text-white">
                <Download class="w-4 h-4 mr-2" />
                Export
              </Button>
            </div>
          </div>
        </div>

        <div class="overflow-hidden">
          <div v-if="filteredEmployees.length === 0" class="py-16 text-center">
            <div class="text-gray-400 text-lg">No employees found :P</div>
            <div class="text-gray-500 text-sm mt-2">Try adjusting your search or filter criteria</div>
          </div>

          <div v-else>
            <Table>
              <TableHeader>
                <TableRow class="bg-gray-50 border-b border-gray-200">
                  <th class="px-6 py-3 border-b text-left">
                    <input type="checkbox" @change="toggleAll($event)" :checked="isAllSelected" />
                  </th>
                  <TableHead class="font-bold text-gray-900 py-4 px-6">Name</TableHead>
                  <TableHead class="font-bold text-gray-900 py-4 px-6">Department & Position</TableHead>
                  <TableHead class="font-bold text-gray-900 py-4 px-6">Average Rating</TableHead>
                  <TableHead class="font-bold text-gray-900 py-4 px-6">Action</TableHead>
                </TableRow>
              </TableHeader>
              <TableRow v-for="emp in filteredEmployees" :key="emp.id"
                class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 border-b">
                  <input type="checkbox" :value="emp.id" v-model="selectedIds" />
                </td>
                <TableCell class="py-4 px-6">
                  <div class="font-medium text-gray-900">{{ emp.full_name }}</div>
                </TableCell>
                <TableCell class="py-4 px-6">
                  <div class="space-y-1">
                    <div class="text-sm font-medium text-gray-900">Hauling</div>
                    <div class="text-sm text-gray-500">{{ emp.position }}</div>
                  </div>
                </TableCell>
                <TableCell class="py-4 px-6">
                  <span v-if="emp.average_rating !== null"
                    class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium" :class="{
                      'bg-green-100 text-green-800': emp.average_rating >= 4,
                      'bg-yellow-100 text-yellow-800': emp.average_rating >= 3 && emp.average_rating < 4,
                      'bg-red-100 text-red-800': emp.average_rating < 3
                    }">
                    {{ emp.average_rating }}
                  </span>
                  <span v-else class="text-gray-400 text-sm">No ratings yet</span>
                </TableCell>
                <TableCell class="py-4 px-6">
                  <a :href="`/employee-ratings/${emp.id}/history-page`"
                    class="text-zinc-500 hover:text-blue-800 font-medium text-sm underline">
                    View History
                  </a>
                </TableCell>
              </TableRow>
            </Table>


            <!-- Pagination -->
            <div class="border-t border-gray-200 px-6 py-4">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Results Info -->
                <div class="text-sm text-gray-700">
                  <span class="font-medium">
                    {{
                    props.employees.total === 0
                    ? 0
                    : (props.employees.current_page - 1) * props.employees.per_page + 1
                    }}
                  </span>
                  –
                  <span class="font-medium">
                    {{
                    Math.min(
                    props.employees.current_page * props.employees.per_page,
                    props.employees.total
                    )
                    }}
                  </span>
                  of <span class="font-medium">{{ props.employees.total }}</span> results
                </div>

                <!-- Per Page Selector -->
                <div class="flex items-center gap-2">
                  <span class="text-sm text-gray-700">Showing</span>
                  <select
                    class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    :value="props.employees.per_page" @change="changePerPage">
                    <option v-for="n in [10, 20, 50]" :key="n" :value="n">{{ n }}</option>
                  </select>
                  <span class="text-sm text-gray-700">items per page</span>
                </div>

                <!-- Pagination Controls -->
                <div class="flex items-center gap-1">
                  <button
                    class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                    :disabled="!props.employees.prev_page_url" @click="goToPage(props.employees.prev_page_url)">
                    Previous
                  </button>

                  <div class="flex items-center gap-1 mx-2">
                    <template v-for="page in paginationPages" :key="page">
                      <span v-if="page === '...'" class="px-2 text-gray-400">…</span>
                      <button v-else class="px-3 py-1 rounded-md text-sm font-medium transition-colors" :class="{
                        'bg-blue-600 text-white': page === props.employees.current_page,
                        'text-gray-700 hover:bg-gray-100': page !== props.employees.current_page
                      }" @click="goToPagePageNumber(page)" :disabled="page === props.employees.current_page">
                        {{ page }}
                      </button>
                    </template>
                  </div>

                  <button
                    class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                    :disabled="!props.employees.next_page_url" @click="goToPage(props.employees.next_page_url)">
                    Next
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div v-if="showArchiveModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 relative">
        <button @click="showArchiveModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <div class="p-8 text-center">
          <div class="mx-auto flex items-center justify-center mb-6">
            <TriangleAlert class="h-24 w-24 text-yellow-500" stroke-width="3" />
          </div>

          <h3 class="text-3xl font-semibold text-yellow-500 mb-4">Archive Item(s)</h3>

          <p class="text-gray-600 mb-8 text-base">
            Are you sure you want to archive the selected item(s)?
          </p>

          <div class="flex justify-center gap-4">
            <button @click="showArchiveModal = false"
              class="px-8 py-3 text-gray-600 hover:text-gray-800 hover:bg-gray-100 font-medium">
              Cancel
            </button>
            <button @click="confirmArchive"
              class="px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-md">
              Archive
            </button>
          </div>
        </div>
      </div>
    </div>

  </AppLayout>
</template>