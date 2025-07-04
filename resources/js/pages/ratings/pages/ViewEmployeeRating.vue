<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import CoworkerRatingDisplay from '@/pages/ratings/components/CoworkerRatingDisplay.vue';

type Coworker = {
    id: number;
    first_name: string;
    last_name: string;
    position?: {
        name: string;
    };
};

const props = defineProps<{
    jobOrders: Array<any>;
    employeeId: number;
}>();

function getCoworkers(order: any): Coworker[] {
    const form3 = order.serviceable?.form3;
    if (!form3) return [];

    const coworkers: Coworker[] = (form3.haulers || []).filter((h: any) => h.id !== props.employeeId);
    const roles = ['team_leader', 'driver', 'safetyOfficer', 'mechanic'];

    roles.forEach(role => {
        const person = form3[role];
        if (person && person.id !== props.employeeId && !coworkers.some(c => c.id === person.id)) {
            coworkers.unshift(person);
        }
    });
    

    console.log(`Job Order #${order.id} - Coworkers:`, coworkers);
    return coworkers;
}

function getCoworkerRating(coworker: any) {
    // first (and only) performance row written by this evaluator
    const perf = coworker.performances_as_employee?.[0];

    // first (and only) rating row in that performance
    const ratingRow = perf?.ratings?.[0];

    return {
        rating: ratingRow?.performance_rating?.scale ?? 0,   // number 1-5
        description: ratingRow?.description ?? '',
    };
}

function getRole(order: any, coworker: Coworker): { role: string; department: string } {
    const form3 = order.serviceable?.form3;
    if (!form3) return { role: '', department: 'Hauling Department' };

    let role = '';
    if (form3.team_leader?.id === coworker.id) role = '(Team Leader)';
    else if (form3.driver?.id === coworker.id) role = '(Driver)';
    else if (form3.safetyOfficer?.id === coworker.id) role = '(Safety Officer)';
    else if (form3.mechanic?.id === coworker.id) role = '(Mechanic)';
    else if ((form3.haulers || []).some((h: any) => h.id === coworker.id)) role = '(Hauler)';

    const result = { role, department: 'Hauling Department' };
    console.log(`Role for coworker ID ${coworker.id}:`, result);
    return result;
}

function formatDate(dateString: string) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    }) + ' at ' + date.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
}


// // Watch overall remarks per job order
// props.jobOrders.forEach(order => {
//     const remark = order.serviceable?.ratings?.find(r => r.evaluator_id === props.employeeId)?.overall_remarks;
//     console.log(`Job Order #${order.id} - Overall Remark:`, remark ?? 'No remarks provided.');
// });
</script>

<template>
    <AppLayout>
        <div v-for="order in props.jobOrders" :key="order.id" class="p-12 px-32">
            <!-- Header -->
            <div class="mb-8 w-full">
                <div class="flex justify-between items-start mb-1">
                    <h1 class="text-2xl font-bold text-blue-900">Performance Evaluation</h1>
                    <div class="text-xl text-right font-semibold text-blue-900">Job Order: #{{ order.id }}</div>
                </div>
                <div class="flex justify-between items-center">
                    <p class="text-xs text-gray-500">Employee performance rating overview</p>
                    <p class="text-xs text-gray-500">Date and Time of Service: {{ formatDate(order.created_at) }}</p>
                </div>
            </div>

            <!-- Coworker Rating Display -->
            <div class="px-12">
                <div v-if="getCoworkers(order).length" class="divide-y divide-gray-100">
                    <CoworkerRatingDisplay v-for="coworker in getCoworkers(order)" :key="coworker.id"
                        :name="`${coworker.first_name} ${coworker.last_name}`" :role="getRole(order, coworker).role"
                        :department="getRole(order, coworker).department" :rating="getCoworkerRating(coworker).rating"
                        :description="getCoworkerRating(coworker).description" />

                </div>

                <div v-else class="p-8 text-center text-gray-500 italic">
                    No co-workers were rated in this hauling.
                </div>
            </div>

            <!-- Overall Remarks -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Overall Remarks</label>
                <div class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50 min-h-[3rem]">
                    {{ order.performance_summary?.overall_remarks || 'No remarks provided.' }}
                </div>

            </div>

            <!-- Close Button -->
            <div class="flex justify-end gap-3 mt-6">
                <button type="button"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-neutral-100 transition-colors"
                    @click="router.visit('/ratings')">
                    Close
                </button>
            </div>
        </div>
    </AppLayout>
</template>
