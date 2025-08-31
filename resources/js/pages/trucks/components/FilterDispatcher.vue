<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
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

const dispatchers = inject<Employee[]>('dispatchers', [])

const { table } = useTruckTable()

const onSelect = (dispatcherId: number) => {
  const dispatcherFilter = table.value.filters.dispatchers

  if (dispatcherFilter.includes(dispatcherId)) {
    const index = dispatcherFilter.findIndex((d: number) => d === dispatcherId)

    dispatcherFilter.splice(index, 1)
  } else {
    dispatcherFilter.push(dispatcherId)
  }
}
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
          <CommandGroup>
            <CommandItem
              v-for="dispatcher in dispatchers"
              :key="dispatcher.id"
              :value="dispatcher.id"
              @select="onSelect(dispatcher.id)"
            >
              {{ dispatcher.fullName }}
            </CommandItem>
          </CommandGroup>
        </CommandList>
      </Command>
    </PopoverContent>
  </Popover>
</template>
