<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Allocation;
use App\Models\Campaign;
use App\Models\Billboard;
use App\Models\AllocationUploaderAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AllocationController extends Controller
{
    public function index()
    {
        $allocations = Allocation::with(['campaign.customer', 'billboard', 'assignments.uploader'])->get();
        return view('allocations.index', compact('allocations'));
    }

    public function create()
    {
        $campaigns = Campaign::where('status', 'Active')->get();
        $billboards = Billboard::where('active_flag', true)->get(); // Should filter by availability ideally
        return view('allocations.create', compact('campaigns', 'billboards'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:campaign,campaign_id',
            'billboard_id' => 'required|exists:billboard,billboard_id',
            'allocated_from' => 'required|date',
            'allocated_to' => 'required|date|after_or_equal:allocated_from',
            'assign_uploader_id' => 'nullable|exists:app_user,user_id'
        ]);

        try {
            DB::beginTransaction();

            $allocation = Allocation::create([
                'campaign_id' => $request->campaign_id,
                'billboard_id' => $request->billboard_id,
                'allocated_from' => $request->allocated_from,
                'allocated_to' => $request->allocated_to,
                'allocation_status' => 'Planned', // Default status
            ]);

            // Assign current user or selected user as uploader? 
            // For now, if "assign_uploader_id" is present, assign them. Default to current user if not?
            // SDD says "Uploader Assignment Module... to define who is responsible".
            // I'll add a field to the form to optionally assign an uploader immediately.
            
            if ($request->filled('assign_uploader_id')) {
                AllocationUploaderAssignment::create([
                    'allocation_id' => $allocation->allocation_id,
                    'uploader_user_id' => $request->assign_uploader_id,
                    'active_flag' => true,
                    'assigned_on' => now(),
                ]);
            } else {
                 // Auto-assign self for demo purposes if not specified? 
                 // Or just leave unassigned. SDD: "Only assigned uploaders can upload".
                 // So we MUST assign someone. I'll make the current user assigned by default in the controller if nothing selected, or require it.
                 // Let's force assignment to Auth user for simplicity in this MVP.
                 AllocationUploaderAssignment::create([
                    'allocation_id' => $allocation->allocation_id,
                    'uploader_user_id' => Auth::id(),
                    'active_flag' => true,
                    'assigned_on' => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('allocations.index')->with('success', 'Allocation created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Catch trigger errors
            return back()->with('error', 'Error creating allocation: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $allocation = Allocation::with(['campaign', 'billboard.type', 'billboard.area', 'pictures.uploader', 'pictures.verification'])->findOrFail($id);
        return view('allocations.show', compact('allocation'));
    }
}
