@extends('layouts.app')

@section('title', 'Allocation Details')

@section('content')
<div style="margin-top: 2rem;">
    <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
        
        <!-- Left Column: Details -->
        <div style="flex: 1; min-width: 300px;">
            <h1 class="mb-6">Allocation Details</h1>
            <div class="glass-card" style="padding: 2rem; margin-bottom: 2rem;">
                <div class="mb-4">
                    <div style="color: var(--text-muted); font-size: 0.9rem;">Campaign</div>
                    <div style="font-size: 1.2rem; font-weight: 600;">{{ $allocation->campaign->campaign_code }}</div>
                </div>
                <div class="mb-4">
                    <div style="color: var(--text-muted); font-size: 0.9rem;">Billboard</div>
                    <div style="font-size: 1.2rem; font-weight: 600;">{{ $allocation->billboard->display_name }}</div>
                    <div>{{ $allocation->billboard->billboard_code }} | {{ $allocation->billboard->type->type_name }}</div>
                </div>
                <div class="mb-4">
                    <div style="color: var(--text-muted); font-size: 0.9rem;">Duration</div>
                    <div>{{ $allocation->allocated_from->format('M d, Y') }} — {{ $allocation->allocated_to->format('M d, Y') }}</div>
                </div>
                <div class="mb-4">
                    <div style="color: var(--text-muted); font-size: 0.9rem;">Status</div>
                    <div>{{ $allocation->allocation_status }}</div>
                </div>
            </div>

            <!-- Upload Section -->
            <div class="glass-card" style="padding: 2rem;">
                <h3>Upload Proof</h3>
                <form action="{{ route('proof.upload', $allocation->allocation_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <input type="file" name="photo" class="form-input" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Photo</button>
                </form>
            </div>
        </div>

        <!-- Right Column: Pictures -->
        <div style="flex: 2; min-width: 300px;">
            <h2 class="mb-6">Proof of Execution</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
                @forelse($allocation->pictures as $picture)
                <div class="glass-card" style="overflow: hidden;">
                    <a href="{{ Storage::url($picture->file_path) }}" target="_blank">
                        <!-- If storage not linked correctly in local dev, this might break, but standard Laravel way -->
                        <img src="{{ Storage::url($picture->file_path) }}" style="width: 100%; height: 200px; object-fit: cover;">
                    </a>
                    <div style="padding: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $picture->uploaded_at->format('M d H:i') }}</span>
                            <span class="badge" style="background: rgba(255,255,255,0.1);">{{ $picture->picture_status }}</span>
                        </div>
                        <div style="font-size: 0.9rem;">By: {{ $picture->uploader->full_name ?? 'Unknown' }}</div>

                        @if($picture->picture_status != 'Approved' && $picture->picture_status != 'Rejected')
                        <div style="margin-top: 1rem; border-top: var(--border); padding-top: 1rem;">
                            <form action="{{ route('proof.verify', $picture->picture_id) }}" method="POST">
                                @csrf
                                <div style="display: flex; gap: 0.5rem;">
                                    <button name="result" value="Approved" class="btn btn-success" style="flex: 1; padding: 0.5rem;">Approve</button>
                                    <button name="result" value="Rejected" class="btn btn-danger" style="flex: 1; padding: 0.5rem;">Reject</button>
                                </div>
                            </form>
                        </div>
                        @else
                            <div style="margin-top: 0.5rem; color: {{ $picture->picture_status == 'Approved' ? '#10b981' : '#ef4444' }}; font-weight: 600;">
                                Verified: {{ $picture->picture_status }}
                            </div>
                        @endif
                    </div>
                </div>
                @empty
                <div style="grid-column: 1 / -1; padding: 2rem; text-align: center; color: var(--text-muted); background: var(--card-bg); border-radius: 1rem;">
                    No photos uploaded yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
