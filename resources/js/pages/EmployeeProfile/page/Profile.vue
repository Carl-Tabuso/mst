<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types'
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

const breadcrumbs: BreadcrumbItem[] = [
  {
    href: '#',
    title: 'Profile',
  },
  {
    href: '#',
    title: props.employee.full_name,
  },
]
</script>

<template>
  <Head :title="employee.full_name" />
  <AppLayout>
    <MainContainer>
      <div class="mx-auto max-w-7xl space-y-4 sm:space-y-6">
        <div class="flex flex-col">
          <h3
            class="flex flex-row items-center gap-3 text-3xl font-bold text-muted-foreground"
          >
            {{ employee.first_name }}'s Emloyee Profile
          </h3>
        </div>
        <ProfileHeader
          :employee="props.employee"
          :avatar="props.employee.avatar"
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
    </MainContainer>
  </AppLayout>
</template>
