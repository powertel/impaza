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
        $user = $request->user();
        
        // Determine scope based on permissions
        // NOC / Admin: View Global Stats (or Regional)
        if ($user->can('noc-clear-faults-list')) {
            $query = DB::table('faults');
            
            // Filter by region if applicable (e.g. Regional NOC)
            if (in_array((int)$user->section_id, [2, 3], true) && !empty($user->region)) {
                $query->leftJoin('cities', 'faults.city_id', '=', 'cities.id')
                      ->where('cities.region', $user->region);
            }

            $assignedCount = $query->count();
            $resolvedCount = (clone $query)->where('faults.status_id', 6)->count(); // Status 6 = Resolved/NOC Cleared
            
            // Calculate average resolution time from assignments
            $avgQuery = DB::table('fault_assignments')
                ->whereNotNull('resolved_at');
                
            if (in_array((int)$user->section_id, [2, 3], true) && !empty($user->region)) {
                 $avgQuery->leftJoin('faults', 'fault_assignments.fault_id', '=', 'faults.id')
                          ->leftJoin('cities', 'faults.city_id', '=', 'cities.id')
                          ->where('cities.region', $user->region);
            }
            
            $avgResolutionSec = (int) floor($avgQuery->avg('duration_seconds') ?? 0);
            
        } 
        // Section Manager: View Section Stats
        elseif ($user->can('department-faults-list')) {
            $query = DB::table('faults')
                ->leftJoin('fault_section', 'faults.id', '=', 'fault_section.fault_id')
                ->where('fault_section.section_id', $user->section_id);

            if (in_array((int)$user->section_id, [2, 3], true) && !empty($user->region)) {
                $query->leftJoin('cities', 'faults.city_id', '=', 'cities.id')
                      ->where('cities.region', $user->region);
            }

            $assignedCount = $query->count();
            $resolvedCount = (clone $query)->where('faults.status_id', 6)->count();
            
            $avgQuery = DB::table('fault_assignments')
                ->leftJoin('users', 'fault_assignments.user_id', '=', 'users.id')
                ->where('users.section_id', $user->section_id)
                ->whereNotNull('resolved_at');
                
            $avgResolutionSec = (int) floor($avgQuery->avg('duration_seconds') ?? 0);

        } 
        // Technician: View Own Stats
        else {
            $assignedCount = (int) DB::table('fault_assignments')
                ->where('user_id', $user->id)
                ->count();

            $resolvedQuery = DB::table('fault_assignments')
                ->where('user_id', $user->id)
                ->whereNotNull('resolved_at');

            $resolvedCount = (int) $resolvedQuery->count();
            $avgResolutionSec = (int) floor($resolvedQuery->avg('duration_seconds') ?? 0);
        }

        $completionRate = $assignedCount > 0 ? round(($resolvedCount / $assignedCount) * 100, 1) : 0.0;
        $remainingCount = max(0, $assignedCount - $resolvedCount);
        
        // For NOC/Managers, "Remaining" might be better calculated as "Not Status 6"
        // But the logic above (Assigned - Resolved) holds mathematically if Assigned = Total and Resolved = Status 6.

        return response()->json([
            'periodLabel' => $user->can('noc-clear-faults-list') ? 'Global' : ($user->can('department-faults-list') ? 'Section' : 'Personal'),
            'selectedYear' => null,
            'selectedMonth' => null,
            'assigned' => $assignedCount,
            'resolved' => $resolvedCount,
            'remaining' => $remainingCount,
            'avgResolutionSec' => $avgResolutionSec,
            'completionRate' => $completionRate,
        ]);
    }
}