import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ProfileProps } from '../types/types';

export const useProfile = (props: ProfileProps) => {
  const form = useForm({
    first_name: props.employee.first_name,
    middle_name: props.employee.middle_name,
    last_name: props.employee.last_name,
    suffix: props.employee.suffix,
  });

  const positionName = computed(() => props.employee.position?.name?.toLowerCase() || '');

  const showJobOrders = computed(() => ['team leader', 'hauler', 'driver'].includes(positionName.value));
  const showITServices = computed(() => ['technician', 'electrician', 'engineer'].includes(positionName.value));
  const showPerformance = computed(() => ['team leader', 'hauler', 'driver', 'technician', 'electrician', 'engineer'].includes(positionName.value));
  const showJobOrdersCreated = computed(() => ['frontliner', 'admin'].includes(positionName.value));

  const formatStatus = (status: string) => status.replace(/[_-]/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

  const getStatusColor = (status: string) => {
    const lower = status.toLowerCase();
    if (lower.includes('pending') || lower.includes('waiting')) return 'text-yellow-600 bg-yellow-100';
    if (lower.includes('progress') || lower.includes('processing')) return 'text-blue-600 bg-blue-100';
    if (lower.includes('completed') || lower.includes('done')) return 'text-green-600 bg-green-100';
    if (lower.includes('failed') || lower.includes('rejected')) return 'text-red-600 bg-red-100';
    return 'text-gray-600 bg-gray-100';
  };

  const submit = () => {
    form.patch(route('employees.profile.update', props.employee.id));
  };

  return {
    form,
    positionName,
    showJobOrders,
    showITServices,
    showPerformance,
    showJobOrdersCreated,
    formatStatus,
    getStatusColor,
    submit
  };
};
