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
import { Tabs, TabsContent } from '@/components/ui/tabs'
import { TooltipProvider } from '@/components/ui/tooltip'
import { useIncidentData } from '@/composables/useIncidentData'
import { useIncidentFilters } from '@/composables/useIncidentFilters'
import type { Incident, IncidentProps } from '@/types/incident'
import { Icon } from '@iconify/vue'
import { refDebounced } from '@vueuse/core'
import { Search } from 'lucide-vue-next'
import { computed, onMounted, onUnmounted, ref, watch, withDefaults } from 'vue'
import IncidentDisplay from './IncidentDisplay.vue'
import IncidentList from './IncidentList.vue'

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
  markAsRead,
} = useIncidentData()

const {
  searchValue,
  activeTab,
  sortBy,
  tempFilters,
  activeFilters,
  applyFilters,
  clearFilters,
} = useIncidentFilters(incidents)

const debouncedSearch = refDebounced(searchValue, 300)
const incidentsRefreshKey = ref(0)
const selectedIncidentData = computed(() =>
  incidents.value.find((item) => item.id === selectedIncident.value),
)

const isMobile = ref(false)
const checkMobile = () => {
  isMobile.value = window.innerWidth < 768
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
})

const isEditing = computed(() => {
  if (!selectedIncident.value) return false
  const incident = incidents.value.find((i) => i.id === selectedIncident.value)
  return incident?.status === 'draft'
})

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

const handleNoIncident = (updatedIncident: Incident) => {
  const index = incidents.value.findIndex((i) => i.id === updatedIncident.id)
  if (index !== -1) {
    incidents.value[index] = { ...incidents.value[index], ...updatedIncident }
    incidentsRefreshKey.value++
  }
}

const handleCancelEdit = () => {
  fetchIncidents()
}

const handleIncidentUpdate = () => {
  fetchIncidents()
}

const refreshIncidents = () => {
  incidentsRefreshKey.value++
  fetchIncidents()
}

watch([debouncedSearch, activeTab, activeFilters], () => {
  fetchIncidents({
    search: debouncedSearch.value,
    tab: activeTab.value,
    statuses: activeFilters.value.statuses,
    dateFrom: activeFilters.value.dateFrom,
    dateTo: activeFilters.value.dateTo,
  })
})

defineEmits(['cancel-edit', 'no-incident'])
</script>

<template>
  <div
    class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
  >
    <form class="w-full md:max-w-xs">
      <div class="relative">
        <Search class="absolute left-2 top-2.5 size-4 text-muted-foreground" />
        <Input
          v-model="searchValue"
          placeholder="Search incidents..."
          class="pl-8"
        />
      </div>
    </form>

    <div class="flex flex-wrap gap-2">
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <Button
            variant="outline"
            class="flex-1 md:flex-initial"
          >
            <Icon
              icon="lucide:filter"
              class="mr-2 size-4"
            />
            Filter
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent
          class="w-64 p-4"
          :side="isMobile ? 'bottom' : 'right'"
          :align="isMobile ? 'center' : 'end'"
        >
          <div class="space-y-3">
            <div>
              <p class="text-sm font-semibold">Status</p>
              <div class="mt-2 space-y-1">
                <div
                  v-for="status in [
                    'draft',
                    'verified',
                    'for verification',
                    'dropped',
                    'no_incident',
                  ]"
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

      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <Button
            variant="outline"
            class="flex-1 md:flex-initial"
          >
            <Icon
              icon="lucide:arrow-up-down"
              class="mr-2 size-4"
            />
            Sort
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent
          :side="isMobile ? 'bottom' : 'right'"
          :align="isMobile ? 'center' : 'end'"
        >
          <DropdownMenuItem @click="sortBy = 'recent'"
            >Recently Added</DropdownMenuItem
          >
          <DropdownMenuItem @click="sortBy = 'asc'">A-Z</DropdownMenuItem>
          <DropdownMenuItem @click="sortBy = 'desc'">Z-A</DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>

    <div class="flex flex-wrap gap-2">
      <Button
        variant="outline"
        @click="archiveSelected"
        :disabled="selectedIncidents.length === 0"
        :class="{
          'cursor-not-allowed opacity-50': selectedIncidents.length === 0,
          'hover:bg-gray-100': selectedIncidents.length > 0,
        }"
        class="flex-1 md:flex-initial"
      >
        <Icon
          icon="lucide:archive"
          class="mr-2 size-4"
        />
        <span class="hidden md:inline">Archive</span>
        <span
          v-if="selectedIncidents.length > 0"
          class="ml-1"
        >
          ({{ selectedIncidents.length }})
        </span>
      </Button>
      <Button
        variant="outline"
        @click="refreshIncidents"
        class="flex-1 md:flex-initial"
      >
        <Icon
          icon="lucide:refresh-cw"
          class="mr-2 size-4"
        />
        <span class="hidden md:inline">Refresh</span>
      </Button>
    </div>
  </div>

  <div class="pt-5">
    <TooltipProvider :delay-duration="0">
      <!-- Mobile view: Show either list or detail -->
      <div
        v-if="isMobile"
        class="h-[calc(100vh-180px)] overflow-auto rounded-lg border"
      >
        <div v-if="!selectedIncident">
          <IncidentList
            :key="incidentsRefreshKey"
            :on-mark-as-read="handleMarkAsRead"
            @item-click="selectedIncident = $event"
            v-model:selected-incident="selectedIncident"
            v-model:selected-incidents="selectedIncidents"
            :items="incidents"
            :loading="loading"
            :search-query="debouncedSearch"
            :active-tab="activeTab"
            :selected-statuses="activeFilters.statuses"
            :date-from="activeFilters.dateFrom"
            :date-to="activeFilters.dateTo"
            :sort-by="sortBy"
          />
        </div>
        <div
          v-else
          class="h-full"
        >
          <div class="flex items-center border-b p-2">
            <Button
              variant="ghost"
              @click="selectedIncident = null"
              class="mr-2"
            >
              <Icon
                icon="lucide:arrow-left"
                class="size-4"
              />
            </Button>
            <h3 class="text-lg font-semibold">Incident Details</h3>
          </div>
          <IncidentDisplay
            :key="selectedIncident"
            :incident="selectedIncidentData"
            :is-editing="isEditing"
            @cancel-edit="handleCancelEdit"
            @no-incident="handleNoIncident"
            @incident-updated="handleIncidentUpdate"
          />
        </div>
      </div>

      <!-- Desktop view: Use resizable panels -->
      <ResizablePanelGroup
        v-else
        direction="horizontal"
        class="max-h-[800px] min-h-[500px] w-full rounded-lg border"
      >
        <ResizablePanel
          :default-size="props.defaultLayout[0]"
          :min-size="20"
          :max-size="80"
          class="relative overflow-auto"
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
                  :key="incidentsRefreshKey"
                  :on-mark-as-read="handleMarkAsRead"
                  @item-click="selectedIncident = $event"
                  v-model:selected-incident="selectedIncident"
                  v-model:selected-incidents="selectedIncidents"
                  :items="incidents"
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
          :default-size="props.defaultLayout[1]"
          :min-size="30"
          class="relative overflow-auto"
        >
          <IncidentDisplay
            :key="selectedIncident"
            :incident="selectedIncidentData"
            :is-editing="isEditing"
            @cancel-edit="handleCancelEdit"
            @no-incident="handleNoIncident"
            @incident-updated="handleIncidentUpdate"
          />
        </ResizablePanel>
      </ResizablePanelGroup>
    </TooltipProvider>
  </div>
</template>
