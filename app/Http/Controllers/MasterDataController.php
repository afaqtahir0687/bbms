<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billboard;
use App\Models\BillboardType;
use App\Models\Area;
use App\Models\RatingScale;

class MasterDataController extends Controller
{
    public function index()
    {
        // For simplicity in this demo, Master Data index is the Billboard Inventory list
        $billboards = Billboard::with(['area.city.province', 'type', 'marketRating'])->get();
        return view('master-data.index', compact('billboards'));
    }

    public function create()
    {
        $types = BillboardType::all();
        $areas = Area::with('city')->get();
        $ratings = RatingScale::all();
        return view('master-data.create', compact('types', 'areas', 'ratings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'billboard_code' => 'required|unique:billboard,billboard_code|max:50',
            'display_name' => 'required|max:255',
            'area_id' => 'required|exists:area,area_id',
            'billboard_type_id' => 'required|exists:billboard_type,billboard_type_id',
            'market_rating_id' => 'nullable|exists:rating_scale,rating_id',
            'status' => 'required',
        ]);

        Billboard::create([
            'billboard_code' => $request->billboard_code,
            'display_name' => $request->display_name,
            'area_id' => $request->area_id,
            'billboard_type_id' => $request->billboard_type_id,
            'market_rating_id' => $request->market_rating_id,
            'status' => $request->status,
            'active_flag' => true
        ]);

        return redirect()->route('master-data.index')->with('success', 'Billboard inventory added successfully.');
    }
}
