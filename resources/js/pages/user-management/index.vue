<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { UserResponse } from '@/types/user'
import { columns } from './columns'
import UserDataTable from '@/components/user-management/UserDataTable.vue'
import UserCreateModal from '@/components/user-management/UserCreateModal.vue'
import axios from 'axios'
import{  Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'

const users = ref<UserResponse>({
    data: [],
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0
})
const isLoading = ref(true)

const fetchUsers = async (params: {
    page?: number
    per_page?: number
    search?: string
    status?: string
    sort?: string
} = {}) => {
    isLoading.value = true
    try {
        const response = await axios.get<UserResponse>('/users', {
            params: {
                page: params.page || users.value.current_page,
                per_page: params.per_page || users.value.per_page,
                search: params.search,
                status: params.status,
                sort: params.sort
            }
        })
        users.value = response.data
    } catch (error) {
        console.error('Error fetching users:', error)
    } finally {
        isLoading.value = false
    }
}

onMounted(() => fetchUsers())
</script>

<template>
    <AppLayout>
      
        <div class="space-y-6 p-6 container">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-blue-800">User Management</h1>
       <div class="flex justify-end mb-4">
    <UserCreateModal />
  </div>
    </div>
    <UserDataTable
      :columns="columns"
      :data="users.data"
      :pagination="{
        current_page: users.current_page,
        last_page: users.last_page,
        per_page: users.per_page,
        total: users.total,
      }"
      :isLoading="isLoading"
      @page-change="(page) => fetchUsers({ page })"
      @per-page-change="(per_page) => fetchUsers({ per_page, page: 1 })"
      @filter-change="(filters) => fetchUsers({ ...filters, page: 1 })"
    />
  </div>
  </AppLayout>
  
</template>