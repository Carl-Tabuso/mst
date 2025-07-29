<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Input } from '@/components/ui/input'
import AppLayout from '@/layouts/AppLayout.vue'
import { ChevronDown, Search, SortAsc } from 'lucide-vue-next'
import { ref } from 'vue'
import Pagination from '../components/Pagination.vue'
import { useArchivedJobOrders } from '../hooks/useArchivedJobOrders'

const {
  archivedJobOrders,
  selectedIds,
  isSelected,
  toggleSelection,
  toggleSelectAll,
  allSelected,
  formatDate,
  filters,
  handleSearch,
  handleRestore,
  handleForceDelete,
  pagination,
  handleSort,
} = useArchivedJobOrders()

const search = ref(filters.search || '')
</script>

<template>
  <AppLayout title="Archived Job Orders">
    <div class="space-y-6 p-6">
      <div class="space-y-1">
        <h1 class="text-3xl font-semibold text-blue-900">Archived</h1>
        <p class="text-gray-500">Manage and restore previously deleted items</p>
      </div>

      <div class="flex flex-col items-center justify-between gap-4 lg:flex-row">
        <div class="flex w-full gap-3 lg:max-w-xl">
          <div class="relative flex-1">
            <Search
              class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
            />
            <Input
              v-model="search"
              placeholder="Search..."
              class="h-10 border-gray-300 pl-10 focus:ring-blue-500"
              @keyup.enter="handleSearch(search)"
            />
          </div>

          <!-- Sort dropdown -->
          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <Button
                variant="outline"
                class="h-10 flex-shrink-0 border-gray-300 px-4 hover:bg-gray-50"
              >
                <SortAsc class="mr-2 h-4 w-4" /> Sort
                <ChevronDown class="ml-1 h-4 w-4" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="start">
              <DropdownMenuItem @click="handleSort('deleted_at_desc')"
                >Recently Deleted</DropdownMenuItem
              >
              <DropdownMenuItem @click="handleSort('deleted_at_asc')"
                >Oldest Deleted</DropdownMenuItem
              >
              <DropdownMenuItem @click="handleSort('client_asc')"
                >Client A‑Z</DropdownMenuItem
              >
              <DropdownMenuItem @click="handleSort('client_desc')"
                >Client Z‑A</DropdownMenuItem
              >
            </DropdownMenuContent>
          </DropdownMenu>
        </div>

        <div class="flex gap-2">
          <Button
            class="h-10 bg-blue-600 text-sm text-white hover:bg-blue-700"
            :disabled="!selectedIds.length"
            @click="handleRestore"
          >
            Restore ({{ selectedIds.length }})
          </Button>
          <Button
            variant="destructive"
            class="h-10 text-sm"
            :disabled="!selectedIds.length"
            @click="handleForceDelete"
          >
            Delete ({{ selectedIds.length }})
          </Button>
        </div>
      </div>

      <!-- Data Table -->
      <div class="overflow-hidden rounded-2xl border border-gray-200 shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="sticky top-0 z-10 bg-gray-50">
            <tr>
              <th class="w-12 px-4 py-3 text-center">
                <input
                  type="checkbox"
                  :checked="allSelected"
                  @change="toggleSelectAll"
                  class="form-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
              </th>
              <th class="px-4 py-3 text-left font-medium text-gray-600">
                Client
              </th>
              <th class="px-4 py-3 text-left font-medium text-gray-600">
                Contact Person
              </th>
              <th class="px-4 py-3 text-left font-medium text-gray-600">
                Serviceable Type
              </th>
              <th class="px-4 py-3 text-left font-medium text-gray-600">
                Deleted At
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr
              v-for="job in archivedJobOrders.data"
              :key="job.id"
              class="transition-colors hover:bg-gray-50"
            >
              <td class="w-12 px-4 py-3 text-center">
                <input
                  type="checkbox"
                  :checked="isSelected(job.id)"
                  @change="() => toggleSelection(job.id)"
                  class="form-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
              </td>
              <td class="px-4 py-3 font-medium text-gray-800">
                {{ job.client }}
              </td>
              <td class="px-4 py-3 text-gray-700">{{ job.contact_person }}</td>
              <td class="px-4 py-3 text-gray-700">
                {{ job.serviceable_type }}
              </td>
              <td class="px-4 py-3 text-gray-500">
                {{ formatDate(job.deleted_at) }}
              </td>
            </tr>
            <tr v-if="archivedJobOrders.data.length === 0">
              <td
                colspan="5"
                class="py-6 text-center text-gray-400"
              >
                No archived job orders found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination :pagination="pagination" />
    </div>
  </AppLayout>
</template>
