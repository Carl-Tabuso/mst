<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import ProfileHeader from '../components/ProfileHeader.vue'
import PerformanceEvaluation from '../components/PerformanceEvaluation.vue'
import JobOrdersTable from '../components/JobOrdersTable.vue'
import ITServicesList from '../components/ITServicesList.vue'
import JobOrderStats from '../components/JobOrderStats.vue'
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
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
      <div class="max-w-7xl mx-auto">
        <ProfileHeader :employee="props.employee" :profile-image-url="props.profileImageUrl" />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

          <div v-if="showPerformance" class="lg:col-span-1">
            <PerformanceEvaluation :employee="props.employee" :performance-stats="props.performanceStats"
              :performance-evaluations="props.performanceEvaluations" />
          </div>

          <div v-if="showFrontlinerContent" :class="showPerformance ? 'lg:col-span-1' : 'lg:col-span-2'">
            <JobOrdersTable :job-orders="props.createdJobOrdersList ?? []" :position-name="positionName" />
          </div>

          <div v-if="showTeamLeaderStats" class="lg:col-span-2">
            <TeamLeaderStats :team-stats="props.teamStats" :assigned-job-orders="props.assignedJobOrders"
              :average-performance-rating="props.averagePerformanceRating" :format-status="formatStatus"
              :get-status-color="getStatusColor" />
          </div>

          <div v-if="showITServices" class="lg:col-span-2">
            <ITServicesList :services="props.itServices ?? []" />
          </div>

          <div v-if="showJobOrders && !showFrontlinerContent && !showTeamLeaderStats" class="lg:col-span-2">
            <JobOrderStats :job-order-stats="props.jobOrderStats" :assigned-job-orders="props.assignedJobOrders"
              :position-name="positionName" :format-status="formatStatus" :get-status-color="getStatusColor"
              :average-performance-rating="props.averagePerformanceRating" />
          </div>

        </div>
      </div>
    </div>
  </AppLayout>
</template>