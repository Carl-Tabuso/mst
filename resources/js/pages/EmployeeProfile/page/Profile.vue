<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import ITServicesList from '../components/ITServicesList.vue'
import JobOrdersTable from '../components/JobOrdersTable.vue'
import JobOrderStats from '../components/JobOrderStats.vue'
import PerformanceEvaluation from '../components/PerformanceEvaluation.vue'
import ProfileHeader from '../components/ProfileHeader.vue'
import TeamLeaderStats from '../components/TeamLeaderStats.vue'
import { useProfile } from '../helpers/useProfile'
import { EnhancedProfileProps } from '../types/types'

const props = defineProps<EnhancedProfileProps>()

const {
  positionName,
  showJobOrders,
  showITServices,
  showPerformance,
  showTeamLeaderStats,
  showFrontlinerContent,
  formatStatus,
  getStatusColor,
} = useProfile(props)
</script>

<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50 dark:bg-zinc-900">
      <div class="px-3 py-4 sm:px-4 sm:py-6 lg:px-6">
        <div class="mx-auto max-w-7xl space-y-4 sm:space-y-6">
          <ProfileHeader
            :employee="props.employee"
            :profile-image-url="props.profileImageUrl"
          />

          <div class="grid grid-cols-1 gap-4 sm:gap-6 xl:grid-cols-2">
            <div
              v-if="showPerformance"
              class="xl:col-span-1"
            >
              <PerformanceEvaluation
                :employee="props.employee"
                :performance-stats="props.performanceStats"
                :performance-evaluations="props.performanceEvaluations"
              />
            </div>

            <div
              v-if="showFrontlinerContent"
              :class="showPerformance ? 'xl:col-span-1' : 'xl:col-span-2'"
            >
              <JobOrdersTable
                :job-orders="props.createdJobOrdersList ?? []"
                :position-name="positionName"
              />
            </div>

            <div
              v-if="showTeamLeaderStats"
              class="xl:col-span-2"
            >
              <TeamLeaderStats
                :team-stats="props.teamStats"
                :assigned-job-orders="props.assignedJobOrders"
                :average-performance-rating="props.averagePerformanceRating"
                :format-status="formatStatus"
                :get-status-color="getStatusColor"
              />
            </div>

            <div
              v-if="showITServices"
              class="xl:col-span-2"
            >
              <ITServicesList :services="props.itServices ?? []" />
            </div>

            <div
              v-if="
                showJobOrders && !showFrontlinerContent && !showTeamLeaderStats
              "
              class="xl:col-span-2"
            >
              <JobOrderStats
                :job-order-stats="props.jobOrderStats"
                :assigned-job-orders="props.assignedJobOrders"
                :position-name="positionName"
                :format-status="formatStatus"
                :get-status-color="getStatusColor"
                :average-performance-rating="props.averagePerformanceRating"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
