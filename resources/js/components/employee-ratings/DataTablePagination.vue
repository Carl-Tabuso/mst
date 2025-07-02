<script setup lang="ts">
import type { Table } from '@tanstack/vue-table'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue
} from "@/components/ui/select"
import {
    ChevronLeftIcon,
    ChevronRightIcon,
    ChevronsLeft,
    ChevronsRight,
} from 'lucide-vue-next'
import { Button } from '@/components/ui/button';
import { computed } from 'vue';

interface DataTablePaginationProps {
    table: Table<any>
    lastPage: number
}

const props = defineProps<DataTablePaginationProps>()

const pageSizes = [10, 25, 50, 100]

const currentPage = computed(() => props.table.getState().pagination.pageIndex + 1)

const canGoPrevPage = computed(() => props.table.getState().pagination.pageIndex > 0)
const canGoNextPage = computed(() => currentPage.value < props.lastPage)
</script>

<template>
    <div class="flex items-center justify-between gap-16 p-3">
        <div class="flex items-center space-x-6 lg:space-x-24">
            <div class="flex items-center space-x-2">
                <p class="text-sm font-medium">
                    Rows per page
                </p>
                <Select :model-value="table.getState().pagination.pageSize"
                    @update:model-value="(value) => (table.setPageSize(Number(value)))">
                    <SelectTrigger class="h-8 w-[70px]">
                        <SelectValue :placeholder="table.getState().pagination.pageSize.toString()" />
                    </SelectTrigger>
                    <SelectContent side="top">
                        <SelectItem v-for="pageSize in pageSizes" :key="pageSize" :value="pageSize">
                            {{ pageSize }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="flex w-[100px] items-center justify-center text-sm font-medium">
                Page {{ currentPage }} of
                {{ lastPage }}
            </div>
            <div class="flex items-center space-x-2">
                <Button variant="outline" class="hidden h-8 w-8 p-0 lg:flex" :disabled="!canGoPrevPage"
                    @click="table.setPageIndex(0)">
                    <span class="sr-only">Go to first page</span>
                    <ChevronsLeft class="h-4 w-4" />
                </Button>
                <Button variant="outline" class="h-8 w-8 p-0" :disabled="!canGoPrevPage" @click="table.previousPage()">
                    <span class="sr-only">Go to previous page</span>
                    <ChevronLeftIcon class="h-4 w-4" />
                </Button>
                <Button variant="outline" class="h-8 w-8 p-0" :disabled="!canGoNextPage" @click="table.nextPage()">
                    <span class="sr-only">Go to next page</span>
                    <ChevronRightIcon class="h-4 w-4" />
                </Button>
                <Button variant="outline" class="hidden h-8 w-8 p-0 lg:flex" :disabled="!canGoNextPage"
                    @click="table.setPageIndex(table.getPageCount() - 1)">
                    <span class="sr-only">Go to last page</span>
                    <ChevronsRight class="h-4 w-4" />
                </Button>
            </div>
        </div>
    </div>
</template>
