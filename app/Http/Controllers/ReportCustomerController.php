<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PipelineStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportCustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->global_role === 'super_admin') {
            $companyId = $request->query('company_id');

            if (!$companyId) {
                $companyId = Company::where('status', 'active')->orderBy('name')->value('id');
            }
        } else {
            $companyId = $user->company_id;
        }

        $days = (int) $request->query('days', 30);
        if ($days <= 0 || $days > 365) {
            $days = 30;
        }

        $fromDate = now()->subDays($days);

        $totalCustomers = Customer::where('company_id', $companyId)->count();

        $byType = Customer::selectRaw('ps.type, count(customers.id) as total')
            ->join('pipeline_stages as ps', 'ps.id', '=', 'customers.current_stage_id')
            ->where('customers.company_id', $companyId)
            ->groupBy('ps.type')
            ->pluck('total', 'type');

        $byStage = Customer::selectRaw('ps.id, ps.name, ps.type, count(customers.id) as total')
            ->join('pipeline_stages as ps', 'ps.id', '=', 'customers.current_stage_id')
            ->where('customers.company_id', $companyId)
            ->groupBy('ps.id', 'ps.name', 'ps.type')
            ->orderBy('ps.sort_order')
            ->get();

        $newCustomersInPeriod = Customer::where('company_id', $companyId)
            ->where('created_at', '>=', $fromDate)
            ->count();

        $closedWonInPeriod = Customer::select('customers.id')
            ->join('pipeline_stages as ps', 'ps.id', '=', 'customers.current_stage_id')
            ->where('customers.company_id', $companyId)
            ->where('ps.type', 'won')
            ->where('customers.updated_at', '>=', $fromDate)
            ->count();

        $companies = [];
        if ($user->global_role === 'super_admin') {
            $companies = Company::orderBy('name')->get();
        }

        return view('admin.reports.customers', compact(
            'totalCustomers',
            'byType',
            'byStage',
            'days',
            'fromDate',
            'newCustomersInPeriod',
            'closedWonInPeriod',
            'companies',
            'companyId'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $user = Auth::user();

        if ($user->global_role === 'super_admin') {
            $companyId = $request->query('company_id');

            if (!$companyId) {
                $companyId = Company::where('status', 'active')->orderBy('name')->value('id');
            }
        } else {
            $companyId = $user->company_id;
        }

        $days = (int) $request->query('days', 30);
        if ($days <= 0 || $days > 365) {
            $days = 30;
        }

        $fromDate = now()->subDays($days);

        $byStage = Customer::selectRaw('ps.name, ps.type, count(customers.id) as total')
            ->join('pipeline_stages as ps', 'ps.id', '=', 'customers.current_stage_id')
            ->where('customers.company_id', $companyId)
            ->groupBy('ps.id', 'ps.name', 'ps.type')
            ->orderBy('ps.sort_order')
            ->get();

        $fileName = 'report_customers_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($byStage, $fromDate, $days) {
            $handle = fopen('php://output', 'w');

            // Info periode di baris pertama
            fputcsv($handle, ['Report Customers']);
            fputcsv($handle, ['Periode (hari)', $days]);
            fputcsv($handle, ['Sejak', $fromDate->timezone('Asia/Jakarta')->format('Y-m-d')]);
            fputcsv($handle, []); // baris kosong

            // Header
            fputcsv($handle, ['Stage', 'Type', 'Total Customers']);

            foreach ($byStage as $row) {
                fputcsv($handle, [
                    $row->name,
                    $row->type,
                    $row->total,
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }


}
