export function useIncidentStatus() {
  const statusMap = {
    'for verification': { label: 'For Verification', variant: 'outline' },
    verified: { label: 'Verified', variant: 'success' },
    dropped: { label: 'Dropped', variant: 'destructive' },
    'no incident': { label: 'No Incident', variant: 'secondary' },
    draft: { label: 'Draft', variant: 'secondary' },
  } as const

  type StatusKey = keyof typeof statusMap
  type StatusDisplay = { label: string; variant: string }

  const getStatusDisplay = (status: string): StatusDisplay => {
    const key = status.toLowerCase() as StatusKey
    if (key in statusMap) {
      return statusMap[key]
    }
    return { label: status, variant: 'secondary' }
  }

  return {
    getStatusDisplay,
  }
}
