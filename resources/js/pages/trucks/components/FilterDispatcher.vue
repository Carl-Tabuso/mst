<script setup lang="ts">
import EmployeePopoverSelection from '@/components/EmployeePopoverSelection.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
  CommandSeparator,
} from '@/components/ui/command'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { Employee } from '@/types'
import { UserRoundSearch } from 'lucide-vue-next'
import { inject } from 'vue'
import { useTruckTable } from '../composables/useTruckTable'

const {
  table,
  onDispatcherFilterSelect,
  onClearDispatcherFilter,
  isProcessing,
} = useTruckTable()

const dispatchers = inject<Employee[]>('dispatchers', [])
</script>

<template>
  <Popover>
    <PopoverTrigger as-child>
      <Button
        variant="ghost"
        class="ml-1"
      >
        <UserRoundSearch class="mr-2" />
        Dispatcher
        <Badge
          v-if="table.filters.dispatchers.length"
          variant="secondary"
          class="rounded-full font-extrabold"
        >
          {{ table.filters.dispatchers.length }}
        </Badge>
      </Button>
    </PopoverTrigger>
    <PopoverContent
      class="w-full p-0"
      align="start"
    >
      <Command :class="{ 'pointer-events-none opacity-50': isProcessing }">
        <CommandInput placeholder="Search dispatcher" />
        <CommandList>
          <CommandEmpty> No results found. </CommandEmpty>
          <CommandGroup class="max-h-64 overflow-auto">
            <CommandItem
              v-for="dispatcher in dispatchers"
              :key="dispatcher.id"
              :value="dispatcher.id"
              class="cursor-pointer"
              @select="onDispatcherFilterSelect(dispatcher.id)"
            >
              <EmployeePopoverSelection
                :employee="dispatcher"
                :is-selected="table.filters.dispatchers.includes(dispatcher.id)"
              />
            </CommandItem>
          </CommandGroup>
          <template v-if="table.filters.dispatchers.length">
            <CommandSeparator />
            <CommandGroup>
              <CommandItem
                value="clear"
                class="cursor-pointer justify-center text-center"
                @select="onClearDispatcherFilter"
              >
                Clear Filters
              </CommandItem>
            </CommandGroup>
          </template>
          <CommandSeparator />
        </CommandList>
      </Command>
    </PopoverContent>
  </Popover>
</template>
