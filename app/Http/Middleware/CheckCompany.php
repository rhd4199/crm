<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompany
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin bebas, tidak wajib punya company
        if ($user->global_role === 'super_admin') {
            return $next($request);
        }

        if (!$user->company_id) {
            abort(403, 'Anda tidak terhubung ke perusahaan manapun.');
        }

        $company = Company::find($user->company_id);

        if (!$company || $company->status !== 'active') {
            abort(403, 'Perusahaan Anda tidak aktif atau tidak ditemukan.');
        }

        // Optional: set currentCompany ke container
        app()->instance('currentCompany', $company);

        return $next($request);
    }
}
