<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Picture;
use App\Models\Allocation;
use App\Models\Verification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProofController extends Controller
{
    public function store(Request $request, $allocationId)
    {
        $request->validate([
            'photo' => 'required|image|max:10240', // 10MB max
        ]);

        $allocation = Allocation::findOrFail($allocationId);

        try {
            $path = $request->file('photo')->store('proofs', 'public');

            Picture::create([
                'allocation_id' => $allocation->allocation_id,
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now(),
                'file_path' => $path,
                'picture_status' => 'Submitted',
            ]);

            return back()->with('success', 'Photo uploaded successfully.');
        } catch (\Exception $e) {
             return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    public function verify(Request $request, $pictureId)
    {
        $picture = Picture::findOrFail($pictureId);
        
        $request->validate([
            'result' => 'required|in:Approved,Rejected',
        ]);

        Verification::create([
            'picture_id' => $picture->picture_id,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'result' => $request->result,
        ]);

        // Update picture status too? SDD says "Picture lifecycle: Submitted -> Verified / Rejected"
        // But Picture has picture_status. Verification entity is separate.
        // I will update picture status as well for easier querying.
        $picture->update(['picture_status' => $request->result]);

        return back()->with('success', 'Verification recorded.');
    }
}
