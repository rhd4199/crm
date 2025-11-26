<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Interaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InteractionController extends Controller
{
    public function store(Request $request, Customer $customer)
    {
        $user = Auth::user();
    
        // Batasi akses per company
        if ($user->global_role !== 'super_admin' && $customer->company_id !== $user->company_id) {
            abort(403, 'Anda tidak berhak mengakses customer ini.');
        }
    
        $data = $request->validate([
            'channel'       => ['required', 'in:whatsapp,phone,email,meeting,other'],
            'direction'     => ['required', 'in:outbound,inbound'],
            'summary'       => ['required', 'string'],
            'follow_up_at'  => ['nullable', 'date'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
        ]);
    
        $data['customer_id'] = $customer->id;
        $data['company_id']  = $customer->company_id;
        $data['user_id']     = $user->id;
    
        $interaction = Interaction::create($data);
    
        // update last_contact_at di customer
        $customer->last_contact_at = now();
        $customer->save();
    
        // activity log
        ActivityLog::create([
            'company_id'   => $customer->company_id,
            'user_id'      => $user->id,
            'action'       => 'interaction_created',
            'loggable_type'=> Interaction::class,
            'loggable_id'  => $interaction->id,
            'data'         => [
                'customer_id' => $customer->id,
                'channel'     => $interaction->channel,
                'direction'   => $interaction->direction,
            ],
        ]);
    
        // Kalau form kirim redirect=back, balik ke halaman sebelumnya (misal: list)
        if ($request->input('redirect') === 'back') {
            return back()->with('success', 'Interaksi berhasil dicatat.');
        }
    
        // default: ke halaman detail customer
        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Interaksi berhasil dicatat.');
    }
    
}
