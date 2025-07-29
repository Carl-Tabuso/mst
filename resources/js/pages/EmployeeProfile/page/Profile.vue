<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import CreatedJobOrder from '../components/CreatedJobOrder.vue'
import ITServicesList from '../components/ITServicesList.vue'
import JobOrderStats from '../components/JobOrderStats.vue'
import { useProfile } from '../helpers/useProfile'
import { JobOrder, ProfileProps } from '../types/types'

const props = defineProps<
  ProfileProps & {
    createdJobOrders?: {
      total: number
      by_status: Record<string, number>
    } | null
    createdJobOrdersList?: JobOrder[] | null
  }
>()

const {
  form,
  positionName,
  showJobOrders,
  showITServices,
  showPerformance,
  formatStatus,
  getStatusColor,
  submit,
} = useProfile(props)

console.log('Created Job Orders from backend:', props.createdJobOrdersList)
</script>

<template>
  <AppLayout>
    <div class="mt-10 max-w-3xl p-6">
      <h1 class="mb-2 text-2xl font-bold">Employee Profile</h1>
      <div class="mb-6">
        <div class="text-lg font-semibold">{{ props.employee.full_name }}</div>
        <div class="text-gray-600">
          {{ props.employee.position?.name || 'No Position' }}
        </div>
      </div>

      <form
        @submit.prevent="submit"
        class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2"
      >
        <div>
          <label class="block text-sm font-medium">First Name</label>
          <input
            v-model="form.first_name"
            class="w-full rounded border px-2 py-1"
          />
        </div>
        <div>
          <label class="block text-sm font-medium">Middle Name</label>
          <input
            v-model="form.middle_name"
            class="w-full rounded border px-2 py-1"
          />
        </div>
        <div>
          <label class="block text-sm font-medium">Last Name</label>
          <input
            v-model="form.last_name"
            class="w-full rounded border px-2 py-1"
          />
        </div>
        <div>
          <label class="block text-sm font-medium">Suffix</label>
          <input
            v-model="form.suffix"
            class="w-full rounded border px-2 py-1"
          />
        </div>
        <div class="md:col-span-2">
          <button
            type="submit"
            class="mt-2 rounded bg-blue-600 px-4 py-2 text-white"
            :disabled="form.processing"
          >
            Update Profile
          </button>
        </div>
      </form>

      <div
        v-if="showPerformance"
        class="mt-2 font-semibold text-blue-700"
      >
        Total Performance Rate:
        <span v-if="props.averagePerformanceRating !== null">{{
          props.averagePerformanceRating
        }}</span>
        <span v-else>No ratings yet</span>
      </div>

      <div v-if="showITServices">
        <ITServicesList
          :services="props.employee.itServicesAsTechnician || []"
        />
      </div>

      <div v-if="positionName === 'team leader'">
        <JobOrderStats
          :jobOrderStats="props.jobOrderStats"
          :assignedJobOrders="props.assignedJobOrders"
          :positionName="positionName"
          :formatStatus="formatStatus"
          :getStatusColor="getStatusColor"
        />
      </div>

      <div v-else-if="showJobOrders">
        <JobOrderStats
          :jobOrderStats="props.jobOrderStats"
          :assignedJobOrders="props.assignedJobOrders"
          :positionName="positionName"
          :formatStatus="formatStatus"
          :getStatusColor="getStatusColor"
        />
      </div>

      <CreatedJobOrder
        v-if="positionName === 'frontliner'"
        :createdJobOrders="props.createdJobOrders"
        :createdJobOrdersList="props.createdJobOrdersList ?? []"
        :positionName="positionName"
        :formatStatus="formatStatus"
        :getStatusColor="getStatusColor"
      />
    </div>
  </AppLayout>
</template>
