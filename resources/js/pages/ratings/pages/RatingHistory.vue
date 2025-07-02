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
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { ChevronDown, Filter, Search, SortAsc } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    employee: { id: number; full_name: string; position: string };
    received: {
        data: Array<{
            job_order_id: number;
            from: string;
            from_position: string;
            scale: string;
            description: string;
            date: string;
        }>;
        meta: {
            current_page: number;
            last_page: number;
            per_page: number;
            total: number;
        };
    };
    filters: {
        sort: string | null;
        scale_from: number | null;
        scale_to: number | null;
        date_from?: string | null;
        date_to?: string | null;
    };
}>();


const search = ref('');
const sort = ref(props.filters.sort || '');
const ratingFrom = ref<number | null>(props.filters.scale_from);
const ratingTo = ref<number | null>(props.filters.scale_to);
const dateFrom = ref<string | null>(null);
const dateTo = ref<string | null>(null);

const filteredReceived = computed(() => {
    return props.received.data.filter((r) => {
        const matchesSearch =
            r.from.toLowerCase().includes(search.value.toLowerCase()) ||
            r.from_position.toLowerCase().includes(search.value.toLowerCase());

        const scale = parseFloat(r.scale);
        const withinRange =
            (ratingFrom.value === null || scale >= ratingFrom.value) &&
            (ratingTo.value === null || scale <= ratingTo.value);

        return matchesSearch && withinRange;
    });
});


function applyFilterSort() {
    const url = new URL(window.location.href);

    if (sort.value) url.searchParams.set('sort', sort.value);
    else url.searchParams.delete('sort');

    if (ratingFrom.value !== null) url.searchParams.set('scale_from', ratingFrom.value.toString());
    else url.searchParams.delete('scale_from');

    if (ratingTo.value !== null) url.searchParams.set('scale_to', ratingTo.value.toString());
    else url.searchParams.delete('scale_to');

    if (dateFrom.value) url.searchParams.set('date_from', dateFrom.value);
    else url.searchParams.delete('date_from');

    if (dateTo.value) url.searchParams.set('date_to', dateTo.value);
    else url.searchParams.delete('date_to');

    router.visit(url.pathname + '?' + url.searchParams.toString());
}

function clearFilters() {
    sort.value = '';
    ratingFrom.value = null;
    ratingTo.value = null;
    dateFrom.value = null;
    dateTo.value = null;
    applyFilterSort();
}

const perPageOptions = [10, 20, 50];

function changePerPage(e: Event) {
    const selected = (e.target as HTMLSelectElement).value;
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', selected);
    router.visit(url.pathname + '?' + url.searchParams.toString());
}

const paginationPages = computed(() => {
    const pages = [];
    const totalPages = props.received.meta.last_page;
    const current = props.received.meta.current_page;

    for (let i = 1; i <= totalPages; i++) {
        if (
            i === 1 ||
            i === totalPages ||
            Math.abs(i - current) <= 1
        ) {
            pages.push(i);
        } else if (
            (i === current - 2 && i !== 1) ||
            (i === current + 2 && i !== totalPages)
        ) {
            pages.push('...');
        }
    }

    return [...new Set(pages)];
});

function goToPage(page: number) {
    const url = new URL(window.location.href);

    // keep existing params, just replace "page"
    url.searchParams.set('page', page.toString());

    router.visit(url.pathname + '?' + url.searchParams.toString(), {
        preserveScroll: true,
    });
}

