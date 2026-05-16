<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\LteSiteSurvey;
use App\Models\LteSiteSurveyPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LteSiteSurveyController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $surveys = LteSiteSurvey::query()
            ->where('user_id', $user->id)
            ->withCount('photos')
            ->orderByDesc('created_at')
            ->limit(100)
            ->get([
                'id',
                'status',
                'survey_date',
                'survey_performed_by',
                'site_name',
                'jc_number',
                'coordinates',
                'province_region',
                'created_at',
                'submitted_at',
            ]);

        return response()->json(['success' => true, 'data' => $surveys]);
    }

    public function show(Request $request, LteSiteSurvey $survey)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        if ((int) $survey->user_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $survey->load(['photos:id,lte_site_survey_id,label,file_path,mime_type,original_name,created_at']);

        return response()->json(['success' => true, 'data' => $survey]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'payload' => 'required|json',
            'status' => 'nullable|string|in:draft,submitted',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|array',
            'photos.*.*' => 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/heic,image/heif,application/pdf',
        ]);

        $payload = json_decode($data['payload'], true);
        if (!is_array($payload)) {
            return response()->json(['success' => false, 'message' => 'Invalid payload'], 422);
        }

        $status = $data['status'] ?? 'submitted';
        $surveyDate = data_get($payload, 'meta.date');
        $surveyPerformedBy = data_get($payload, 'meta.surveyPerformedBy');

        $siteName = data_get($payload, 'general.siteName');
        $jcNumber = data_get($payload, 'general.jcNumber');
        $coordinates = data_get($payload, 'general.coordinates');
        $physicalAddress = data_get($payload, 'general.physicalAddress');
        $provinceRegion = data_get($payload, 'general.provinceRegion');

        $lat = data_get($payload, 'general.latitude');
        $lng = data_get($payload, 'general.longitude');
        if ($lat !== null && $lng !== null && !is_string($coordinates)) {
            $coordinates = '';
        }
        if ($lat !== null && $lng !== null && trim((string) $coordinates) === '') {
            $coordinates = (string) $lat . ', ' . (string) $lng;
            data_set($payload, 'general.coordinates', $coordinates);
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
            $survey = LteSiteSurvey::create([
                'user_id' => $user->id,
                'status' => $status,
                'survey_date' => $parsedSurveyDate,
                'survey_performed_by' => $surveyPerformedBy,
                'site_name' => $siteName,
                'jc_number' => $jcNumber,
                'coordinates' => $coordinates,
                'latitude' => $lat,
                'longitude' => $lng,
                'physical_address' => $physicalAddress,
                'province_region' => $provinceRegion,
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
            Log::error('Mobile LTE site survey save failed', [
                'ref' => $logRef,
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
            Log::error($e);
            return response()->json(['success' => false, 'message' => 'Failed to save survey. Ref: ' . $logRef], 500);
        }

        return response()->json(['success' => true, 'message' => 'Survey saved', 'id' => $survey->id]);
    }
}
