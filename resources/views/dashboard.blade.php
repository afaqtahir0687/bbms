@extends('layouts.app')

@section('title', 'Dashboard - Billboard Tracking')

@section('content')
<div style="margin-top: 2rem;">
    <h1 class="mb-6">Dashboard</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <!-- Stat Card 1 -->
        <div class="glass-card" style="padding: 1.5rem;">
            <div style="color: var(--text-muted); font-size: 0.9rem;">Total Billboards</div>
            <div style="font-size: 2.5rem; font-weight: 700;">{{ \App\Models\Billboard::count() }}</div>
        </div>

        <!-- Stat Card 2 -->
        <div class="glass-card" style="padding: 1.5rem;">
            <div style="color: var(--text-muted); font-size: 0.9rem;">Active Campaigns</div>
            <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary);">{{ \App\Models\Campaign::where('status', 'Active')->count() }}</div>
        </div>

        <!-- Stat Card 3 -->
        <div class="glass-card" style="padding: 1.5rem;">
            <div style="color: var(--text-muted); font-size: 0.9rem;">Pending Allocations</div>
            <div style="font-size: 2.5rem; font-weight: 700; color: #f59e0b;">{{ \App\Models\Allocation::where('allocation_status', 'Pending')->count() }}</div>
        </div>

         <!-- Stat Card 4 -->
         <div class="glass-card" style="padding: 1.5rem;">
            <div style="color: var(--text-muted); font-size: 0.9rem;">Photos Verified</div>
            <div style="font-size: 2.5rem; font-weight: 700; color: #10b981;">{{ \App\Models\Verification::where('result', 'Approved')->count() }}</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <h2 class="mb-4">Quick Actions</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="{{ route('soft-bookings.index') }}" class="glass-card btn" style="text-align: center; color: var(--text-main); display: block;">
            <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📅</div>
            Create Booking
        </a>
        <a href="{{ route('allocations.index') }}" class="glass-card btn" style="text-align: center; color: var(--text-main); display: block;">
            <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📌</div>
            Manage Allocations
        </a>
        <a href="{{ route('master-data.index') }}" class="glass-card btn" style="text-align: center; color: var(--text-main); display: block;">
            <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🏭</div>
            Add Inventory
        </a>
        <a href="{{ route('geography.index') }}" class="glass-card btn" style="text-align: center; color: var(--text-main); display: block;">
            <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🌍</div>
            Geography
        </a>
    </div>
</div>
@endsection
