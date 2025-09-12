<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem, EloquentCollection, User } from '@/types'
import ArchivedTabs from '../components/ArchivedTabs.vue'
import PageHeader from '../components/PageHeader.vue'
import ArchivedUserDataTable from './components/DataTable.vue'
import { columns } from './components/columns'

interface IndexProps {
  data: {
    data: User[]
    meta: EloquentCollection
  }
}

defineProps<IndexProps>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Archives',
    href: '#',
  },
  {
    title: 'Users',
    href: '/archives/users',
  },
]
</script>

<template>
  <Head title="Archived Users" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
        <div class="mb-1">
          <PageHeader
            archive-type="Users"
            sub-title="You can manage and restore the archived employee accounts here!"
          />
        </div>
        <ArchivedTabs />
        <ArchivedUserDataTable
          :columns="columns"
          :data="data.data"
          :meta="data.meta"
        />
      </div>
    </MainContainer>
  </AppLayout>
</template>
