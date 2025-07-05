<script setup lang="ts">
// const props
</script>

<template>
  <div class="flex items-center gap-x-4">
    <Label
      for="teamLeader"
      class="w-48 shrink-0"
    >
      Team Leader
    </Label>
    <Popover v-model:open="isTeamLeaderPopoverOpen">
      <PopoverTrigger
        class="w-[400px]"
        as-child
      >
        <Button
          variant="outline"
          class=""
        >
          <template v-if="teamLeader">
            <Badge
              class="overflow-hidden truncate text-ellipsis rounded-full font-normal"
              variant="secondary"
              >{{ teamLeader?.fullName }}
            </Badge>
          </template>
          <template v-else>
            <span class="text-muted-foreground"> Select a Team Leader </span>
          </template>
          <ChevronsUpDown class="ml-auto h-4 w-4" />
        </Button>
      </PopoverTrigger>
      <PopoverContent class="p-0">
        <Command>
          <CommandInput placeholder="Search for a team leader" />
          <CommandList>
            <CommandEmpty> No employee found. </CommandEmpty>
            <CommandGroup>
              <CommandItem
                v-for="employee in employees"
                :key="employee.id"
                :value="employee"
                @select="() => handleTeamLeaderChange(employee)"
              >
                <span>
                  {{ employee.fullName }}
                </span>
                <Check
                  :class="
                    cn(
                      'ml-auto h-4 w-4',
                      teamLeader?.id === employee.id
                        ? 'opacity-100'
                        : 'opacity-0',
                    )
                  "
                />
              </CommandItem>
            </CommandGroup>
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
  </div>
</template>
