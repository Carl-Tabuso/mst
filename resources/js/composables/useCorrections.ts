import { JobOrderStatus } from '@/constants/job-order-statuses'

export function useCorrections() {
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
  } as const

  const getNestedObject = (obj: any, path: string) => {
    return path.split('.').reduce((acc, key) => acc?.[key], obj)
  }

  const isDateString = (field: keyof typeof fieldMap) => {
    return (<Array<keyof typeof fieldMap>>['date_time','payment_date', 'approved_date']).includes(field)
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

  return {
    fieldMap,
    getNestedObject,
    canCorrectProposalInformation,
    isDateString,
  }
}

export type CorrectionFieldKey = keyof ReturnType<
  typeof useCorrections
>['fieldMap']
