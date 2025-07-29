export interface Technician {
  id: number
  first_name: string
  middle_name: string
  last_name: string
  suffix: string
}

export interface MachineStatusOption {
  value: string
  label: string
}

export interface ExistingService {
  id: number
  machine_type: string
  model: string
  serial_no: string
  tag_no: string
}

export interface ITServiceFormProps {
  technicians: Technician[]
  machineTypes: string[]
  machineStatuses: MachineStatusOption[]
  existingServices: ExistingService[]
}
