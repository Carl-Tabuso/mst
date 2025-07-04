export interface Coworker {
    id: number;
    first_name: string;
    last_name: string;
    position?: {
      name: string;
    };
  }
  
  export interface Rating {
    evaluatee_id: number;
    performance_rating_id: number;
    description: string;
  }
  
  export interface EmployeeWithRating {
    id: number;
    full_name: string;
    position: string;
    average_rating: number | null;
  }
  
  export interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    next_page_url: string | null;
    prev_page_url: string | null;
  }
