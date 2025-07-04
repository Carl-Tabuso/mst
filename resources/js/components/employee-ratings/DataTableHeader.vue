<script lang="ts" setup>

import type { Column } from '@tanstack/vue-table'
import type { JobOrder } from '@/types'
import { Button } from '@/components/ui/button'
import { ChevronsUpDown } from 'lucide-vue-next'

const props = defineProps<{
    column: Column<JobOrder>
}>()

const toggleSort = () => props.column.toggleSorting(props.column.getIsSorted() === 'asc')
</script>

<template>
    <Button variant="ghost" @click="toggleSort"
        class="flex items-center justify-start text-[13px] pl-0 hover:bg-transparent [&_svg]:size-3">
        <span class="">
            {{ props.column.columnDef.meta?.label || '' }}
        </span>
        <ChevronsUpDown class="ml-2" />
    </Button>
</template>