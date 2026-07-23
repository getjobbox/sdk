import type { HttpClient } from '../http/client';
import { toCsv } from '../http/client';
import type {
  CategoriesResult,
  CountryOptionsResult,
  JobGetResult,
  JobListParams,
  JobListResult,
  OpportunitiesCountResult,
  SimilarJobsResult,
} from '../types/jobs';

function opportunityOnlyWire(
  value: JobListParams['opportunityOnly']
): string | undefined {
  if (value === undefined) return undefined;
  if (value === true || value === 'true' || value === '1') return 'true';
  if (value === false || value === 'false' || value === '0') return 'false';
  return undefined;
}

export class JobsResource {
  constructor(private readonly http: HttpClient) {}

  async list(params: JobListParams = {}): Promise<JobListResult> {
    const page = params.page ?? 1;
    const perPage = params.perPage ?? 28;

    const data = await this.http.request<{ jobs: JobListResult['jobs']; total: number }>(
      'GET',
      '/sdk/jobs',
      {
        query: {
          search: params.search,
          location: params.location,
          country: params.country,
          work_mode: toCsv(params.workMode),
          seniority_level: toCsv(params.seniorityLevel),
          employment_types: toCsv(params.employmentTypes),
          benefit_filters: toCsv(params.benefitFilters),
          companies: toCsv(params.companies),
          date_from: params.dateFrom,
          date_to: params.dateTo,
          salary_min: params.salaryMin,
          salary_max: params.salaryMax,
          category: params.category,
          compensation_type: params.compensationType,
          application_mode: toCsv(params.applicationMode),
          opportunity_only: opportunityOnlyWire(params.opportunityOnly),
          page,
          per_page: perPage,
        },
      }
    );

    return {
      jobs: Array.isArray(data?.jobs) ? data.jobs : [],
      total: typeof data?.total === 'number' ? data.total : 0,
      page,
      perPage,
    };
  }

  async get(id: string): Promise<JobGetResult> {
    const data = await this.http.request<{ job: JobGetResult['job'] }>('GET', `/sdk/jobs/${encodeURIComponent(id)}`);
    return { job: data.job };
  }

  async similar(id: string): Promise<SimilarJobsResult> {
    const data = await this.http.request<{ jobs?: SimilarJobsResult['jobs'] } | SimilarJobsResult['jobs']>(
      'GET',
      `/sdk/jobs/${encodeURIComponent(id)}/similar`
    );
    if (Array.isArray(data)) {
      return { jobs: data };
    }
    if (data && typeof data === 'object' && Array.isArray((data as { jobs?: unknown }).jobs)) {
      return { jobs: (data as { jobs: SimilarJobsResult['jobs'] }).jobs };
    }
    return { jobs: [] };
  }

  async categories(): Promise<CategoriesResult> {
    return this.http.request<CategoriesResult>('GET', '/sdk/jobs/meta/categories');
  }

  async countryOptions(): Promise<CountryOptionsResult> {
    return this.http.request<CountryOptionsResult>('GET', '/sdk/jobs/meta/country-options');
  }

  async opportunitiesCount(): Promise<OpportunitiesCountResult> {
    return this.http.request<OpportunitiesCountResult>('GET', '/sdk/jobs/meta/opportunities-count');
  }
}
