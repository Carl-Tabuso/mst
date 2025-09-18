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
} from '@/components/ui/command'
import { Input } from '@/components/ui/input'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { getInitials } from '@/composables/useInitials'
import { type Employee } from '@/types'
import { Check, ChevronsUpDown } from 'lucide-vue-next'
import { computed } from 'vue'

interface Form5Item {
  item_name: string
  quantity: number
}

interface Props {
  employees?: Employee[]
  isEditing?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  employees: () => [],
  isEditing: false,
})

const assignedPerson = defineModel<number | null>('assignedPerson')
const items = defineModel<Form5Item[]>('items', {
  required: true,
  default: () => [],
})
const purpose = defineModel<string>('purpose', { required: true, default: '' })

const purposeOptions = [
  'Payment',
  'Process and Pick Up Document',
  'Bank Transaction',
  'Document Delivery',
  'Document Pickup',
  'Bid Bond Pickup',
  'Payment Transaction',
  'Bid Bond Submission',
]

const selectedEmployee = computed(() =>
  props.employees.find(e => e.id === assignedPerson.value)
)

const addItem = () => {
  items.value = [...items.value, { item_name: '', quantity: 1 }]
}

const removeItem = (index: number) => {
  if (items.value.length > 1) {
    items.value = items.value.filter((_, i) => i !== index)
  } else if (props.isEditing) {
    items.value = [{ item_name: '', quantity: 1 }]
  }
}

if (items.value.length === 0 && props.isEditing) {
  addItem()
}
</script>

<template>
  <div class="pt-6 mt-8 space-y-6 border-t border-gray-200">
    <div class="mb-6">
      <div class="text-xl font-semibold leading-6">Form 5 Details</div>
      <p class="text-sm text-muted-foreground">
        Additional information required for Form 5 job orders.
      </p>
    </div>

    <div class="grid items-center grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">
      <!-- Assigned Person -->
      <div class="space-y-3">
        <label class="text-sm font-medium">Assigned Person</label>
        <Popover>
          <PopoverTrigger
            as-child
            :disabled="!isEditing"
          >
            <Button
              variant="outline"
              class="justify-start w-full"
            >
              <template v-if="assignedPerson && selectedEmployee">
                <Avatar class="h-7 w-7 shrink-0">
                  <AvatarImage
                    v-if="selectedEmployee.account?.avatar"
                    :src="selectedEmployee.account.avatar"
                    :alt="selectedEmployee.fullName"
                  />
                  <AvatarFallback>
                    {{ getInitials(selectedEmployee.fullName) }}
                  </AvatarFallback>
                </Avatar>
                <span class="ml-2 text-xs">
                  {{ selectedEmployee.fullName }}
                </span>
              </template>
              <span
                v-else
                class="font-normal text-muted-foreground"
              >
                Select assigned person
              </span>
              <ChevronsUpDown class="w-4 h-4 ml-auto opacity-50" />
            </Button>
          </PopoverTrigger>
          <PopoverContent
            class="w-full p-0"
            align="start"
          >
            <Command>
              <CommandInput placeholder="Search assigned person" />
              <CommandList>
                <CommandEmpty>No results found.</CommandEmpty>
                <CommandGroup>
                  <CommandItem
                    v-for="employee in props.employees"
                    :key="employee.id"
                    :value="employee.id"
                    class="cursor-pointer"
                    @select="assignedPerson = employee.id"
                  >
                    <Avatar class="h-7 w-7">
                      <AvatarImage
                        v-if="employee.account?.avatar"
                        :src="employee.account.avatar"
                        :alt="employee.fullName"
                      />
                      <AvatarFallback>
                        {{ getInitials(employee.fullName) }}
                      </AvatarFallback>
                    </Avatar>
                    <div class="ml-2 text-sm">
                      {{ employee.fullName }}
                    </div>
                    <Check
                      :class="[
                        'ml-auto h-4 w-4',
                        employee.id === assignedPerson ? 'opacity-100' : 'opacity-0',
                      ]"
                    />
                  </CommandItem>
                </CommandGroup>
              </CommandList>
            </Command>
          </PopoverContent>
        </Popover>
      </div>

      <div class="space-y-3">
        <label class="text-sm font-medium">Purpose</label>
        <Popover>
          <PopoverTrigger
            as-child
            :disabled="!isEditing"
          >
            <Button
              variant="outline"
              class="justify-start w-full"
            >
              <span
                v-if="purpose"
                class="truncate"
              >
                {{ purpose }}
              </span>
              <span
                v-else
                class="font-normal text-muted-foreground"
              >
                Select purpose
              </span>
              <ChevronsUpDown class="w-4 h-4 ml-auto opacity-50" />
            </Button>
          </PopoverTrigger>
          <PopoverContent
            class="w-full p-0"
            align="start"
          >
            <Command>
              <CommandInput placeholder="Search purpose" />
              <CommandList>
                <CommandEmpty>No results found.</CommandEmpty>
                <CommandGroup>
                  <CommandItem
                    v-for="option in purposeOptions"
                    :key="option"
                    :value="option"
                    class="cursor-pointer"
                    @select="purpose = option"
                  >
                    <span class="truncate">
                      {{ option }}
                    </span>
                    <Check
                      :class="[
                        'ml-auto h-4 w-4',
                        option === purpose ? 'opacity-100' : 'opacity-0',
                      ]"
                    />
                  </CommandItem>
                </CommandGroup>
              </CommandList>
            </Command>
          </PopoverContent>
        </Popover>
      </div>
    </div>

    <div class="space-y-3">
      <label class="text-sm font-medium">Items</label>
      <div class="space-y-3">
        <div
          v-for="(item, index) in items"
          :key="index"
          class="flex items-center gap-3"
        >
          <Input
            v-model="item.item_name"
            :disabled="!isEditing"
            placeholder="Item name"
            class="flex-1"
            :required="isEditing"
          />
          <Input
            v-model.number="item.quantity"
            type="number"
            min="1"
            :disabled="!isEditing"
            placeholder="Qty"
            class="w-20"
            :required="isEditing"
          />
          <Button
            v-if="isEditing"
            type="button"
            @click="removeItem(index)"
            variant="destructive"
            class="px-3 py-2"
            :disabled="items.length <= 1"
          >
            Remove
          </Button>
        </div>

        <Button
          v-if="isEditing"
          type="button"
          @click="addItem"
          variant="outline"
          class="mt-2"
        >
          + Add Another Item
        </Button>

        <div
          v-if="!isEditing"
          class="mt-4"
        >
          <div
            v-if="items.length > 0"
            class="space-y-2"
          >
            <div
              v-for="(item, index) in items"
              :key="index"
              class="flex justify-between py-2 border-b"
            >
              <span class="font-medium">
                {{ item.item_name || 'Unnamed item' }}
              </span>
              <span class="text-gray-600">Qty: {{ item.quantity }}</span>
            </div>
          </div>
          <div
            v-else
            class="py-2 italic text-gray-500"
          >
            No items added
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


