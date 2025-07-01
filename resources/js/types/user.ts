export interface User {
  id: number;
  email: string;
  employee: {
    id: number;
    position: string;
    full_name: string;
  };
}

export type Position = 
  | 'Team Leader'
  | 'Safety Officer'
  | 'Human Resource'
