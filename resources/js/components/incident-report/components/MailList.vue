<script lang="ts" setup>
import { format } from 'date-fns'
import { cn } from '@/lib/utils'
import { Badge } from '@/components/ui/badge'
import { Clock } from 'lucide-vue-next'
import { ScrollArea } from '@/components/ui/scroll-area'
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
interface Mail {
  id: string
  subject: string
  description: string
  plainText: string
  occured_at: string 
  location: string
  infraction_type: string
    read: boolean
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
interface MailListProps {
  initialItems?: Mail[]
  searchQuery?: string
  activeTab?: string
  selectedStatuses?: string[]
  dateFrom?: string
  dateTo?: string
  sortBy?: string
}

const props = defineProps<MailListProps>()
const selectedMail = defineModel<string>('selectedMail', { required: false })
const items = ref<Mail[]>(props.initialItems || [])
const loading = ref(false)
const error = ref(null)

const transformIncidentToMail = (incident: any): Mail => {
    let label = 'Other';
  if (incident.job_order?.serviceable_type === 'form4') {
    label = 'Waste Management';
  } else if (incident.job_order?.serviceable_type === 'form5') {
    label = 'Other Services';
  } else if (incident.job_order?.serviceable_type === 'it_service') {
    label = 'IT Services';
  }
  return {
    id: incident.id.toString(),
    name: incident.name,
    email: incident.email,
    subject: incident.subject,
    html: incident.description,
    plainText: incident.plainText,
 occured_at: incident.occured_at,
  read: incident.is_read || false,
  labels: [label],
     status: incident.status,
    jobOrder: incident.job_order ? {
      id: incident.job_order.id,
      serviceable_type: incident.job_order.serviceable_type,
      status: incident.job_order.status,
      creator: incident.job_order.creator
    } : null,
    involved_employees: incident.involved_employees || []
  }
}
onMounted(async () => {
  loading.value = true
  try {
    const response = await axios.get('/incidents')
    items.value = response.data.map(transformIncidentToMail)
  } catch (err) {
    console.error('Failed to fetch incidents:', err)
    error.value = err
  } finally {
    loading.value = false
  }
})

const fetchIncidents = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/incidents') // Note the /api prefix
    items.value = response.data.map(transformIncidentToMail)
  } catch (err) {
    console.error('Failed to fetch incidents:', err)
    error.value = err
  } finally {
    loading.value = false
  }
}
const markAsRead = async (itemId: string) => {
  try {
    await axios.patch(`/incidents/${itemId}/read`);
    const item = items.value.find(item => item.id === itemId);
    if (item) {
      item.read = true;
    }
  } catch (err) {
    console.error('Failed to mark as read:', err);
  }
};
function getBadgeVariantFromLabel(label: string) {
  if (['waste management'].includes(label.toLowerCase())) {
    return 'default'
  }
  if (['it services'].includes(label.toLowerCase())) {
    return 'secondary'
  }
  if (['other services'].includes(label.toLowerCase())) {
    return 'tertiary'
  }
  return 'secondary'
}
function getStatusBadgeVariant(status: string) {
  if (status.toLowerCase() === 'for verification') {
    return 'tertiary'
  }
  if (status.toLowerCase() === 'verified') {
    return 'default'
  }
  if (status.toLowerCase() === 'pending') {
    return 'secondary'
  }
  return 'outline' 
}
const handleItemClick = async (itemId: string) => {
  selectedMail.value = itemId;
  const item = items.value.find(item => item.id === itemId);
  if (item && !item.read) {
    await markAsRead(itemId);
  }
};
const filteredItems = computed(() => {
  let result = [...items.value]
  
  // Apply search filter
  if (props.searchQuery?.trim()) {
    const searchTerm = props.searchQuery.toLowerCase()
    result = result.filter(item => 
      item.subject.toLowerCase().includes(searchTerm) ||
      item.plainText.toLowerCase().includes(searchTerm) ||
      item.location?.toLowerCase().includes(searchTerm) ||
      item.infraction_type?.toLowerCase().includes(searchTerm) ||
      item.involved_employees?.some(emp => 
        emp.name.toLowerCase().includes(searchTerm)
    ))
  }

  // Apply tab filter
// Apply tab filter
if (props.activeTab && props.activeTab !== 'All') {
  result = result.filter(item => 
    item.labels?.includes(props.activeTab)
  )
}
  // Apply status filter
  if (props.selectedStatuses?.length > 0) {
    result = result.filter(item => 
      props.selectedStatuses.includes(item.status)
    )
  }

 // In filteredItems computed property
if (props.dateFrom || props.dateTo) {
  result = result.filter(item => {
    try {
      // Parse dates and convert to UTC midnight/end-of-day
      const itemDate = new Date(item.occured_at)
      const itemDateUTC = new Date(Date.UTC(
        itemDate.getFullYear(),
        itemDate.getMonth(),
        itemDate.getDate()
      ))
      
      // Parse filter dates as UTC
      const fromDate = props.dateFrom 
        ? new Date(Date.UTC(
            new Date(props.dateFrom).getFullYear(),
            new Date(props.dateFrom).getMonth(),
            new Date(props.dateFrom).getDate()
          ))
        : null
      
      const toDate = props.dateFrom 
        ? new Date(Date.UTC(
            new Date(props.dateTo).getFullYear(),
            new Date(props.dateTo).getMonth(),
            new Date(props.dateTo).getDate(),
            23, 59, 59
          ))
        : null

      console.log('Comparing dates:', {
        itemDate: itemDateUTC.toISOString(),
        fromDate: fromDate?.toISOString(),
        toDate: toDate?.toISOString()
      })

      return (
        (!fromDate || itemDateUTC >= fromDate) &&
        (!toDate || itemDateUTC <= toDate)
      )
    } catch (e) {
      console.error('Error parsing date:', e)
      return false
    }
  })
}
  if (props.sortBy === 'recent') {
    result.sort((a, b) => new Date(b.occured_at).getTime() - new Date(a.occured_at).getTime())
  } else if (props.sortBy === 'asc') {
    result.sort((a, b) => a.subject.localeCompare(b.subject))
  } else if (props.sortBy === 'desc') {
    result.sort((a, b) => b.subject.localeCompare(a.subject))
  }

  return result
})

