<?php

namespace App\Http\Controllers;

use App\Models\LteSiteSurvey;
use App\Models\LteSiteSurveyPhoto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LteSiteSurveyController extends Controller
{
    public function reports(Request $request)
    {
        $filter = strtolower((string) $request->input('filter', 'month'));
        $selectedRegionRaw = trim((string) $request->input('region', ''));
        $selectedRegion = $selectedRegionRaw === '' ? null : $selectedRegionRaw;
        $selectedStatusRaw = trim((string) $request->input('status', ''));
        $selectedStatus = $selectedStatusRaw === '' ? null : $selectedStatusRaw;
        $selectedCapturedBy = (int) $request->input('captured_by', 0);
        $selectedPerformedBy = trim((string) $request->input('performed_by', ''));
        $selectedPerformedBy = $selectedPerformedBy === '' ? null : $selectedPerformedBy;

        $availableRegions = LteSiteSurvey::query()
            ->whereNotNull('province_region')
            ->where('province_region', '!=', '')
            ->distinct()
            ->orderBy('province_region')
            ->pluck('province_region')
            ->toArray();

        $availableYears = DB::table('lte_site_surveys')
            ->selectRaw('YEAR(created_at) as y')
            ->distinct()
            ->orderByDesc('y')
            ->pluck('y')
            ->toArray();

        $availablePerformedBy = LteSiteSurvey::query()
            ->whereNotNull('survey_performed_by')
            ->where('survey_performed_by', '!=', '')
            ->distinct()
            ->orderBy('survey_performed_by')
            ->pluck('survey_performed_by')
            ->toArray();

        $users = User::query()->where('is_access', 0)->orderBy('name')->get(['id', 'name']);

        $now = Carbon::now();
        $yearInput = $request->input('year', $now->year);
        $isAllYears = strtolower((string) $yearInput) === 'all';
        $selectedYear = $isAllYears ? null : (int) $yearInput;
        $selectedMonth = (int) ($request->input('month', $now->month));
        $quarter = (int) ($request->input('quarter', 1));

        $startDateInput = trim((string) $request->input('start_date', ''));
        $endDateInput = trim((string) $request->input('end_date', ''));

        $periodStart = $now->copy()->startOfMonth();
        $periodEnd = $now->copy()->endOfMonth();
        $periodLabelText = 'Selected period';

        try {
            if ($filter === 'month') {
                $y = $selectedYear ?? (int) $now->year;
                $periodStart = Carbon::create($y, $selectedMonth, 1)->startOfMonth();
                $periodEnd = Carbon::create($y, $selectedMonth, 1)->endOfMonth();
                $periodLabelText = 'Monthly';
            } elseif ($filter === 'year') {
                if ($isAllYears && !empty($availableYears)) {
                    $minYear = min($availableYears);
                    $maxYear = max($availableYears);
                    $periodStart = Carbon::create((int) $minYear, 1, 1)->startOfYear();
                    $periodEnd = Carbon::create((int) $maxYear, 12, 31)->endOfDay();
                    $periodLabelText = 'All years';
                } else {
                    $y = $selectedYear ?? (int) $now->year;
                    $periodStart = Carbon::create($y, 1, 1)->startOfYear();
                    $periodEnd = Carbon::create($y, 12, 31)->endOfYear();
                    $periodLabelText = 'Yearly';
                }
            } elseif ($filter === 'quarter') {
                $y = $selectedYear ?? (int) $now->year;
                $q = max(1, min(4, (int) $quarter));
                $startMonth = (($q - 1) * 3) + 1;
                $periodStart = Carbon::create($y, $startMonth, 1)->startOfMonth();
                $periodEnd = Carbon::create($y, $startMonth, 1)->addMonths(2)->endOfMonth();
                $periodLabelText = 'Quarterly';
            } elseif ($filter === 'weekly') {
                if ($startDateInput !== '' && $endDateInput !== '') {
                    $periodStart = Carbon::parse($startDateInput)->startOfDay();
                    $periodEnd = Carbon::parse($endDateInput)->endOfDay();
                    $periodLabelText = 'Custom range';
                } else {
                    $periodStart = $now->copy()->startOfWeek(Carbon::MONDAY);
                    $periodEnd = $now->copy()->endOfWeek(Carbon::SUNDAY);
                    $periodLabelText = 'This week';
                }
            }
        } catch (\Throwable $e) {
            $periodStart = $now->copy()->startOfMonth();
            $periodEnd = $now->copy()->endOfMonth();
            $periodLabelText = 'Selected period';
        }

        $applyFilters = function ($q) use (
            $periodStart,
            $periodEnd,
            $selectedRegion,
            $selectedStatus,
            $selectedCapturedBy,
            $selectedPerformedBy
        ) {
            $q->whereBetween('s.created_at', [$periodStart, $periodEnd]);
            if ($selectedRegion) {
                $q->where('s.province_region', $selectedRegion);
            }
            if ($selectedStatus) {
                $q->where('s.status', $selectedStatus);
            }
            if ($selectedCapturedBy > 0) {
                $q->where('s.user_id', $selectedCapturedBy);
            }
            if ($selectedPerformedBy) {
                $q->where('s.survey_performed_by', $selectedPerformedBy);
            }
        };

        $base = DB::table('lte_site_surveys as s');
        $applyFilters($base);

        $statusCounts = (clone $base)
            ->select('s.status', DB::raw('COUNT(*) as c'))
            ->groupBy('s.status')
            ->pluck('c', 'status')
            ->toArray();

        $total = array_sum(array_map('intval', $statusCounts));
        $draft = (int) ($statusCounts['draft'] ?? 0);
        $submitted = (int) ($statusCounts['submitted'] ?? 0);

        $withPhotos = (int) DB::table('lte_site_surveys as s')
            ->leftJoin('lte_site_survey_photos as p', 'p.lte_site_survey_id', '=', 's.id')
            ->whereNotNull('p.id')
            ->when(true, function ($q) use ($applyFilters) {
                $applyFilters($q);
            })
            ->distinct('s.id')
            ->count('s.id');

        $withRemarks = (int) DB::table('lte_site_surveys as s')
            ->leftJoin('lte_site_survey_remarks as r', 'r.lte_site_survey_id', '=', 's.id')
            ->whereNotNull('r.id')
            ->when(true, function ($q) use ($applyFilters) {
                $applyFilters($q);
            })
            ->distinct('s.id')
            ->count('s.id');

        $regionBreakdown = (clone $base)
            ->select('s.province_region as k', DB::raw('COUNT(*) as c'))
            ->whereNotNull('s.province_region')
            ->where('s.province_region', '!=', '')
            ->groupBy('s.province_region')
            ->orderByDesc('c')
            ->limit(12)
            ->get();

        $performedByBreakdown = (clone $base)
            ->select('s.survey_performed_by as k', DB::raw('COUNT(*) as c'))
            ->whereNotNull('s.survey_performed_by')
            ->where('s.survey_performed_by', '!=', '')
            ->groupBy('s.survey_performed_by')
            ->orderByDesc('c')
            ->limit(12)
            ->get();

        $backhaulBreakdown = collect();
        $powerBreakdown = collect();
        try {
            $backhaulExpr = "COALESCE(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(s.payload,'$.transmission.backhaulType')),''),'unknown')";
            $powerExpr = "COALESCE(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(s.payload,'$.power.powerSourceType')),''),'unknown')";

            $backhaulBreakdown = (clone $base)
                ->selectRaw($backhaulExpr . " as k, COUNT(*) as c")
                ->groupBy('k')
                ->orderByDesc('c')
                ->get();

            $powerBreakdown = (clone $base)
                ->selectRaw($powerExpr . " as k, COUNT(*) as c")
                ->groupBy('k')
                ->orderByDesc('c')
                ->get();
        } catch (\Throwable $e) {
            $rows = (clone $base)->select(['s.id', 's.payload'])->get();
            $backhaulMap = [];
            $powerMap = [];
            foreach ($rows as $row) {
                $payload = is_array($row->payload) ? $row->payload : (array) json_decode((string) $row->payload, true);
                $backhaul = (string) data_get($payload, 'transmission.backhaulType', 'unknown');
                $power = (string) data_get($payload, 'power.powerSourceType', 'unknown');
                $backhaul = trim($backhaul) !== '' ? $backhaul : 'unknown';
                $power = trim($power) !== '' ? $power : 'unknown';
                $backhaulMap[$backhaul] = ($backhaulMap[$backhaul] ?? 0) + 1;
                $powerMap[$power] = ($powerMap[$power] ?? 0) + 1;
            }
            arsort($backhaulMap);
            arsort($powerMap);
            $backhaulBreakdown = collect($backhaulMap)->map(function ($c, $k) {
                return (object) ['k' => $k, 'c' => (int) $c];
            })->values();
            $powerBreakdown = collect($powerMap)->map(function ($c, $k) {
                return (object) ['k' => $k, 'c' => (int) $c];
            })->values();
        }

        $surveys = LteSiteSurvey::query()
            ->from('lte_site_surveys as s')
            ->with('user:id,name')
            ->select('s.*')
            ->whereBetween('s.created_at', [$periodStart, $periodEnd])
            ->when($selectedRegion, fn ($q) => $q->where('s.province_region', $selectedRegion))
            ->when($selectedStatus, fn ($q) => $q->where('s.status', $selectedStatus))
            ->when($selectedCapturedBy > 0, fn ($q) => $q->where('s.user_id', $selectedCapturedBy))
            ->when($selectedPerformedBy, fn ($q) => $q->where('s.survey_performed_by', $selectedPerformedBy))
            ->orderByDesc('s.created_at')
            ->paginate(20)
            ->appends($request->query());

        return view('lte_site_surveys.reports', [
            'filter' => $filter,
            'availableRegions' => $availableRegions,
            'availableYears' => $availableYears,
            'availablePerformedBy' => $availablePerformedBy,
            'users' => $users,
            'selectedRegion' => $selectedRegion,
            'selectedStatus' => $selectedStatus,
            'selectedCapturedBy' => $selectedCapturedBy,
            'selectedPerformedBy' => $selectedPerformedBy,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'quarter' => $quarter,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'periodLabelText' => $periodLabelText,
            'total' => $total,
            'draft' => $draft,
            'submitted' => $submitted,
            'withPhotos' => $withPhotos,
            'withRemarks' => $withRemarks,
            'regionBreakdown' => $regionBreakdown,
            'performedByBreakdown' => $performedByBreakdown,
            'backhaulBreakdown' => $backhaulBreakdown,
            'powerBreakdown' => $powerBreakdown,
            'surveys' => $surveys,
        ]);
    }

    public function map(Request $request)
    {
        $status = trim((string) $request->input('status', ''));
        $status = $status === '' ? null : $status;

        $rows = LteSiteSurvey::query()
            ->select(['id', 'site_name', 'province_region', 'status', 'latitude', 'longitude', 'coordinates', 'created_at'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->limit(5000)
            ->get();

        $points = [];
        $missing = 0;
        foreach ($rows as $s) {
            $lat = $s->latitude;
            $lng = $s->longitude;

            if (($lat === null || $lat === '') || ($lng === null || $lng === '')) {
                $coords = trim((string) ($s->coordinates ?? ''));
                if ($coords !== '' && str_contains($coords, ',')) {
                    [$a, $b] = array_map('trim', explode(',', $coords, 2));
                    if (is_numeric($a) && is_numeric($b)) {
                        $lat = (float) $a;
                        $lng = (float) $b;
                    }
                }
            }

            if (!is_numeric($lat) || !is_numeric($lng)) {
                $missing++;
                continue;
            }

            $points[] = [
                'id' => (int) $s->id,
                'site_name' => (string) ($s->site_name ?: 'Untitled'),
                'province_region' => (string) ($s->province_region ?: ''),
                'status' => (string) ($s->status ?: ''),
                'lat' => (float) $lat,
                'lng' => (float) $lng,
                'created_at' => $s->created_at ? $s->created_at->format('Y-m-d H:i') : '',
            ];
        }

        return view('lte_site_surveys.map', [
            'status' => $status,
            'points' => $points,
            'total' => $rows->count(),
            'plotted' => count($points),
            'missing' => $missing,
        ]);
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $status = trim((string) $request->input('status', ''));
        $perPage = (int) $request->input('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 20;

        $query = LteSiteSurvey::query()
            ->with('user:id,name')
            ->with('photos')
            ->withCount('photos')
            ->orderByDesc('created_at');

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('site_name', 'like', '%' . $q . '%')
                    ->orWhere('jc_number', 'like', '%' . $q . '%')
                    ->orWhere('province_region', 'like', '%' . $q . '%')
                    ->orWhere('coordinates', 'like', '%' . $q . '%');
            });
        }

        $statsQuery = clone $query;
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'draft' => (clone $statsQuery)->where('status', 'draft')->count(),
            'submitted' => (clone $statsQuery)->where('status', 'submitted')->count(),
            'latest_created_at' => (clone $statsQuery)->max('created_at'),
        ];

        $surveys = $query->paginate($perPage)->appends($request->only('q', 'status', 'per_page'));

        $remarksBySurvey = collect();
        $surveyIds = $surveys->getCollection()->pluck('id')->filter()->values();
        if ($surveyIds->count()) {
            $remarksRecords = DB::table('lte_site_survey_remarks')
                ->leftJoin('users', 'lte_site_survey_remarks.user_id', '=', 'users.id')
                ->whereIn('lte_site_survey_remarks.lte_site_survey_id', $surveyIds)
                ->orderBy('lte_site_survey_remarks.created_at', 'desc')
                ->get([
                    'lte_site_survey_remarks.id',
                    'lte_site_survey_remarks.lte_site_survey_id',
                    'lte_site_survey_remarks.created_at',
                    'lte_site_survey_remarks.remark',
                    'lte_site_survey_remarks.file_path',
                    'lte_site_survey_remarks.mime_type',
                    'lte_site_survey_remarks.original_name',
                    'users.name as user_name',
                ]);

            $remarksBySurvey = $remarksRecords->groupBy('lte_site_survey_id');
        }

        $materials = $this->defaultMaterials();
        $photoLabels = $this->photoLabels();
        $users = User::query()->where('is_access', 0)->orderBy('name')->get(['id', 'name']);

        return view('lte_site_surveys.index', compact('surveys', 'q', 'status', 'perPage', 'materials', 'photoLabels', 'users', 'stats', 'remarksBySurvey'))
        ->with('i');
    }

    public function create()
    {
        $materials = $this->defaultMaterials();
        $photoLabels = $this->photoLabels();
        $users = User::query()->where('is_access', 0)->orderBy('name')->get(['id', 'name']);

        return view('lte_site_surveys.create', compact('materials', 'photoLabels', 'users'));
    }

    public function show(LteSiteSurvey $lte_site_survey)
    {
        $lte_site_survey->load(['user:id,name', 'photos']);

        return view('lte_site_surveys.show', ['survey' => $lte_site_survey]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        try {
            $data = $request->validate([
                'status' => 'nullable|string|in:draft,submitted',

                'meta.date' => 'nullable|date',
                'meta.surveyPerformedByUserId' => 'nullable|exists:users,id',
                'meta.surveyPerformedBy' => 'nullable|string|max:255',

                'general.siteName' => 'required|string|max:255',
                'general.jcNumber' => 'nullable|string|max:255',
                'general.coordinates' => 'nullable|string|max:255',
                'general.latitude' => 'nullable|numeric|between:-90,90',
                'general.longitude' => 'nullable|numeric|between:-180,180',
                'general.physicalAddress' => 'nullable|string|max:2000',
                'general.provinceRegion' => 'nullable|string|max:255',
                'general.contactDetails' => 'nullable|string|max:2000',

                'notes.notes' => 'nullable|string|max:4000',

                'accessSecurity.securityFenceAvailable' => 'nullable|boolean',
                'accessSecurity.conditionOfFence' => 'nullable|string|in:good,bad,not_available',
                'accessSecurity.siteAccess24h' => 'nullable|boolean',
                'accessSecurity.guardAvailable' => 'nullable|boolean',
                'accessSecurity.lineOfSightAvailability' => 'nullable|boolean',

                'tower.terrainType' => 'nullable|string|max:255',
                'tower.towerOwner' => 'nullable|string|max:255',
                'tower.allocatedHeight' => 'nullable|string|max:255',

                'transmission.nearestManholeCoordinates' => 'nullable|string|max:255',
                'transmission.distanceFromExistingFibre' => 'nullable|string|max:255',
                'transmission.distanceFromNearestPop' => 'nullable|string|max:255',
                'transmission.distanceFromNearestPop2' => 'nullable|string|max:255',
                'transmission.allocatedPort' => 'nullable|string|max:255',
                'transmission.requiredBackhaulCapacity' => 'nullable|string|max:255',
                'transmission.backhaulType' => 'nullable|string|in:fibre,microwave',

                'power.powerSourceType' => 'nullable|string|max:255',
                'power.phase' => 'nullable|string|in:single_phase,three_phase',
                'power.inputVoltage' => 'nullable|string|max:255',
                'power.batteryCapacity' => 'nullable|string|max:255',
                'power.batteryAutonomyHrs' => 'nullable|string|max:255',
                'power.earthingSystemInstalled' => 'nullable|string|in:available,not_available',
                'power.cableUtilitySourceToSite' => 'nullable|string|in:available,not_available',
                'power.conditionOfDb' => 'nullable|string|in:good,bad,not_available',

                'civilWorks.trenchingRequired' => 'nullable|boolean',
                'civilWorks.breakingConcreteTar' => 'nullable|boolean',
                'civilWorks.polePlantingRequired' => 'nullable|boolean',
                'civilWorks.constructionOfPlinth' => 'nullable|boolean',
                'civilWorks.newManholeRequired' => 'nullable|boolean',

                'materials' => 'nullable|array',
                'materials.civils' => 'nullable|array',
                'materials.nte' => 'nullable|array',

                'photos' => 'nullable|array',
                'photos.*' => 'nullable|array',
                'photos.*.*' => 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/heic,image/heif,application/pdf',
            ]);
        } catch (ValidationException $e) {
            Log::warning('LTE site survey validation failed', [
                'route' => optional($request->route())->getName(),
                'user_id' => optional($user)->id,
                'ip' => $request->ip(),
                'errors' => $e->errors(),
                'input' => $request->except(['photos', '_token']),
            ]);
            throw $e;
        }

        $rawStatus = $request->input('status');
        if (in_array($rawStatus, ['draft', 'submitted'], true)) {
            $data['status'] = $rawStatus;
        }

        [$status, $payload, $coords] = $this->buildPayloadAndCoords($data, $user, null);

        $surveyDate = data_get($payload, 'meta.date');
        $surveyPerformedBy = data_get($payload, 'meta.surveyPerformedBy');

        $lat = data_get($payload, 'general.latitude');
        $lng = data_get($payload, 'general.longitude');

        $parsedSurveyDate = null;
        if ($surveyDate) {
            try {
                $parsedSurveyDate = Carbon::parse($surveyDate)->toDateString();
            } catch (\Throwable $e) {
                $parsedSurveyDate = null;
            }
        }

        DB::beginTransaction();
        try {
            $logRef = (string) Str::uuid();
            $survey = LteSiteSurvey::create([
                'user_id' => $user->id,
                'status' => $status,
                'survey_date' => $parsedSurveyDate,
                'survey_performed_by' => $surveyPerformedBy,
                'site_name' => data_get($payload, 'general.siteName'),
                'jc_number' => data_get($payload, 'general.jcNumber'),
                'coordinates' => $coords,
                'latitude' => $lat,
                'longitude' => $lng,
                'physical_address' => data_get($payload, 'general.physicalAddress'),
                'province_region' => data_get($payload, 'general.provinceRegion'),
                'payload' => $payload,
                'submitted_at' => $status === 'submitted' ? now() : null,
            ]);

            $files = $request->file('photos') ?: [];
            if (is_array($files)) {
                foreach ($files as $label => $file) {
                    if (!$file) continue;

                    $list = is_array($file) ? $file : [$file];
                    foreach ($list as $single) {
                        if (!$single) continue;
                        $stored = $single->storePublicly('lte-site-surveys', 'public');
                        LteSiteSurveyPhoto::create([
                            'lte_site_survey_id' => $survey->id,
                            'label' => (string) $label,
                            'file_path' => $stored,
                            'mime_type' => $single->getClientMimeType(),
                            'original_name' => $single->getClientOriginalName(),
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $logRef = $logRef ?? (string) Str::uuid();
            Log::error('LTE site survey save failed', [
                'ref' => $logRef,
                'route' => optional($request->route())->getName(),
                'user_id' => optional($user)->id,
                'ip' => $request->ip(),
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
            Log::error($e);
            return back()->withInput()->with('error', 'Failed to save survey. Ref: ' . $logRef);
        }

        return redirect()->route('lte-site-surveys.index')->with('success', 'Survey saved.');
    }

    public function update(Request $request, LteSiteSurvey $lte_site_survey)
    {
        $user = $request->user();

        try {
            $data = $request->validate([
                'status' => 'nullable|string|in:draft,submitted',

                'meta.date' => 'nullable|date',
                'meta.surveyPerformedByUserId' => 'nullable|exists:users,id',
                'meta.surveyPerformedBy' => 'nullable|string|max:255',

                'general.siteName' => 'required|string|max:255',
                'general.jcNumber' => 'nullable|string|max:255',
                'general.coordinates' => 'nullable|string|max:255',
                'general.latitude' => 'nullable|numeric|between:-90,90',
                'general.longitude' => 'nullable|numeric|between:-180,180',
                'general.physicalAddress' => 'nullable|string|max:2000',
                'general.provinceRegion' => 'nullable|string|max:255',
                'general.contactDetails' => 'nullable|string|max:2000',

                'notes.notes' => 'nullable|string|max:4000',

                'accessSecurity.securityFenceAvailable' => 'nullable|boolean',
                'accessSecurity.conditionOfFence' => 'nullable|string|in:good,bad,not_available',
                'accessSecurity.siteAccess24h' => 'nullable|boolean',
                'accessSecurity.guardAvailable' => 'nullable|boolean',
                'accessSecurity.lineOfSightAvailability' => 'nullable|boolean',

                'tower.terrainType' => 'nullable|string|max:255',
                'tower.towerOwner' => 'nullable|string|max:255',
                'tower.allocatedHeight' => 'nullable|string|max:255',

                'transmission.nearestManholeCoordinates' => 'nullable|string|max:255',
                'transmission.distanceFromExistingFibre' => 'nullable|string|max:255',
                'transmission.distanceFromNearestPop' => 'nullable|string|max:255',
                'transmission.distanceFromNearestPop2' => 'nullable|string|max:255',
                'transmission.allocatedPort' => 'nullable|string|max:255',
                'transmission.requiredBackhaulCapacity' => 'nullable|string|max:255',
                'transmission.backhaulType' => 'nullable|string|in:fibre,microwave',

                'power.powerSourceType' => 'nullable|string|max:255',
                'power.phase' => 'nullable|string|in:single_phase,three_phase',
                'power.inputVoltage' => 'nullable|string|max:255',
                'power.batteryCapacity' => 'nullable|string|max:255',
                'power.batteryAutonomyHrs' => 'nullable|string|max:255',
                'power.earthingSystemInstalled' => 'nullable|string|in:available,not_available',
                'power.cableUtilitySourceToSite' => 'nullable|string|in:available,not_available',
                'power.conditionOfDb' => 'nullable|string|in:good,bad,not_available',

                'civilWorks.trenchingRequired' => 'nullable|boolean',
                'civilWorks.breakingConcreteTar' => 'nullable|boolean',
                'civilWorks.polePlantingRequired' => 'nullable|boolean',
                'civilWorks.constructionOfPlinth' => 'nullable|boolean',
                'civilWorks.newManholeRequired' => 'nullable|boolean',

                'materials' => 'nullable|array',
                'materials.civils' => 'nullable|array',
                'materials.nte' => 'nullable|array',

                'photos' => 'nullable|array',
                'photos.*' => 'nullable|array',
                'photos.*.*' => 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/heic,image/heif,application/pdf',
            ]);
        } catch (ValidationException $e) {
            Log::warning('LTE site survey update validation failed', [
                'route' => optional($request->route())->getName(),
                'user_id' => optional($user)->id,
                'survey_id' => $lte_site_survey->id,
                'ip' => $request->ip(),
                'errors' => $e->errors(),
                'input' => $request->except(['photos', '_token']),
            ]);
            throw $e;
        }

        $rawStatus = $request->input('status');
        if (in_array($rawStatus, ['draft', 'submitted'], true)) {
            $data['status'] = $rawStatus;
        }

        [$status, $payload, $coords] = $this->buildPayloadAndCoords($data, $user, $lte_site_survey);

        $surveyDate = data_get($payload, 'meta.date');
        $surveyPerformedBy = data_get($payload, 'meta.surveyPerformedBy');
        $lat = data_get($payload, 'general.latitude');
        $lng = data_get($payload, 'general.longitude');

        $parsedSurveyDate = null;
        if ($surveyDate) {
            try {
                $parsedSurveyDate = Carbon::parse($surveyDate)->toDateString();
            } catch (\Throwable $e) {
                $parsedSurveyDate = null;
            }
        }

        DB::beginTransaction();
        try {
            $logRef = (string) Str::uuid();
            $lte_site_survey->update([
                'status' => $status,
                'survey_date' => $parsedSurveyDate,
                'survey_performed_by' => $surveyPerformedBy,
                'site_name' => data_get($payload, 'general.siteName'),
                'jc_number' => data_get($payload, 'general.jcNumber'),
                'coordinates' => $coords,
                'latitude' => $lat,
                'longitude' => $lng,
                'physical_address' => data_get($payload, 'general.physicalAddress'),
                'province_region' => data_get($payload, 'general.provinceRegion'),
                'payload' => $payload,
                'submitted_at' => $status === 'submitted' ? ($lte_site_survey->submitted_at ?: now()) : null,
            ]);

            $files = $request->file('photos') ?: [];
            if (is_array($files)) {
                foreach ($files as $label => $file) {
                    if (!$file) continue;

                    $list = is_array($file) ? $file : [$file];
                    foreach ($list as $single) {
                        if (!$single) continue;
                        $stored = $single->storePublicly('lte-site-surveys', 'public');
                        LteSiteSurveyPhoto::create([
                            'lte_site_survey_id' => $lte_site_survey->id,
                            'label' => (string) $label,
                            'file_path' => $stored,
                            'mime_type' => $single->getClientMimeType(),
                            'original_name' => $single->getClientOriginalName(),
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $logRef = $logRef ?? (string) Str::uuid();
            Log::error('LTE site survey update failed', [
                'ref' => $logRef,
                'route' => optional($request->route())->getName(),
                'user_id' => optional($user)->id,
                'survey_id' => $lte_site_survey->id,
                'ip' => $request->ip(),
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
            Log::error($e);
            return back()->withInput()->with('error', 'Failed to update survey. Ref: ' . $logRef);
        }

        return redirect()->route('lte-site-surveys.index')->with('success', 'Survey updated.');
    }

    public function storeRemark(Request $request, LteSiteSurvey $lte_site_survey)
    {
        $user = $request->user();

        $data = $request->validate([
            'remark' => 'required|string|min:2|max:4000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/heic,image/heif,application/pdf',
        ]);

        $remarkText = trim((string) ($data['remark'] ?? ''));
        $files = $request->file('attachments') ?: [];

        DB::beginTransaction();
        try {
            if (is_array($files) && count($files)) {
                foreach ($files as $file) {
                    if (!$file) {
                        continue;
                    }
                    $stored = $file->storePublicly('lte-site-survey-remarks', 'public');
                    DB::table('lte_site_survey_remarks')->insert([
                        'lte_site_survey_id' => $lte_site_survey->id,
                        'user_id' => $user->id,
                        'remark' => $remarkText,
                        'file_path' => $stored,
                        'mime_type' => $file->getClientMimeType(),
                        'original_name' => $file->getClientOriginalName(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                DB::table('lte_site_survey_remarks')->insert([
                    'lte_site_survey_id' => $lte_site_survey->id,
                    'user_id' => $user->id,
                    'remark' => $remarkText,
                    'file_path' => null,
                    'mime_type' => null,
                    'original_name' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $logRef = (string) Str::uuid();
            Log::error('LTE site survey remark save failed', [
                'ref' => $logRef,
                'route' => optional($request->route())->getName(),
                'user_id' => optional($user)->id,
                'survey_id' => $lte_site_survey->id,
                'ip' => $request->ip(),
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
            Log::error($e);
            return back()->withInput()->with('error', 'Failed to save remark. Ref: ' . $logRef);
        }

        return back()->with('success', 'Remark added.');
    }

    public function servePhoto(Request $request, LteSiteSurveyPhoto $photo)
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }
        $survey = LteSiteSurvey::query()->find($photo->lte_site_survey_id);
        if (!$survey) {
            abort(404);
        }

        $disk = Storage::disk('public');
        $filePath = (string) ($photo->file_path ?? '');
        if ($filePath === '' || !$disk->exists($filePath)) {
            abort(404);
        }

        $safeName = trim((string) ($photo->original_name ?? ''));
        if ($safeName === '') {
            $safeName = basename($filePath);
        }
        $safeName = str_replace(["\r", "\n", '"'], '', $safeName);

        return response()->file($disk->path($filePath), [
            'Content-Type' => (string) ($photo->mime_type ?? 'application/octet-stream'),
            'Content-Disposition' => 'inline; filename="' . $safeName . '"',
        ]);
    }

    public function serveRemarkFile(Request $request, int $remark)
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }
        $row = DB::table('lte_site_survey_remarks')->where('id', $remark)->first();
        if (!$row) {
            abort(404);
        }

        $survey = LteSiteSurvey::query()->find($row->lte_site_survey_id);
        if (!$survey) {
            abort(404);
        }

        $disk = Storage::disk('public');
        $filePath = (string) ($row->file_path ?? '');
        if ($filePath === '' || !$disk->exists($filePath)) {
            abort(404);
        }

        $safeName = trim((string) ($row->original_name ?? ''));
        if ($safeName === '') {
            $safeName = basename($filePath);
        }
        $safeName = str_replace(["\r", "\n", '"'], '', $safeName);

        return response()->file($disk->path($filePath), [
            'Content-Type' => (string) ($row->mime_type ?? 'application/octet-stream'),
            'Content-Disposition' => 'inline; filename="' . $safeName . '"',
        ]);
    }

    private function buildPayloadAndCoords(array $data, $user, ?LteSiteSurvey $existing)
    {
        $status = $data['status'] ?? ($existing ? $existing->status : 'draft');

        $existingPayload = [];
        if ($existing) {
            $existingPayload = is_array($existing->payload) ? $existing->payload : (array) $existing->payload;
        }

        $performedByUserId = (int) data_get($data, 'meta.surveyPerformedByUserId', 0);
        $performedByName = trim((string) data_get($data, 'meta.surveyPerformedBy', ''));
        if ($performedByUserId > 0) {
            $u = User::find($performedByUserId);
            if ($u && $u->name) {
                $performedByName = (string) $u->name;
            }
        }
        if ($performedByName === '') {
            $performedByName = (string) (optional($user)->name ?? '');
        }

        $payload = [
            'meta' => [
                'date' => data_get($data, 'meta.date'),
                'surveyPerformedByUserId' => $performedByUserId > 0 ? $performedByUserId : null,
                'surveyPerformedBy' => $performedByName,
            ],
            'general' => [
                'siteName' => data_get($data, 'general.siteName'),
                'jcNumber' => data_get($data, 'general.jcNumber'),
                'coordinates' => data_get($data, 'general.coordinates'),
                'latitude' => data_get($data, 'general.latitude'),
                'longitude' => data_get($data, 'general.longitude'),
                'physicalAddress' => data_get($data, 'general.physicalAddress'),
                'provinceRegion' => data_get($data, 'general.provinceRegion'),
                'contactDetails' => data_get($data, 'general.contactDetails'),
            ],
            'notes' => [
                'notes' => data_get($data, 'notes.notes', data_get($existingPayload, 'notes.notes')),
            ],
            'accessSecurity' => [
                'securityFenceAvailable' => (bool) data_get($data, 'accessSecurity.securityFenceAvailable', false),
                'conditionOfFence' => data_get($data, 'accessSecurity.conditionOfFence'),
                'siteAccess24h' => (bool) data_get($data, 'accessSecurity.siteAccess24h', false),
                'guardAvailable' => (bool) data_get($data, 'accessSecurity.guardAvailable', false),
                'lineOfSightAvailability' => (bool) data_get($data, 'accessSecurity.lineOfSightAvailability', false),
            ],
            'tower' => [
                'terrainType' => data_get($data, 'tower.terrainType'),
                'towerOwner' => data_get($data, 'tower.towerOwner'),
                'allocatedHeight' => data_get($data, 'tower.allocatedHeight'),
            ],
            'transmission' => [
                'nearestManholeCoordinates' => data_get($data, 'transmission.nearestManholeCoordinates'),
                'distanceFromExistingFibre' => data_get($data, 'transmission.distanceFromExistingFibre'),
                'distanceFromNearestPop' => data_get($data, 'transmission.distanceFromNearestPop'),
                'distanceFromNearestPop2' => data_get($data, 'transmission.distanceFromNearestPop2'),
                'allocatedPort' => data_get($data, 'transmission.allocatedPort'),
                'requiredBackhaulCapacity' => data_get($data, 'transmission.requiredBackhaulCapacity'),
                'backhaulType' => data_get($data, 'transmission.backhaulType'),
            ],
            'power' => [
                'powerSourceType' => data_get($data, 'power.powerSourceType'),
                'phase' => data_get($data, 'power.phase'),
                'inputVoltage' => data_get($data, 'power.inputVoltage'),
                'batteryCapacity' => data_get($data, 'power.batteryCapacity'),
                'batteryAutonomyHrs' => data_get($data, 'power.batteryAutonomyHrs'),
                'earthingSystemInstalled' => data_get($data, 'power.earthingSystemInstalled'),
                'cableUtilitySourceToSite' => data_get($data, 'power.cableUtilitySourceToSite'),
                'conditionOfDb' => data_get($data, 'power.conditionOfDb'),
            ],
            'civilWorks' => [
                'trenchingRequired' => (bool) data_get($data, 'civilWorks.trenchingRequired', false),
                'breakingConcreteTar' => (bool) data_get($data, 'civilWorks.breakingConcreteTar', false),
                'polePlantingRequired' => (bool) data_get($data, 'civilWorks.polePlantingRequired', false),
                'constructionOfPlinth' => (bool) data_get($data, 'civilWorks.constructionOfPlinth', false),
                'newManholeRequired' => (bool) data_get($data, 'civilWorks.newManholeRequired', false),
            ],
            'materials' => [
                'civils' => array_values((array) data_get($data, 'materials.civils', [])),
                'nte' => array_values((array) data_get($data, 'materials.nte', [])),
            ],
        ];

        $lat = data_get($payload, 'general.latitude');
        $lng = data_get($payload, 'general.longitude');
        $coords = trim((string) data_get($payload, 'general.coordinates', ''));
        if ($lat !== null && $lng !== null && $coords === '') {
            $coords = (string) $lat . ', ' . (string) $lng;
            $payload['general']['coordinates'] = $coords;
        }

        return [$status, $payload, $coords];
    }

    private function defaultMaterials()
    {
        return [
            'civils' => [
                ['description' => 'Fibre Cable', 'unit' => 'm', 'qty' => ''],
                ['description' => 'PVC Trunking', 'unit' => 'm', 'qty' => ''],
                ['description' => 'Manholes', 'unit' => 'ea', 'qty' => ''],
                ['description' => 'Trenching Normal Ground', 'unit' => 'm', 'qty' => ''],
                ['description' => 'Trenching Gravel', 'unit' => 'm', 'qty' => ''],
                ['description' => 'Total Trenching (HDPE Ducts)', 'unit' => 'm', 'qty' => ''],
                ['description' => 'Length requiring Wayleaves', 'unit' => 'm', 'qty' => ''],
                ['description' => 'Steel Pipes', 'unit' => 'm', 'qty' => ''],
                ['description' => 'PVC pipes (90mm)', 'unit' => 'm', 'qty' => ''],
                ['description' => 'Poles', 'unit' => 'ea', 'qty' => ''],
                ['description' => 'Tar', 'unit' => 'm', 'qty' => ''],
                ['description' => 'Plinth to be constructed', 'unit' => 'm³', 'qty' => ''],
                ['description' => 'Grounding System', 'unit' => 'm', 'qty' => ''],
                ['description' => 'Commercial Power Cable', 'unit' => 'm', 'qty' => ''],
                ['description' => 'Distribution Board', 'unit' => 'ea', 'qty' => ''],
            ],
            'nte' => [
                ['description' => 'SFP modules', 'unit' => 'ea', 'qty' => ''],
                ['description' => 'Convertors', 'unit' => 'ea', 'qty' => ''],
                ['description' => 'UTP Cable', 'unit' => 'm', 'qty' => ''],
                ['description' => 'RJ45 Connectors', 'unit' => 'ea', 'qty' => ''],
                ['description' => 'Switch', 'unit' => 'ea', 'qty' => ''],
                ['description' => 'Access Points', 'unit' => 'ea', 'qty' => ''],
                ['description' => '3m sc-sc patch cord', 'unit' => 'ea', 'qty' => ''],
                ['description' => 'Patch panel', 'unit' => 'ea', 'qty' => ''],
                ['description' => 'SM midi-couplers', 'unit' => 'ea', 'qty' => ''],
                ['description' => 'ST Connectors', 'unit' => 'ea', 'qty' => ''],
                ['description' => 'Pig tails', 'unit' => 'ea', 'qty' => ''],
                ['description' => 'Splice Protectors', 'unit' => 'ea', 'qty' => ''],
                ['description' => 'Dome Boxes way', 'unit' => 'ea', 'qty' => ''],
                ['description' => 'Cabinet', 'unit' => 'ea', 'qty' => ''],
            ],
        ];
    }

    private function photoLabels()
    {
        return [
            'nearest_joint_box' => 'Nearest Joint Box',
            'fibre_route_towards_tower' => 'Fibre Route towards Tower',
            'tower_overview' => 'Tower Overview',
            'new_plinth_space' => 'New Plinth Space',
            'power_connection_image' => 'Power Connection Image',
            'termination_point_image' => 'Termination Point Image',
            'route_sketch' => 'Route Sketch with Measurements',
        ];
    }
}
