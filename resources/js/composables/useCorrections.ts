import { JobOrderStatus } from '@/constants/job-order-statuses'
import { JobOrder } from '@/types'
import { format } from 'date-fns'
import { useJobOrderDicts } from './useJobOrderDicts'

const fieldMap = {
  date_time: { path: 'dateTime', label: 'Date and Time of Service' },
  client: { path: 'client', label: 'Client' },
  address: { path: 'address', label: 'Address' },
  department: { path: 'department', label: 'Department/Branch' },
  contact_no: { path: 'contactNo', label: 'Contact Number' },
  contact_person: { path: 'contactPerson', label: 'Contact Person' },
  contact_position: { path: 'contactPosition', label: 'Contact Position' },
  payment_date: { path: 'serviceable.paymentDate', label: 'Payment Date' },
  or_number: { path: 'serviceable.orNumber', label: 'O.R. Number' },
  bid_bond: { path: 'serviceable.bidBond', label: 'Bid Bond' },
  payment_type: {
    path: 'serviceable.form3.paymentType',
    label: 'Payment Type',
  },
  approved_date: {
    path: 'serviceable.form3.approvedDate',
    label: 'Approved Date',
  },

  // IT Service
  technician_id: { path: 'serviceable.technicianId', label: 'Technician' },
  machine_type: { path: 'serviceable.machineType', label: 'Machine Type' },
  model: { path: 'serviceable.model', label: 'Model' },
  serial_no: { path: 'serviceable.serialNo', label: 'Serial Number' },
  tag_no: { path: 'serviceable.tagNo', label: 'Tag Number' },
  machine_problem: {
    path: 'serviceable.machineProblem',
    label: 'Machine Problem',
  },
  initial_service_performed: {
    path: 'serviceable.initialOnsiteReport.servicePerformed',
    label: 'Service Performed',
  },
  recommendation: {
    path: 'serviceable.initialOnsiteReport.recommendation',
    label: 'Recommendation',
  },
  initial_machine_status: {
    path: 'serviceable.initialOnsiteReport.machineStatus',
    label: 'Machine Status',
  },
  file_name: {
    path: 'serviceable.initialOnsiteReport.fileName',
    label: 'Report File',
  },
  file_hash: {
    path: 'serviceable.initialOnsiteReport.fileHash',
    label: 'Report File Hash',
    ignore: true,
  },
  final_service_performed: {
    path: 'serviceable.finalOnsiteReport.servicePerformed',
    label: 'Service Performed',
  },
  parts_replaced: {
    path: 'serviceable.finalOnsiteReport.partsReplaced',
    label: 'Parts Replaced',
  },
  remarks: {
    path: 'serviceable.finalOnsiteReport.remarks',
    label: 'Remarks',
  },
  final_machine_status: {
    path: 'serviceable.finalOnsiteReport.machineStatus',
    label: 'Machine Status',
  },
  technician: {
    path: 'serviceable.technician',
    label: 'Technician',
  },
} as const

export function useCorrections(changes?: any, model?: JobOrder) {
  const getNestedObject = (obj: any, path: string) => {
    return path.split('.').reduce((acc, key) => acc?.[key], obj)
  }

  const isDateString = (field: keyof typeof fieldMap) => {
    return (<Array<keyof typeof fieldMap>>[
      'date_time',
      'payment_date',
      'approved_date',
    ]).includes(field)
  }

  const isEmployee = (field: keyof typeof fieldMap) => {
    return (<Array<keyof typeof fieldMap>>['technician']).includes(field)
  }

  const fieldFormatters: Partial<
    Record<keyof typeof fieldMap, (val: any) => string>
  > = {
    date_time: (val) => {
      const d = typeof val === 'string' ? new Date(val) : val
      return {
        date: format(d, 'MMMM d, yyyy'),
        time: format(d, 'HH:mm'),
      }
    },
    payment_date: (val) => {
      const d = typeof val === 'string' ? new Date(val) : val
      return format(d, 'MMMM d, yyyy')
    },
    approved_date: (val) => {
      const d = typeof val === 'string' ? new Date(val) : val
      return format(d, 'MMMM d, yyyy')
    },
    initial_machine_status: (val) => {
      const { machineStatusMap } = useJobOrderDicts()
      return machineStatusMap[val].label
    },
    final_machine_status: (val) => {
      const { machineStatusMap } = useJobOrderDicts()
      return machineStatusMap[val].label
    },
  }

  const getChangedOrCurrentValue = (field: keyof typeof fieldMap) => {
    const formatter = fieldFormatters[field]
    const changedFields = Object.keys(changes)

    if (changedFields.includes(field)) {
      const value = changes[field]
      if (field === 'date_time' && formatter) {
        return {
          defaultValue: formatter(value),
          class: 'bg-amber-50 border-warning dark:bg-transparent',
        }
      }
      return {
        defaultValue: formatter ? formatter(value) : value,
        class: 'bg-amber-50 border-warning dark:bg-transparent',
      }
    } else {
      const value = getNestedObject(model, fieldMap[field].path)
      return {
        defaultValue: formatter && value ? formatter(value) : value,
      }
    }
  }

  const canCorrectProposalInformation = (status: JobOrderStatus) => {
    const validStatuses: Array<JobOrderStatus> = [
      'for verification',
      'failed',
      'pre-hauling',
      'in-progress',
      'on-hold',
      'closed',
      'completed',
    ]

    return validStatuses.includes(status)
  }

  const canCorrectInitialOnsiteReport = (status: JobOrderStatus) => {
    const validStatuses: Array<JobOrderStatus> = [
      'for final service',
      'completed',
    ]

    return validStatuses.includes(status)
  }

  const canCorrectFinalOnsiteReport = (status: JobOrderStatus) => {
    const validStatuses: Array<JobOrderStatus> = ['completed']

    return validStatuses.includes(status)
  }

  return {
    fieldMap,
    getNestedObject,
    canCorrectProposalInformation,
    canCorrectInitialOnsiteReport,
    canCorrectFinalOnsiteReport,
    isDateString,
    isEmployee,
    getChangedOrCurrentValue,
  }
}

export type CorrectionFieldKey = keyof ReturnType<
  typeof useCorrections
>['fieldMap']
