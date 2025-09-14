<script lang="ts" setup>
import { Badge } from '@/components/ui/badge'
import { ScrollArea } from '@/components/ui/scroll-area'
import { cn } from '@/lib/utils'
import type { Incident } from '@/types/incident'
import { format } from 'date-fns'
import { Clock } from 'lucide-vue-next'
import { computed } from 'vue'

interface IncidentListProps {
  items: Incident[]
  searchQuery?: string
  activeTab?: string
  selectedStatuses?: string[]
  dateFrom?: string
  dateTo?: string
  sortBy?: string
  onMarkAsRead?: (id: string) => Promise<void>
}

const emit = defineEmits(['itemClick'])
const props = defineProps<IncidentListProps>()
const selectedIncident = defineModel<string | number>('selectedIncident', { required: false })
const selectedIncidents = defineModel<Array<string | number>>('selectedIncidents', { default: [] })


const getStatusBadgeVariant = (status: string) => {
  const statusMap: Record<string, string> = {
    'for verification': 'tertiary',
    'verified': 'default',
    'pending': 'secondary',
    'draft': 'outline',
    'no_incident': 'secondary',
  }
  return statusMap[status.toLowerCase()] || 'outline'
}

const filteredItems = computed(() => {
  let result = [...props.items]
  const searchTerm = props.searchQuery?.trim().toLowerCase()

  if (searchTerm) {
    result = result.filter(
      (item) =>
        item.subject.toLowerCase().includes(searchTerm) ||
        item.plainText?.toLowerCase().includes(searchTerm) ||
        item.location?.toLowerCase().includes(searchTerm) ||
        item.infraction_type?.toLowerCase().includes(searchTerm) ||
        item.involved_employees?.some((emp) =>
          emp.name.toLowerCase().includes(searchTerm),
        ),
    )
  }

  if (props.activeTab && props.activeTab !== 'All') {
    result = result.filter((item) =>
      item.labels?.includes(props.activeTab as string),
    )
  }

  if (props.selectedStatuses?.length) {
    result = result.filter((item) =>
      props.selectedStatuses.includes(item.status),
    )
  }

  if (props.dateFrom || props.dateTo) {
    result = result.filter((item) => {
      try {
        const itemDate = new Date(item.occured_at)
        const fromDate = props.dateFrom ? new Date(props.dateFrom) : null
        const toDate = props.dateTo ? new Date(props.dateTo) : null

        return (
          (!fromDate || itemDate >= fromDate) && (!toDate || itemDate <= toDate)
        )
      } catch {
        return false
      }
    })
  }

  if (props.sortBy === 'recent') {
    result.sort(
      (a, b) =>
        new Date(b.occured_at).getTime() - new Date(a.occured_at).getTime(),
    )
  } else if (props.sortBy === 'asc') {
    result.sort((a, b) => a.subject.localeCompare(b.subject))
  } else if (props.sortBy === 'desc') {
    result.sort((a, b) => b.subject.localeCompare(a.subject))
  }

  return result
})

const toggleSelection = (incidentId: string) => {
  const index = selectedIncidents.value.indexOf(incidentId)
  if (index === -1) {
    selectedIncidents.value.push(incidentId)
  } else {
    selectedIncidents.value.splice(index, 1)
  }
}

const handleItemClick = (itemId: string) => {
  const item = props.items.find((i) => i.id === itemId)
  if (!item) return

  emit('itemClick', itemId)
  if (!item.is_read && props.onMarkAsRead) {
    props.onMarkAsRead(itemId)
  }
}
</script>

<template>
  <ScrollArea class="flex h-screen">
    <div class="flex flex-1 flex-col pt-0">
      <TransitionGroup
        name="list"
        appear
      >
        <div
          v-for="item of filteredItems"
          :key="item.id"
          class="group relative flex items-start gap-3 border p-3 text-left text-sm transition-all hover:bg-accent"
          :class="{
            'bg-accent': selectedIncident === item.id || !item.is_read,
            'bg-blue-50': selectedIncidents.includes(item.id),
          }"
          @click="handleItemClick(item.id)"
        >
          <div
            class="absolute left-3 top-10 transition-opacity"
            :class="{
              'opacity-100': selectedIncidents.includes(item.id),
              'opacity-0 group-hover:opacity-100': !selectedIncidents.includes(
                item.id,
              ),
            }"
            @click.stop
          >
            <input
              type="checkbox"
              :checked="selectedIncidents.includes(item.id)"
              @change="toggleSelection(item.id)"
              class="h-4 w-4 rounded border-primary text-primary focus:ring-primary"
            />
          </div>
          <div class="flex flex-1 flex-col gap-2">
            <div class="flex w-full flex-col gap-1">
              <div class="flex items-center">
                <div class="font-semibold">
                  {{ item.subject }}
                </div>
              </div>
            </div>

            <div
              :class="
                cn(
                  'line-clamp-1 text-xs',
                  !item.is_read
                    ? 'font-bold text-primary'
                    : 'text-muted-foreground',
                )
              "
            >
              {{ item.plainText || 'No description' }}
            </div>

            <div
              :class="
                cn(
                  'flex items-center gap-2 whitespace-nowrap',
                  !item.is_read
                    ? 'font-semibold text-primary'
                    : 'text-muted-foreground',
                )
              "
            >
              <Clock class="size-4" />
              {{
                format(new Date(item.occured_at), "MMMM d, yyyy 'at' hh:mmaaa")
              }}

              <Badge
                v-if="item.status"
                :variant="getStatusBadgeVariant(item.status)"
              >
                {{ item.status }}
              </Badge>
            </div>
          </div>
        </div>
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