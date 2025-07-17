<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
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
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { getInitials } from '@/composables/useInitials'
import { Employee } from '@/types'
import { Check, ChevronsUpDown, X } from 'lucide-vue-next'
import { computed } from 'vue'
import EmployeePopoverSelection from './EmployeePopoverSelection.vue'
import EmployeeCommandListPlaceholder from './placeholders/EmployeeCommandListPlaceholder.vue'

interface HaulersSelectionProps {
  employees?: Employee[]
  selectedHaulers?: Employee[]
  index?: number
  isDisabled?: boolean
}

const props = withDefaults(defineProps<HaulersSelectionProps>(), {
  isDisabled: false,
})

const isExistingHauler = (employeeId: number) => {
  return props?.selectedHaulers?.map((h) => h.id).includes(employeeId)
}

const remainingEmployees = computed(() => {
  const filtered = props.employees?.filter(
    (e) => !props.selectedHaulers?.flatMap((sh) => sh.id).includes(e.id),
  )

  return new Set(filtered)
})
</script>

<template>
  <div class="flex items-center gap-x-10">
    <Label class="w-28 shrink-0"> Haulers </Label>
    <Popover @update:open="(value) => $emit('onHaulerToggle', value)">
      <PopoverTrigger
        class="w-[400px]"
        as-child
      >
        <Button
          variant="outline"
          class="bg-muted"
        >
          <div
            v-if="selectedHaulers?.length"
            class="flex items-center justify-between gap-2 rounded-md text-xs"
          >
            <div class="flex items-center gap-2 overflow-hidden">
              <Avatar class="h-7 w-7 shrink-0 rounded-full">
                <AvatarImage
                  v-if="selectedHaulers[0]?.account?.avatar"
                  :src="selectedHaulers[0].account.avatar"
                  :alt="selectedHaulers[0].fullName"
                />
                <AvatarFallback>
                  {{ getInitials(selectedHaulers[0].fullName) }}
                </AvatarFallback>
              </Avatar>
              <span class="truncate">
                <template v-if="selectedHaulers.length < 2">
                  {{ selectedHaulers[0].fullName }}
                </template>
                <template v-else>
                  {{
                    `${selectedHaulers[0].fullName} and ${selectedHaulers.length - 1} more`
                  }}
                </template>
              </span>
            </div>
            <Button
              v-if="!isDisabled"
              variant="ghost"
              size="icon"
              type="button"
              class="ml-1 h-5 w-5 text-muted-foreground hover:text-foreground"
              @click="$emit('onRemoveExistingHaulers', index)"
            >
              <X />
            </Button>
          </div>
          <template v-else>
            <span class="text-muted-foreground"> Select haulers </span>
          </template>
          <ChevronsUpDown class="ml-auto h-4 w-4" />
        </Button>
      </PopoverTrigger>
      <PopoverContent class="w-72 p-0">
        <Command>
          <CommandInput placeholder="Search for haulers" />
          <CommandList>
            <CommandEmpty> No results found. </CommandEmpty>
            <template v-if="selectedHaulers?.length">
              <div :class="['overflow-y-auto', { 'max-h-40': ! isDisabled }]">
                <CommandGroup>
                  <CommandItem
                    v-for="hauler in selectedHaulers"
                    :key="hauler.id"
                    :value="hauler"
                    @select="$emit('onHaulerSelect', hauler, index)"
                  >
                    <EmployeePopoverSelection
                      :employee="hauler"
                      is-selected
                    />
                  </CommandItem>
                </CommandGroup>
              </div>
              <CommandSeparator v-if="! isDisabled" />
            </template>
            <CommandGroup v-if="! isDisabled">
              <template v-if="!employees">
                <EmployeeCommandListPlaceholder :count="7" />
              </template>
              <template v-else>
                <CommandItem
                  v-for="employee in remainingEmployees"
                  :key="employee.id"
                  :value="employee"
                  @select="$emit('onHaulerSelect', employee, index)"
                >
                  <div
                    v-if="!isExistingHauler(employee.id)"
                    class="mr-1 flex h-4 w-4 items-center justify-center rounded-sm border border-primary opacity-50 [&_svg]:invisible"
                  >
                    <Check :class="['h-4 w-4']" />
                  </div>
                  <Avatar class="h-7 w-7 overflow-hidden rounded-full">
                    <AvatarImage
                      v-if="employee?.account?.avatar"
                      :src="employee.account.avatar"
                      :alt="employee.fullName"
                    />
                    <AvatarFallback class="rounded-full">
                      {{ getInitials(employee.fullName) }}
                    </AvatarFallback>
                  </Avatar>
                  <div class="grid flex-1 text-left text-sm leading-tight">
                    <span class="truncate">
                      {{ employee.fullName }}
                    </span>
                  </div>
                </CommandItem>
              </template>
            </CommandGroup>
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
  </div>
</template>
