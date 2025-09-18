<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import CoworkerRatingDisplay from '@/pages/ratings/components/CoworkerRatingDisplay.vue'
import { router } from '@inertiajs/vue3'
import { onMounted } from 'vue'

type Coworker = {
  id: number
  first_name: string
  last_name: string
  avatar: string | null
  position?: {
    name: string
  }
}

type RatedTeamMember = {
  employee_id: number
  employee: Coworker
  avatar: string | null
  ratings: Array<{
    performance_rating: {
      scale: number
    }
    description: string
  }>
  role: string
}

const props = defineProps<{
  jobOrders: Array<any>
  employeeId: number
  ratedTeamMembers?: RatedTeamMember[]
}>()

onMounted(() => {
  console.log('All ratedTeamMembers:', props.ratedTeamMembers)
  const emp26 = props.ratedTeamMembers?.find((m) => m.employee_id === 26)
  console.log('Employee 26 avatar path:', emp26?.avatar)
  console.log('Employee 26 nested employee.avatar:', emp26?.employee.avatar)
})

function getCoworkers(order: any): Coworker[] {
  if (props.ratedTeamMembers && props.ratedTeamMembers.length > 0) {
    const coworkers = props.ratedTeamMembers
      .filter((member) => {
        return member.employee_id !== props.employeeId
      })
      .map((member) => {
        return member.employee
      })

    return coworkers
  }

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

function getCoworkerRating(coworker: any) {
  const ratedMember = props.ratedTeamMembers?.find(
    (member) => member.employee_id === coworker.id,
  )

  if (ratedMember && ratedMember.ratings && ratedMember.ratings.length > 0) {
    const firstRating = ratedMember.ratings[0]
    return {
      rating: firstRating.performance_rating?.scale ?? 0,
      description: firstRating.description ?? '',
    }
  }

  const perf = coworker.performances_as_employee?.[0]
  const ratingRow = perf?.ratings?.[0]

  return {
    rating: ratingRow?.performance_rating?.scale ?? 0,
    description: ratingRow?.description ?? '',
  }
}

function getRole(
  order: any,
  coworker: Coworker,
): { role: string; department: string } {
  const ratedMember = props.ratedTeamMembers?.find(
    (member) => member.employee_id === coworker.id,
  )
  if (ratedMember && ratedMember.role) {
    let formattedRole = ''
    switch (ratedMember.role) {
      case 'team_leader':
        formattedRole = '(Team Leader)'
        break
      case 'team_driver':
        formattedRole = '(Driver)'
        break
      case 'safety_officer':
        formattedRole = '(Safety Officer)'
        break
      case 'team_mechanic':
        formattedRole = '(Mechanic)'
        break
      case 'hauler':
        formattedRole = '(Hauler)'
        break
      default:
        formattedRole = `(${ratedMember.role})`
    }
    return { role: formattedRole, department: '' }
  }

  const form3 = order.serviceable?.form3
  if (!form3) return { role: '', department: '' }

  let role = ''
  if (form3.team_leader?.id === coworker.id) role = '(Team Leader)'
  else if (form3.driver?.id === coworker.id) role = '(Driver)'
  else if (form3.safetyOfficer?.id === coworker.id) role = '(Safety Officer)'
  else if (form3.mechanic?.id === coworker.id) role = '(Mechanic)'
  else if ((form3.haulers || []).some((h: any) => h.id === coworker.id))
    role = '(Hauler)'

  const result = { role, department: '' }
  return result
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
    <div class="min-h-screen bg-white dark:bg-zinc-900">
      <div
        v-for="order in props.jobOrders"
        :key="order.id"
        class="mx-auto max-w-7xl p-4 sm:p-6 md:p-8 lg:p-12"
      >
        <div class="mb-6 w-full sm:mb-8">
          <div
            class="mb-2 flex flex-col gap-2 sm:mb-1 sm:flex-row sm:items-start sm:justify-between"
          >
            <h1
              class="text-xl font-bold text-blue-900 dark:text-white sm:text-2xl lg:text-3xl"
            >
              Performance Evaluation
            </h1>
            <div
              class="text-right text-lg font-semibold text-blue-900 dark:text-white sm:text-xl"
            >
              Job Order: {{ order.ticket }}
            </div>
          </div>
          <div
            class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between sm:gap-0"
          >
            <p class="text-xs text-gray-500 dark:text-gray-400 sm:text-sm">
              Employee performance rating overview
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 sm:text-sm">
              Date and Time of Service: {{ formatDate(order.created_at) }}
            </p>
          </div>
        </div>

        <div class="px-0 sm:px-4 md:px-8 lg:px-12">
          <div
            v-if="getCoworkers(order).length"
            class="divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white dark:divide-gray-700 dark:border-gray-700 dark:bg-gray-800"
          >
            <CoworkerRatingDisplay
              v-for="coworker in getCoworkers(order)"
              :key="coworker.id"
              :name="`${coworker.first_name} ${coworker.last_name}`"
              :avatar="
                props.ratedTeamMembers?.find(
                  (m) => m.employee_id === coworker.id,
                )?.avatar ?? undefined
              "
              :role="getRole(order, coworker).role"
              :department="getRole(order, coworker).department"
              :rating="getCoworkerRating(coworker).rating"
              :description="getCoworkerRating(coworker).description"
            />
          </div>

          <div
            v-else
            class="rounded-lg border border-gray-200 bg-gray-50 p-6 text-center italic text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 sm:p-8"
          >
            No co-workers were rated in this hauling.
          </div>
        </div>

        <div class="mt-6 sm:mt-8">
          <label
            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
          >
            Overall Remarks
          </label>
          <div
            class="min-h-[3rem] w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
          >
            {{
              order.performance_summary?.overall_remarks ||
              'No remarks provided.'
            }}
          </div>
        </div>

        <div class="mt-6 flex flex-col justify-end gap-3 sm:mt-8 sm:flex-row">
          <button
            type="button"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 font-medium text-gray-700 transition-colors hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-900 sm:w-auto"
            @click="router.visit('/ratings')"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
