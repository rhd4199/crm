<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Interaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $auth = Auth::user();

        if ($auth->global_role === 'super_admin') {
            $companyId = $request->query('company_id');
    
            if (!$companyId) {
                $companyId = Company::where('status', 'active')->orderBy('name')->value('id');
            }
        } else {
            $companyId = $auth->company_id;
        }


        $days = (int) $request->query('days', 30);
        if ($days <= 0 || $days > 365) {
            $days = 30;
        }

        $fromDate = now()->subDays($days);

        // Ambil semua user di company dengan role marketing / CS
        $employees = User::where('company_id', $companyId)
            ->whereIn('company_role', ['admin', 'marketing', 'customer_service'])
            ->orderByRaw("field(company_role, 'admin','marketing','customer_service')")
            ->orderBy('name')
            ->get();

        $stats = [];

        foreach ($employees as $emp) {
            // berapa customer yang di-handle sebagai marketing
            $handledAsMarketing = Customer::where('company_id', $companyId)
                ->where('assigned_marketing_id', $emp->id)
                ->count();

            // berapa customer yang di-handle sebagai CS
            $handledAsCS = Customer::where('company_id', $companyId)
                ->where('assigned_cs_id', $emp->id)
                ->count();

            // berapa interaksi yang dilakukan dalam periode
            $interactionsCount = Interaction::where('company_id', $companyId)
                ->where('user_id', $emp->id)
                ->where('created_at', '>=', $fromDate)
                ->count();

            // berapa closed-won yang dia pegang (sebagai marketing) dalam periode
            $closedWon = Customer::select('customers.id')
                ->join('pipeline_stages as ps', 'ps.id', '=', 'customers.current_stage_id')
                ->where('customers.company_id', $companyId)
                ->where('customers.assigned_marketing_id', $emp->id)
                ->where('ps.type', 'won')
                ->where('customers.updated_at', '>=', $fromDate)
                ->count();

            $stats[] = [
                'employee'             => $emp,
                'handled_marketing'    => $handledAsMarketing,
                'handled_cs'           => $handledAsCS,
                'interactions'         => $interactionsCount,
                'closed_won'           => $closedWon,
            ];
        }

        $companies = [];
        if ($auth->global_role === 'super_admin') {
            $companies = Company::orderBy('name')->get();
        }

        return view('admin.reports.employees', compact('stats', 'days', 'fromDate', 'companies', 'companyId'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $auth = Auth::user();

        if ($auth->global_role === 'super_admin') {
            $companyId = $request->query('company_id');

            if (!$companyId) {
                $companyId = Company::where('status', 'active')->orderBy('name')->value('id');
            }
        } else {
            $companyId = $auth->company_id;
        }

        $days = (int) $request->query('days', 30);
        if ($days <= 0 || $days > 365) {
            $days = 30;
        }

        $fromDate = now()->subDays($days);

        $employees = User::where('company_id', $companyId)
            ->whereIn('company_role', ['admin', 'marketing', 'customer_service'])
            ->orderByRaw("field(company_role, 'admin','marketing','customer_service')")
            ->orderBy('name')
            ->get();

        $rows = [];

        foreach ($employees as $emp) {
            $handledAsMarketing = Customer::where('company_id', $companyId)
                ->where('assigned_marketing_id', $emp->id)
                ->count();

            $handledAsCS = Customer::where('company_id', $companyId)
                ->where('assigned_cs_id', $emp->id)
                ->count();

            $interactionsCount = Interaction::where('company_id', $companyId)
                ->where('user_id', $emp->id)
                ->where('created_at', '>=', $fromDate)
                ->count();

            $closedWon = Customer::select('customers.id')
                ->join('pipeline_stages as ps', 'ps.id', '=', 'customers.current_stage_id')
                ->where('customers.company_id', $companyId)
                ->where('customers.assigned_marketing_id', $emp->id)
                ->where('ps.type', 'won')
                ->where('customers.updated_at', '>=', $fromDate)
                ->count();

            $rows[] = [
                'name'               => $emp->name,
                'email'              => $emp->email,
                'role'               => $emp->company_role,
                'handled_marketing'  => $handledAsMarketing,
                'handled_cs'         => $handledAsCS,
                'interactions'       => $interactionsCount,
                'closed_won'         => $closedWon,
            ];
        }

        $fileName = 'report_employees_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows, $fromDate, $days) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Report Karyawan']);
            fputcsv($handle, ['Periode (hari)', $days]);
            fputcsv($handle, ['Sejak', $fromDate->timezone('Asia/Jakarta')->format('Y-m-d')]);
            fputcsv($handle, []);

            fputcsv($handle, [
                'Nama',
                'Email',
                'Role',
                'Customers (Marketing)',
                'Customers (CS)',
                'Interaksi (periode)',
                'Closed Won (periode)',
            ]);

            foreach ($rows as $r) {
                fputcsv($handle, [
                    $r['name'],
                    $r['email'],
                    $r['role'],
                    $r['handled_marketing'],
                    $r['handled_cs'],
                    $r['interactions'],
                    $r['closed_won'],
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

}
