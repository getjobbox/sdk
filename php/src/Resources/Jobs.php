<?php

declare(strict_types=1);

namespace GetJobBox\Resources;

use GetJobBox\Http\Client;

final class Jobs
{
    public function __construct(private readonly Client $http)
    {
    }

    /**
     * List catalog jobs.
     *
     * CamelCase param keys map to wire snake_case. Array filters become CSV.
     *
     * @param array{
     *   search?: string|null,
     *   location?: string|null,
     *   country?: string|null,
     *   workMode?: string|list<string>|null,
     *   seniorityLevel?: string|list<string>|null,
     *   employmentTypes?: string|list<string>|null,
     *   benefitFilters?: string|list<string>|null,
     *   companies?: string|list<string>|null,
     *   dateFrom?: string|null,
     *   dateTo?: string|null,
     *   salaryMin?: int|float|null,
     *   salaryMax?: int|float|null,
     *   category?: string|null,
     *   compensationType?: string|null,
     *   applicationMode?: string|list<string>|null,
     *   opportunityOnly?: bool|string|null,
     *   page?: int|null,
     *   perPage?: int|null
     * } $params
     *
     * @return array{jobs: list<array<string, mixed>>, total: int, page: int, perPage: int}
     */
    public function list(array $params = []): array
    {
        $page = isset($params['page']) && $params['page'] !== null ? (int) $params['page'] : 1;
        $perPage = isset($params['perPage']) && $params['perPage'] !== null ? (int) $params['perPage'] : 28;

        $data = $this->http->request('GET', '/sdk/jobs', [
            'search' => $params['search'] ?? null,
            'location' => $params['location'] ?? null,
            'country' => $params['country'] ?? null,
            'work_mode' => Client::toCsv($params['workMode'] ?? null),
            'seniority_level' => Client::toCsv($params['seniorityLevel'] ?? null),
            'employment_types' => Client::toCsv($params['employmentTypes'] ?? null),
            'benefit_filters' => Client::toCsv($params['benefitFilters'] ?? null),
            'companies' => Client::toCsv($params['companies'] ?? null),
            'date_from' => $params['dateFrom'] ?? null,
            'date_to' => $params['dateTo'] ?? null,
            'salary_min' => $params['salaryMin'] ?? null,
            'salary_max' => $params['salaryMax'] ?? null,
            'category' => $params['category'] ?? null,
            'compensation_type' => $params['compensationType'] ?? null,
            'application_mode' => Client::toCsv($params['applicationMode'] ?? null),
            'opportunity_only' => self::opportunityOnlyWire($params['opportunityOnly'] ?? null),
            'page' => $page,
            'per_page' => $perPage,
        ]);

        $jobs = [];
        $total = 0;
        if (is_array($data)) {
            if (isset($data['jobs']) && is_array($data['jobs'])) {
                /** @var list<array<string, mixed>> $jobs */
                $jobs = array_values(array_filter($data['jobs'], 'is_array'));
            }
            if (isset($data['total']) && is_numeric($data['total'])) {
                $total = (int) $data['total'];
            }
        }

        return [
            'jobs' => $jobs,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
        ];
    }

    /**
     * @return array{job: array<string, mixed>}
     */
    public function get(string $id): array
    {
        $data = $this->http->request('GET', '/sdk/jobs/' . rawurlencode($id));
        $job = (is_array($data) && isset($data['job']) && is_array($data['job'])) ? $data['job'] : [];

        return ['job' => $job];
    }

    /**
     * @return array{jobs: list<array<string, mixed>>}
     */
    public function similar(string $id): array
    {
        $data = $this->http->request('GET', '/sdk/jobs/' . rawurlencode($id) . '/similar');
        if (is_array($data) && array_is_list($data)) {
            /** @var list<array<string, mixed>> $list */
            $list = array_values(array_filter($data, 'is_array'));

            return ['jobs' => $list];
        }
        if (is_array($data) && isset($data['jobs']) && is_array($data['jobs'])) {
            /** @var list<array<string, mixed>> $list */
            $list = array_values(array_filter($data['jobs'], 'is_array'));

            return ['jobs' => $list];
        }

        return ['jobs' => []];
    }

    /**
     * @return array<string, mixed>
     */
    public function categories(): array
    {
        $data = $this->http->request('GET', '/sdk/jobs/meta/categories');

        return is_array($data) ? $data : [];
    }

    /**
     * @return array<string, mixed>
     */
    public function countryOptions(): array
    {
        $data = $this->http->request('GET', '/sdk/jobs/meta/country-options');

        return is_array($data) ? $data : [];
    }

    /**
     * @return array<string, mixed>
     */
    public function opportunitiesCount(): array
    {
        $data = $this->http->request('GET', '/sdk/jobs/meta/opportunities-count');

        return is_array($data) ? $data : [];
    }

    private static function opportunityOnlyWire(bool|string|null $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if ($value === true || $value === 'true' || $value === '1') {
            return 'true';
        }
        if ($value === false || $value === 'false' || $value === '0') {
            return 'false';
        }

        return null;
    }
}
