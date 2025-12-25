<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SoftBooking;
use App\Models\Campaign;
use App\Models\Billboard;
use Illuminate\Support\Facades\DB;

class SoftBookingController extends Controller
{
    public function index()
    {
        $bookings = SoftBooking::with(['campaign.customer', 'billboard'])->get();
        return view('soft-bookings.index', compact('bookings'));
    }

    public function create()
    {
        $campaigns = Campaign::where('status', 'Active')->get();
        $billboards = Billboard::where('active_flag', true)->get();
        return view('soft-bookings.create', compact('campaigns', 'billboards'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:campaign,campaign_id',
            'billboard_id' => 'required|exists:billboard,billboard_id',
            'hold_from' => 'required|date',
            'hold_to' => 'required|date|after_or_equal:hold_from',
            'expires_at' => 'nullable|date',
        ]);

        try {
            SoftBooking::create([
                'campaign_id' => $request->campaign_id,
                'billboard_id' => $request->billboard_id,
                'hold_from' => $request->hold_from,
                'hold_to' => $request->hold_to,
                'hold_status' => 'HOLD',
                'expires_at' => $request->expires_at,
            ]);

            return redirect()->route('soft-bookings.index')->with('success', 'Soft booking created successfully.');
        } catch (\Exception $e) {
            // Trigger will catch overlaps
            return back()->with('error', 'Error creating booking: ' . $e->getMessage())->withInput();
        }
    }
    
    public function convert($id)
    {
        // FUTURE: Helper to convert SoftBooking -> Allocation
        // For now, just a placeholder or manual action
        return back()->with('error', 'Auto-conversion not implemented yet. Please create Allocation manually.');
    }
}