</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-white">
            <div class="bg-white px-6 pt-8">
                <div class="max-w-7xl mx-auto">
                    <div class="space-y-2">
                        <h1 class="text-4xl font-semibold text-blue-900">Evaluation History</h1>
                        <p class="text-lg text-gray-500">
                            Track and view ratings for {{ props.employee.full_name }} ({{ props.employee.position }}).
                        </p>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto py-6">
                <div class="flex flex-col lg:flex-row justify-between gap-4 pb-6">
                    <div class="relative w-full max-w-md">
                        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
                        <Input v-model="search" placeholder="Search evaluations..."
                            class="pl-10 h-10 border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                    </div>

                    <div class="flex items-center gap-3">
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="outline" class="h-10 px-4 border-gray-300 hover:bg-gray-50">
                                    <Filter class="w-4 h-4 mr-2" /> Filter
                                    <ChevronDown class="w-4 h-4 ml-2" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="min-w-[300px] p-4 space-y-4">
                                <!-- Rating Section -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-800 mb-1">Rating</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <Input type="number" v-model="ratingFrom" placeholder="From" min="0" max="5"
                                            step="0.1" />
                                        <Input type="number" v-model="ratingTo" placeholder="To" min="0" max="5"
                                            step="0.1" />
                                    </div>
                                </div>

                                <!-- Date Section -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-800 mb-1">Date</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <Input type="date" v-model="dateFrom" placeholder="From" />
                                        <Input type="date" v-model="dateTo" placeholder="To" />
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <Button variant="outline" class="flex-1 h-9 text-sm"
                                        @click="clearFilters">Clear</Button>
                                    <Button class="flex-1 h-9 text-sm bg-blue-600 text-white"
                                        @click="applyFilterSort">Apply Filter</Button>
                                </div>
                            </DropdownMenuContent>
                        </DropdownMenu>

                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="outline" class="h-10 px-4 border-gray-300 hover:bg-gray-50">
                                    <SortAsc class="w-4 h-4 mr-2" /> Sort
                                    <ChevronDown class="w-4 h-4 ml-2" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem @click="sort = 'date_desc'; applyFilterSort()">
                                    Date (Newest)
                                </DropdownMenuItem>
                                <DropdownMenuItem @click="sort = 'date_asc'; applyFilterSort()">
                                    Date (Oldest)
                                </DropdownMenuItem>
                                <DropdownMenuItem @click="sort = 'scale_desc'; applyFilterSort()">
                                    Highest Rating
                                </DropdownMenuItem>
                                <DropdownMenuItem @click="sort = 'scale_asc'; applyFilterSort()">
                                    Lowest Rating
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>

                <div class="overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-gray-50 border-b">
                                <TableHead class="px-6 py-3">Job Order</TableHead>
                                <TableHead class="px-6 py-3">Evaluator</TableHead>
                                <TableHead class="px-6 py-3">Department & Position</TableHead>
                                <TableHead class="px-6 py-3">Rating</TableHead>
                                <TableHead class="px-6 py-3">Remarks</TableHead>
                                <TableHead class="px-6 py-3">Date & Time</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="(row, i) in filteredReceived" :key="i" class="hover:bg-gray-50">
                                <TableCell class="px-6 py-4">JO-{{ row.job_order_id }}</TableCell>
                                <TableCell class="px-6 py-4 font-semibold">{{ row.from }}</TableCell>
                                <TableCell class="px-6 py-4">{{ row.from_position }}</TableCell>
                                <TableCell class="px-6 py-4 font-semibold text-green-600">{{ row.scale }}</TableCell>
                                <TableCell class="px-6 py-4 max-w-sm truncate">{{ row.description }}</TableCell>
                                <TableCell class="px-6 py-4 text-gray-500">{{ row.date }}</TableCell>
                            </TableRow>
                            <TableRow v-if="filteredReceived.length === 0">
                                <TableCell colspan="6" class="text-center text-gray-400 py-4">No ratings found.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                    <!-- Pagination Summary & Controls -->
                    <div class="border-t border-gray-200 px-6 py-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <!-- Results Info -->
                            <div class="text-sm text-gray-700">
                                <span class="font-medium">
                                    {{
                                    props.received.meta.total === 0
                                    ? 0
                                    : (props.received.meta.current_page - 1) * props.received.meta.per_page + 1
                                    }}
                                </span>
                                –
                                <span class="font-medium">
                                    {{
                                    Math.min(
                                    props.received.meta.current_page * props.received.meta.per_page,
                                    props.received.meta.total
                                    )
                                    }}
                                </span>
                                of <span class="font-medium">{{ props.received.meta.total }}</span> results
                            </div>

                            <!-- Per Page Selector -->
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-700">Showing</span>
                                <select
                                    class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :value="props.received.meta.per_page" @change="changePerPage">
                                    <option v-for="n in perPageOptions" :key="n" :value="n">{{ n }}</option>
                                </select>
                                <span class="text-sm text-gray-700">items per page</span>
                            </div>

                            <!-- Pagination Controls -->
                            <div class="flex items-center gap-1">
                                <button
                                    class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                                    :disabled="props.received.meta.current_page === 1"
                                    @click="goToPage(props.received.meta.current_page - 1)">
                                    Previous
                                </button>

                                <div class="flex items-center gap-1 mx-2">
                                    <template v-for="page in paginationPages" :key="page">
                                        <span v-if="page === '...'" class="px-2 text-gray-400">…</span>
                                        <button v-else
                                            class="px-3 py-1 rounded-md text-sm font-medium transition-colors" :class="{
                                                'bg-blue-600 text-white': page === props.received.meta.current_page,
                                                'text-gray-700 hover:bg-gray-100': page !== props.received.meta.current_page
                                            }" @click="goToPage(page)"
                                            :disabled="page === props.received.meta.current_page">
                                            {{ page }}
                                        </button>
                                    </template>
                                </div>

                                <button
                                    class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                                    :disabled="props.received.meta.current_page === props.received.meta.last_page"
                                    @click="goToPage(props.received.meta.current_page + 1)">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
