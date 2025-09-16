<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem, EloquentCollection, User, Employee } from '@/types'
import { columns } from './columns'
import UserDataTable from '@/components/user-management/UserDataTable.vue'
import PageHeader from '@/components/user-management/PageHeader.vue'
import UserCreationModal from '@/components/user-management/UserCreationModal.vue'
import { ref } from 'vue'

interface IndexProps {
  data: {
    data: User[]
    meta: EloquentCollection
  }
  emptySearchImg: string
  employees: Employee[]
}

const props = defineProps<IndexProps>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Users',
    href: '/users',
  },
]

// console.log(props.employees);
const isCreateModalOpen = ref(false)

const openCreateModal = () => {
  isCreateModalOpen.value = true
}

const closeCreateModal = () => {
  isCreateModalOpen.value = false
}
</script>

<template>
  <Head title="Users" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto mb-6 mt-3 w-full max-w-screen-xl px-6">
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
        <PageHeader
          title="Users"
          sub-title="You can manage the list of user accounts here!"
          @create="openCreateModal"
        />
        <UserDataTable
          :columns="columns"
          :data="data.data"
          :meta="data.meta"
          :empty-img-uri="emptySearchImg"
          :routeName="'users.index'"
        />
      </div>
    </div>

    <UserCreationModal
      :open="isCreateModalOpen"
      :employees="props.employees"
      @close="closeCreateModal"
    />
  </AppLayout>
</template>