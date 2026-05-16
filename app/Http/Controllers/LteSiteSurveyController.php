<?php

namespace App\Http\Controllers;

use App\Models\LteSiteSurvey;
use App\Models\LteSiteSurveyPhoto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LteSiteSurveyController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $status = trim((string) $request->input('status', ''));

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

        $surveys = $query->paginate(25)->appends($request->only('q', 'status'));

        $materials = $this->defaultMaterials();
        $photoLabels = $this->photoLabels();
        $users = User::query()->where('is_access', 0)->orderBy('name')->get(['id', 'name']);

        return view('lte_site_surveys.index', compact('surveys', 'q', 'status', 'materials', 'photoLabels', 'users'))
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
        if ((int) $lte_site_survey->user_id !== (int) optional($user)->id) {
            abort(403);
        }

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
