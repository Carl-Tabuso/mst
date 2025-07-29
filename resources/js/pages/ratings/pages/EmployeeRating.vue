<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import ConfirmRatingModal from '@/pages/ratings/components/ConfirmRatingModal.vue'
import CoworkerRatingForm from '@/pages/ratings/components/CoworkerRatingForm.vue'
import { useCoworkerRatings } from '@/pages/ratings/composables/useCoworkerRatings.vue'
import type { Coworker } from '@/pages/ratings/types/rating'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps<{
  jobOrders: Array<any>
  employeeId: number
}>()

const { ratings, setRating, setDescription } = useCoworkerRatings()
const overallRemarks = ref('')

function getCoworkers(order: any): Coworker[] {
  const form3 = order.serviceable?.form3
  if (!form3) return []

  const coworkers: Coworker[] = (form3.haulers || []).filter(
    (h: any) => h.id !== props.employeeId,
  )
  const roles = ['team_leader', 'driver', 'safetyOfficer', 'mechanic']

  roles.forEach((role) => {
    const person = form3[role]
    if (
      person &&
      person.id !== props.employeeId &&
      !coworkers.some((c) => c.id === person.id)
    ) {
      coworkers.unshift(person)
    }
  })
  return coworkers
}

function getRole(
  order: any,
  coworker: Coworker,
): { role: string; department: string } {
  const form3 = order.serviceable?.form3
  if (!form3) return { role: '', department: 'Hauling Department' }

  if (form3.team_leader?.id === coworker.id)
    return { role: '(Team Leader)', department: 'Hauling Department' }
  if (form3.driver?.id === coworker.id)
    return { role: '(Driver)', department: 'Hauling Department' }
  if (form3.safetyOfficer?.id === coworker.id)
    return { role: '(Safety Officer)', department: 'Hauling Department' }
  if (form3.mechanic?.id === coworker.id)
    return { role: '(Mechanic)', department: 'Hauling Department' }
  if ((form3.haulers || []).some((h: any) => h.id === coworker.id))
    return { role: '(Hauler)', department: 'Hauling Department' }
  return { role: '', department: 'Hauling Department' }
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
      onError: (errors) => {
        console.error('Rating submission failed:', errors)
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
</script>

<template>
  <AppLayout>
    <div
      v-for="order in props.jobOrders"
      :key="order.id"
      class="p-12 px-32"
    >
      <!-- header -->
      <div class="mb-8 w-full">
        <div class="mb-1 flex items-start justify-between">
          <h1 class="text-2xl font-bold text-blue-900">
            Performance Evaluation
          </h1>
          <div class="text-right text-xl font-semibold text-blue-900">
            Job Order: #{{ order.id }}
          </div>
        </div>

        <div class="flex items-center justify-between">
          <p class="text-xs text-gray-500">
            Evaluate employees' performance based on previous assigned hauling
          </p>
          <p class="text-xs text-gray-500">
            Date and Time of Service: {{ formatDate(order.created_at) }}
          </p>
        </div>
      </div>

      <!-- Coworker Rating Forms -->
      <div class="px-12">
        <div
          v-if="getCoworkers(order).length"
          class="divide-y divide-gray-100"
        >
          <CoworkerRatingForm
            v-for="coworker in getCoworkers(order)"
            :key="coworker.id"
            :name="`${coworker.first_name} ${coworker.last_name}`"
            :role="getRole(order, coworker).role"
            :department="getRole(order, coworker).department"
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
          class="p-8 text-center italic text-gray-500"
        >
          No co-workers to rate for this hauling. It means you were the only one
          assigned to this job order HAHA.
        </div>
      </div>

      <!-- Overall Remarks -->
      <div
        v-if="getCoworkers(order).length"
        class="mt-6"
      >
        <label class="mb-2 block text-sm font-medium text-gray-700">
          Overall Remarks
        </label>
        <textarea
          v-model="overallRemarks"
          class="w-full resize-none rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
          rows="4"
          placeholder="Enter your overall remarks here..."
        />
      </div>

      <!-- Action Buttons -->
      <div
        v-if="getCoworkers(order).length"
        class="mt-6 flex justify-end gap-3"
      >
        <button
          type="button"
          class="rounded-lg border border-gray-300 px-4 py-2 font-medium text-gray-700 transition-colors hover:bg-neutral-100"
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
              ? 'cursor-not-allowed bg-gray-300 text-white'
              : 'bg-blue-900 text-white hover:bg-blue-700',
          ]"
          @click="openConfirmModal(order)"
        >
          Submit
        </button>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <ConfirmRatingModal
      v-model:show="showModal"
      :summary="modalSummary"
      @confirm="handleModalConfirm"
      @close="showModal = false"
    />
  </AppLayout>
</template>
