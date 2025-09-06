import { ServiceTypeCardBindings } from '@/pages/home'
import { MonitorCog, Truck, Wrench } from 'lucide-vue-next'

export const serviceTypes: ServiceTypeCardBindings = {
  'Waste Management': {
    icon: Truck,
    bgClass: 'bg-tertiary/75 text-white',
    textClass: 'text-tertiary',
  },
  'IT Service': {
    icon: MonitorCog,
    bgClass: 'bg-sky-900/75 text-white',
    textClass: 'text-sky-900',
  },
  'Other Services': {
    icon: Wrench,
    bgClass: 'bg-zinc-700/75 text-white',
    textClass: 'text-muted-foreground',
  },
}
