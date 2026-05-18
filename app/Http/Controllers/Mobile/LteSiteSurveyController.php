<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\LteSiteSurvey;
use App\Models\LteSiteSurveyPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LteSiteSurveyController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $perPage = (int) $request->query('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 20;
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $status = $status === '' ? null : $status;

        $query = LteSiteSurvey::query()
            ->with(['user:id,name'])
            ->withCount('photos')
            ->orderByDesc('created_at')
            ->select([
                'id',
                'user_id',
                'status',
                'survey_date',
                'survey_performed_by',
                'site_name',
                'jc_number',
                'coordinates',
                'province_region',
                'created_at',
                'submitted_at',
            ])
            ->when($status, fn ($qq) => $qq->where('status', $status))
            ->when($q !== '', function ($qq) use ($q) {
                $like = '%' . $q . '%';
                $qq->where(function ($w) use ($like) {
                    $w->where('site_name', 'like', $like)
                        ->orWhere('jc_number', 'like', $like)
                        ->orWhere('province_region', 'like', $like)
                        ->orWhere('coordinates', 'like', $like);
                });
            });

        $paginated = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    public function show(Request $request, LteSiteSurvey $survey)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $survey->load(['photos:id,lte_site_survey_id,label,file_path,mime_type,original_name,created_at']);

        $remarks = DB::table('lte_site_survey_remarks as r')
            ->leftJoin('users as u', 'r.user_id', '=', 'u.id')
            ->where('r.lte_site_survey_id', $survey->id)
            ->orderByDesc('r.created_at')
            ->get([
                'r.id',
                'r.user_id',
                'u.name as user_name',
                'r.remark',
                'r.file_path',
                'r.mime_type',
                'r.original_name',
                'r.created_at',
            ]);

        $payload = $survey->toArray();
        $payload['remarks'] = $remarks;

        return response()->json(['success' => true, 'data' => $payload]);
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

    public function update(Request $request, LteSiteSurvey $survey)
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

        $status = $data['status'] ?? ($survey->status ?: 'draft');
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

            $survey->update([
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
                'submitted_at' => $status === 'submitted' ? ($survey->submitted_at ?: now()) : null,
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
            Log::error('Mobile LTE site survey update failed', [
                'ref' => $logRef,
                'user_id' => $user->id,
                'survey_id' => $survey->id,
                'ip' => $request->ip(),
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
            Log::error($e);
            return response()->json(['success' => false, 'message' => 'Failed to update survey. Ref: ' . $logRef], 500);
        }

        return response()->json(['success' => true, 'message' => 'Survey updated', 'id' => $survey->id]);
    }

    public function enabledUsers(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $users = DB::table('users')
            ->where('is_access', '=', 0)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return response()->json(['success' => true, 'data' => $users]);
    }

    public function storeRemark(Request $request, LteSiteSurvey $survey)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'remark' => 'required|string|max:4000',
            'file' => 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/heic,image/heif,application/pdf',
        ]);

        $filePath = null;
        $mimeType = null;
        $originalName = null;
        if ($request->file('file')) {
            $f = $request->file('file');
            $filePath = $f->storePublicly('lte-site-survey-remarks', 'public');
            $mimeType = $f->getClientMimeType();
            $originalName = $f->getClientOriginalName();
        }

        $id = DB::table('lte_site_survey_remarks')->insertGetId([
            'lte_site_survey_id' => $survey->id,
            'user_id' => $user->id,
            'remark' => $data['remark'],
            'file_path' => $filePath,
            'mime_type' => $mimeType,
            'original_name' => $originalName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $row = DB::table('lte_site_survey_remarks as r')
            ->leftJoin('users as u', 'r.user_id', '=', 'u.id')
            ->where('r.id', $id)
            ->first([
                'r.id',
                'r.user_id',
                'u.name as user_name',
                'r.remark',
                'r.file_path',
                'r.mime_type',
                'r.original_name',
                'r.created_at',
            ]);

        return response()->json(['success' => true, 'data' => $row]);
    }

    public function servePhoto(Request $request, LteSiteSurveyPhoto $photo)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
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

        $absolutePath = $disk->path($filePath);
        $mime = $photo->mime_type ?: $disk->mimeType($filePath) ?: 'application/octet-stream';

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . addslashes($photo->original_name ?: basename($filePath)) . '"',
        ]);
    }

    public function destroyPhoto(Request $request, LteSiteSurveyPhoto $photo)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $survey = LteSiteSurvey::query()->find($photo->lte_site_survey_id);
        if (!$survey) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
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
            Log::error('Mobile LTE site survey photo delete failed', [
                'ref' => $logRef,
                'user_id' => $user->id,
                'survey_id' => optional($survey)->id,
                'photo_id' => $photo->id,
                'ip' => $request->ip(),
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
            Log::error($e);
            return response()->json(['success' => false, 'message' => 'Failed to remove image. Ref: ' . $logRef], 500);
        }

        return response()->json(['success' => true]);
    }

    public function serveRemarkFile(Request $request, int $remark)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
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

        $absolutePath = $disk->path($filePath);
        $mime = $row->mime_type ?: $disk->mimeType($filePath) ?: 'application/octet-stream';

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . addslashes($row->original_name ?: basename($filePath)) . '"',
        ]);
    }
}
