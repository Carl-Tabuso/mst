<script setup lang="ts">
import CommandListPlaceholder from '@/components/placeholders/CommandListPlaceholder.vue'
import { Button } from '@/components/ui/button'
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { Truck } from '@/types'
import { router } from '@inertiajs/vue3'
import { Check, ChevronsUpDown } from 'lucide-vue-next'
import { inject } from 'vue'

interface TruckSelectionProps {
  canEdit: boolean
}

defineProps<TruckSelectionProps>()

const truckList = inject<Truck[] | undefined>('trucks', [])

const truckModel = defineModel<Truck | null>('truck')

const onTruckSelectionToggle = () => {
  if (truckList?.length) return

  router.reload({
    only: ['trucks'],
  })
}
</script>

<template>
  <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-x-10">
    <Label class="w-full shrink-0 sm:w-28"> Truck </Label>
    <Popover @update:open="onTruckSelectionToggle">
      <PopoverTrigger
        as-child
        class="w-full sm:w-[400px]"
        :disabled="!canEdit"
      >
        <Button variant="outline">
          <div v-if="truckModel">
            {{ truckModel.plateNo }}
          </div>
          <p
            v-else
            class="font-normal text-muted-foreground"
          >
            Select a truck
          </p>
          <ChevronsUpDown class="ml-auto h-4 w-4" />
        </Button>
      </PopoverTrigger>
      <PopoverContent
        class="p-0"
        align="start"
      >
        <Command>
          <CommandInput placeholder="Search model or plate number" />
          <CommandList>
            <CommandEmpty> No results found. </CommandEmpty>
            <CommandGroup>
              <template v-if="!truckList?.length">
                <CommandListPlaceholder />
              </template>
              <template v-else>
                <CommandItem
                  v-for="availableTruck in truckList"
                  :key="availableTruck.id"
                  :value="availableTruck"
                  @select="() => (truckModel = availableTruck)"
                >
                  <span class="flex flex-col">
                    <span class="text-sm font-medium text-foreground">
                      {{ availableTruck.plateNo }}
                    </span>
                    <span class="text-xs text-muted-foreground">
                      {{ availableTruck.model }}
                    </span>
                  </span>
                  <Check
                    :class="[
                      'ml-auto h-4 w-4',
                      truckModel?.id === availableTruck.id
                        ? 'opacity-100'
                        : 'opacity-0',
                    ]"
                  />
                </CommandItem>
              </template>
            </CommandGroup>
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
  </div>
</template>
