import { ArrowDownIcon, ArrowRightIcon, ArrowUpIcon } from 'lucide-vue-next'
import { h } from 'vue'

export const labels = [
  {
    value: 'bug',
    label: 'Bug',
  },
  {
    value: 'feature',
    label: 'Feature',
  },
  {
    value: 'documentation',
    label: 'Documentation',
  },
]

export const statuses = [
  {
    value: 'pending',
    label: 'Pending',
  },
  {
    value: 'approved',
    label: 'Approved',
  },

  {
    value: 'rejected',
    label: 'Rejected',
  },
]

export const priorities = [
  {
    value: 'low',
    label: 'Low',
    icon: h(ArrowDownIcon),
  },
  {
    value: 'medium',
    label: 'Medium',
    icon: h(ArrowRightIcon),
  },
  {
    value: 'high',
    label: 'High',
    icon: h(ArrowUpIcon),
  },
]