</script>

<template>
  <ScrollArea class="h-screen flex">
    <div class="flex-1 flex flex-col pt-0">
      <TransitionGroup name="list" appear>
        <button
          v-for="item of filteredItems"
          :key="item.id"
          :class="cn(
            'flex flex-col items-start gap-2 border p-3 text-left text-sm transition-all hover:bg-blue-100',
            (selectedMail === item.id || !item.read) && 'bg-blue-100'
          )"
          @click="handleItemClick(item.id)"
        >
          <div class="flex w-full flex-col gap-1">
            <div class="flex items-center">
              <div class="flex items-center gap-2">
                <div class="font-semibold">
                  {{ item.subject }}
                </div>
              </div>
              <div
                :class="cn(
                  'ml-auto text-xs',
                  selectedMail === item.id
                    ? 'text-foreground'
                    : 'text-muted-foreground',
                )"
              >
              </div>
            </div>
          </div>
          <div
            :class="cn(
              'line-clamp-1 text-xs text-muted-foreground',
              !item.read && 'font-bold text-blue-900'
            )"
          >
            {{ item.plainText }}
          </div>
          <div
            :class="cn(
              'flex items-center gap-2 whitespace-nowrap text-zinc-400',
              !item.read && 'font-semibold'
            )"
          >
            <Clock class="size-4"></Clock> 
{{ format(new Date(item.occured_at), "MMMM d, yyyy 'at' hh:mmaaa") }}            <Badge 
              v-for="label of item.labels" 
              :key="label" 
              :variant="getBadgeVariantFromLabel(label)"
            >
              {{ label }}
            </Badge>
                   <Badge 
              v-if="item.status"
              :variant="getStatusBadgeVariant(item.status)"
            >
              {{ item.status }}
            </Badge>
          </div>
        </button>
      </TransitionGroup>
    </div>
  </ScrollArea>
</template>
<style scoped>
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateY(15px);
}

.list-leave-active {
  position: absolute;
}
</style>
