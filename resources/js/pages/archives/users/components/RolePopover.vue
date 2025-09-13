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
import UserRoleBadge from '@/components/UserRoleBadge.vue'
import { userRoles } from '@/constants/user-role'
import { Check, Circle } from 'lucide-vue-next'
import { computed } from 'vue'
import { useArchivedUserTable } from '../../composables/useArchivedUserTable'

const { dataTable, onRoleSelect, onClearRoles } = useArchivedUserTable()

const selectedRoles = computed(() => new Set(dataTable.roles.value))
</script>

<template>
  <Popover>
    <PopoverTrigger as-child>
      <Button
        variant="ghost"
        class="ml-1"
      >
        <Circle class="mr-2" />
        Role
        <Badge
          v-if="selectedRoles.size"
          variant="secondary"
          class="rounded-full font-extrabold"
        >
          {{ selectedRoles.size }}
        </Badge>
      </Button>
    </PopoverTrigger>
    <PopoverContent
      class="w-full p-0"
      align="start"
    >
      <Command>
        <CommandInput placeholder="Search user role" />
        <CommandList>
          <CommandEmpty> No results found. </CommandEmpty>
          <CommandGroup class="max-h-64 overflow-auto">
            <CommandItem
              v-for="{ id } in userRoles"
              :key="id"
              :value="id"
              class="cursor-pointer"
              @select="onRoleSelect(id)"
            >
              <div class="flex flex-row items-center gap-3">
                <div
                  class="flex h-4 w-4 items-center justify-center rounded-sm border border-primary"
                  :class="[
                    selectedRoles.has(id)
                      ? 'bg-primary text-primary-foreground'
                      : 'opacity-50 [&_svg]:invisible',
                  ]"
                >
                  <Check class="h-4 w-4" />
                </div>
                <UserRoleBadge :role-name="id" />
              </div>
            </CommandItem>
          </CommandGroup>
          <template v-if="selectedRoles.size">
            <CommandSeparator />
            <CommandGroup>
              <CommandItem
                value="clear"
                class="cursor-pointer justify-center text-center"
                @select="onClearRoles"
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
