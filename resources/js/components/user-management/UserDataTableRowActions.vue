<script setup lang="ts">
import type { Row } from '@tanstack/vue-table'
import { User } from '@/types/user'
import { Ellipsis } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'

interface DataTableRowActionsProps {
    row: Row<User>
}

const props = defineProps<DataTableRowActionsProps>()
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button
                variant="ghost"
                class="flex h-8 w-8 p-0 data-[state=open]:bg-muted"
            >
                <Ellipsis class="h-4 w-4" />
                <span class="sr-only">Open menu</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="w-[160px]">
            <DropdownMenuItem>Edit</DropdownMenuItem>
            <DropdownMenuItem v-if="row.original.status === 'active'">
                Deactivate
            </DropdownMenuItem>
            <DropdownMenuItem v-else-if="row.original.status === 'deactivated'">
                Activate
            </DropdownMenuItem>
            <DropdownMenuItem v-else>
                Create Account
            </DropdownMenuItem>
            <DropdownMenuItem class="text-red-600">
                Delete
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>