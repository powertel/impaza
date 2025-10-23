<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    /**
     * Return technician statistics for the authenticated user.
     * Optional query params: year, month (matching HomeController period logic)
     */
    public function myStats(Request $request)
    {
        $userId = $request->user()->id;

        // All-time counts without any date filters
        $myAssignedCount = (int) DB::table('fault_assignments')
            ->where('user_id', $userId)
            ->count();

        $myResolvedQuery = DB::table('fault_assignments')
            ->where('user_id', $userId)
            ->whereNotNull('resolved_at');

        $myResolvedCount = (int) $myResolvedQuery->count();
        $myAvgResolutionSec = (int) floor($myResolvedQuery->avg('duration_seconds') ?? 0);
        $myCompletionRate = $myAssignedCount > 0 ? round(($myResolvedCount / $myAssignedCount) * 100, 1) : 0.0;
        $myRemainingCount = max(0, $myAssignedCount - $myResolvedCount);

        return response()->json([
            'periodLabel' => 'All Time',
            'selectedYear' => null,
            'selectedMonth' => null,
            'assigned' => $myAssignedCount,
            'resolved' => $myResolvedCount,
            'remaining' => $myRemainingCount,
            'avgResolutionSec' => $myAvgResolutionSec,
            'completionRate' => $myCompletionRate,
        ]);
    }
}