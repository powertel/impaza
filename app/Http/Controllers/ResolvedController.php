<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Fault;
use App\Models\Remark;
use App\Services\FaultLifecycle;
use Carbon\Carbon;

class ResolvedController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 20);
        $perPage = in_array($perPage, [10,20,50,100]) ? $perPage : 20;
        $q = trim((string) $request->query('q',''));

        $nocClearedId = 6;
        $since = now()->subHours(24);

        $query = DB::table('faults')
            ->leftjoin('customers','faults.customer_id','=','customers.id')
            ->leftjoin('links','faults.link_id','=','links.id')
            ->leftjoin('users as assigned_users','faults.assignedTo','=','assigned_users.id')
            ->leftjoin('users as reported_users','faults.user_id','=','reported_users.id')
            ->leftjoin('users as assessed_users','faults.assessed_by','=','assessed_users.id')
            ->leftjoin('account_managers', 'customers.account_manager_id','=','account_managers.id')
            ->leftjoin('users as account_manager_users','account_managers.user_id','=','account_manager_users.id')
            ->leftjoin('statuses','faults.status_id','=','statuses.id')
            ->leftjoin('cities','faults.city_id','=','cities.id')
            ->leftjoin('suburbs','faults.suburb_id','=','suburbs.id')
            ->leftjoin('pops','faults.pop_id','=','pops.id')
            ->leftjoin('reasons_for_outages as suspectedRFO','faults.suspectedRfo_id','=','suspectedRFO.id')
            ->leftjoin('reasons_for_outages as confirmedRFO','faults.confirmedRfo_id','=','confirmedRFO.id')
            ->where('faults.status_id', '=', $nocClearedId)
            ->where('faults.updated_at', '>=', $since)
            ->orderBy('faults.updated_at', 'desc')
            ->select([
                'faults.id',
                'faults.user_id',
                'faults.fault_ref_number',
                'customers.customer',
                'faults.customer_id',
                'faults.city_id',
                'faults.suburb_id',
                'faults.pop_id',
                'faults.link_id',
                'faults.status_id',
                'faults.contactName',
                'faults.phoneNumber',
                'faults.contactEmail',
                'faults.address',
                'account_manager_users.name as accountManager',
                'faults.suspectedRfo_id',
                'links.link',
                'statuses.description',
                'assigned_users.name as assignedTo',
                'reported_users.name as reportedBy',
                'assessed_users.name as assessedBy',
                'faults.serviceType',
                'faults.serviceAttribute',
                'faults.faultType',
                'faults.priorityLevel',
                'faults.created_at',
                'faults.updated_at',
                'cities.city',
                'suburbs.suburb',
                'pops.pop',
                'suspectedRFO.RFO as RFO',
                'faults.confirmedRfo_id',
                'confirmedRFO.RFO as confirmedRFO'
                ]);

        if ($q !== '') {
            $like = "%".$q."%";
            $query->where(function($qq) use ($like) {
                $qq->where('faults.fault_ref_number', 'like', $like)
                   ->orWhere('customers.customer', 'like', $like)
                   ->orWhere('account_manager_users.name', 'like', $like)
                   ->orWhere('links.link', 'like', $like)
                   ->orWhere('assigned_users.name', 'like', $like)
                   ->orWhere('reported_users.name', 'like', $like)
                   ->orWhere('cities.city', 'like', $like)
                   ->orWhere('suburbs.suburb', 'like', $like)
                   ->orWhere('pops.pop', 'like', $like);
            });
        }

        $faults = $query->paginate($perPage)->withQueryString();

        $faultIds = $faults->getCollection()->pluck('id');
        $remarksRecords = DB::table('remarks')
            ->leftjoin('remark_activities','remarks.remarkActivity_id','=','remark_activities.id')
            ->leftjoin('users','remarks.user_id','=','users.id')
            ->whereIn('remarks.fault_id', $faultIds)
            ->orderBy('remarks.created_at', 'desc')
            ->get(['remarks.id','remarks.fault_id','remarks.created_at','remarks.remark','remarks.file_path','users.name','remark_activities.activity']);
        $remarksByFault = $remarksRecords->groupBy('fault_id');

         // Compute Age for each fault: from logged date to NOC-cleared date (status 6), otherwise to now
        $faultAges = [];
        $faultAgeStart = [];
        $faultAgeEnd = [];
        $nocClearedId = (int) (DB::table('statuses')->where('status_code', 'CLN')->value('id') ?? 6);
        $faultIdsList = $faults->getCollection()->pluck('id')->all();
        if (!empty($faultIdsList)) {
            $clearedLogs = DB::table('fault_stage_logs')
                ->whereIn('fault_id', $faultIdsList)
                ->where('status_id', $nocClearedId)
                ->select('fault_id','started_at')
                ->get()
                ->keyBy('fault_id');

            foreach ($faults->getCollection() as $f) {
                $start = Carbon::parse($f->created_at);
                $end = null;
                if ((int)$f->status_id === $nocClearedId && isset($clearedLogs[$f->id])) {
                    $end = Carbon::parse($clearedLogs[$f->id]->started_at);
                } else {
                    $end = Carbon::now();
                }
                $days = $start->diffInDays($end);
                $hours = $start->copy()->addDays($days)->diffInHours($end);
                $hours = $hours % 24;
                $minutes = $start->copy()->addDays($days)->addHours($hours)->diffInMinutes($end);
                $minutes = $minutes % 60;
                $faultAges[$f->id] = ($days > 0 ? ($days.'d ') : '') . ($hours.'h ') . ($minutes.'m');
                $faultAgeStart[$f->id] = $start->format('c');
                $faultAgeEnd[$f->id] = ((int)$f->status_id === $nocClearedId && isset($clearedLogs[$f->id])) ? Carbon::parse($clearedLogs[$f->id]->started_at)->format('c') : null;
            }
        }

        return view('resolved.index', compact('faults','perPage','remarksByFault','faultAges','faultAgeStart','faultAgeEnd'));
    }

    public function revoke(Request $request, Fault $fault)
    {
        $validated = $request->validate([
            'remark' => ['required','string']
        ]);

        $fault->update(['status_id' => 5]);
        FaultLifecycle::recordStatusChange($fault, 5, $request->user()->id);
        FaultLifecycle::reopenStageForStatus($fault, 5, $request->user()->id);
        // Do NOT reassign during NOC review; assignment remains closed until rectification

        Remark::create([
            'fault_id' => $fault->id,
            'user_id' => $request->user()->id,
            'remark' => 'Resolved revoke: '.$validated['remark'],
        ]);

        return back()->with('success', 'Fault has been Reopened');
    }
}