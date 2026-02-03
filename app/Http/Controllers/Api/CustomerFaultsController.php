<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;

class CustomerFaultsController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'account_number' => ['required', 'string'],
        ]);

        $account = trim((string) $request->query('account_number', ''));

        $customer = Customer::query()
            ->where('account_number', $account)
            ->first();

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $faults = DB::table('faults')
            ->leftJoin('customers', 'faults.customer_id', '=', 'customers.id')
            ->leftJoin('links', 'faults.link_id', '=', 'links.id')
            ->leftJoin('statuses', 'faults.status_id', '=', 'statuses.id')
            ->leftJoin('cities', 'faults.city_id', '=', 'cities.id')
            ->leftJoin('suburbs', 'faults.suburb_id', '=', 'suburbs.id')
            ->leftJoin('pops', 'faults.pop_id', '=', 'pops.id')
            ->where('faults.customer_id', '=', $customer->id)
            ->orderBy('faults.created_at', 'desc')
            ->get([
                'faults.id',
                'faults.fault_ref_number',
                'links.link',
                'statuses.description as status_description',
                'faults.status_id',
                'faults.priorityLevel',
                'faults.created_at',
                'cities.city',
                'suburbs.suburb',
                'pops.pop',
            ]);

        $faults->transform(function ($fault) {
            $statusId = $fault->status_id;
            
            if ($statusId == 1) {
                $fault->status = $fault->status_description;
            } elseif (($statusId >= 2 && $statusId <= 5) || ($statusId >= 7 && $statusId <= 12)) {
                $fault->status = 'In Progress';
            } elseif ($statusId == 6) {
                $fault->status = 'Completed';
            } else {
                // Default fallback if status_id is outside the specified ranges
                $fault->status = $fault->status_description; 
            }
            
            // Remove helper fields if we don't want them in the final response
            unset($fault->status_description);
            unset($fault->status_id);
            
            return $fault;
        });

        return response()->json([
            'customer' => [
                'id' => $customer->id,
                'customer' => $customer->customer,
                'account_number' => $customer->account_number,
                'contract_number' => $customer->contract_number,
            ],
            'faults' => $faults,
        ]);
    }
}

