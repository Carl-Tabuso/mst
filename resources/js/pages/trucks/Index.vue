<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import AppLayout from '@/layouts/AppLayout.vue'
import {
  BreadcrumbItem,
  EloquentCollection,
  Employee,
  PaginationLinks,
  Truck,
} from '@/types'
import { provide } from 'vue'
import AddNewTruck from './components/AddNewTruck.vue'
import Toolbar from './components/Toolbar.vue'
import TruckCard from './components/TruckCard.vue'
import TruckListPagination from './components/TruckListPagination.vue'

interface IndexProps {
  data: {
    data: Truck[]
    meta: EloquentCollection
    links: PaginationLinks
  }
  dispatchers: Employee[]
}

const props = defineProps<IndexProps>()

provide('dispatchers', props.dispatchers)

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Truck Inventory',
    href: route('truck.index'),
  },
]
</script>

<template>
  <Head title="Truck Inventory" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto mb-6 mt-3 w-full max-w-screen-xl px-6">
      <div class="flex flex-row items-center justify-between">
        <div class="flex flex-col">
          <h3 class="scroll-m-20 text-3xl font-bold text-primary">
            Truck Inventory
          </h3>
          <p class="text-muted-foreground">
            You can manage the trucks being used for waste management haulings
            here.
          </p>
        </div>
        <AddNewTruck />
      </div>
      <div class="mt-6">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
          <div class="flex items-center justify-between gap-1 py-1 lg:flex-row">
            <Toolbar
              :dispatchers="dispatchers"
              :is-empty="data.meta.total === 0"
            />
            <span class="flex flex-row items-center gap-1">
              <p class="text-sm">Total</p>
              <Badge
                variant="secondary"
                class="rounded-full font-bold"
              >
                {{ data.meta.total }}
              </Badge>
            </span>
          </div>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
            <TruckCard
              v-for="truck in data.data"
              :key="truck.id"
              :truck="truck"
            />
          </div>
          <div class="mt-2 flex justify-center">
            <TruckListPagination :meta="data.meta" />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
