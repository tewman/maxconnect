<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;

class AdDataService
{
    /**
     * Fetch campaign data from the external API and return aggregated metrics.
     *
     * @return array{metrics: array<string, float|int>, error?: string}
     */
    public function getAggregatedMetrics(): array
    {
        $url = config('services.ad_api.url');
        $token = config('services.ad_api.token');

        if (empty($url) || empty($token)) {
            Log::warning('Ad API URL or token not configured');
            return ['metrics' => [], 'error' => 'Ad API is not configured.'];
        }

        try {
            // Laravel 9's Http::retry signature is retry(int $times, int $sleepMilliseconds = 0, callable $when = null)
            // so we use a fixed back‑off (1s) and keep conditional retry logic in $when.
            $response = Http::retry(
                5,
                1000,
                function (Exception $exception, $request) {
                    Log::warning('Ad API request exception', ['message' => $exception->getMessage()]);
                    // Only retry on:
                    // - Connection issues (timeouts, DNS failure, etc.)
                    // - HTTP 503 specifically
                    return $exception instanceof ConnectionException ||
                        ($exception instanceof RequestException &&
                            $exception->response?->status() === 503);
                }
            )
                ->timeout(15)
                ->withToken($token)
                ->get($url);

            if (!$response->successful()) {
                Log::warning('Ad API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [
                    'metrics' => [],
                    'error' => 'Unable to load campaign data. Please try again later.',
                ];
            }
            Log::warning('response: ' . json_encode($response->json()));
            $data = $response->json();
            $campaigns = $this->extractCampaigns($data);

            if (empty($campaigns)) {
                return ['metrics' => [], 'error' => null];
            }

            $metrics = $this->aggregateMetrics($campaigns);
            return ['metrics' => $metrics, 'error' => null];
        } catch (\Exception $e) {
            Log::error('Ad API request exception', ['message' => $e->getMessage()]);
            return [
                'metrics' => [],
                'error' => 'Unable to load campaign data. Please try again later.',
            ];
        }
    }

    /**
     * Extract the list of campaigns from the API response (handles wrapped payloads).
     *
     * @param mixed $data
     * @return array<int, array<string, mixed>>
     */
    protected function extractCampaigns($data): array
    {
        if (!is_array($data)) {
            return [];
        }
        if (isset($data['data']) && is_array($data['data'])) {
            return $data['data'];
        }
        if ($this->isList($data)) {
            return $data;
        }
        return [];
    }

    /**
     * Sum numeric metric fields across all campaigns.
     *
     * @param array<int, array<string, mixed>> $campaigns
     * @return array<string, float|int>
     */
    protected function aggregateMetrics(array $campaigns): array
    {
        $totals = [];
        $metricKeys = null;

        foreach ($campaigns as $campaign) {
            if (!is_array($campaign)) {
                continue;
            }
            if ($metricKeys === null) {
                $metricKeys = $this->detectMetricKeys($campaign);
            }
            foreach ($metricKeys as $key) {
                $value = $campaign[$key] ?? 0;
                if (is_numeric($value)) {
                    $totals[$key] = ($totals[$key] ?? 0) + (float) $value;
                }
            }
        }

        return $totals;
    }

    /**
     * Detect which keys in a campaign are numeric metrics (exclude id, name, etc.).
     *
     * @param array<string, mixed> $campaign
     * @return array<int, string>
     */
    protected function detectMetricKeys(array $campaign): array
    {
        $skip = ['id', 'name', 'campaign_id', 'campaign_name', 'title', 'created_at', 'updated_at'];
        $keys = [];
        foreach ($campaign as $key => $value) {
            if (in_array(strtolower($key), $skip, true)) {
                continue;
            }
            if (is_numeric($value) || (is_string($value) && is_numeric($value))) {
                $keys[] = $key;
            }
        }
        return $keys;
    }

    /**
     * @param array<mixed> $arr
     */
    protected function isList(array $arr): bool
    {
        if ($arr === []) {
            return true;
        }
        return array_keys($arr) === range(0, count($arr) - 1);
    }
}
