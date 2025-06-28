<script lang="ts" setup>
import { refDebounced } from '@vueuse/core'
import { Search } from 'lucide-vue-next'
import { computed } from 'vue'
import { Input } from '@/components/ui/input'
import { ResizableHandle, ResizablePanel, ResizablePanelGroup } from '@/components/ui/resizable'
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/components/ui/dropdown-menu'
import { Icon } from '@iconify/vue'
import { Button } from "@/components/ui/button"
import { Separator } from '@/components/ui/separator'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { TooltipProvider } from '@/components/ui/tooltip'
import MailDisplay from './MailDisplay.vue'
import MailList from './MailList.vue'
import { withDefaults } from 'vue'
import type { MailProps } from '@/types/incident'
import { useMailData } from '@/composables/useMailData'
import { useMailFilters } from '@/composables/useMailFilters'
import axios from 'axios'

const handleMarkAsRead = async (id: string) => {
  try {
    const item = mails.value.find(m => m.id === id);
    if (item) item.is_read = true;
    
    await axios.patch(`/incidents/${id}/read`);
    
    await fetchIncidents();
  } catch (err) {
    console.error('Failed to mark as read:', err);
    const item = mails.value.find(m => m.id === id);
    if (item) item.is_read = false;
  }
}
const props = withDefaults(defineProps<MailProps>(), {
  defaultCollapsed: false,
  defaultLayout: () => [30, 70],
  
})
const handleIncidentSubmitted = async () => {
  await fetchIncidents() 
}




const { 
  mails, 
  loading, 
  selectedMail, 
  selectedMails, 
  archiveSelected, 
  fetchIncidents 
} = useMailData()

const { 
  searchValue,
  activeTab,
  sortBy,
  tempFilters,
  activeFilters,
  filteredMailList,
  applyFilters,
  clearFilters
} = useMailFilters(mails)

const debouncedSearch = refDebounced(searchValue, 250)
const selectedMailData = computed(() => mails.value.find(item => item.id === selectedMail.value))

defineEmits(['cancel-compose'])
</script>

<template>
  <div class="border-b w-full mb-5">
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
            <Icon icon="lucide:filter" class="mr-2 size-4" />
            Filter
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="w-64 p-4">
          <div class="space-y-3">
            <div>
              <p class="text-sm font-semibold">Status</p>
              <div class="space-y-1 mt-2">
                <div v-for="status in ['verified', 'for verification', 'dropped']" :key="status">
                  <label class="flex items-center space-x-2 text-sm">
                    <input 
                      type="checkbox" 
                      v-model="tempFilters.statuses" 
                      :value="status"
                      :checked="tempFilters.statuses.includes(status)"
                    />
                    <span>{{ status }}</span>
                  </label>
                </div>
              </div>
            </div>

            <Separator />

            <div>
              <p class="text-sm font-semibold mb-2">Date Reported</p>
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
              <Button variant="outline" @click="clearFilters">Clear</Button>
              <Button @click="applyFilters">Apply Filter</Button>
            </div>
          </div>
        </DropdownMenuContent>
      </DropdownMenu>

      <!-- Sort Dropdown (immediate) -->
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <Button variant="outline">
            <Icon icon="lucide:arrow-up-down" class="mr-2 size-4" />
            Sort
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent>
          <DropdownMenuItem @click="sortBy = 'recent'">Recently Added</DropdownMenuItem>
          <DropdownMenuItem @click="sortBy = 'asc'">A-Z</DropdownMenuItem>
          <DropdownMenuItem @click="sortBy = 'desc'">Z-A</DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>

    <div class="flex items-center gap-2">
      <Button 
        variant="outline" 
        @click="archiveSelected"
        :disabled="selectedMails.length === 0"
        :class="{
          'opacity-50 cursor-not-allowed': selectedMails.length === 0,
          'hover:bg-gray-100': selectedMails.length > 0
        }"
      >
        <Icon icon="lucide:archive" class="mr-2 size-4" />
        Archive
        <span v-if="selectedMails.length > 0" class="ml-1">
          ({{ selectedMails.length }})
        </span>
      </Button>
      <Button variant="outline" @click="fetchIncidents">
        <Icon icon="lucide:download" class="mr-2 size-4" />
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
        <!-- Mail List Panel -->
        <ResizablePanel 
          :default-size="20"
          :min-size="20"
          :max-size="80"
          class="relative overflow-hidden"
        >
          <Tabs default-value="all" class="h-full">
            <div class="flex h-full flex-col">
              <TabsContent value="all" class="mt-0 flex-1 overflow-scroll">
<MailList 
 :on-mark-as-read="handleMarkAsRead"
    @item-click="selectedMail = $event"
                  v-model:selected-mail="selectedMail" 
                  v-model:selected-mails="selectedMails"
                  :items="filteredMailList"
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
         <MailDisplay 
  :mail="selectedMailData" 
  :is-composing="props.isComposing"
  @cancel-compose="$emit('cancel-compose')"
  @incident-submitted="handleIncidentSubmitted"
/>
        </ResizablePanel>
      </ResizablePanelGroup>
    </TooltipProvider>
  </div>
</template>