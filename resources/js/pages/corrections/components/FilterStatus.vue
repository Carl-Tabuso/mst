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
import {
  correctionStatuses,
  CorrectionStatusType,
} from '@/constants/correction-statuses'
import { router } from '@inertiajs/vue3'
import { useUrlSearchParams } from '@vueuse/core'
import { Check, PlusCircle } from 'lucide-vue-next'
import { ref } from 'vue'

const selectedStatuses = ref<CorrectionStatusType[]>([])

const isLoading = ref<boolean>(false)

const urlParams = useUrlSearchParams('history')

if ( Object.keys(urlParams).find(key => key.includes('statuses'))) {
  selectedStatuses.value = Array.from(
    Object.values(urlParams),
  ) as CorrectionStatusType[]
}

const onSelect = (status: CorrectionStatusType) => {
  isLoading.value = true

  const isRemoving = selectedStatuses.value.includes(status)
  const filters = isRemoving
    ? selectedStatuses.value.filter((s) => s !== status)
    : [...selectedStatuses.value, status]

  router.get(
    route('job_order.correction.index'),
    {
      statuses: filters,
    },
    {
      preserveState: true,
      replace: true,
      onSuccess: () => (selectedStatuses.value = filters),
      onError: () => (isLoading.value = false),
      onCancel: () => (isLoading.value = false),
      onFinish: () => (isLoading.value = false),
    },
  )
}

const onClearFilters = () => {
  isLoading.value = true

  router.get(
    route('job_order.correction.index'),
    {},
    {
      preserveState: true,
      replace: true,
      onSuccess: () => (selectedStatuses.value = []),
      onError: () => (isLoading.value = false),
      onCancel: () => (isLoading.value = false),
      onFinish: () => (isLoading.value = false),
    },
  )
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
      class="w- w-44 p-0"
      align="start"
    >
      <Command :class="[{ 'pointer-events-none opacity-60': isLoading }]">
        <CommandInput placeholder="Request Status" />
        <CommandList>
          <CommandEmpty>No results found.</CommandEmpty>
          <CommandGroup>
            <CommandItem
              v-for="status in correctionStatuses"
              :key="status.id"
              :value="status.id"
              @select="onSelect(status.id)"
              class="cursor-pointer"
            >
              <div
                :class="
                  cn(
                    'mr-2 flex h-4 w-4 items-center justify-center rounded-sm border border-primary',
                    selectedStatuses.includes(status.id)
                      ? 'bg-primary text-primary-foreground'
                      : 'opacity-50 [&_svg]:invisible',
                  )
                "
              >
                <Check :class="cn('h-4 w-4')" />
              </div>
              <span>{{ status.label }}</span>
            </CommandItem>
          </CommandGroup>

          <template v-if="selectedStatuses.length > 0">
            <CommandSeparator />
            <CommandGroup>
              <CommandItem
                :value="{ label: 'Clear filters' }"
                class="justify-center text-center"
                @select="onClearFilters"
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
