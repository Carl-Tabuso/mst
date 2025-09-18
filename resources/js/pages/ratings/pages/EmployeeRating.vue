<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import ConfirmRatingModal from '@/pages/ratings/components/ConfirmRatingModal.vue'
import CoworkerRatingForm from '@/pages/ratings/components/CoworkerRatingForm.vue'
import { useCoworkerRatings } from '@/pages/ratings/composables/useCoworkerRatings'
import type { Coworker } from '@/pages/ratings/types/rating'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps<{
  jobOrders: Array<any>
  employeeId: number
  teamMembersToRate?: Array<{
    employee_id: number
    employee: any
    form3_id: number
    role: string
    avatar?: string
  }>
}>()

const { ratings, setRating, setDescription } = useCoworkerRatings()
const overallRemarks = ref('')

function getCoworkers(order: any): Coworker[] {
  if (!props.teamMembersToRate) return []

  const form3Id = order.serviceable?.form3?.id
  return props.teamMembersToRate
    .filter((member) => member.form3_id === form3Id)
    .map((member) => member.employee)
}

function getRole(
  order: any,
  coworker: Coworker,
): { role: string; department: string } {
  if (!props.teamMembersToRate) return { role: '', department: '' }

  const form3Id = order.serviceable?.form3?.id
  const member = props.teamMembersToRate.find(
    (m) => m.form3_id === form3Id && m.employee.id === coworker.id,
  )

  if (!member) return { role: '', department: '' }

  const roleMap: Record<string, string> = {
    team_leader: '(Team Leader)',
    team_driver: '(Driver)',
    safety_officer: '(Safety Officer)',
    team_mechanic: '(Mechanic)',
    hauler: '(Hauler)',
  }

  return {
    role: roleMap[member.role] || `(${member.role})`,
    department: '',
  }
}

function allRated(order: any): boolean {
  const form3Id = order.serviceable?.form3?.id
  const currentRatings = ratings[form3Id] || []
  return getCoworkers(order).every((coworker) =>
    currentRatings.some(
      (r) => r.evaluatee_id === coworker.id && r.performance_rating_id,
    ),
  )
}

const showModal = ref(false)
const modalSummary = ref<any[]>([])
const modalOrder = ref<any>(null)

function openConfirmModal(order: any) {
  const coworkers = getCoworkers(order)
  const form3Id = order.serviceable?.form3?.id
  const currentRatings = ratings[form3Id] || []

  modalSummary.value = coworkers.map((c) => {
    const r = currentRatings.find((r) => r.evaluatee_id === c.id) || {}
    return {
      id: c.id,
      name: `${c.first_name} ${c.last_name}`,
      position: c.position?.name || '',
      rating: r.performance_rating_id || '',
      description: r.description || '',
    }
  })

  modalOrder.value = order
  showModal.value = true
}

function handleModalConfirm() {
  if (!modalOrder.value) return
  const form3Id = modalOrder.value.serviceable?.form3?.id
  const ratingsPayload = (ratings[form3Id] || []).map((r) => ({
    ...r,
    job_order_id: modalOrder.value.id,
  }))

  router.post(
    '/ratings/store',
    {
      ratings: ratingsPayload,
      overall_remarks: overallRemarks.value,
    },
    {
      onSuccess: () => {
        ratings[form3Id] = []
        overallRemarks.value = ''
        showModal.value = false
      },
    },
  )
}

function formatDate(dateString: string) {
  const date = new Date(dateString)
  return (
    date.toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
    }) +
    ' at ' +
    date.toLocaleTimeString('en-US', {
      hour: 'numeric',
      minute: '2-digit',
      hour12: true,
    })
  )
}
function getTicketDisplay(order: any): string {
  return order.ticket || `JO #${order.id}`
}
</script>

<template>
  <AppLayout>
    <div
      v-for="order in props.jobOrders"
      :key="order.id"
      class="container mx-auto p-4 sm:p-6 lg:p-12"
    >
      <div class="mb-8 w-full">
        <div
          class="mb-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
        >
          <h1
            class="text-xl font-bold text-blue-900 dark:text-white sm:text-2xl"
          >
            Performance Evaluation
          </h1>
          <div
            class="text-sm font-semibold text-blue-900 dark:text-white sm:text-lg"
          >
            Job Order: {{ getTicketDisplay(order) }}
          </div>
        </div>

        <div
          class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between"
        >
          <p class="text-xs text-gray-500 dark:text-gray-400 sm:text-sm">
            Evaluate employees' performance based on previous assigned hauling
          </p>
          <p class="text-xs text-gray-500 dark:text-gray-400 sm:text-sm">
            Date and Time of Service: {{ formatDate(order.created_at) }}
          </p>
        </div>
      </div>

      <div class="sm:px-4 lg:px-12">
        <div
          v-if="getCoworkers(order).length"
          class="divide-y divide-gray-100 dark:divide-gray-700"
        >
          <CoworkerRatingForm
            v-for="coworker in getCoworkers(order)"
            :key="coworker.id"
            :name="`${coworker.first_name} ${coworker.last_name}`"
            :role="getRole(order, coworker).role"
            :department="getRole(order, coworker).department"
            :avatar="
              props.teamMembersToRate?.find(
                (m) =>
                  m.employee_id === coworker.id &&
                  m.form3_id === order.serviceable?.form3?.id,
              )?.avatar ||
              coworker.avatar ||
              ''
            "
            :rating="
              ratings[order.serviceable?.form3?.id]?.find(
                (r) => r.evaluatee_id === coworker.id,
              )?.performance_rating_id || 0
            "
            :description="
              ratings[order.serviceable?.form3?.id]?.find(
                (r) => r.evaluatee_id === coworker.id,
              )?.description || ''
            "
            @update:rating="
              (n) => setRating(order.serviceable?.form3?.id, coworker.id, n)
            "
            @update:description="
              (val) =>
                setDescription(order.serviceable?.form3?.id, coworker.id, val)
            "
          />
        </div>

        <div
          v-else
          class="p-6 text-center italic text-gray-500 dark:text-gray-400"
        >
          No co-workers to rate for this hauling.
          <br />
          <span class="text-xs"
            >(It means you were the only one assigned to this job order
            😅)</span
          >
        </div>
      </div>

      <div
        v-if="getCoworkers(order).length"
        class="mt-6"
      >
        <label
          class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
        >
          Overall Remarks
        </label>
        <textarea
          v-model="overallRemarks"
          class="w-full resize-none rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 sm:text-base"
          rows="4"
          placeholder="Enter your overall remarks here..."
        />
      </div>

      <div
        v-if="getCoworkers(order).length"
        class="mt-6 flex flex-col justify-end gap-3 sm:flex-row"
      >
        <button
          type="button"
          class="rounded-lg border border-gray-300 px-4 py-2 font-medium text-gray-700 transition-colors hover:bg-neutral-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
          @click="router.visit('/ratings')"
        >
          Cancel
        </button>
        <button
          type="button"
          :disabled="!allRated(order)"
          :class="[
            'rounded-lg px-6 py-2 font-medium transition-colors',
            !allRated(order)
              ? 'cursor-not-allowed bg-gray-300 text-white dark:bg-gray-600'
              : 'bg-blue-900 text-white hover:bg-blue-700',
          ]"
          @click="openConfirmModal(order)"
        >
          Submit
        </button>
      </div>
    </div>

    <ConfirmRatingModal
      v-model:show="showModal"
      :summary="modalSummary"
      @confirm="handleModalConfirm"
      @close="showModal = false"
    />
  </AppLayout>
</template>
