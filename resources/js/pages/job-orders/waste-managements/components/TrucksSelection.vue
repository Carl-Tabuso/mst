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
  CommandSeparator,
} from '@/components/ui/command'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { Truck } from '@/types'
import { router } from '@inertiajs/vue3'
import { ChevronsUpDown, X } from 'lucide-vue-next'
import { computed, inject, Ref } from 'vue'
import TruckPopoverSelection from './TruckPopoverSelection.vue'

interface TrucksSelectionProps {
  isAuthorize: boolean
  isOpen: boolean
}

const props = defineProps<TrucksSelectionProps>()

const truckList = inject<Readonly<Ref<Truck[]>>>('trucks')

const selectedTrucks = defineModel<Truck[]>('trucks', { default: [] })

const isSelectedTrucksMoreThanThree = computed(
  () => selectedTrucks.value.length > 3,
)
const placeholderTrucks = computed(() => {
  return isSelectedTrucksMoreThanThree.value
    ? selectedTrucks.value.slice(0, 3)
    : selectedTrucks.value
})

const remainingTrucks = computed(() => {
  const filtered = truckList?.value?.filter((truck) => {
    const selectedTruckIds = selectedTrucks.value.map((truck) => truck.id)
    return !selectedTruckIds.includes(truck.id)
  })
  return new Set(filtered)
})

const onTruckSelectionToggle = () => {
  if (truckList?.value?.length) return

  router.reload({
    only: ['trucks'],
  })
}

const onTruckSelect = (truck: Truck) => {
  if (!props.isAuthorize || !props.isOpen) return

  const isSelected = selectedTrucks.value.includes(truck)
  if (isSelected) {
    const index = selectedTrucks.value.findIndex(
      (struck) => struck.id === truck.id,
    )
    selectedTrucks.value.splice(index, 1)
  } else {
    selectedTrucks.value.push(truck)
  }
}

const canEdit = computed(() => props.isAuthorize && props.isOpen)
</script>

<template>
  <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-x-10">
    <Label class="w-full shrink-0 sm:w-28"> Trucks </Label>
    <Popover @update:open="onTruckSelectionToggle">
      <PopoverTrigger
        as-child
        class="w-full sm:w-[400px]"
      >
        <Button variant="outline">
          <div
            v-if="placeholderTrucks.length"
            class="flex items-center justify-between gap-2 rounded-md text-xs"
          >
            <div class="flex flex-row items-center gap-1 overflow-hidden">
              <div
                v-for="truck in placeholderTrucks"
                :key="truck.id"
                class="flex flex-row gap-2"
              >
                {{ `${truck.plateNo}, ` }}
              </div>
              <span v-if="isSelectedTrucksMoreThanThree">
                and {{ selectedTrucks.length - 3 }} more
              </span>
            </div>
            <Button
              v-if="canEdit"
              variant="ghost"
              size="icon"
              type="button"
              class="ml-1 h-5 w-5 text-muted-foreground"
              @click="selectedTrucks = []"
            >
              <X />
            </Button>
          </div>
          <p
            v-else
            class="font-normal text-muted-foreground"
          >
            Select trucks
          </p>
          <ChevronsUpDown class="ml-auto h-4 w-4" />
        </Button>
      </PopoverTrigger>
      <PopoverContent
        class="w-72 p-0"
        align="start"
      >
        <Command>
          <CommandInput placeholder="Search model or plate number" />
          <CommandList>
            <CommandEmpty> No results found. </CommandEmpty>
            <template v-if="selectedTrucks.length">
              <div :class="['overflow-y-auto', { 'max-h-40': canEdit }]">
                <CommandGroup>
                  <CommandItem
                    v-for="truck in selectedTrucks"
                    :key="truck.id"
                    :value="truck"
                    @select="onTruckSelect(truck)"
                  >
                    <TruckPopoverSelection
                      :truck="truck"
                      is-selected
                    />
                  </CommandItem>
                </CommandGroup>
              </div>
              <CommandSeparator v-if="canEdit" />
            </template>
            <CommandGroup v-if="canEdit">
              <template v-if="!truckList?.length">
                <CommandListPlaceholder />
              </template>
              <template v-else>
                <CommandItem
                  v-for="truck in remainingTrucks"
                  :key="truck.id"
                  :value="truck"
                  @select="onTruckSelect(truck)"
                >
                  <TruckPopoverSelection :truck="truck" />
                </CommandItem>
              </template>
            </CommandGroup>
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
  </div>
</template>
