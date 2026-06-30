<?php

namespace App\Http\Controllers;

use App\Models\CustomerConnectivitySurvey;
use App\Models\CustomerConnectivitySurveyPhoto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomerConnectivitySurveyController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->can('surveys-list')) {
            abort(403);
        }

        $perPage = (int) $request->query('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 20;
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $status = $status === '' ? null : $status;

        $baseQuery = CustomerConnectivitySurvey::query()
            ->with(['user:id,name'])
            ->withCount('photos')
            ->orderByDesc('created_at');

        $applyFilters = function ($qq) use ($status, $q) {
            $qq
                ->when($status, fn ($w) => $w->where('status', $status))
                ->when($q !== '', function ($w) use ($q) {
                    $like = '%' . $q . '%';
                    $w->where(function ($inner) use ($like) {
                        $inner->where('customer_name', 'like', $like)
                            ->orWhere('account_or_jc_number', 'like', $like)
                            ->orWhere('site_name', 'like', $like)
                            ->orWhere('coordinates', 'like', $like);
                    });
                });
        };

        $query = clone $baseQuery;
        $applyFilters($query);
        $surveys = $query->paginate($perPage)->withQueryString();

        $statsQuery = CustomerConnectivitySurvey::query();
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'submitted' => (clone $statsQuery)->where('status', 'submitted')->count(),
            'draft' => (clone $statsQuery)->where('status', 'draft')->count(),
            'latest_created_at' => (clone $statsQuery)->max('created_at'),
        ];

        $photoLabels = $this->photoLabels();
        $users = User::query()->where('is_access', 0)->orderBy('name')->get(['id', 'name']);

        return view('customer_connectivity_surveys.index', [
            'surveys' => $surveys,
            'q' => $q,
            'status' => $status,
            'perPage' => $perPage,
            'stats' => $stats,
            'photoLabels' => $photoLabels,
            'users' => $users,
        ]);
    }

    public function show(Request $request, CustomerConnectivitySurvey $survey)
    {
        $user = $request->user();
        if (!$user || !$user->can('surveys-list')) {
            abort(403);
        }

        $survey->load(['user:id,name', 'photos']);
        $users = User::query()->where('is_access', 0)->orderBy('name')->get(['id', 'name']);

        return view('customer_connectivity_surveys.show', [
            'survey' => $survey,
            'payload' => is_array($survey->payload) ? $survey->payload : [],
            'photoLabels' => $this->photoLabels(),
            'users' => $users,
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->can('survey-create')) {
            abort(403);
        }

        $photoLabels = $this->photoLabels();
        $users = User::query()->where('is_access', 0)->orderBy('name')->get(['id', 'name']);

        return view('customer_connectivity_surveys.create', compact('photoLabels', 'users'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->can('survey-create')) {
            abort(403);
        }

        $data = $request->validate([
            'status' => 'required|string|in:draft,submitted',
            'meta' => 'nullable|array',
            'meta.date' => 'nullable|string|max:50',
            'meta.surveyPerformedByUserId' => 'nullable|integer|exists:users,id',
            'meta.surveyPerformedBy' => 'nullable|string|max:255',

            'general' => 'nullable|array',
            'general.customerName' => 'required_if:status,submitted|nullable|string|max:255',
            'general.accountOrJcNumber' => 'nullable|string|max:255',
            'general.siteName' => 'required_if:status,submitted|nullable|string|max:255',
            'general.physicalAddress' => 'nullable|string|max:4000',
            'general.coordinates' => 'nullable|string|max:255',
            'general.latitude' => 'nullable|string|max:50',
            'general.longitude' => 'nullable|string|max:50',
            'general.customerContactName' => 'nullable|string|max:255',
            'general.customerContactPhone' => 'nullable|string|max:255',
            'general.customerContactEmail' => 'nullable|string|max:255',

            'serviceRequirements' => 'nullable|array',
            'serviceRequirements.serviceType' => 'nullable|string|max:255',
            'serviceRequirements.bandwidthDown' => 'nullable|string|max:50',
            'serviceRequirements.bandwidthUp' => 'nullable|string|max:50',
            'serviceRequirements.servicePurpose' => 'nullable|string|max:255',
            'serviceRequirements.redundancyRequired' => 'nullable|string|max:255',
            'serviceRequirements.handoverInterface' => 'nullable|string|max:255',
            'serviceRequirements.publicIpsRequired' => 'nullable|string|max:255',
            'serviceRequirements.publicIpsQty' => 'nullable|string|max:50',
            'serviceRequirements.vlanNotes' => 'nullable|string|max:4000',

            'permissions' => 'nullable|array',
            'permissions.accessContact' => 'nullable|string|max:255',
            'permissions.surveyDoneWith' => 'nullable|string|max:255',
            'permissions.workingHours' => 'nullable|string|max:255',
            'permissions.permissionsRequired' => 'nullable|string|max:255',
            'permissions.notes' => 'nullable|string|max:4000',

            'outdoor' => 'nullable|array',
            'outdoor.nearestPopNode' => 'nullable|string|max:255',
            'outdoor.feederSwitchOlt' => 'nullable|string|max:255',
            'outdoor.freePortAvailable' => 'nullable|string|max:50',
            'outdoor.portId' => 'nullable|string|max:255',
            'outdoor.estimatedDistance' => 'nullable|string|max:255',
            'outdoor.routeType' => 'nullable|string|max:255',
            'outdoor.existingInfrastructure' => 'nullable|string|max:255',
            'outdoor.obstructionsRisks' => 'nullable|string|max:255',
            'outdoor.nearestManholePoleReference' => 'nullable|string|max:255',
            'outdoor.manholeJbDetails' => 'nullable|string|max:4000',
            'outdoor.proposedRefs' => 'nullable|string|max:4000',

            'indoor' => 'nullable|array',
            'indoor.spaceForEquipment' => 'nullable|string|max:255',
            'indoor.cabinetAvailable' => 'nullable|string|max:255',
            'indoor.cabinetSize' => 'nullable|string|max:255',
            'indoor.newCabinetRequired' => 'nullable|string|max:255',
            'indoor.powerAvailable' => 'nullable|string|max:255',
            'indoor.socketType' => 'nullable|string|max:255',
            'indoor.distanceToSocket' => 'nullable|string|max:255',
            'indoor.backupPower' => 'nullable|string|max:255',
            'indoor.airConditioning' => 'nullable|string|max:255',
            'indoor.earthing' => 'nullable|string|max:255',
            'indoor.internalCablingRoute' => 'nullable|string|max:255',
            'indoor.notes' => 'nullable|string|max:4000',

            'photos' => 'nullable|array',
            'photos.*' => 'nullable|array',
            'photos.*.*' => 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/heic,image/heif,application/pdf',

            'boq' => 'nullable|array',
            'boq.civils' => 'nullable|array',
            'boq.civils.*.description' => 'nullable|string|max:255',
            'boq.civils.*.unit' => 'nullable|string|max:50',
            'boq.civils.*.qty' => 'nullable|string|max:50',
            'boq.nte' => 'nullable|array',
            'boq.nte.*.description' => 'nullable|string|max:255',
            'boq.nte.*.unit' => 'nullable|string|max:50',
            'boq.nte.*.qty' => 'nullable|string|max:50',
        ]);

        $payload = [
            'meta' => $data['meta'] ?? [],
            'general' => $data['general'] ?? [],
            'serviceRequirements' => $data['serviceRequirements'] ?? [],
            'permissions' => $data['permissions'] ?? [],
            'outdoor' => $data['outdoor'] ?? [],
            'indoor' => $data['indoor'] ?? [],
            'boq' => $data['boq'] ?? [],
        ];

        $status = $data['status'] ?? 'draft';

        $surveyDate = data_get($payload, 'meta.date');
        $performedByUserId = (int) data_get($payload, 'meta.surveyPerformedByUserId', 0);
        $surveyPerformedBy = trim((string) data_get($payload, 'meta.surveyPerformedBy', ''));
        if ($performedByUserId > 0) {
            $u = User::query()->find($performedByUserId);
            if ($u && $u->name) {
                $surveyPerformedBy = (string) $u->name;
            }
        }
        if ($surveyPerformedBy === '') {
            $surveyPerformedBy = (string) $user->name;
        }
        data_set($payload, 'meta.surveyPerformedByUserId', $performedByUserId > 0 ? $performedByUserId : null);
        data_set($payload, 'meta.surveyPerformedBy', $surveyPerformedBy);

        $customerName = data_get($payload, 'general.customerName');
        $accountOrJcNumber = data_get($payload, 'general.accountOrJcNumber');
        $siteName = data_get($payload, 'general.siteName');
        $coordinates = data_get($payload, 'general.coordinates');
        $physicalAddress = data_get($payload, 'general.physicalAddress');

        $latRaw = trim((string) data_get($payload, 'general.latitude'));
        $lngRaw = trim((string) data_get($payload, 'general.longitude'));
        $lat = $latRaw !== '' && is_numeric($latRaw) ? (float) $latRaw : null;
        $lng = $lngRaw !== '' && is_numeric($lngRaw) ? (float) $lngRaw : null;

        if ($lat !== null) data_set($payload, 'general.latitude', $lat);
        if ($lng !== null) data_set($payload, 'general.longitude', $lng);

        if ($lat !== null && $lng !== null) {
            if (!is_string($coordinates)) {
                $coordinates = '';
            }
            if (trim((string) $coordinates) === '') {
                $coordinates = (string) $lat . ', ' . (string) $lng;
                data_set($payload, 'general.coordinates', $coordinates);
            }
        }

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
            $survey = CustomerConnectivitySurvey::create([
                'user_id' => $user->id,
                'status' => $status,
                'survey_date' => $parsedSurveyDate,
                'survey_performed_by' => $surveyPerformedBy,
                'customer_name' => $customerName,
                'account_or_jc_number' => $accountOrJcNumber,
                'site_name' => $siteName,
                'coordinates' => $coordinates,
                'latitude' => $lat,
                'longitude' => $lng,
                'physical_address' => $physicalAddress,
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
                        $stored = $single->storePublicly('customer-connectivity-surveys', 'public');
                        CustomerConnectivitySurveyPhoto::create([
                            'customer_connectivity_survey_id' => $survey->id,
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
            Log::error('Customer connectivity survey save failed', [
                'ref' => $logRef,
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
            Log::error($e);
            return back()->withInput()->with('error', 'Failed to save survey. Ref: ' . $logRef);
        }

        return redirect()->route('customer-connectivity-surveys.show', $survey->id)->with('success', 'Survey saved.');
    }

    public function update(Request $request, CustomerConnectivitySurvey $survey)
    {
        $user = $request->user();
        if (!$user || !$user->can('survey-edit')) {
            abort(403);
        }

        $data = $request->validate([
            'status' => 'required|string|in:draft,submitted',
            'meta' => 'nullable|array',
            'meta.date' => 'nullable|string|max:50',
            'meta.surveyPerformedByUserId' => 'nullable|integer|exists:users,id',
            'meta.surveyPerformedBy' => 'nullable|string|max:255',

            'general' => 'nullable|array',
            'general.customerName' => 'required_if:status,submitted|nullable|string|max:255',
            'general.accountOrJcNumber' => 'nullable|string|max:255',
            'general.siteName' => 'required_if:status,submitted|nullable|string|max:255',
            'general.physicalAddress' => 'nullable|string|max:4000',
            'general.coordinates' => 'nullable|string|max:255',
            'general.latitude' => 'nullable|string|max:50',
            'general.longitude' => 'nullable|string|max:50',
            'general.customerContactName' => 'nullable|string|max:255',
            'general.customerContactPhone' => 'nullable|string|max:255',
            'general.customerContactEmail' => 'nullable|string|max:255',

            'serviceRequirements' => 'nullable|array',
            'serviceRequirements.serviceType' => 'nullable|string|max:255',
            'serviceRequirements.bandwidthDown' => 'nullable|string|max:50',
            'serviceRequirements.bandwidthUp' => 'nullable|string|max:50',
            'serviceRequirements.servicePurpose' => 'nullable|string|max:255',
            'serviceRequirements.redundancyRequired' => 'nullable|string|max:255',
            'serviceRequirements.handoverInterface' => 'nullable|string|max:255',
            'serviceRequirements.publicIpsRequired' => 'nullable|string|max:255',
            'serviceRequirements.publicIpsQty' => 'nullable|string|max:50',
            'serviceRequirements.vlanNotes' => 'nullable|string|max:4000',

            'permissions' => 'nullable|array',
            'permissions.accessContact' => 'nullable|string|max:255',
            'permissions.surveyDoneWith' => 'nullable|string|max:255',
            'permissions.workingHours' => 'nullable|string|max:255',
            'permissions.permissionsRequired' => 'nullable|string|max:255',
            'permissions.notes' => 'nullable|string|max:4000',

            'outdoor' => 'nullable|array',
            'outdoor.nearestPopNode' => 'nullable|string|max:255',
            'outdoor.feederSwitchOlt' => 'nullable|string|max:255',
            'outdoor.freePortAvailable' => 'nullable|string|max:50',
            'outdoor.portId' => 'nullable|string|max:255',
            'outdoor.estimatedDistance' => 'nullable|string|max:255',
            'outdoor.routeType' => 'nullable|string|max:255',
            'outdoor.existingInfrastructure' => 'nullable|string|max:255',
            'outdoor.obstructionsRisks' => 'nullable|string|max:255',
            'outdoor.nearestManholePoleReference' => 'nullable|string|max:255',
            'outdoor.manholeJbDetails' => 'nullable|string|max:4000',
            'outdoor.proposedRefs' => 'nullable|string|max:4000',

            'indoor' => 'nullable|array',
            'indoor.spaceForEquipment' => 'nullable|string|max:255',
            'indoor.cabinetAvailable' => 'nullable|string|max:255',
            'indoor.cabinetSize' => 'nullable|string|max:255',
            'indoor.newCabinetRequired' => 'nullable|string|max:255',
            'indoor.powerAvailable' => 'nullable|string|max:255',
            'indoor.socketType' => 'nullable|string|max:255',
            'indoor.distanceToSocket' => 'nullable|string|max:255',
            'indoor.backupPower' => 'nullable|string|max:255',
            'indoor.airConditioning' => 'nullable|string|max:255',
            'indoor.earthing' => 'nullable|string|max:255',
            'indoor.internalCablingRoute' => 'nullable|string|max:255',
            'indoor.notes' => 'nullable|string|max:4000',

            'boq' => 'nullable|array',
            'boq.civils' => 'nullable|array',
            'boq.civils.*.description' => 'nullable|string|max:255',
            'boq.civils.*.unit' => 'nullable|string|max:50',
            'boq.civils.*.qty' => 'nullable|string|max:50',
            'boq.nte' => 'nullable|array',
            'boq.nte.*.description' => 'nullable|string|max:255',
            'boq.nte.*.unit' => 'nullable|string|max:50',
            'boq.nte.*.qty' => 'nullable|string|max:50',

            'photos' => 'nullable|array',
            'photos.*' => 'nullable|array',
            'photos.*.*' => 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/heic,image/heif,application/pdf',
        ]);

        $payload = [
            'meta' => $data['meta'] ?? [],
            'general' => $data['general'] ?? [],
            'serviceRequirements' => $data['serviceRequirements'] ?? [],
            'permissions' => $data['permissions'] ?? [],
            'outdoor' => $data['outdoor'] ?? [],
            'indoor' => $data['indoor'] ?? [],
            'boq' => $data['boq'] ?? [],
        ];

        $status = $data['status'] ?? ($survey->status ?: 'draft');

        $surveyDate = data_get($payload, 'meta.date');
        $performedByUserId = (int) data_get($payload, 'meta.surveyPerformedByUserId', 0);
        $surveyPerformedBy = trim((string) data_get($payload, 'meta.surveyPerformedBy', ''));
        if ($performedByUserId > 0) {
            $u = User::query()->find($performedByUserId);
            if ($u && $u->name) {
                $surveyPerformedBy = (string) $u->name;
            }
        }
        if ($surveyPerformedBy === '') {
            $surveyPerformedBy = (string) $user->name;
        }
        data_set($payload, 'meta.surveyPerformedByUserId', $performedByUserId > 0 ? $performedByUserId : null);
        data_set($payload, 'meta.surveyPerformedBy', $surveyPerformedBy);

        $customerName = data_get($payload, 'general.customerName');
        $accountOrJcNumber = data_get($payload, 'general.accountOrJcNumber');
        $siteName = data_get($payload, 'general.siteName');
        $coordinates = data_get($payload, 'general.coordinates');
        $physicalAddress = data_get($payload, 'general.physicalAddress');

        $latRaw = trim((string) data_get($payload, 'general.latitude'));
        $lngRaw = trim((string) data_get($payload, 'general.longitude'));
        $lat = $latRaw !== '' && is_numeric($latRaw) ? (float) $latRaw : null;
        $lng = $lngRaw !== '' && is_numeric($lngRaw) ? (float) $lngRaw : null;

        if ($lat !== null) data_set($payload, 'general.latitude', $lat);
        if ($lng !== null) data_set($payload, 'general.longitude', $lng);

        if ($lat !== null && $lng !== null) {
            if (!is_string($coordinates)) {
                $coordinates = '';
            }
            if (trim((string) $coordinates) === '') {
                $coordinates = (string) $lat . ', ' . (string) $lng;
                data_set($payload, 'general.coordinates', $coordinates);
            }
        }

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

            $survey->update([
                'status' => $status,
                'survey_date' => $parsedSurveyDate,
                'survey_performed_by' => $surveyPerformedBy,
                'customer_name' => $customerName,
                'account_or_jc_number' => $accountOrJcNumber,
                'site_name' => $siteName,
                'coordinates' => $coordinates,
                'latitude' => $lat,
                'longitude' => $lng,
                'physical_address' => $physicalAddress,
                'payload' => $payload,
                'submitted_at' => $status === 'submitted' ? ($survey->submitted_at ?: now()) : null,
            ]);

            $files = $request->file('photos') ?: [];
            if (is_array($files)) {
                foreach ($files as $label => $file) {
                    if (!$file) continue;

                    $list = is_array($file) ? $file : [$file];
                    foreach ($list as $single) {
                        if (!$single) continue;
                        $stored = $single->storePublicly('customer-connectivity-surveys', 'public');
                        CustomerConnectivitySurveyPhoto::create([
                            'customer_connectivity_survey_id' => $survey->id,
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
            Log::error('Customer connectivity survey update failed', [
                'ref' => $logRef,
                'user_id' => $user->id,
                'survey_id' => $survey->id,
                'ip' => $request->ip(),
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
            Log::error($e);
            return back()->withInput()->with('error', 'Failed to update survey. Ref: ' . $logRef);
        }

        return redirect()->route('customer-connectivity-surveys.show', $survey->id)->with('success', 'Survey updated.');
    }

    public function servePhoto(Request $request, CustomerConnectivitySurveyPhoto $photo)
    {
        $user = $request->user();
        if (!$user || !$user->can('surveys-list')) {
            abort(403);
        }

        $survey = CustomerConnectivitySurvey::query()->find($photo->customer_connectivity_survey_id);
        if (!$survey) {
            abort(404);
        }

        $disk = Storage::disk('public');
        $filePath = (string) ($photo->file_path ?? '');
        if ($filePath === '' || !$disk->exists($filePath)) {
            abort(404);
        }

        $absolutePath = $disk->path($filePath);
        $mime = $photo->mime_type ?: $disk->mimeType($filePath) ?: 'application/octet-stream';

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . addslashes($photo->original_name ?: basename($filePath)) . '"',
        ]);
    }

    public function destroyPhoto(Request $request, CustomerConnectivitySurveyPhoto $photo)
    {
        $user = $request->user();
        if (!$user || !$user->can('survey-edit')) {
            abort(403);
        }

        $survey = CustomerConnectivitySurvey::query()->find($photo->customer_connectivity_survey_id);
        if (!$survey) {
            abort(404);
        }

        $disk = Storage::disk('public');
        $filePath = (string) ($photo->file_path ?? '');

        DB::beginTransaction();
        try {
            $photo->delete();
            if ($filePath !== '' && $disk->exists($filePath)) {
                $disk->delete($filePath);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $logRef = (string) Str::uuid();
            Log::error('Customer connectivity survey photo delete failed', [
                'ref' => $logRef,
                'route' => optional($request->route())->getName(),
                'user_id' => optional($user)->id,
                'survey_id' => optional($survey)->id,
                'photo_id' => $photo->id,
                'ip' => $request->ip(),
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
            Log::error($e);

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to remove image. Ref: ' . $logRef], 500);
            }
            return back()->with('error', 'Failed to remove image. Ref: ' . $logRef);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Image removed.');
    }

    private function photoLabels()
    {
        return [
            'building_entry' => 'Building entry access point',
            'cabinet_space' => 'Equipment space / cabinet location',
            'nearest_manhole_pole' => 'Nearest manhole / pole / duct access',
            'route_obstacles' => 'Route obstacles (tar/tree cutting/etc.)',
            'power_point' => 'Power connection point',
            'indoor_route' => 'Indoor cable route',
            'termination_point' => 'Termination point mounting location',
            'route_sketch' => 'Route Sketch with Measurements',
        ];
    }
}
