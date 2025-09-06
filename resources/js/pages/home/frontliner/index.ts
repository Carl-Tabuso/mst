export interface JobOrderCorrectionRequestStatus {
  status: 'Approved' | 'Pending' | 'Rejected'
  total: number
}
