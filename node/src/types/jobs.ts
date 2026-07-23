/** Wire-level job object (fields vary; typed loosely for forward compatibility). */
export interface Job {
  id: string;
  title?: string | null;
  company?: string | null;
  location?: string | null;
  category?: string | null;
  work_mode?: string | null;
  seniority_level?: string | null;
  employment_type?: string | null;
  status?: string | null;
  [key: string]: unknown;
}

export interface JobListResult {
  jobs: Job[];
  total: number;
  page: number;
  perPage: number;
}

export interface JobGetResult {
  job: Job;
}

export interface SimilarJobsResult {
  jobs: Job[];
}

export interface CategoriesResult {
  categories: Array<{
    id: string;
    slug: string;
    label: string;
    sort_order?: number;
  }>;
}

export interface CountryOptionsResult {
  options?: unknown;
  [key: string]: unknown;
}

export interface OpportunitiesCountResult {
  total?: number;
  [key: string]: unknown;
}

export interface JobListParams {
  search?: string;
  location?: string;
  country?: string;
  /** Serialized as CSV on the wire */
  workMode?: string | string[];
  seniorityLevel?: string | string[];
  employmentTypes?: string | string[];
  benefitFilters?: string | string[];
  companies?: string | string[];
  dateFrom?: string;
  dateTo?: string;
  salaryMin?: number;
  salaryMax?: number;
  category?: string;
  compensationType?: 'job' | 'gig';
  applicationMode?: string | string[];
  opportunityOnly?: boolean | 'true' | '1' | 'false' | '0';
  page?: number;
  perPage?: number;
}
