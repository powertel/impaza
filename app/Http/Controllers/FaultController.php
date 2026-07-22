<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fault;
use App\Models\Suburb;
use App\Models\City;
use App\Models\Pop;
use App\Models\Customer;
use App\Models\Link;
use App\Models\Remark;
use App\Models\AccountManager;
use App\Models\FaultSection;
use App\Models\ReasonsForOutage;
use DB;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Services\FaultLifecycle;
use Carbon\Carbon;
 



class FaultController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:fault-list|fault-create|fault-edit|fault-delete', ['only' => ['index','store']]);
         $this->middleware('permission:fault-create', ['only' => ['create','store']]);
         $this->middleware('permission:fault-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:fault-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = (int) request('per_page', 20);
        $perPage = in_array($perPage, [10,20,50,100]) ? $perPage : 20;
        $q = trim((string) request('q', ''));
        $statusFilter = request('status', 'all');
        $ageFilter = request('age', 'all');

        $faultsQuery = DB::table('faults')
                ->leftjoin('customers','faults.customer_id','=','customers.id')
                ->leftjoin('links','faults.link_id','=','links.id')
                ->leftjoin('users as assigned_users','faults.assignedTo','=','assigned_users.id')
                ->leftjoin('users as assessed_users','faults.assessed_by','=','assessed_users.id')
				->leftjoin('users as reported_users','faults.user_id','=','reported_users.id')
                ->leftjoin('account_managers', 'customers.account_manager_id','=','account_managers.id')
                ->leftjoin('users as account_manager_users','account_managers.user_id','=','account_manager_users.id')
                ->leftjoin('statuses','faults.status_id','=','statuses.id')
                ->leftjoin('reasons_for_outages as suspectedRFO','faults.suspectedRfo_id','=','suspectedRFO.id')
                ->leftjoin('reasons_for_outages as confirmedRFO','faults.confirmedRfo_id','=','confirmedRFO.id')
                ->leftjoin('cities','faults.city_id','=','cities.id')
                ->leftjoin('suburbs','faults.suburb_id','=','suburbs.id')
                ->leftjoin('pops','faults.pop_id','=','pops.id')
                ->orderBy('faults.created_at', 'desc')
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
                'cities.city',
                'suburbs.suburb',
                'pops.pop',
                'suspectedRFO.RFO as RFO',
                'faults.confirmedRfo_id',
                'confirmedRFO.RFO as confirmedRFO'
                ]);

        if ($q !== '') {
            $like = "%".$q."%";
            $faultsQuery->where(function($qq) use ($like) {
                $qq->where('faults.fault_ref_number', 'like', $like)
                   ->orWhere('customers.customer', 'like', $like)
                   ->orWhere('account_manager_users.name', 'like', $like)
                   ->orWhere('links.link', 'like', $like)
                   ->orWhere('assigned_users.name', 'like', $like)
                   ->orWhere('reported_users.name', 'like', $like)
                   ->orWhere('statuses.description', 'like', $like)
                   ->orWhere('cities.city', 'like', $like)
                   ->orWhere('suburbs.suburb', 'like', $like)
                   ->orWhere('pops.pop', 'like', $like);
            });
        }

        // Status filter: 'lt4' or specific status id
        if ($statusFilter === 'lt4') {
            $faultsQuery->where('faults.status_id', '!=',6);
        } elseif (ctype_digit((string) $statusFilter)) {
            $faultsQuery->where('faults.status_id', '=', (int)$statusFilter);
        }

        // Age filter: today / within 72 hours / over 72 hours
        if ($ageFilter === 'today') {
            $faultsQuery->whereDate('faults.created_at', Carbon::today());
        } elseif ($ageFilter === 'lt72') {
            $faultsQuery->where('faults.created_at', '>=', Carbon::now()->subHours(72));
        } elseif ($ageFilter === 'gt72') {
            $faultsQuery->where('faults.created_at', '<', Carbon::now()->subHours(72));
        }

        $faults = $faultsQuery->paginate($perPage)->withQueryString();
        
        // Collect remarks for all listed faults and group by fault_id
        $faultIds = $faults->getCollection()->pluck('id');
        $remarksRecords = DB::table('remarks')
            ->leftjoin('remark_activities','remarks.remarkActivity_id','=','remark_activities.id')
            ->leftjoin('users','remarks.user_id','=','users.id')
            ->whereIn('remarks.fault_id', $faultIds)
            ->orderBy('remarks.created_at', 'desc')
            ->get([
                'remarks.id',
                'remarks.fault_id',
                'remarks.created_at',
                'remarks.remark',
                'remarks.file_path',
                'users.name',
                'remark_activities.activity'
            ]);

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
        
        $city = City::all();
        $customer = DB::table('customers')
            ->orderBy('customers.customer', 'asc')
            ->get();
        $location = Suburb::all();
        $link = Link::all();
        $pop = Pop::all();
        $accountManager = AccountManager::all();
        /* $suspectedRFO = ReasonsForOutage::whereBetween('id', [1, 5])->get(); */
        $suspectedRFO = ReasonsForOutage::all();
        // Load all statuses for dynamic filter options
        $openStatuses = DB::table('statuses')
            ->orderBy('id','asc')
            ->get(['id','description']);

        // Age stats for open faults (status_id < 4)
        $ageStats = [
            'open_total' => DB::table('faults')->where('status_id','!=',$nocClearedId)->count(),
            'open_today' => DB::table('faults')->where('status_id','!=',$nocClearedId)->whereDate('created_at', Carbon::today())->count(),
            'open_lt72'  => DB::table('faults')->where('status_id','!=',$nocClearedId)->where('created_at', '>=', Carbon::now()->subHours(72))->count(),
            'open_gt72'  => DB::table('faults')->where('status_id','!=',$nocClearedId)->where('created_at', '<', Carbon::now()->subHours(72))->count(),
        ];

        return view('faults.index',compact('faults','customer','city','accountManager','location','link','pop','suspectedRFO','remarksByFault','openStatuses','ageStats','faultAges','faultAgeStart','faultAgeEnd'))
        ->with('i');

    }

    /**
     * Build the base faults query honoring filters used in index.
     */
    protected function buildFilteredFaultsQuery(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $statusFilter = $request->query('status', 'all');
        $ageFilter = $request->query('age', 'all');

        $faultsQuery = DB::table('faults')
                ->leftjoin('customers','faults.customer_id','=','customers.id')
                ->leftjoin('links','faults.link_id','=','links.id')
                ->leftjoin('users as assigned_users','faults.assignedTo','=','assigned_users.id')
                ->leftjoin('users as assessed_users','faults.assessed_by','=','assessed_users.id')
				->leftjoin('users as reported_users','faults.user_id','=','reported_users.id')
                ->leftjoin('account_managers', 'customers.account_manager_id','=','account_managers.id')
                ->leftjoin('users as account_manager_users','account_managers.user_id','=','account_manager_users.id')
                ->leftjoin('statuses','faults.status_id','=','statuses.id')
                ->leftjoin('reasons_for_outages','faults.suspectedRfo_id','=','reasons_for_outages.id')
                ->leftjoin('cities','faults.city_id','=','cities.id')
                ->leftjoin('suburbs','faults.suburb_id','=','suburbs.id')
                ->leftjoin('pops','faults.pop_id','=','pops.id')
                ->orderBy('faults.created_at', 'desc')
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
                'cities.city',
                'suburbs.suburb',
                'pops.pop',
                'reasons_for_outages.RFO as RFO'
                ]);

        if ($q !== '') {
            $like = "%".$q."%";
            $faultsQuery->where(function($qq) use ($like) {
                $qq->where('faults.fault_ref_number', 'like', $like)
                   ->orWhere('customers.customer', 'like', $like)
                   ->orWhere('account_manager_users.name', 'like', $like)
                   ->orWhere('links.link', 'like', $like)
                   ->orWhere('assigned_users.name', 'like', $like)
                   ->orWhere('reported_users.name', 'like', $like)
                   ->orWhere('statuses.description', 'like', $like)
                   ->orWhere('cities.city', 'like', $like)
                   ->orWhere('suburbs.suburb', 'like', $like)
                   ->orWhere('pops.pop', 'like', $like);
            });
        }

        // Status filter: 'lt4' or specific status id
        if ($statusFilter === 'lt4') {
            $faultsQuery->where('faults.status_id', '<', 4);
        } elseif (ctype_digit((string) $statusFilter)) {
            $faultsQuery->where('faults.status_id', '=', (int)$statusFilter);
        }

        // Age filter: today / within 72 hours / over 72 hours
        if ($ageFilter === 'today') {
            $faultsQuery->whereDate('faults.created_at', \Carbon\Carbon::today());
        } elseif ($ageFilter === 'lt72') {
            $faultsQuery->where('faults.created_at', '>=', \Carbon\Carbon::now()->subHours(72));
        } elseif ($ageFilter === 'gt72') {
            $faultsQuery->where('faults.created_at', '<', \Carbon\Carbon::now()->subHours(72));
        }

        return $faultsQuery;
    }

    /**
     * Export faults to CSV (Excel-compatible), bypassing pagination.
     */
    public function exportCsv(Request $request)
    {
        $faults = $this->buildFilteredFaultsQuery($request)->get();

        $filename = 'faults_'.date('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $columns = [
            'Ref No', 'Customer', 'Account Manager', 'Link', 'Assigned To',
            'Date Reported', 'Logged By', 'Status'
        ];

        $callback = function() use ($faults, $columns) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $columns);
            foreach ($faults as $f) {
                fputcsv($out, [
                    $f->fault_ref_number,
                    $f->customer,
                    $f->accountManager,
                    $f->link,
                    $f->assignedTo,
                    \Carbon\Carbon::parse($f->created_at)->format('Y-m-d H:i'),
                    $f->reportedBy,
                    $f->description,
                ]);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    /**
     * Export faults to PDF via Dompdf, bypassing pagination.
     */
    public function exportPdf(Request $request)
    {
        $faults = $this->buildFilteredFaultsQuery($request)->get();
        $filename = 'faults_'.date('Ymd_His').'.pdf';

        // If Dompdf not installed, provide a helpful error
        if (!class_exists('Barryvdh\\DomPDF\\Facade\\Pdf')) {
            return response()->json([
                'message' => 'PDF export requires barryvdh/laravel-dompdf. Please install the dependency.',
            ], 500);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('faults.export_pdf', [
            'faults' => $faults,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }


    public function managedCustomers(Request $request)
    {
        $userId = $request->user()->id;
        $perPage = (int) request('per_page', 20);
        $perPage = in_array($perPage, [10,20,50,100]) ? $perPage : 20;
        $q = trim((string) request('q', ''));
        $statusFilter = request('status', 'all');
        $ageFilter = request('age', 'all');

        $faultsQuery = DB::table('faults')
                ->leftjoin('customers','faults.customer_id','=','customers.id')
                ->leftjoin('links','faults.link_id','=','links.id')
                ->leftjoin('users as assigned_users','faults.assignedTo','=','assigned_users.id')
                ->leftjoin('users as assessed_users','faults.assessed_by','=','assessed_users.id')
				->leftjoin('users as reported_users','faults.user_id','=','reported_users.id')
                ->leftjoin('account_managers', 'customers.account_manager_id','=','account_managers.id')
                ->leftjoin('users as account_manager_users','account_managers.user_id','=','account_manager_users.id')
                ->leftjoin('statuses','faults.status_id','=','statuses.id')
                ->leftjoin('reasons_for_outages as suspectedRFO','faults.suspectedRfo_id','=','suspectedRFO.id')
                ->leftjoin('reasons_for_outages as confirmedRFO','faults.confirmedRfo_id','=','confirmedRFO.id')
                ->leftjoin('cities','faults.city_id','=','cities.id')
                ->leftjoin('suburbs','faults.suburb_id','=','suburbs.id')
                ->leftjoin('pops','faults.pop_id','=','pops.id')
                ->where(function($q) use ($userId, $request) {
                    $q->where('account_manager_users.id', '=', $userId);
                    $scopeRegion = trim((string) ($request->user()->region ?? ''));
                    if ($scopeRegion !== '') {
                        $q->orWhere('cities.region', '=', $scopeRegion);
                    }
                })
                ->orderBy('faults.created_at', 'desc')
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
                'cities.city',
                'suburbs.suburb',
                'pops.pop',
                'suspectedRFO.RFO as RFO',
                'faults.confirmedRfo_id',
                'confirmedRFO.RFO as confirmedRFO'
                ]);

        if ($q !== '') {
            $like = "%".$q."%";
            $faultsQuery->where(function($qq) use ($like) {
                $qq->where('faults.fault_ref_number', 'like', $like)
                   ->orWhere('customers.customer', 'like', $like)
                   ->orWhere('account_manager_users.name', 'like', $like)
                   ->orWhere('links.link', 'like', $like)
                   ->orWhere('assigned_users.name', 'like', $like)
                   ->orWhere('reported_users.name', 'like', $like)
                   ->orWhere('statuses.description', 'like', $like)
                   ->orWhere('cities.city', 'like', $like)
                   ->orWhere('suburbs.suburb', 'like', $like)
                   ->orWhere('pops.pop', 'like', $like);
            });
        }

        // Status filter: 'lt4' or specific status id
        if ($statusFilter === 'lt4') {
            $faultsQuery->where('faults.status_id', '!=',6);
        } elseif (ctype_digit((string) $statusFilter)) {
            $faultsQuery->where('faults.status_id', '=', (int)$statusFilter);
        }

        // Age filter: today / within 72 hours / over 72 hours
        if ($ageFilter === 'today') {
            $faultsQuery->whereDate('faults.created_at', Carbon::today());
        } elseif ($ageFilter === 'lt72') {
            $faultsQuery->where('faults.created_at', '>=', Carbon::now()->subHours(72));
        } elseif ($ageFilter === 'gt72') {
            $faultsQuery->where('faults.created_at', '<', Carbon::now()->subHours(72));
        }

        $faults = $faultsQuery->paginate($perPage)->withQueryString();
        
        // Collect remarks for all listed faults and group by fault_id
        $faultIds = $faults->getCollection()->pluck('id');
        $remarksRecords = DB::table('remarks')
            ->leftjoin('remark_activities','remarks.remarkActivity_id','=','remark_activities.id')
            ->leftjoin('users','remarks.user_id','=','users.id')
            ->whereIn('remarks.fault_id', $faultIds)
            ->orderBy('remarks.created_at', 'desc')
            ->get([
                'remarks.id',
                'remarks.fault_id',
                'remarks.created_at',
                'remarks.remark',
                'remarks.file_path',
                'users.name',
                'remark_activities.activity'
            ]);

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
        
        $city = City::all();
        $customer = DB::table('customers')
            ->orderBy('customers.customer', 'asc')
            ->get();
        $location = Suburb::all();
        $link = Link::all();
        $pop = Pop::all();
        $accountManager = AccountManager::all();
        /* $suspectedRFO = ReasonsForOutage::whereBetween('id', [1, 5])->get(); */
        $suspectedRFO = ReasonsForOutage::all();
        // Load all statuses for dynamic filter options
        $openStatuses = DB::table('statuses')
            ->orderBy('id','asc')
            ->get(['id','description']);

        // Age stats for open faults (status_id < 4)
        $ageStats = [
            'open_total' => DB::table('faults')->where('status_id','!=',$nocClearedId)->count(),
            'open_today' => DB::table('faults')->where('status_id','!=',$nocClearedId)->whereDate('created_at', Carbon::today())->count(),
            'open_lt72'  => DB::table('faults')->where('status_id','!=',$nocClearedId)->where('created_at', '>=', Carbon::now()->subHours(72))->count(),
            'open_gt72'  => DB::table('faults')->where('status_id','!=',$nocClearedId)->where('created_at', '<', Carbon::now()->subHours(72))->count(),
        ];

        return view('faults.index',compact('faults','customer','city','accountManager','location','link','pop','suspectedRFO','remarksByFault','openStatuses','ageStats','faultAges','faultAgeStart','faultAgeEnd'))
        ->with('i');

    }

    /**
     * Display faults for customers managed by the logged-in Account Manager.
     */
    public function managedCustomers1(Request $request)
    {
        $userId = $request->user()->id;

        $perPage = (int) request('per_page', 20);
        $perPage = in_array($perPage, [10,20,50,100]) ? $perPage : 20;
        $q = trim((string) request('q', ''));

        $faultsQuery = DB::table('faults')
                ->leftjoin('customers','faults.customer_id','=','customers.id')
                ->leftjoin('links','faults.link_id','=','links.id')
                ->leftjoin('users as assigned_users','faults.assignedTo','=','assigned_users.id')
                ->leftjoin('users as assessed_users','faults.assessed_by','=','assessed_users.id')
				->leftjoin('users as reported_users','faults.user_id','=','reported_users.id')
                ->leftjoin('account_managers', 'customers.account_manager_id','=','account_managers.id')
                ->leftjoin('users as account_manager_users','account_managers.user_id','=','account_manager_users.id')
                ->leftjoin('statuses','faults.status_id','=','statuses.id')
                ->leftjoin('reasons_for_outages as suspectedRFO','faults.suspectedRfo_id','=','suspectedRFO.id')
                ->leftjoin('reasons_for_outages as confirmedRFO','faults.confirmedRfo_id','=','confirmedRFO.id')
                ->leftjoin('cities','faults.city_id','=','cities.id')
                ->leftjoin('suburbs','faults.suburb_id','=','suburbs.id')
                ->leftjoin('pops','faults.pop_id','=','pops.id')
                ->where(function($q) use ($userId, $request) {
                    $q->where('account_manager_users.id', '=', $userId);
                    $scopeRegion = trim((string) ($request->user()->region ?? ''));
                    if ($scopeRegion !== '') {
                        $q->orWhere('cities.region', '=', $scopeRegion);
                    }
                })
                ->orderBy('faults.created_at', 'desc')
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
                'cities.city',
                'suburbs.suburb',
                'pops.pop',
                'suspectedRFO.RFO as RFO',
                'faults.confirmedRfo_id',
                'confirmedRFO.RFO as confirmedRFO'
                ]);

        if ($q !== '') {
            $like = "%".$q."%";
            $faultsQuery->where(function($qq) use ($like) {
                $qq->where('faults.fault_ref_number', 'like', $like)
                   ->orWhere('customers.customer', 'like', $like)
                   ->orWhere('account_manager_users.name', 'like', $like)
                   ->orWhere('links.link', 'like', $like)
                   ->orWhere('assigned_users.name', 'like', $like)
                   ->orWhere('reported_users.name', 'like', $like)
                   ->orWhere('statuses.description', 'like', $like)
                   ->orWhere('cities.city', 'like', $like)
                   ->orWhere('suburbs.suburb', 'like', $like)
                   ->orWhere('pops.pop', 'like', $like);
            });
        }

        $faults = $faultsQuery->paginate($perPage)->withQueryString();
        
        // Collect remarks for all listed faults and group by fault_id
        $faultIds = $faults->getCollection()->pluck('id');
        $remarksRecords = DB::table('remarks')
            ->leftjoin('remark_activities','remarks.remarkActivity_id','=','remark_activities.id')
            ->leftjoin('users','remarks.user_id','=','users.id')
            ->whereIn('remarks.fault_id', $faultIds)
            ->orderBy('remarks.created_at', 'desc')
            ->get([
                'remarks.id',
                'remarks.fault_id',
                'remarks.created_at',
                'remarks.remark',
                'remarks.file_path',
                'users.name',
                'remark_activities.activity'
            ]);

        $remarksByFault = $remarksRecords->groupBy('fault_id');
        
        $city = City::all();
        $customer = DB::table('customers')
            ->orderBy('customers.customer', 'asc')
            ->get();
        $location = Suburb::all();
        $link = Link::all();
        $pop = Pop::all();
        $accountManager = AccountManager::all();
        $suspectedRFO = ReasonsForOutage::all();

        return view('faults.index',compact('faults','customer','city','accountManager','location','link','pop','suspectedRFO','remarksByFault'))
        ->with('i');

    }

        /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
        //Fault::create($request->all());

        DB::beginTransaction();
        try{
            request()->validate([
                'customer_id'=> 'required|exists:customers,id',
                'contactName'=> 'required|string',
                'phoneNumber'=> ['required','string','size:12','regex:/^2637\d{8}$/'],
                'contactEmail'=> 'nullable|email|max:255',
                'address'=> 'nullable|string',
                'link_id'=> 'required|exists:links,id',
                'suspectedRfo_id'=> 'required|exists:reasons_for_outages,id',
                'remark'=> 'required|string',
                'attachment' => 'nullable|mimes:png,jpg,jpeg|max:2048'
            ], [
                'phoneNumber.regex' => 'Phone number must be 12 digits starting with 2637',
                'phoneNumber.size' => 'Phone number must be exactly 12 digits',
            ]);
           
            $req = $request->all();

            // Derive location and service details from selected link
            $lnk = Link::find($request->input('link_id'));
            if($lnk){
                $req['city_id'] = $lnk->city_id ?? null; 
                $req['suburb_id'] = $lnk->suburb_id ?? null; 
                $req['pop_id'] = $lnk->pop_id ?? null;
                $req['serviceType'] = $lnk->service_type ?: 'N/A';
                $req['serviceAttribute'] = trim((string) ($lnk->capacity ?? '')) !== '' ? (string) $lnk->capacity : 'N/A';
            }
            // Normalize email to null when not provided
            if(!$request->filled('contactEmail')){
                $req['contactEmail'] = null;
            }

            // Derive Account Manager from selected customer (snapshot at creation)
            $customer = Customer::find($request->input('customer_id'));
            $accountManagerId = null;
            if ($customer) {
                $amUserId = $customer->account_manager_id; // references users.id
                if ($amUserId) {
                    $user = User::find($amUserId);
                    $accountManager = AccountManager::firstOrCreate(
                        ['user_id' => $amUserId],
                        ['accountManager' => $user ? $user->name : 'Account Manager']
                    );
                    $accountManagerId = $accountManager->id;
                } else {
                    // Fallback to an "Unassigned" Account Manager record to satisfy NOT NULL constraint
                    $accountManager = AccountManager::whereNull('user_id')
                        ->where('accountManager', 'Unassigned')
                        ->first();
                    if (!$accountManager) {
                        $accountManager = AccountManager::create([
                            'accountManager' => 'Unassigned',
                            'user_id' => null,
                        ]);
                    }
                    $accountManagerId = $accountManager->id;
                }
            }
            $req['accountManager_id'] = $accountManagerId;
        
            $resolvedOnCall = (bool)$request->input('resolved_on_call', false);
            $nocClearedId = (int) (DB::table('statuses')->where('status_code', 'CLN')->value('id') ?? 6);
            $req['status_id'] = $resolvedOnCall ? $nocClearedId : 1;
            if ($resolvedOnCall) {
                $req['confirmedRfo_id'] = $req['suspectedRfo_id'];
            }
            $req['user_id'] = $request->user()->id;
            // Build daily-running sequence: PWT2510171, P2510172, …
            $today = date('ymd');                          // 251017
            $prefix = 'PWT' . $today;                      // PWT251017

            // Get the highest sequence used today
            $lastToday = Fault::where('fault_ref_number', 'LIKE', $prefix . '%')
                               ->orderByDesc('fault_ref_number')
                               ->value('fault_ref_number');

            if ($lastToday) {
                // Extract the numeric suffix and increment
                $next = (int)substr($lastToday, strlen($prefix)) + 1;
            } else {
                $next = 1;                                 // First of the day
            }

            $req['fault_ref_number'] = $prefix . sprintf('%03d', $next);

            $fault = Fault::create($req);
            FaultLifecycle::recordStatusChange($fault, (int)$req['status_id'], $request->user()->id);
            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->storePublicly('attachments', 'public');
            } else {
                $path = null;
            }
            $remarkActivity_id = DB::table('remark_activities')->where('activity','=',$request['activity'])->get('remark_activities.id')->first();
            $remark = Remark::create(
                [
                    'fault_id'=> $fault->id,
                    'user_id' => $request->user()->id,
                    'remark' => $request['remark'],
                    'remarkActivity_id'=>$remarkActivity_id->id,
                    'file_path'=>$path
                ]
            );
           
        

            $fault_section = FaultSection::create(
                [
                    'fault_id'=> $fault->id,
                ]
            );

            $aggregatorCustomer = Customer::find($fault->customer_id);
            if ($aggregatorCustomer && (bool) $aggregatorCustomer->is_pop_aggregator && !empty($fault->pop_id)) {
                $aggregatorCustomerId = (int) $aggregatorCustomer->id;
                $popImpactStatusId = (int) (DB::table('statuses')->where('status_code', '=', 'POI')->value('id') ?? 0);
                if ($popImpactStatusId <= 0) {
                    $popImpactStatusId = 1;
                }
                $impactedLinks = Link::query()
                    ->join('customers', 'links.customer_id', '=', 'customers.id')
                    ->where('links.pop_id', '=', (int) $fault->pop_id)
                    ->where('links.customer_id', '!=', $aggregatorCustomerId)
                    ->where(function($q){
                        $q->whereNull('customers.is_pop_aggregator')
                          ->orWhere('customers.is_pop_aggregator', '!=', 1);
                    })
                    ->select('links.*')
                    ->get();

                $seq = $next + 1;
                $accountManagerCache = [];

                foreach ($impactedLinks as $impactedLink) {
                    $impactedCustomer = Customer::find($impactedLink->customer_id);

                    $childAccountManagerId = null;
                    if ($impactedCustomer) {
                        $amUserId = $impactedCustomer->account_manager_id;
                        if ($amUserId) {
                            if (!array_key_exists($amUserId, $accountManagerCache)) {
                                $amUser = User::find($amUserId);
                                $am = AccountManager::firstOrCreate(
                                    ['user_id' => $amUserId],
                                    ['accountManager' => $amUser ? $amUser->name : 'Account Manager']
                                );
                                $accountManagerCache[$amUserId] = $am->id;
                            }
                            $childAccountManagerId = $accountManagerCache[$amUserId];
                        } else {
                            $fallbackAm = AccountManager::whereNull('user_id')
                                ->where('accountManager', 'Unassigned')
                                ->first();
                            if (!$fallbackAm) {
                                $fallbackAm = AccountManager::create([
                                    'accountManager' => 'Unassigned',
                                    'user_id' => null,
                                ]);
                            }
                            $childAccountManagerId = $fallbackAm->id;
                        }
                    }

                    if ($childAccountManagerId === null) {
                        $fallbackAm = AccountManager::whereNull('user_id')
                            ->where('accountManager', 'Unassigned')
                            ->first();
                        if (!$fallbackAm) {
                            $fallbackAm = AccountManager::create([
                                'accountManager' => 'Unassigned',
                                'user_id' => null,
                            ]);
                        }
                        $childAccountManagerId = $fallbackAm->id;
                    }

                    $childContactNumber = $impactedCustomer && !empty($impactedCustomer->contact_number)
                        ? preg_replace('/\s+/', '', (string) $impactedCustomer->contact_number)
                        : (string) $fault->phoneNumber;

                    $childData = [
                        'root_fault_id' => $fault->id,
                        'fault_ref_number' => $prefix . sprintf('%03d', $seq++),
                        'customer_id' => $impactedLink->customer_id,
                        'contactName' => $impactedCustomer ? (string) $impactedCustomer->customer : (string) $fault->contactName,
                        'phoneNumber' => $childContactNumber,
                        'contactEmail' => $fault->contactEmail,
                        'address' => $impactedCustomer && !empty($impactedCustomer->address) ? (string) $impactedCustomer->address : (string) $fault->address,
                        'accountManager_id' => $childAccountManagerId,
                        'city_id' => $impactedLink->city_id ?? $fault->city_id,
                        'suburb_id' => $impactedLink->suburb_id ?? $fault->suburb_id,
                        'pop_id' => $impactedLink->pop_id ?? $fault->pop_id,
                        'link_id' => $impactedLink->id,
                        'suspectedRfo_id' => $fault->suspectedRfo_id,
                        'confirmedRfo_id' => $fault->confirmedRfo_id,
                        'serviceType' => $impactedLink->service_type ?? $fault->serviceType,
                        'serviceAttribute' => $fault->serviceAttribute,
                        'status_id' => ((int) $fault->status_id === (int) $nocClearedId) ? (int) $nocClearedId : $popImpactStatusId,
                        'faultType' => $fault->faultType ?? 'POP OUTAGE',
                        'priorityLevel' => $fault->priorityLevel,
                        'user_id' => $request->user()->id,
                    ];

                    $childFault = Fault::create($childData);
                    FaultLifecycle::recordStatusChange($childFault, (int) $childData['status_id'], $request->user()->id);

                    Remark::create([
                        'fault_id' => $childFault->id,
                        'user_id' => $request->user()->id,
                        'remark' => $request['remark'] . " (Auto-linked to POP fault {$fault->fault_ref_number})",
                        'remarkActivity_id' => $remarkActivity_id->id,
                        'file_path' => null,
                    ]);

                    FaultSection::create([
                        'fault_id' => $childFault->id,
                    ]);
                }
            }
          //  $request->user()->posts()->create($request->only('body'));
            if($fault && $remark && $fault_section)
            {
                DB::commit();
                FaultLifecycle::sendReceiptEmail($fault);
            }
            else
            {
                DB::rollback();
            }
            return redirect()->route('faults.index')
            ->with('success', 'Fault Created');
        }

        catch(Exception $ex)
        {
            DB::rollback();
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Prevent loading the edit view for locked faults
        $faultModel = Fault::find($id);
        if ($faultModel && (int)$faultModel->status_id == 6) {
            return redirect()->route('faults.index')
                ->with('error', 'This fault cannot be edited after it is cleared by NOC.');
        }

        $fault = DB::table('faults')
            ->leftjoin('customers','faults.customer_id','=','customers.id')
            ->leftjoin('links','faults.link_id','=','links.id')
            ->leftjoin('cities','faults.city_id','=','cities.id')
            ->leftjoin('suburbs','faults.suburb_id','=','suburbs.id')
            ->leftjoin('pops','faults.pop_id','=','pops.id')
            ->leftjoin('remarks','remarks.fault_id','=','faults.id')
            ->leftjoin('reasons_for_outages','faults.suspectedRfo_id','=','reasons_for_outages.id')
            ->leftjoin('account_managers','faults.accountManager_id','=','account_managers.id')
            ->where('faults.id','=',$id)
            ->get(['faults.id','faults.customer_id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
            'account_managers.accountManager','faults.accountManager_id','faults.suspectedRfo_id','faults.city_id','cities.city','faults.suburb_id','suburbs.suburb','faults.pop_id','pops.pop','reasons_for_outages.RFO','faults.link_id','links.link'
            ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','remarks.fault_id','remarks.remark','faults.created_at'])
            ->first();
            $remarks= DB::table('remarks')
            ->leftjoin('remark_activities','remarks.remarkActivity_id','=','remark_activities.id')
            ->leftjoin('users','remarks.user_id','=','users.id')
            ->where('remarks.fault_id','=',$id)
            ->get(['remarks.id','remarks.created_at','remarks.remark','remarks.file_path','users.name','remark_activities.activity']);
            
            $cities = City::all();
            $customers = Customer::all();
            $suburbs = Suburb::all();
            $pops = Pop::all();
            $links = Link::all();
            $accountManagers = AccountManager::all();
            $suspectedRFO = ReasonsForOutage::all();


        return view('faults.edit',compact('fault','suspectedRFO','customers','cities','suburbs','pops','links','remarks','accountManagers'));


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $fault = Fault::find($id);

        // Block edits once fault has passed status_id = 1
        if ($fault && (int)$fault->status_id == 6) {
            return redirect()->route('faults.index')
                ->with('error', 'Editing is locked after the fault is cleared by NOC.');
        }

        $originalStatusId = (int) ($fault->status_id ?? 0);
        $data = $request->all();

        $request->validate([
            'attachment' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);

        // If customer changed, derive Account Manager from the selected customer
        if ($request->filled('customer_id')) {
            $customer = Customer::find($request->input('customer_id'));
            if ($customer) {
                $amUserId = $customer->account_manager_id;
                if ($amUserId) {
                    $user = User::find($amUserId);
                    $accountManager = AccountManager::firstOrCreate(
                        ['user_id' => $amUserId],
                        ['accountManager' => $user ? $user->name : 'Account Manager']
                    );
                    $data['accountManager_id'] = $accountManager->id;
                } else {
                    // Fallback to Unassigned to maintain NOT NULL constraint
                    $fallbackAm = AccountManager::whereNull('user_id')
                        ->where('accountManager', 'Unassigned')
                        ->first();
                    if (!$fallbackAm) {
                        $fallbackAm = AccountManager::create([
                            'accountManager' => 'Unassigned',
                            'user_id' => null,
                        ]);
                    }
                    $data['accountManager_id'] = $fallbackAm->id;
                }
            }
        }

        // If link changed, re-derive location and service details
        if ($request->filled('link_id') && $request->input('link_id') != $fault->link_id) {
            $lnk = Link::find($request->input('link_id'));
            if ($lnk) {
                $data['city_id'] = $lnk->city_id;
                $data['suburb_id'] = $lnk->suburb_id;
                $data['pop_id'] = $lnk->pop_id;
                $data['serviceType'] = $lnk->service_type;
            }
        }

        // Handle Resolved on Call
        $newStatusId = $originalStatusId;
        if ($request->has('resolved_on_call') && $request->input('resolved_on_call')) {
             $nocClearedId = (int) (DB::table('statuses')->where('status_code', 'CLN')->value('id') ?? 6);
             $data['status_id'] = $nocClearedId;
             $data['confirmedRfo_id'] = $data['suspectedRfo_id'] ?? $fault->suspectedRfo_id;
             $newStatusId = $nocClearedId;
             
             // Override activity if resolved
             $remarkActivity = DB::table('remark_activities')->where('activity', 'On Call Centre Clear')->first();
             if ($remarkActivity) {
                 $data['activity'] = 'On Call Centre Clear';
             }
        }

        if ($request->filled('remark') || $request->hasFile('attachment')) {
               $activityName = $data['activity'] ?? $request->input('activity', 'ON CALL CENTRE ASSESSMENT');
               $remarkActivity = DB::table('remark_activities')->where('activity', $activityName)->first();
               // If not found, fallback to 'ON CALL CENTRE ASSESSMENT' or ID 1
               if (!$remarkActivity) {
                   $remarkActivity = DB::table('remark_activities')->where('activity', 'ON CALL CENTRE ASSESSMENT')->first();
               }
               $actId = $remarkActivity ? $remarkActivity->id : 10;
               
               $path = null;
             if ($request->hasFile('attachment')) {
                 $path = $request->file('attachment')->storePublicly('attachments', 'public');
             }
             Remark::create([
                'fault_id'=> $fault->id,
                'user_id' => $request->user()->id,
                'remark' => $request->input('remark'),
                'remarkActivity_id' => $actId,
                'file_path' => $path,
             ]);
        }

        $fault->update($data);
        if ($newStatusId !== $originalStatusId && $newStatusId > 0) {
            $fault->refresh();
            FaultLifecycle::recordStatusChange($fault, $newStatusId, $request->user()->id);
        }
        return redirect(route('faults.index'))
        ->with('success','Fault Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function faults(Request $req)
    {
        $faults = DB::table('faults')
                ->leftjoin('customers','faults.customer_id','=','customers.id')
                ->leftjoin('links','faults.link_id','=','links.id')
                ->leftjoin('account_managers','faults.accountManager_id','=','account_managers.id')
                ->leftjoin('statuses','faults.status_id','=','statuses.id')
                ->orderBy('faults.created_at', 'desc')
                ->get(['faults.id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
                'account_managers.accountManager','faults.suspectedRfo','links.link','statuses.description'
                ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.created_at']);

        return response()->json($faults);
    }

    // Cascading selects helpers
    public function findSuburb($cityId)
    {
        $suburbs = Suburb::where('city_id', $cityId)->pluck('suburb', 'id');
        return response()->json($suburbs);
    }

    public function findPop($suburbId)
    {
        $pops = Pop::where('suburb_id', $suburbId)->pluck('pop', 'id');
        return response()->json($pops);
    }

    public function findLink($customerId)
    {
        $links = Link::with(['city', 'suburb'])
                     ->where('customer_id', $customerId)
                     ->get()
                     ->mapWithKeys(function ($link) {
                         return [
                             $link->id => [
                                 'id'     => $link->id,
                                 'link'   => $link->link,
                                 'city'   => $link->city->city ?? null,
                                 'suburb' => $link->suburb->suburb ?? null,
                             ]
                         ];
                     });

        return response()->json($links);
    }


    public function callCentreRestore(Request $request)
    {
      
        //Fault::create($request->all());

        DB::beginTransaction();
        try{
            request()->validate([
                'customer_id'=> 'required|exists:customers,id',
                'contactName'=> 'required|string',
                'phoneNumber'=> ['required','string','size:12','regex:/^2637\d{8}$/'],
                'contactEmail'=> 'nullable|email|max:255',
                'address'=> 'nullable|string',
                'link_id'=> 'required|exists:links,id',
                'suspectedRfo_id'=> 'required|exists:reasons_for_outages,id',
                'remark'=> 'required|string',
                'attachment' => 'nullable|mimes:png,jpg,jpeg|max:2048'
            ], [
                'phoneNumber.regex' => 'Phone number must be 12 digits starting with 2637',
                'phoneNumber.size' => 'Phone number must be exactly 12 digits',
            ]);
           
            $req = $request->all();

            // Derive location and service details from selected link
            $lnk = Link::find($request->input('link_id'));
            if($lnk){
                $req['city_id'] = $lnk->city_id ?? null; 
                $req['suburb_id'] = $lnk->suburb_id ?? null; 
                $req['pop_id'] = $lnk->pop_id ?? null;
                $req['serviceType'] = $lnk->service_type ?: 'N/A';
                $req['serviceAttribute'] = trim((string) ($lnk->capacity ?? '')) !== '' ? (string) $lnk->capacity : 'N/A';
            }
            // Normalize email to null when not provided
            if(!$request->filled('contactEmail')){
                $req['contactEmail'] = null;
            }

            // Derive Account Manager from selected customer (snapshot at creation)
            $customer = Customer::find($request->input('customer_id'));
            $accountManagerId = null;
            if ($customer) {
                $amUserId = $customer->account_manager_id; // references users.id
                if ($amUserId) {
                    $user = User::find($amUserId);
                    $accountManager = AccountManager::firstOrCreate(
                        ['user_id' => $amUserId],
                        ['accountManager' => $user ? $user->name : 'Account Manager']
                    );
                    $accountManagerId = $accountManager->id;
                } else {
                    // Fallback to an "Unassigned" Account Manager record to satisfy NOT NULL constraint
                    $accountManager = AccountManager::whereNull('user_id')
                        ->where('accountManager', 'Unassigned')
                        ->first();
                    if (!$accountManager) {
                        $accountManager = AccountManager::create([
                            'accountManager' => 'Unassigned',
                            'user_id' => null,
                        ]);
                    }
                    $accountManagerId = $accountManager->id;
                }
            }
            $req['accountManager_id'] = $accountManagerId;
        
            //This is where i am creating the fault
            $req['status_id'] = 1;
			$req['user_id'] =$request->user()->id;
            // Build daily-running sequence: PWT2510171, P2510172, …
            $today = date('ymd');                          // 251017
            $prefix = 'PWT' . $today;                      // PWT251017

            // Get the highest sequence used today
            $lastToday = Fault::where('fault_ref_number', 'LIKE', $prefix . '%')
                               ->orderByDesc('fault_ref_number')
                               ->value('fault_ref_number');

            if ($lastToday) {
                // Extract the numeric suffix and increment
                $next = (int)substr($lastToday, strlen($prefix)) + 1;
            } else {
                $next = 1;                                 // First of the day
            }

            $req['fault_ref_number'] = $prefix . sprintf('%03d', $next);

            $fault = Fault::create($req);
            // Start lifecycle at "Waiting for assessment" (status_id = 1)
            FaultLifecycle::recordStatusChange($fault, 1, $request->user()->id);
            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->storePublicly('attachments', 'public');
            } else {
                $path = null;
            }
            $remarkActivity_id = DB::table('remark_activities')->where('activity','=',$request['activity'])->get('remark_activities.id')->first();
            $remark = Remark::create(
                [
                    'fault_id'=> $fault->id,
                    'user_id' => $request->user()->id,
                    'remark' => $request['remark'],
                    'remarkActivity_id'=>$remarkActivity_id->id,
                    'file_path'=>$path
                ]
            );
           
        

            $fault_section = FaultSection::create(
                [
                    'fault_id'=> $fault->id,
                ]
            );
          //  $request->user()->posts()->create($request->only('body'));
            if($fault && $remark && $fault_section)
            {
                DB::commit();
                FaultLifecycle::sendReceiptEmail($fault);
            }
            else
            {
                DB::rollback();
            }
            return redirect()->route('faults.index')
            ->with('success', 'Fault Created');
        }

        catch(Exception $ex)
        {
            DB::rollback();
        }

    }
}
