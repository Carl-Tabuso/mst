export interface Technician {
  id: number;
  first_name: string;
  middle_name: string;
  last_name: string;
  suffix: string;
}

export interface MachineStatusOption {
  value: string;
  label: string;
}

export interface ExistingService {
  id: number;
  machine_type: string;
  model: string;
  serial_no: string;
  tag_no: string;
}

export interface ITServiceFormProps {
  technicians: Technician[];
  machineTypes: string[];
  machineStatuses: MachineStatusOption[];
  existingServices: ExistingService[];
}

export interface JobOrder {
  id: number;
  job_order_no: string;
  serviceableId: number;
  serviceable?: {
    status?: {
      value?: string
    }
    reportInitial?: { id: number }
    reportFinal?: { id: number }
  }
  status: string;
  date_time: string;
  client: string;
  contact_person: string;
  contact_no: string;
  address: string;
  department: string;
  created_at: string;
  updated_at: string;
}

export interface ITService {
  id: number;
  machine_type: string;
  model: string;
  serial_no: string;
  tag_no: string;
  machine_problem: string;
  technician?: {
    id: number;
    first_name: string;
    middle_name: string;
    last_name: string;
    suffix: string;
  };
  date_time?: string;
}

export interface ITServiceReport {
  id: number;
  it_service_id: number;
  onsite_type: 'Initial' | 'Final';
  service_performed?: string;
  recommendation?: string;
  machine_status?: string;
  machineStatusLabel?: string | null;
  attached_file?: string;
  parts_replaced?: string;
  final_remark?: string;
  final_machine_status?: string;
  created_at?: string;
  updated_at?: string;
}
