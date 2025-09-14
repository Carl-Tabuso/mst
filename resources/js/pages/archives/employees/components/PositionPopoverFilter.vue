<script setup lang="ts">
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
import { Position } from '@/types'
import { BriefcaseBusiness, Check } from 'lucide-vue-next'
import { computed, inject } from 'vue'
import { useArchivedEmployeeTable } from '../../composables/useArchivedEmployeeTable'

const positions = inject<Position[]>('positions')

const { dataTable, onPositionSelect, onClearPosition } =
  useArchivedEmployeeTable()

const selectedPositions = computed(() => new Set(dataTable.positions.value))
</script>

<template>
  <Popover>
    <PopoverTrigger as-child>
      <Button
        variant="ghost"
        class="ml-1"
      >
        <BriefcaseBusiness class="mr-2" />
        Position
        <Badge
          v-if="selectedPositions.size"
          variant="secondary"
          class="rounded-full font-extrabold"
        >
          {{ selectedPositions.size }}
        </Badge>
      </Button>
    </PopoverTrigger>
    <PopoverContent
      class="w-full p-0"
      align="start"
    >
      <Command>
        <CommandInput placeholder="Search job position" />
        <CommandList>
          <CommandEmpty> No results found. </CommandEmpty>
          <CommandGroup class="max-h-64 overflow-auto">
            <CommandItem
              v-for="{ id, name } in positions"
              :key="id"
              :value="id"
              @select="onPositionSelect(id)"
              class="cursor-pointer"
            >
              <div class="flex flex-row items-center gap-3">
                <div
                  class="flex h-4 w-4 items-center justify-center rounded-sm border border-primary"
                  :class="[
                    selectedPositions.has(id)
                      ? 'bg-primary text-primary-foreground'
                      : 'opacity-50 [&_svg]:invisible',
                  ]"
                >
                  <Check class="h-4 w-4" />
                </div>
                <span class="truncate">
                  {{ name }}
                </span>
              </div>
            </CommandItem>
          </CommandGroup>
          <template v-if="selectedPositions.size">
            <CommandSeparator />
            <CommandGroup>
              <CommandItem
                value="clear"
                class="cursor-pointer justify-center text-center"
                @select="onClearPosition"
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
