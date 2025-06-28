<script lang="ts" setup>
import { refDebounced } from '@vueuse/core'
import { Search } from 'lucide-vue-next'
import { computed, ref, onMounted, watch } from 'vue'
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
import axios from 'axios'

interface Mail {
  id: string
  subject: string
  description: string
  plainText: string
  html: string
  read: boolean
  occured_at: string 
  location: string
  infraction_type: string
  status: string
  created_by: {
    id: number
    name: string
    email: string
  }
  involved_employees: Array<{
    id: number
    name: string
  }>
  job_order?: {
    id: number
    serviceable_type: string
    status: string
  }
}

interface MailProps {
  defaultLayout?: number[]
  defaultCollapsed?: boolean
  navCollapsedSize: number
  isComposing?: boolean
}

const props = withDefaults(defineProps<MailProps>(), {
  defaultCollapsed: false,
  defaultLayout: () => [30, 70],
})

// State management
const isCollapsed = ref(props.defaultCollapsed)
const selectedMail = ref<string>()
const mails = ref<Mail[]>([])
const loading = ref(false)
const error = ref(null)

// Immediate filter states (apply automatically)
const searchValue = ref('')
const debouncedSearch = refDebounced(searchValue, 250)
const activeTab = ref('All')
const sortBy = ref('recent')

// Manual filter states (require apply button)
const tempFilters = ref({
  statuses: [] as string[],
  dateFrom: '',
  dateTo: ''
})

const activeFilters = ref({
  statuses: [] as string[],
  dateFrom: '',
  dateTo: ''
})

// Fetch incidents from API
const fetchIncidents = async () => {
  loading.value = true
  try {
    const response = await axios.get('/incidents')
    mails.value = response.data.map(transformIncidentToMail)
    if (mails.value.length > 0) {
      selectedMail.value = mails.value[0].id
    }
  } catch (err) {
    error.value = err
    console.error('Failed to fetch incidents:', err)
  } finally {
    loading.value = false
  }
}

const transformIncidentToMail = (incident: any): Mail => {
  const jobOrder = incident.job_order || {}
  const serviceType = jobOrder.serviceable_type || ''
  
  let labels: string[] = []
  if (serviceType === 'form4') {
    labels = ['Waste Management']
  } else if (serviceType === 'form5') {
    labels = ['Other Services']
  } else if (serviceType === 'it_service') {
    labels = ['IT Services']
  }

  return {
    id: incident.id.toString(),
    subject: incident.subject,
    description: incident.description,
    html: incident.description,
    plainText: incident.plainText,
    read: false,
    occured_at: incident.occured_at,
    location: incident.location,
    infraction_type: incident.infraction_type,
    status: incident.status,
    created_by: incident.created_by || {  
      id: 0,
      name: 'Unknown',
      email: ''
    },
    involved_employees: incident.involved_employees || [],
    job_order: incident.job_order ? {
      id: incident.job_order.id,
      serviceable_type: incident.job_order.serviceable_type,
      status: incident.job_order.status
    } : undefined
  }
}

// Apply manual filters
const applyFilters = () => {
  activeFilters.value = {
    statuses: [...tempFilters.value.statuses],
    dateFrom: tempFilters.value.dateFrom,
    dateTo: tempFilters.value.dateTo
  }
}

// Clear manual filters
const clearFilters = () => {
  tempFilters.value = {
    statuses: [],
    dateFrom: '',
    dateTo: ''
  }
  applyFilters()
}

// Filter and sort computed properties
const filteredMailList = computed(() => {
  let output = [...mails.value]
  
  // Apply search filter (immediate)
  if (debouncedSearch.value.trim()) {
    const searchTerm = debouncedSearch.value.toLowerCase()
    output = output.filter(item => 
      item.subject.toLowerCase().includes(searchTerm) ||
      item.plainText.toLowerCase().includes(searchTerm) ||
      item.location?.toLowerCase().includes(searchTerm) ||
      item.infraction_type?.toLowerCase().includes(searchTerm) ||
      item.involved_employees?.some(emp => 
        emp.name.toLowerCase().includes(searchTerm))
)}

  // Apply tab filter (immediate)
  if (activeTab.value !== 'All') {
    output = output.filter(item => {
      if (!item.job_order) return false
      
      if (activeTab.value === 'Waste Management') {
        return item.job_order.serviceable_type === 'form4'
      } else if (activeTab.value === 'IT Services') {
        return item.job_order.serviceable_type === 'it_service'
      } else if (activeTab.value === 'Other Services') {
        return item.job_order.serviceable_type === 'form5'
      }
      return true
    })
  }

  // Apply status filter (manual)
  if (activeFilters.value.statuses.length > 0) {
    output = output.filter(item => 
      activeFilters.value.statuses.includes(item.status))
  }

  // Apply date filter (manual)
  if (activeFilters.value.dateFrom || activeFilters.value.dateTo) {
    output = output.filter(item => {
      const itemDate = new Date(item.occured_at)
      const fromDate = activeFilters.value.dateFrom ? new Date(activeFilters.value.dateFrom) : null
      const toDate = activeFilters.value.dateTo ? new Date(activeFilters.value.dateTo) : null
      
      return (
        (!fromDate || itemDate >= fromDate) &&
        (!toDate || itemDate <= toDate)
      )
    })
  }

  // Apply sorting (immediate)
  if (sortBy.value === 'recent') {
    output.sort((a, b) => new Date(b.occured_at).getTime() - new Date(a.occured_at).getTime())
  } else if (sortBy.value === 'asc') {
    output.sort((a, b) => a.subject.localeCompare(b.subject))
  } else if (sortBy.value === 'desc') {
    output.sort((a, b) => b.subject.localeCompare(a.subject))
  }

  return output
})

const unreadMailList = computed(() => filteredMailList.value.filter(item => !item.read))
const selectedMailData = computed(() => mails.value.find(item => item.id === selectedMail.value))

const refreshData = () => {
  fetchIncidents()
}

// Initial data load
onMounted(fetchIncidents)

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

    <!-- Filter & Sort -->
    <div class="flex items-center gap-2">
      <!-- Filter Dropdown (manual apply) -->
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
                <div v-for="status in ['Pending', 'Resolved', 'Closed']" :key="status">
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

    <!-- Archive and Export -->
    <div class="flex items-center gap-2">
      <Button variant="outline" @click="refreshData">
        <Icon icon="lucide:refresh-cw" class="mr-2 size-4" />
        Refresh
      </Button>
      <Button>
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
    v-model:selected-mail="selectedMail" 
    :items="mails"
    :loading="loading"
    :search-query="debouncedSearch"
    :active-tab="activeTab"
    :selected-statuses="activeFilters.statuses"
    :date-from="activeFilters.dateFrom"
    :date-to="activeFilters.dateTo"
    :sort-by="sortBy"
  />
              </TabsContent>
              <TabsContent value="unread" class="mt-0 flex-1 overflow-auto">
               <MailList 
    v-model:selected-mail="selectedMail" 
    :items="mails"
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
            :is-composing="isComposing"
            @cancel-compose="$emit('cancel-compose')"
          />
        </ResizablePanel>
      </ResizablePanelGroup>
    </TooltipProvider>
  </div>
</template>