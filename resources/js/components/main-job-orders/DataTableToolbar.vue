<script setup lang="ts" generic="TData">
import type { Table } from '@tanstack/vue-table'
import { Search, Settings2, Download, Archive } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu'
import { computed } from 'vue'
import FilterPopover from './FilterPopover.vue'
import { useRouter, Router } from 'vue-router'

interface DataTableToolbarProps {
    table: Table<TData>
    globalFilter: string | number
    routeName: string
}

const props = defineProps<DataTableToolbarProps>()
const router = useRouter()

const handleOnSearch = (value: string | number) => {
    props.table.setGlobalFilter(value)
    router.replace({
        name: props.routeName,
        query: { search: value }
    })
}

const hasRowSelection = computed(() => props.table.getSelectedRowModel().rows.length > 0);

const visibleColumnCount = computed(() =>
    props.table.getAllColumns().filter((column) =>
        column.getCanHide() && column.getIsVisible()
    ).length
)
</script>

<template>
    <div class="flex items-center py-1">
        <div class="relative w-full max-w-xs items-center">
            <Input id="search" type="text" class="max-w-sm pl-12 h-9" placeholder="Search" :model-value="globalFilter"
                @update:model-value="handleOnSearch" />
            <span class="absolute start-0 inset-y-0 flex items-center justify-center px-5">
                <Search class="size-4 text-muted-foreground" />
            </span>
        </div>

        <FilterPopover :routeName="routeName" />

        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="ghost" class="mx-1">
                    <Settings2 class="mr-2" :stroke-width="1" />
                    View
                    <div class="hidden space-x-1 lg:flex">
                        <Badge variant="secondary" class="rounded-full font-normal">
                            {{ visibleColumnCount }}
                        </Badge>
                    </div>
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent>
                <DropdownMenuLabel>Toggle columns</DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuCheckboxItem v-for="column in table.getAllColumns().filter(c => c.getCanHide())"
                    :key="column.id" :checked="column.getIsVisible()"
                    @update:checked="(value: boolean) => column.toggleVisibility(value)">
                    {{ column.columnDef.meta?.label }}
                </DropdownMenuCheckboxItem>
            </DropdownMenuContent>
        </DropdownMenu>

        <Button variant="ghost">
            <Download class="mr-2" :stroke-width="1" />
            Export
        </Button>

        <div class="ml-auto">
            <Button :disabled="!hasRowSelection" :variant="hasRowSelection ? 'destructive' : 'secondary'" class="mx-3">
                <Archive class="mr-2" />
                Archive
                <template v-if="hasRowSelection">
                    <div class="hidden lg:flex">
                        <Badge variant="secondary" class="rounded-full font-normal">{{
                            table.getSelectedRowModel().rows.length }}
                        </Badge>
                    </div>
                </template>
            </Button>
        </div>
    </div>
</template>
