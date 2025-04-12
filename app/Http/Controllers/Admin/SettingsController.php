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
     * Display the admin dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // License stats
        $licenseStats = [
            'total' => AccessKey::count(),
            'active' => AccessKey::where('revoked', false)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', Carbon::now());
                })->count(),
            'expired' => AccessKey::where('revoked', false)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<', Carbon::now())
                ->count(),
            'revoked' => AccessKey::where('revoked', true)->count(),
            'expiring_soon' => AccessKey::where('revoked', false)
                ->whereNotNull('expires_at')
                ->whereBetween('expires_at', [Carbon::now(), Carbon::now()->addDays(30)])
                ->count(),
        ];
    }
}
