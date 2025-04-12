<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ValidationLog;
use App\Models\AccessKey;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ValidationLogController extends Controller
{
    /**
     * Display a listing of all validation logs
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = ValidationLog::query()->with('accessKey');

        // Apply filters
        if ($request->filled('access_key')) {
            $query->where('access_key', $request->access_key);
        }

        if ($request->filled('domain')) {
            $query->where('domain', 'like', '%' . $request->domain . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        if ($request->filled('ip')) {
            $query->where('ip_address', 'like', '%' . $request->ip . '%');
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $logs = $query->paginate(50);

        // Get filter options for dropdowns
        $statuses = ValidationLog::distinct('status')->pluck('status');
        $keys = AccessKey::select('key', 'owner_name', 'owner_email')
            ->get()
            ->map(function ($key) {
                return [
                    'key' => $key->key,
                    'label' => "{$key->owner_name} ({$key->owner_email}) - {$key->key}"
                ];
            });

        // Get stats
        $stats = [
            'total' => ValidationLog::count(),
            'today' => ValidationLog::whereDate('created_at', Carbon::today())->count(),
            'valid' => ValidationLog::where('status', 'valid')->count(),
            'invalid' => ValidationLog::whereNotIn('status', ['valid', 'domain_registered'])->count(),
            'registrations' => ValidationLog::where('auto_registered', true)->count(),
        ];

        return view('admin.logs.index', compact('logs', 'statuses', 'keys', 'stats'));
    }

    /**
     * Display logs for a specific domain
     *
     * @param string $domain
     * @return \Illuminate\View\View
     */
    public function byDomain($domain)
    {
        $logs = ValidationLog::where('domain', $domain)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $firstSeen = ValidationLog::where('domain', $domain)->min('created_at');
        $lastSeen = ValidationLog::where('domain', $domain)->max('created_at');

        $domainStats = [
            'total_validations' => ValidationLog::where('domain', $domain)->count(),
            'keys_used' => ValidationLog::where('domain', $domain)->distinct('access_key')->count('access_key'),
            'first_seen' => $firstSeen ? Carbon::parse($firstSeen) : null,
            'last_seen' => $lastSeen ? Carbon::parse($lastSeen) : null,
        ];

        return view('admin.logs.by-domain', compact('logs', 'domain', 'domainStats'));
    }

    /**
     * Display logs for a specific access key
     *
     * @param string $access_key
     * @return \Illuminate\View\View
     */
    public function byKey($access_key)
    {
        $key = AccessKey::where('key', $access_key)->firstOrFail();

        $logs = ValidationLog::where('access_key', $access_key)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Convert dates to Carbon instances
        $firstSeen = ValidationLog::where('access_key', $access_key)->min('created_at');
        $lastSeen = ValidationLog::where('access_key', $access_key)->max('created_at');

        $keyStats = [
            'total_validations' => ValidationLog::where('access_key', $access_key)->count(),
            'domains_used' => ValidationLog::where('access_key', $access_key)->distinct('domain')->count('domain'),
            'first_seen' => $firstSeen ? Carbon::parse($firstSeen) : null,
            'last_seen' => $lastSeen ? Carbon::parse($lastSeen) : null,
        ];

        return view('admin.logs.by-key', compact('logs', 'key', 'keyStats'));
    }

    /**
     * Display logs for a specific status
     *
     * @param string $status
     * @return \Illuminate\View\View
     */
    public function byStatus($status)
    {
        // Convert URL parameter to actual status format
        $status = str_replace('-', ' ', $status);

        $logs = ValidationLog::where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Get dates as Carbon instances
        $firstSeen = ValidationLog::where('status', $status)->min('created_at');
        $lastSeen = ValidationLog::where('status', $status)->max('created_at');

        $statusStats = [
            'total' => ValidationLog::where('status', $status)->count(),
            'today' => ValidationLog::where('status', $status)
                ->whereDate('created_at', Carbon::today())
                ->count(),
            'keys_affected' => ValidationLog::where('status', $status)
                ->distinct('access_key')
                ->count('access_key'),
            'domains_affected' => ValidationLog::where('status', $status)
                ->distinct('domain')
                ->count('domain'),
            'first_seen' => $firstSeen ? Carbon::parse($firstSeen) : null,
            'last_seen' => $lastSeen ? Carbon::parse($lastSeen) : null,
        ];

        return view('admin.logs.by-status', compact('logs', 'status', 'statusStats'));
    }

    /**
     * Search for logs
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (empty($query)) {
            return redirect()->route('admin.logs.index');
        }

        $logs = ValidationLog::where('domain', 'like', "%{$query}%")
            ->orWhere('access_key', 'like', "%{$query}%")
            ->orWhere('ip_address', 'like', "%{$query}%")
            ->orWhere('message', 'like', "%{$query}%")
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.logs.search-results', compact('logs', 'query'));
    }

    /**
     * Export logs as CSV
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $query = ValidationLog::query();

        // Apply filters (same as index)
        if ($request->filled('access_key')) {
            $query->where('access_key', $request->access_key);
        }

        if ($request->filled('domain')) {
            $query->where('domain', 'like', '%' . $request->domain . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        // Limit export size
        $logs = $query->orderBy('created_at', 'desc')->limit(10000)->get();

        // Create CSV
        $filename = 'validation_logs_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = ['id', 'access_key', 'domain', 'url', 'ip_address', 'status', 'message', 'auto_registered', 'created_at'];

        $callback = function () use ($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                $row = [];
                foreach ($columns as $column) {
                    $row[] = $log->{$column};
                }
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return Response::streamDownload($callback, $filename, $headers);
    }

    /**
     * Delete a specific log entry
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $log = ValidationLog::findOrFail($id);
        $log->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Log entry deleted successfully'
            ]);
        }

        return redirect()->back()
            ->with('success', 'Log entry deleted successfully.');
    }

    /**
     * Clean up old log entries
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cleanup(Request $request)
    {
        $days = $request->input('days', 90);
        $cutoffDate = Carbon::now()->subDays($days);

        $count = ValidationLog::where('created_at', '<', $cutoffDate)->count();
        ValidationLog::where('created_at', '<', $cutoffDate)->delete();

        return redirect()->route('admin.logs.index')
            ->with('success', "{$count} log entries older than {$days} days have been deleted.");
    }
}
