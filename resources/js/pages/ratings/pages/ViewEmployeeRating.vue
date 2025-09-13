<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import CoworkerRatingDisplay from '@/pages/ratings/components/CoworkerRatingDisplay.vue'
import { router } from '@inertiajs/vue3'

type Coworker = {
  id: number
  first_name: string
  last_name: string
  position?: {
    name: string
  }
}

type RatedTeamMember = {
  employee_id: number
  employee: Coworker
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

function getCoworkers(order: any): Coworker[] {
  if (props.ratedTeamMembers && props.ratedTeamMembers.length > 0) {
    const coworkers = props.ratedTeamMembers
      .filter(member => {
        return member.employee_id !== props.employeeId
      })
      .map(member => {
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
    member => member.employee_id === coworker.id
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
    member => member.employee_id === coworker.id
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
    return { role: formattedRole, department: 'Hauling Department' }
  }

  const form3 = order.serviceable?.form3
  if (!form3) return { role: '', department: 'Hauling Department' }

  let role = ''
  if (form3.team_leader?.id === coworker.id) role = '(Team Leader)'
  else if (form3.driver?.id === coworker.id) role = '(Driver)'
  else if (form3.safetyOfficer?.id === coworker.id) role = '(Safety Officer)'
  else if (form3.mechanic?.id === coworker.id) role = '(Mechanic)'
  else if ((form3.haulers || []).some((h: any) => h.id === coworker.id))
    role = '(Hauler)'

  const result = { role, department: 'Hauling Department' }
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
      <div v-for="order in props.jobOrders" :key="order.id" class="p-4 sm:p-6 md:p-8 lg:p-12 max-w-7xl mx-auto">

        <div class="mb-6 sm:mb-8 w-full">
          <div class="mb-2 sm:mb-1 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-900 dark:text-white">
              Performance Evaluation
            </h1>
            <div class="text-right text-lg sm:text-xl font-semibold text-blue-900 dark:text-white">
              Job Order: {{ order.ticket }}
            </div>
          </div>
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-0">
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
              Employee performance rating overview
            </p>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
              Date and Time of Service: {{ formatDate(order.created_at) }}
            </p>
          </div>
        </div>

        <div class="px-0 sm:px-4 md:px-8 lg:px-12">
          <div v-if="getCoworkers(order).length"
            class="divide-y divide-gray-100 dark:divide-gray-700 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">

            <CoworkerRatingDisplay v-for="coworker in getCoworkers(order)" :key="coworker.id"
              :name="`${coworker.first_name} ${coworker.last_name}`" :role="getRole(order, coworker).role"
              :department="getRole(order, coworker).department" :rating="getCoworkerRating(coworker).rating"
              :description="getCoworkerRating(coworker).description" />
          </div>

          <div v-else
            class="p-6 sm:p-8 text-center italic text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            No co-workers were rated in this hauling.
          </div>
        </div>

        <div class="mt-6 sm:mt-8">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
            Overall Remarks
          </label>
          <div
            class="min-h-[3rem] w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
            {{
              order.performance_summary?.overall_remarks || 'No remarks provided.'
            }}
          </div>
        </div>

        <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row justify-end gap-3">
          <button type="button"
            class="w-full sm:w-auto rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
            @click="router.visit('/ratings')">
            Close
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>