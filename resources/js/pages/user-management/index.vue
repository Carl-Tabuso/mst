<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import PageHeader from '@/components/user-management/PageHeader.vue'
import UserCreationModal from '@/components/user-management/UserCreationModal.vue'
import UserDataTable from '@/components/user-management/UserDataTable.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import {
  type BreadcrumbItem,
  EloquentCollection,
  Employee,
  User,
} from '@/types'
import { ref } from 'vue'
import { columns } from './columns'

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
    title: 'Home',
    href: route('home'),
  },
  {
    title: 'Users',
    href: route('users.index'),
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
    <MainContainer>
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
      <UserCreationModal
        :open="isCreateModalOpen"
        :employees="props.employees"
        @close="closeCreateModal"
      />
    </MainContainer>
  </AppLayout>
</template>
