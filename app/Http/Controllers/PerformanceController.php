<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Section;
use App\Models\Fault;
use App\Models\Department;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        // Date Filter Logic matching Call Centre Reports
        $filter = $request->input('filter', 'month');
        $selectedYear = $request->input('year', Carbon::now()->year);
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedQuarter = $request->input('quarter', isset($request->quarter) ? $request->quarter : Carbon::now()->quarter);
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');
        
        // Region Filter Logic
        $selectedRegionRaw = trim((string) $request->input('region', ''));
        $selectedRegion = $selectedRegionRaw === '' ? null : $selectedRegionRaw;
        $availableRegions = DB::table('cities')->select('region')->whereNotNull('region')->distinct()->orderBy('region')->pluck('region')->toArray();

        if ($filter == 'year') {
            if ($selectedYear == 'all') {
                $startDate = Carbon::create(2000, 1, 1)->startOfDay()->format('Y-m-d');
                $endDate = Carbon::now()->endOfDay()->format('Y-m-d');
            } else {
                $startDate = Carbon::create($selectedYear, 1, 1)->startOfYear()->format('Y-m-d');
                $endDate = Carbon::create($selectedYear, 1, 1)->endOfYear()->format('Y-m-d');
            }
        } elseif ($filter == 'quarter') {
             $startMonth = ($selectedQuarter - 1) * 3 + 1;
             $startDate = Carbon::create($selectedYear, $startMonth, 1)->startOfQuarter()->format('Y-m-d');
             $endDate = Carbon::create($selectedYear, $startMonth, 1)->endOfQuarter()->format('Y-m-d');
        } elseif ($filter == 'weekly') {
             $startDate = $startDateInput ? Carbon::parse($startDateInput)->format('Y-m-d') : Carbon::now()->startOfWeek()->format('Y-m-d');
             $endDate = $endDateInput ? Carbon::parse($endDateInput)->format('Y-m-d') : Carbon::now()->endOfWeek()->format('Y-m-d');
        } else { // month or default
             $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth()->format('Y-m-d');
             $endDate = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth()->format('Y-m-d');
        }

        // Available Years for Filter
        $availableYears = Fault::selectRaw('YEAR(created_at) as year')->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();
        if (empty($availableYears)) {
            $availableYears = [Carbon::now()->year];
        }

        // Technician/User Performance
        // Only users who have at least one fault assigned within the date range
        $usersQuery = User::whereHas('assignedFaults', function ($query) use ($startDate, $endDate, $selectedRegion) {
                $query->whereBetween('faults.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                if ($selectedRegion) {
                    $query->whereHas('city', function($q) use ($selectedRegion) {
                        $q->where('region', $selectedRegion);
                    });
                }
            });

        $totalUsersAssigned = $usersQuery->count();

        $users = $usersQuery->withCount(['assignedFaults as total_faults' => function ($query) use ($startDate, $endDate, $selectedRegion) {
                $query->whereBetween('faults.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                if ($selectedRegion) {
                    $query->whereHas('city', function($q) use ($selectedRegion) {
                        $q->where('region', $selectedRegion);
                    });
                }
            }])
            ->withCount(['assignedFaults as resolved_faults' => function ($query) use ($startDate, $endDate, $selectedRegion) {
                $query->whereIn('faults.status_id', [4, 5, 6]) // CLT, CLC, CLN
                      ->whereBetween('faults.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                if ($selectedRegion) {
                    $query->whereHas('city', function($q) use ($selectedRegion) {
                        $q->where('region', $selectedRegion);
                    });
                }
            }])
            ->with(['assignedFaults' => function ($query) use ($startDate, $endDate, $selectedRegion) {
                $query->whereIn('faults.status_id', [4, 5, 6])
                      ->whereBetween('faults.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                if ($selectedRegion) {
                    $query->whereHas('city', function($q) use ($selectedRegion) {
                        $q->where('region', $selectedRegion);
                    });
                }
                $query->select('faults.id', 'faults.created_at', 'faults.updated_at', 'faults.assignedTo');
            }])
            ->get()
            ->map(function ($user) {
                $user->pending_faults = $user->total_faults - $user->resolved_faults;
                $user->resolution_rate = $user->total_faults > 0 ? round(($user->resolved_faults / $user->total_faults) * 100, 1) : 0;
                
                // Calculate Avg Resolution Time (in Hours)
                $totalHours = $user->assignedFaults->sum(function($fault) {
                    return $fault->created_at->diffInHours($fault->updated_at);
                });
                $user->avg_resolution_time = $user->resolved_faults > 0 ? round($totalHours / $user->resolved_faults, 1) : 0;
                
                unset($user->assignedFaults); // Cleanup to save memory
                return $user;
            });

        // Section Performance
        $sections = Section::whereHas('faults', function ($query) use ($startDate, $endDate, $selectedRegion) {
                $query->whereBetween('faults.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                if ($selectedRegion) {
                    $query->whereHas('city', function($q) use ($selectedRegion) {
                        $q->where('region', $selectedRegion);
                    });
                }
            })
            ->withCount(['faults as total_faults' => function ($query) use ($startDate, $endDate, $selectedRegion) {
                $query->whereBetween('faults.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                if ($selectedRegion) {
                    $query->whereHas('city', function($q) use ($selectedRegion) {
                        $q->where('region', $selectedRegion);
                    });
                }
            }])
            ->withCount(['faults as resolved_faults' => function ($query) use ($startDate, $endDate, $selectedRegion) {
                $query->whereIn('faults.status_id', [4, 5, 6])
                      ->whereBetween('faults.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                if ($selectedRegion) {
                    $query->whereHas('city', function($q) use ($selectedRegion) {
                        $q->where('region', $selectedRegion);
                    });
                }
            }])
            ->with(['faults' => function ($query) use ($startDate, $endDate, $selectedRegion) {
                $query->whereIn('faults.status_id', [4, 5, 6])
                      ->whereBetween('faults.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                if ($selectedRegion) {
                    $query->whereHas('city', function($q) use ($selectedRegion) {
                        $q->where('region', $selectedRegion);
                    });
                }
                $query->select('faults.id', 'faults.created_at', 'faults.updated_at');
            }])
            ->get()
            ->map(function ($section) {
                $section->pending_faults = $section->total_faults - $section->resolved_faults;
                $section->resolution_rate = $section->total_faults > 0 ? round(($section->resolved_faults / $section->total_faults) * 100, 1) : 0;
                
                $totalHours = $section->faults->sum(function($fault) {
                    return $fault->created_at->diffInHours($fault->updated_at);
                });
                $section->avg_resolution_time = $section->resolved_faults > 0 ? round($totalHours / $section->resolved_faults, 1) : 0;
                
                unset($section->faults);
                return $section;
            });

        // Department Performance
        $departments = Department::with(['sections.faults' => function($query) use ($startDate, $endDate, $selectedRegion) {
                $query->whereBetween('faults.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                if ($selectedRegion) {
                    $query->whereHas('city', function($q) use ($selectedRegion) {
                        $q->where('region', $selectedRegion);
                    });
                }
                $query->select('faults.id', 'faults.created_at', 'faults.updated_at', 'faults.status_id');
            }])
            ->get()
            ->map(function ($department) {
                $allFaults = $department->sections->flatMap->faults;
                
                $totalFaults = $allFaults->count();
                $resolvedFaults = $allFaults->whereIn('status_id', [4, 5, 6]);
                $resolvedCount = $resolvedFaults->count();
                $pendingCount = $totalFaults - $resolvedCount;
                $resolutionRate = $totalFaults > 0 ? round(($resolvedCount / $totalFaults) * 100, 1) : 0;

                $totalHours = $resolvedFaults->sum(function($fault) {
                    return $fault->created_at->diffInHours($fault->updated_at);
                });
                $avgResolutionTime = $resolvedCount > 0 ? round($totalHours / $resolvedCount, 1) : 0;

                $department->total_faults = $totalFaults;
                $department->resolved_faults = $resolvedCount;
                $department->pending_faults = $pendingCount;
                $department->resolution_rate = $resolutionRate;
                $department->avg_resolution_time = $avgResolutionTime;

                unset($department->sections); // Cleanup
                return $department;
            })
            ->filter(function ($department) {
                return $department->total_faults > 0;
            });

        // Statistics for KPI Cards
        $topUser = $users->sortByDesc('resolution_rate')->first();
        $topSection = $sections->sortByDesc('resolution_rate')->first();
        $avgUserRate = $users->count() > 0 ? round($users->avg('resolution_rate'), 1) : 0;
        $avgSectionRate = $sections->count() > 0 ? round($sections->avg('resolution_rate'), 1) : 0;
        $avgUserTime = $users->count() > 0 ? round($users->avg('avg_resolution_time'), 1) : 0;

        // Prepare data for Charts
        $chartData = [
            'userLabels' => $users->pluck('name')->toArray(),
            'userRates' => $users->pluck('resolution_rate')->toArray(),
            'sectionLabels' => $sections->pluck('section')->toArray(),
            'sectionRates' => $sections->pluck('resolution_rate')->toArray(),
            'deptLabels' => $departments->pluck('department')->toArray(),
            'deptRates' => $departments->pluck('resolution_rate')->toArray(),
        ];

        return view('performance.index', compact(
            'users', 
            'sections', 
            'departments',
            'topUser', 
            'avgUserRate', 
            'topSection', 
            'avgSectionRate',
            'chartData',
            'totalUsersAssigned',
            'avgUserTime',
            'startDate',
            'endDate',
            'filter',
            'selectedYear',
            'selectedMonth',
            'selectedQuarter',
            'availableYears',
            'availableRegions',
            'selectedRegion'
        ));
    }
}





