<script lang="ts" setup>
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Input } from '@/components/ui/input'
import {
  ResizableHandle,
  ResizablePanel,
  ResizablePanelGroup,
} from '@/components/ui/resizable'
import { Separator } from '@/components/ui/separator'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { TooltipProvider } from '@/components/ui/tooltip'
import { useIncidentData } from '@/composables/useIncidentData'
import { useIncidentFilters } from '@/composables/useIncidentFilters'
import type { IncidentProps } from '@/types/incident'
import { Icon } from '@iconify/vue'
import { refDebounced } from '@vueuse/core'
import { Search } from 'lucide-vue-next'
import { computed, withDefaults, watch } from 'vue'
import IncidentDisplay from './IncidentDisplay.vue'
import IncidentList from './IncidentList.vue'

const handleMarkAsRead = async (id: string) => {
  try {
    const item = incidents.value.find((m) => m.id === id)
    if (item) item.is_read = true
    await markAsRead(id)
  } catch (err) {
    console.error('Failed to mark as read:', err)
    const item = incidents.value.find((m) => m.id === id)
    if (item) item.is_read = false
  }
}

const props = withDefaults(defineProps<IncidentProps>(), {
  defaultCollapsed: false,
  defaultLayout: () => [30, 70],
})

const {
  incidents,
  loading,
  selectedIncident,
  selectedIncidents,
  archiveSelected,
  fetchIncidents,
  markAsRead
} = useIncidentData()

const {
  searchValue,
  activeTab,
  sortBy,
  tempFilters,
  activeFilters,
  filteredIncidentList,
  applyFilters,
  clearFilters,
} = useIncidentFilters(incidents)

const debouncedSearch = refDebounced(searchValue, 250)
const selectedIncidentData = computed(() =>
  incidents.value.find((item) => item.id === selectedIncident.value),
)

// Remove isComposing and replace with isEditing
const isEditing = computed(() => {
  if (!selectedIncident.value) return false
  const incident = incidents.value.find(i => i.id === selectedIncident.value)
  return incident?.status === 'draft'
})

// Watch for filter changes and refetch
watch([debouncedSearch, activeTab, activeFilters], () => {
  fetchIncidents({
    search: debouncedSearch.value,
    tab: activeTab.value,
    statuses: activeFilters.value.statuses,
    dateFrom: activeFilters.value.dateFrom,
    dateTo: activeFilters.value.dateTo
  })
})

// Update the emits to match new functionality
defineEmits(['cancel-edit', 'no-incident'])
</script>

