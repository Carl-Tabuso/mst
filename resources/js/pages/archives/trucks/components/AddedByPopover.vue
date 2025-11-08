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
import { computed, inject } from 'vue'
import { useArchivedTrucksTable } from '../../composables/useArchivedTrucksTable'

const dispatchers = inject<Employee[]>('dispatchers')

const { dataTable, onDispatcherSelect, onClearSelectedDispatchers } =
  useArchivedTrucksTable()

const selectedDispatchersCount = computed(
  () => dataTable.dispatchers.value.length,
)
</script>

<template>
  <Popover>
    <PopoverTrigger
      class="data-[state=open]:bg-accent"
      as-child
    >
      <Button
        variant="ghost"
        class="ml-1"
      >
        <UserRoundSearch class="mr-1" />
        Added By
        <Badge
          v-if="selectedDispatchersCount"
          variant="secondary"
          class="rounded-full font-extrabold"
        >
          {{ selectedDispatchersCount }}
        </Badge>
      </Button>
    </PopoverTrigger>
    <PopoverContent
      class="w-full p-0"
      align="start"
    >
      <Command>
        <CommandInput placeholder="Search dispatcher" />
        <CommandList>
          <CommandEmpty> No results found. </CommandEmpty>
          <CommandGroup class="max-h-64 overflow-auto">
            <CommandItem
              v-for="dispatcher in dispatchers"
              :key="dispatcher.id"
              :value="dispatcher.id"
              class="cursor-pointer"
              @select="onDispatcherSelect(dispatcher.id)"
            >
              <EmployeePopoverSelection
                :employee="dispatcher"
                :is-selected="
                  dataTable.dispatchers.value.includes(dispatcher.id)
                "
              />
            </CommandItem>
          </CommandGroup>
          <template v-if="selectedDispatchersCount">
            <CommandSeparator />
            <CommandGroup>
              <CommandItem
                value="clear"
                class="cursor-pointer justify-center text-center"
                @select="onClearSelectedDispatchers"
              >
                Clear Filters
              </CommandItem>
            </CommandGroup>
          </template>
        </CommandList>
      </Command>
    </PopoverContent>
  </Popover>
</template>
