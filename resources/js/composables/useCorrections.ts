import { JobOrderStatus } from '@/constants/job-order-statuses'

export function useCorrections() {
  const fieldMap = {
    date_time: 'dateTime',
    client: 'client',
    address: 'address',
    department: 'department',
    contact_no: 'contactNo',
    contact_person: 'contactPerson',
    contact_position: 'contactPosition',
    payment_date: 'serviceable.paymentDate',
    or_number: 'serviceable.orNumber',
    bid_bond: 'serviceable.bidBond',
    payment_type: 'serviceable.form3.paymentType',
    approved_date: 'serviceable.form3.approvedDate',
  } as const

  const getNestedObject = (obj: any, path: string) => {
    return path.split('.').reduce((acc, key) => acc?.[key], obj)
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
  }
}

export type CorrectionFieldKey = keyof ReturnType<
  typeof useCorrections
>['fieldMap']
