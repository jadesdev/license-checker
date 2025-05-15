<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessKey;
use App\Models\ValidationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with summary stats
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Key statistics
        $keyStats = [
            'total' => AccessKey::count(),
            'active' => AccessKey::where('revoked', false)->count(),
            'revoked' => AccessKey::where('revoked', true)->count(),
            'expiring_soon' => AccessKey::where('revoked', false)
                ->where('expires_at', '>', Carbon::now())
                ->where('expires_at', '<', Carbon::now()->addDays(30))
                ->count(),
        ];

        // Validation statistics
        $validationStats = [
            'total' => ValidationLog::count(),
            'today' => ValidationLog::whereDate('created_at', Carbon::today())->count(),
            'this_week' => ValidationLog::where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
            'this_month' => ValidationLog::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            'valid' => ValidationLog::where('status', 'valid')->count(),
            'invalid' => ValidationLog::whereNotIn('status', ['valid', 'domain_registered'])->count(),
            'registrations' => ValidationLog::where('auto_registered', true)->count(),
        ];

        // Get recent activity (last 10 validation logs)
        $recentActivity = ValidationLog::with('accessKey')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Most active domains (last 30 days)
        $activeDomains = ValidationLog::select('domain', DB::raw('count(*) as validation_count'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('domain')
            ->orderBy('validation_count', 'desc')
            ->limit(5)
            ->get();

        // Most used access keys (last 30 days)
        $activeKeys = ValidationLog::select('access_key', DB::raw('count(*) as validation_count'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('access_key')
            ->orderBy('validation_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $key = AccessKey::where('key', $item->access_key)->first();

                return [
                    'key' => $item->access_key,
                    'owner_name' => $key ? $key->owner_name : 'Unknown',
                    'owner_email' => $key ? $key->owner_email : 'Unknown',
                    'validation_count' => $item->validation_count,
                ];
            });

        // Chart data for validations over time (last 14 days)
        $dailyValidations = ValidationLog::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count'),
            DB::raw('SUM(CASE WHEN status = "valid" THEN 1 ELSE 0 END) as valid_count'),
            DB::raw('SUM(CASE WHEN status != "valid" THEN 1 ELSE 0 END) as invalid_count')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartData = [
            'labels' => $dailyValidations->pluck('date')->toArray(),
            'validations' => $dailyValidations->pluck('count')->toArray(),
            'valid' => $dailyValidations->pluck('valid_count')->toArray(),
            'invalid' => $dailyValidations->pluck('invalid_count')->toArray(),
        ];

        return view('admin.dashboard.index', compact(
            'keyStats',
            'validationStats',
            'recentActivity',
            'activeDomains',
            'activeKeys',
            'chartData'
        ));
    }

    /**
     * Display detailed usage statistics
     *
     * @return \Illuminate\View\View
     */
    public function usageStats(Request $request)
    {
        // Date range selection (default to last 30 days)
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))
            : Carbon::now()->subDays(30);

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : Carbon::now();

        // Validation trends by day
        $dailyStats = ValidationLog::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as total'),
            DB::raw('SUM(CASE WHEN status = "valid" THEN 1 ELSE 0 END) as valid'),
            DB::raw('SUM(CASE WHEN status != "valid" AND status != "domain_registered" THEN 1 ELSE 0 END) as invalid'),
            DB::raw('SUM(CASE WHEN auto_registered = 1 THEN 1 ELSE 0 END) as registrations')
        )
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Status distribution
        $statusDistribution = ValidationLog::select('status', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('status')
            ->orderBy('count', 'desc')
            ->get();

        // Hourly distribution (for peak usage times)
        $hourlyDistribution = ValidationLog::select(
            DB::raw('HOUR(created_at) as hour'),
            DB::raw('count(*) as count')
        )
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Top IP addresses
        $topIPs = ValidationLog::select('ip_address', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('ip_address')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Summary statistics for the period
        $summary = [
            'total' => ValidationLog::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])->count(),
            'valid' => ValidationLog::where('status', 'valid')
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count(),
            'invalid' => ValidationLog::whereNotIn('status', ['valid', 'domain_registered'])
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count(),
            'registrations' => ValidationLog::where('auto_registered', true)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->count(),
            'unique_domains' => ValidationLog::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->distinct('domain')
                ->count('domain'),
            'unique_keys' => ValidationLog::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->distinct('access_key')
                ->count('access_key'),
            'unique_ips' => ValidationLog::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->distinct('ip_address')
                ->count('ip_address'),
        ];

        // Chart data
        $chartData = [
            'daily' => [
                'labels' => $dailyStats->pluck('date')->toArray(),
                'total' => $dailyStats->pluck('total')->toArray(),
                'valid' => $dailyStats->pluck('valid')->toArray(),
                'invalid' => $dailyStats->pluck('invalid')->toArray(),
                'registrations' => $dailyStats->pluck('registrations')->toArray(),
            ],
            'status' => [
                'labels' => $statusDistribution->pluck('status')->toArray(),
                'data' => $statusDistribution->pluck('count')->toArray(),
            ],
            'hourly' => [
                'labels' => $hourlyDistribution->pluck('hour')->toArray(),
                'data' => $hourlyDistribution->pluck('count')->toArray(),
            ],
        ];

        return view('admin.dashboard.usage', compact(
            'startDate',
            'endDate',
            'dailyStats',
            'statusDistribution',
            'hourlyDistribution',
            'topIPs',
            'summary',
            'chartData'
        ));
    }

    /**
     * Display detailed access key statistics
     *
     * @return \Illuminate\View\View
     */
    public function keyStats(Request $request)
    {
        // Date range selection (default to all time)
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))
            : null;

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : null;

        // Build base query
        $query = AccessKey::query();

        // Apply date filters if provided
        if ($startDate) {
            $query->where('created_at', '>=', $startDate->startOfDay());
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate->endOfDay());
        }

        // Get all keys with their usage statistics
        $keys = $query->get()->map(function ($key) use ($startDate, $endDate) {
            // Base validation log query for this key
            $logQuery = ValidationLog::where('access_key', $key->key);

            // Apply date filters if provided
            if ($startDate) {
                $logQuery->where('created_at', '>=', $startDate->startOfDay());
            }

            if ($endDate) {
                $logQuery->where('created_at', '<=', $endDate->endOfDay());
            }

            // Get validation counts
            $totalValidations = $logQuery->count();
            $validValidations = (clone $logQuery)->where('status', 'valid')->count();
            $invalidValidations = (clone $logQuery)->whereNotIn('status', ['valid', 'domain_registered'])->count();

            // Get unique domains
            $uniqueDomains = (clone $logQuery)->distinct('domain')->count('domain');

            // Get first and last validation
            $firstValidation = (clone $logQuery)->min('created_at');
            $lastValidation = (clone $logQuery)->max('created_at');

            return [
                'key' => $key,
                'total_validations' => $totalValidations,
                'valid_validations' => $validValidations,
                'invalid_validations' => $invalidValidations,
                'unique_domains' => $uniqueDomains,
                'first_validation' => $firstValidation ? Carbon::parse($firstValidation) : null,
                'last_validation' => $lastValidation ? Carbon::parse($lastValidation) : null,
                'is_expired' => $key->expires_at && Carbon::parse($key->expires_at)->isPast(),
            ];
        });

        // Sort by validation count (default)
        $sortField = $request->input('sort', 'total_validations');
        $sortDirection = $request->input('direction', 'desc');

        $keys = $keys->sortBy($sortField, SORT_REGULAR, $sortDirection === 'desc');

        // Key summary statistics
        $summary = [
            'total' => $keys->count(),
            'active' => $keys->where('key.revoked', false)->count(),
            'revoked' => $keys->where('key.revoked', true)->count(),
            'expired' => $keys->where('is_expired', true)->count(),
            'expiring_soon' => $keys->filter(function ($item) {
                return $item['key']->expires_at &&
                    ! Carbon::parse($item['key']->expires_at)->isPast() &&
                    Carbon::parse($item['key']->expires_at)->diffInDays() <= 30;
            })->count(),
            'unused' => $keys->where('total_validations', 0)->count(),
        ];

        // Pagination is manual since we're working with collections
        $perPage = 20;
        $page = $request->input('page', 1);
        $pagedKeys = $keys->forPage($page, $perPage);

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedKeys,
            $keys->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.dashboard.keys', compact('paginator', 'summary', 'startDate', 'endDate'));
    }

    /**
     * Display detailed domain statistics
     *
     * @return \Illuminate\View\View
     */
    public function domainStats(Request $request)
    {
        // Date range selection (default to last 90 days)
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))
            : Carbon::now()->subDays(90);

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : Carbon::now();

        // Get all domains with validation counts
        $domains = ValidationLog::select('domain', DB::raw('count(*) as validation_count'))
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('domain')
            ->get()
            ->map(function ($item) use ($startDate, $endDate) {
                // Base query for this domain
                $domainQuery = ValidationLog::where('domain', $item->domain)
                    ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

                // Get validation statistics
                $validCount = (clone $domainQuery)->where('status', 'valid')->count();
                $invalidCount = (clone $domainQuery)->whereNotIn('status', ['valid', 'domain_registered'])->count();
                $registrationCount = (clone $domainQuery)->where('auto_registered', true)->count();

                // Get unique keys used with this domain
                $uniqueKeys = (clone $domainQuery)->distinct('access_key')->count('access_key');

                // Get first and last validation
                $firstSeen = (clone $domainQuery)->min('created_at');
                $lastSeen = (clone $domainQuery)->max('created_at');

                return [
                    'domain' => $item->domain,
                    'validation_count' => $item->validation_count,
                    'valid_count' => $validCount,
                    'invalid_count' => $invalidCount,
                    'registration_count' => $registrationCount,
                    'unique_keys' => $uniqueKeys,
                    'first_seen' => $firstSeen ? Carbon::parse($firstSeen) : null,
                    'last_seen' => $lastSeen ? Carbon::parse($lastSeen) : null,
                ];
            });

        // Sorting
        $sortField = $request->input('sort', 'validation_count');
        $sortDirection = $request->input('direction', 'desc');

        $domains = $domains->sortBy($sortField, SORT_REGULAR, $sortDirection === 'desc');

        // Summary statistics
        $summary = [
            'total_domains' => $domains->count(),
            'total_validations' => $domains->sum('validation_count'),
            'valid_validations' => $domains->sum('valid_count'),
            'invalid_validations' => $domains->sum('invalid_count'),
            'total_registrations' => $domains->sum('registration_count'),
            'single_use_domains' => $domains->where('validation_count', 1)->count(),
        ];

        // Pagination is manual since we're working with collections
        $perPage = 20;
        $page = $request->input('page', 1);
        $pagedDomains = $domains->forPage($page, $perPage);

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedDomains,
            $domains->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.dashboard.domains', compact('paginator', 'summary', 'startDate', 'endDate'));
    }
}
