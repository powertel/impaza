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
        
        $assignedCount = 0;
        $resolvedCount = 0;
        $avgResolutionSec = 0;
        $periodLabel = 'Personal';
        $waitingAssessmentCount = 0; // New metric for NOC

        // 1. NOC User Logic
        // "Noc anyone with noc section total faults in their section , assigned tohim or her , resolved for him pedning for him or her , copletion rate for him and waiting fro assessment"
        // Assuming NOC user has specific role or permission (e.g., 'noc-clear-faults-list' or section_id = 1 usually)
        if ($user->can('noc-clear-faults-list') || $user->section_id == 1) { // Adjust section_id as per your DB
             $periodLabel = 'NOC Personal';
             
             // Total Faults in their section (NOC section faults) OR Assigned to them specifically?
             // User prompt says "total faults in their section, assigned to him or her"
             // Interpretation: Personal stats within the context of NOC section + Waiting Assessment global count?
             // Let's stick to PERSONAL stats as requested ("assigned to him or her") but maybe add "Waiting Assessment" as extra.

             // Assigned to this user
             $assignedCount = (int) DB::table('fault_assignments')
                ->where('user_id', $user->id)
                ->count();

             // Resolved by this user
             $resolvedQuery = DB::table('fault_assignments')
                ->where('user_id', $user->id)
                ->whereNotNull('resolved_at');
             $resolvedCount = (int) $resolvedQuery->count();
             $avgResolutionSec = (int) floor($resolvedQuery->avg('duration_seconds') ?? 0);

             // Waiting for Assessment
             // "Waiting for assessment" usually implies Status 5 (Assessment) or 4 (Rectified awaiting NOC Clear).
             // User requested "for pending assessment only faults with status = 1" (Status 1 = Reported/Logged?)
             $waitingAssessmentCount = DB::table('faults')->where('status_id', 1)->count();
 
         }
        // 2. Section Manager Logic
        // "section manager should see both sates of all regions in that section"
        elseif ($user->can('department-faults-list')) {
            $periodLabel = 'Section (All Regions)';
            
            // All faults in this section (ignoring region filter to see "all regions")
            $query = DB::table('faults')
                ->leftJoin('fault_section', 'faults.id', '=', 'fault_section.fault_id')
                ->where('fault_section.section_id', $user->section_id);

            $assignedCount = $query->count(); // Total faults in section
            $resolvedCount = (clone $query)->where('faults.status_id', 6)->count(); // Resolved faults in section

            // Avg resolution time for the whole section
            $avgQuery = DB::table('fault_assignments')
                ->leftJoin('users', 'fault_assignments.user_id', '=', 'users.id')
                ->where('users.section_id', $user->section_id)
                ->whereNotNull('resolved_at');
            $avgResolutionSec = (int) floor($avgQuery->avg('duration_seconds') ?? 0);
        }
        // 3. Chief Technician Logic
        // "chief technician they see the number of faults in their section considering region"
        elseif ($user->can('chief-tech-clear-faults-list')) {
            $periodLabel = 'Section (Regional)';
            
            $query = DB::table('faults')
                ->leftJoin('fault_section', 'faults.id', '=', 'fault_section.fault_id')
                ->where('fault_section.section_id', $user->section_id);

            // Consider Region
            if (!empty($user->region)) {
                $query->leftJoin('cities', 'faults.city_id', '=', 'cities.id')
                      ->where('cities.region', $user->region);
            }

            $assignedCount = $query->count();
            $resolvedCount = (clone $query)->where('faults.status_id', 6)->count();

            // Avg resolution time for section + region
            $avgQuery = DB::table('fault_assignments')
                ->leftJoin('users', 'fault_assignments.user_id', '=', 'users.id')
                ->where('users.section_id', $user->section_id)
                ->whereNotNull('resolved_at');

            if (!empty($user->region)) {
                 $avgQuery->where('users.region', $user->region);
            }
            $avgResolutionSec = (int) floor($avgQuery->avg('duration_seconds') ?? 0);
        }
        // 4. Technician Logic (Default)
        // "technician like he sees the assigned faults stats , that were resolved , pending and completions rate"
        else {
            $periodLabel = 'Personal';
            
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
        
        return response()->json([
            'periodLabel' => $periodLabel,
            'assigned' => $assignedCount,
            'resolved' => $resolvedCount,
            'remaining' => $remainingCount,
            'avgResolutionSec' => $avgResolutionSec,
            'completionRate' => $completionRate,
            'waitingAssessment' => $waitingAssessmentCount, // New field for NOC
        ]);
    }
}