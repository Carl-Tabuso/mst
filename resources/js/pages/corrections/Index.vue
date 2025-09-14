<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import {
  BreadcrumbItem,
  EloquentCollection,
  JobOrderCorrection,
  SharedData,
} from '@/types'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import PageHeader from '../job-orders/components/PageHeader.vue'
import JobOrderCorrectionTable from './components/DataTable.vue'
import { columns } from './components/columns'

interface IndexProps {
  data: {
    data: JobOrderCorrection[]
    meta: EloquentCollection
  }
}

defineProps<IndexProps>()

const page = usePage<SharedData>()
const userRole = computed(() => page.props.auth.user.roles[0].name)

const headerSubTitle = computed(() => {
  if (userRole.value === 'head frontliner') {
    return 'You can manage the request for job order corrections here.'
  } else {
    return 'You can track the status of your submitted request for corrections here.'
  }
})

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Job Orders',
    href: '/job-orders',
  },
  {
    title: 'Corrections',
    href: '/job-orders/corrections',
  },
]
</script>

<template>
  <Head title="Job Order Corrections" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
        <PageHeader
          title="Job Order Corrections"
          :sub-title="headerSubTitle"
        />
        <JobOrderCorrectionTable
          :columns="columns"
          :data="data.data"
          :meta="data.meta"
        />
      </div>
    </MainContainer>
  </AppLayout>
</template>