<template>
  <div class="mb-5 w-full border-b">
    <Tabs v-model="activeTab">
      <TabsList>
        <TabsTrigger value="All">All</TabsTrigger>
        <TabsTrigger value="Waste Management">Waste Management</TabsTrigger>
        <TabsTrigger value="IT Services">IT Services</TabsTrigger>
        <TabsTrigger value="Other Services">Other Services</TabsTrigger>
      </TabsList>
    </Tabs>
  </div>

  <div class="flex items-center justify-between gap-2">
    <!-- Search (immediate) -->
    <form class="w-full max-w-xs">
      <div class="relative">
        <Search class="absolute left-2 top-2.5 size-4 text-muted-foreground" />
        <Input
          v-model="searchValue"
          placeholder="Search incidents..."
          class="pl-8"
        />
      </div>
    </form>

    <div class="flex items-center gap-2">
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <Button variant="outline">
            <Icon
              icon="lucide:filter"
              class="mr-2 size-4"
            />
            Filter
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="w-64 p-4">
          <div class="space-y-3">
            <div>
              <p class="text-sm font-semibold">Status</p>
              <div class="mt-2 space-y-1">
                <!-- Add 'draft' and 'no_incident' to status filters -->
                <div
                  v-for="status in ['draft', 'verified', 'for verification', 'dropped', 'no_incident']"
                  :key="status"
                >
                  <label class="flex items-center space-x-2 text-sm">
                    <input
                      type="checkbox"
                      v-model="tempFilters.statuses"
                      :value="status"
                      :checked="tempFilters.statuses.includes(status)"
                    />
                    <span>{{ status.replace('_', ' ').toUpperCase() }}</span>
                  </label>
                </div>
              </div>
            </div>

            <Separator />

            <div>
              <p class="mb-2 text-sm font-semibold">Date Reported</p>
              <div class="space-y-2">
                <Input
                  type="date"
                  v-model="tempFilters.dateFrom"
                  placeholder="From"
                />
                <Input
                  type="date"
                  v-model="tempFilters.dateTo"
                  placeholder="To"
                />
              </div>
            </div>

            <div class="flex justify-end gap-2 pt-3">
              <Button
                variant="outline"
                @click="clearFilters"
                >Clear</Button
              >
              <Button @click="applyFilters">Apply Filter</Button>
            </div>
          </div>
        </DropdownMenuContent>
      </DropdownMenu>

      <!-- Sort Dropdown (immediate) -->
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <Button variant="outline">
            <Icon
              icon="lucide:arrow-up-down"
              class="mr-2 size-4"
            />
            Sort
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent>
          <DropdownMenuItem @click="sortBy = 'recent'"
            >Recently Added</DropdownMenuItem
          >
          <DropdownMenuItem @click="sortBy = 'asc'">A-Z</DropdownMenuItem>
          <DropdownMenuItem @click="sortBy = 'desc'">Z-A</DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>

    <div class="flex items-center gap-2">
      <Button
        variant="outline"
        @click="archiveSelected"
        :disabled="selectedIncidents.length === 0"
        :class="{
          'cursor-not-allowed opacity-50': selectedIncidents.length === 0,
          'hover:bg-gray-100': selectedIncidents.length > 0,
        }"
      >
        <Icon
          icon="lucide:archive"
          class="mr-2 size-4"
        />
        Archive
        <span
          v-if="selectedIncidents.length > 0"
          class="ml-1"
        >
          ({{ selectedIncidents.length }})
        </span>
      </Button>
      <Button
        variant="outline"
        @click="fetchIncidents"
      >
        <Icon
          icon="lucide:download"
          class="mr-2 size-4"
        />
        Export
      </Button>
    </div>
  </div>

  <div class="pt-5">
    <TooltipProvider :delay-duration="0">
      <ResizablePanelGroup
        direction="horizontal"
        class="max-h-[800px] min-h-[500px] w-full rounded-lg border"
      >
        <!-- Incident List Panel -->
        <ResizablePanel
          :default-size="20"
          :min-size="20"
          :max-size="80"
          class="relative overflow-hidden"
        >
          <Tabs
            default-value="all"
            class="h-full"
          >
            <div class="flex h-full flex-col">
              <TabsContent
                value="all"
                class="mt-0 flex-1 overflow-scroll"
              >
                <IncidentList
                  :on-mark-as-read="handleMarkAsRead"
                  @item-click="selectedIncident = $event"
                  v-model:selected-incident="selectedIncident"
                  v-model:selected-incidents="selectedIncidents"
                  :items="filteredIncidentList"
                  :loading="loading"
                  :search-query="debouncedSearch"
                  :active-tab="activeTab"
                  :selected-statuses="activeFilters.statuses"
                  :date-from="activeFilters.dateFrom"
                  :date-to="activeFilters.dateTo"
                  :sort-by="sortBy"
                />
              </TabsContent>
            </div>
          </Tabs>
        </ResizablePanel>

        <ResizableHandle />

        <ResizablePanel
          :default-size="defaultLayout[1]"
          :min-size="30"
          class="relative overflow-auto"
        >
          <IncidentDisplay
            :incident="selectedIncidentData"
            :is-editing="isEditing"
            @cancel-edit="$emit('cancel-edit')"
            @no-incident="$emit('no-incident')"
          />
        </ResizablePanel>
      </ResizablePanelGroup>
    </TooltipProvider>
  </div>
</template>