<script setup lang="ts" generic="TData">
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { cn } from '@/lib/utils'

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
import { Check, PlusCircle } from 'lucide-vue-next'
import { ref } from 'vue'

interface FilterStatusProps {
  statuses?: any[]
}

const props = defineProps<FilterStatusProps>()

const correctionStatuses = [
  {
    id: 'pending',
    label: 'Pending',
  },
  {
    id: 'approved',
    label: 'Approved',
  },
  {
    id: 'rejected',
    label: 'Rejected',
  },
]

const selectedStatuses = ref<any[]>(props.statuses || [])

const onSelect = (status: any) => {
  if (selectedStatuses.value.includes(status)) {
    const index = selectedStatuses.value.findIndex((s) => s.id === status.id)
    selectedStatuses.value.splice(index, 1)
  } else {
    selectedStatuses.value.push(status)
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
        <PlusCircle class="mr-2" />
        Status
        <template v-if="selectedStatuses?.length > 0">
          <Badge
            variant="secondary"
            class="rounded-full font-extrabold"
          >
            {{ selectedStatuses.length }}
          </Badge>
        </template>
      </Button>
    </PopoverTrigger>
    <PopoverContent
      class="w-[200px] p-0"
      align="start"
    >
      <Command>
        <CommandInput placeholder="Request Status" />
        <CommandList>
          <CommandEmpty>No results found.</CommandEmpty>
          <CommandGroup>
            <CommandItem
              v-for="status in correctionStatuses"
              :key="status.id"
              :value="status"
              @select="onSelect(status)"
            >
              <div
                :class="
                  cn(
                    'mr-2 flex h-4 w-4 items-center justify-center rounded-sm border border-primary',
                    selectedStatuses.includes(status)
                      ? 'bg-primary text-primary-foreground'
                      : 'opacity-50 [&_svg]:invisible',
                  )
                "
              >
                <Check :class="cn('h-4 w-4')" />
              </div>
              <span>{{ status.label }}</span>
              <!-- <span
                v-if="facets?.get(option.value)"
                class="ml-auto flex h-4 w-4 items-center justify-center font-mono text-xs"
              >
                {{ facets.get(option.value) }}
              </span> -->
            </CommandItem>
          </CommandGroup>

          <template v-if="selectedStatuses.length > 0">
            <CommandSeparator />
            <CommandGroup>
              <CommandItem
                :value="{ label: 'Clear filters' }"
                class="justify-center text-center"
                @select="() => (selectedStatuses = [])"
              >
                Clear filters
              </CommandItem>
            </CommandGroup>
          </template>
        </CommandList>
      </Command>
    </PopoverContent>
  </Popover>
</template>
