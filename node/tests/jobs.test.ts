import assert from 'node:assert/strict';
import { describe, it, mock } from 'node:test';
import { JobBox, JobBoxApiError } from '../src/index';

function jsonResponse(status: number, body: unknown, headers: Record<string, string> = {}) {
  return new Response(JSON.stringify(body), {
    status,
    headers: { 'Content-Type': 'application/json', ...headers },
  });
}

describe('JobBox Jobs SDK', () => {
  it('sends X-JobBox-Api-Key and User-Agent', async () => {
    const fetchMock = mock.fn(async (input: RequestInfo | URL, init?: RequestInit) => {
      const headers = new Headers(init?.headers);
      assert.equal(headers.get('X-JobBox-Api-Key'), 'jb_test_secret');
      assert.match(String(headers.get('User-Agent')), /^JobBoxNodeSDK\//);
      assert.equal(String(input), 'https://api.getjobbox.com/api/v1/sdk/jobs?page=1&per_page=28');
      return jsonResponse(200, { success: true, data: { jobs: [], total: 0 } });
    });

    const client = new JobBox({ apiKey: 'jb_test_secret', fetch: fetchMock as unknown as typeof fetch });
    await client.jobs.list();
    assert.equal(fetchMock.mock.callCount(), 1);
  });

  it('serializes array filters as CSV snake_case query params', async () => {
    const fetchMock = mock.fn(async (input: RequestInfo | URL) => {
      const url = new URL(String(input));
      assert.equal(url.searchParams.get('work_mode'), 'remote,hybrid');
      assert.equal(url.searchParams.get('seniority_level'), 'senior');
      assert.equal(url.searchParams.get('search'), 'react');
      return jsonResponse(200, {
        success: true,
        data: { jobs: [{ id: '1', title: 'Engineer' }], total: 1 },
      });
    });

    const client = new JobBox({
      apiKey: 'jb_test_secret',
      fetch: fetchMock as unknown as typeof fetch,
    });
    const result = await client.jobs.list({
      search: 'react',
      workMode: ['remote', 'hybrid'],
      seniorityLevel: ['senior'],
    });
    assert.equal(result.total, 1);
    assert.equal(result.jobs[0]?.title, 'Engineer');
    assert.equal(result.page, 1);
    assert.equal(result.perPage, 28);
  });

  it('unwraps data envelope for get', async () => {
    const fetchMock = mock.fn(async () =>
      jsonResponse(200, { success: true, data: { job: { id: 'abc', title: 'Dev' } } })
    );
    const client = new JobBox({ apiKey: 'k', fetch: fetchMock as unknown as typeof fetch });
    const { job } = await client.jobs.get('abc');
    assert.equal(job.title, 'Dev');
  });

  it('maps API errors to JobBoxApiError', async () => {
    const fetchMock = mock.fn(async () =>
      jsonResponse(401, { success: false, code: 'JB_APIKEY_401', message: 'Invalid or revoked API key' })
    );
    const client = new JobBox({ apiKey: 'bad', fetch: fetchMock as unknown as typeof fetch, maxRetries: 0 });
    await assert.rejects(
      () => client.jobs.list(),
      (err: unknown) => {
        assert.ok(err instanceof JobBoxApiError);
        assert.equal(err.status, 401);
        assert.equal(err.code, 'JB_APIKEY_401');
        return true;
      }
    );
  });

  it('retries GET on 503 then succeeds', async () => {
    let calls = 0;
    const fetchMock = mock.fn(async () => {
      calls += 1;
      if (calls === 1) {
        return jsonResponse(503, { success: false, message: 'unavailable' });
      }
      return jsonResponse(200, { success: true, data: { categories: [] } });
    });

    const client = new JobBox({
      apiKey: 'k',
      fetch: fetchMock as unknown as typeof fetch,
      maxRetries: 2,
    });
    const result = await client.jobs.categories();
    assert.deepEqual(result.categories, []);
    assert.equal(calls, 2);
  });

  it('requires apiKey', () => {
    assert.throws(() => new JobBox({ apiKey: '' }), /apiKey/);
  });
});
