<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Province;
use App\Models\City;
use App\Models\Area;
use Illuminate\Support\Facades\Storage;

class GeographyController extends Controller
{
    // Helper to upload image
    private function uploadImage(Request $request, $existingPath = null) {
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($existingPath && Storage::disk('public')->exists($existingPath)) {
                Storage::disk('public')->delete($existingPath);
            }
            return $request->file('image')->store('geography', 'public');
        }
        return $existingPath;
    }

    // === Country ===
    public function index()
    {
        $countries = Country::all();
        return view('geography.index', compact('countries'));
    }

    public function storeCountry(Request $request)
    {
        $request->validate([
            'country_code' => 'required|unique:country,country_code|max:50',
            'country_name' => 'required|max:255',
            'image' => 'nullable|image|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180'
        ]);
        
        $data = $request->all();
        $data['image_path'] = $this->uploadImage($request);

        Country::create($data);
        return back()->with('success', 'Country added.');
    }

    public function showCountry($id)
    {
        $country = Country::with('provinces')->findOrFail($id);
        return view('geography.show_country', compact('country'));
    }

    public function editCountry($id)
    {
        $country = Country::findOrFail($id);
        return view('geography.edit_country', compact('country'));
    }

    public function updateCountry(Request $request, $id)
    {
        $country = Country::findOrFail($id);
        $request->validate([
            'country_code' => 'required|max:50|unique:country,country_code,' . $id . ',country_id',
            'country_name' => 'required|max:255',
            'image' => 'nullable|image|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180'
        ]);

        $data = $request->except(['image', '_token', '_method']);
        $data['image_path'] = $this->uploadImage($request, $country->image_path);
        
        // Ensure boundary_data is handled safely if not present
        if ($request->has('boundary_data')) {
            $data['boundary_data'] = $request->boundary_data;
        }

        $country->update($data);
        return redirect()->route('geography.index')->with('success', 'Country updated.');
    }

    public function destroyCountry($id)
    {
        $country = Country::findOrFail($id);
        if ($country->provinces()->count() > 0) {
            return back()->with('error', 'Cannot delete country with existing provinces.');
        }
        if ($country->image_path) Storage::disk('public')->delete($country->image_path);
        $country->delete();
        return redirect()->route('geography.index')->with('success', 'Country deleted.');
    }

    // === Province ===
    public function storeProvince(Request $request, $countryId)
    {
        $request->validate([
            'province_code' => 'required|unique:province,province_code|max:50',
            'province_name' => 'required|max:255',
            'image' => 'nullable|image|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180'
        ]);

        Province::create([
            'country_id' => $countryId,
            'province_code' => $request->province_code,
            'province_name' => $request->province_name,
            'image_path' => $this->uploadImage($request),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);
        return back()->with('success', 'Province added.');
    }

    public function showProvince($id)
    {
        $province = Province::with(['country', 'cities'])->findOrFail($id);
        return view('geography.show_province', compact('province'));
    }

    public function editProvince($id)
    {
        $province = Province::findOrFail($id);
        return view('geography.edit_province', compact('province'));
    }

    public function updateProvince(Request $request, $id)
    {
        $province = Province::findOrFail($id);
        $request->validate([
            'province_code' => 'required|max:50|unique:province,province_code,' . $id . ',province_id',
            'province_name' => 'required|max:255',
            'image' => 'nullable|image|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180'
        ]);

        $data = [
            'province_code' => $request->province_code,
            'province_name' => $request->province_name,
            'image_path' => $this->uploadImage($request, $province->image_path),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ];

        if ($request->has('boundary_data')) {
            $data['boundary_data'] = $request->boundary_data;
        }

        $province->update($data);
        return redirect()->route('geography.showCountry', $province->country_id)->with('success', 'Province updated.');
    }

    public function destroyProvince($id)
    {
        $province = Province::findOrFail($id);
        if ($province->cities()->count() > 0) {
            return back()->with('error', 'Cannot delete province with existing cities.');
        }
        if ($province->image_path) Storage::disk('public')->delete($province->image_path);
        $province->delete();
        return back()->with('success', 'Province deleted.');
    }

    // === City ===
    public function storeCity(Request $request, $provinceId)
    {
        $request->validate([
            'city_code' => 'required|unique:city,city_code|max:50',
            'city_name' => 'required|max:255',
            'image' => 'nullable|image|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180'
        ]);

        City::create([
            'province_id' => $provinceId,
            'city_code' => $request->city_code,
            'city_name' => $request->city_name,
            'image_path' => $this->uploadImage($request),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);
        return back()->with('success', 'City added.');
    }

    public function showCity($id)
    {
        $city = City::with(['province', 'areas'])->findOrFail($id);
        return view('geography.show_city', compact('city'));
    }

    public function editCity($id)
    {
        $city = City::findOrFail($id);
        return view('geography.edit_city', compact('city'));
    }

    public function updateCity(Request $request, $id)
    {
        $city = City::findOrFail($id);
        $request->validate([
            'city_code' => 'required|max:50|unique:city,city_code,' . $id . ',city_id',
            'city_name' => 'required|max:255',
            'image' => 'nullable|image|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180'
        ]);

        $data = [
            'city_code' => $request->city_code,
            'city_name' => $request->city_name,
            'image_path' => $this->uploadImage($request, $city->image_path),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ];

        if ($request->has('boundary_data')) {
            $data['boundary_data'] = $request->boundary_data;
        }

        $city->update($data);
        return redirect()->route('geography.showProvince', $city->province_id)->with('success', 'City updated.');
    }

    public function destroyCity($id)
    {
        $city = City::findOrFail($id);
        if ($city->areas()->count() > 0) {
            return back()->with('error', 'Cannot delete city with existing areas.');
        }
        if ($city->image_path) Storage::disk('public')->delete($city->image_path);
        $city->delete();
        return back()->with('success', 'City deleted.');
    }

    // === Area ===
    public function storeArea(Request $request, $cityId)
    {
        $request->validate([
            'area_code' => 'required|unique:area,area_code|max:50',
            'area_name' => 'required|max:255',
            'image' => 'nullable|image|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180'
        ]);

        Area::create([
            'city_id' => $cityId,
            'area_code' => $request->area_code,
            'area_name' => $request->area_name,
            'image_path' => $this->uploadImage($request),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);
        return back()->with('success', 'Area added.');
    }
    
    public function showArea($id)
    {
        $area = Area::with(['city.province.country', 'billboards.type', 'billboards.marketRating'])->findOrFail($id);
        return view('geography.show_area', compact('area'));
    }

    public function editArea($id)
    {
        $area = Area::findOrFail($id);
        return view('geography.edit_area', compact('area'));
    }

    public function updateArea(Request $request, $id)
    {
        $area = Area::findOrFail($id);
        $request->validate([
            'area_code' => 'required|max:50|unique:area,area_code,' . $id . ',area_id',
            'area_name' => 'required|max:255',
            'image' => 'nullable|image|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180'
        ]);

        $data = [
            'area_code' => $request->area_code,
            'area_name' => $request->area_name,
            'image_path' => $this->uploadImage($request, $area->image_path),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ];

        if ($request->has('boundary_data')) {
            $data['boundary_data'] = $request->boundary_data;
        }

        $area->update($data);
        return redirect()->route('geography.showCity', $area->city_id)->with('success', 'Area updated.');
    }

    public function destroyArea($id)
    {
        $area = Area::findOrFail($id);
        if ($area->billboards()->count() > 0) {
            return back()->with('error', 'Cannot delete area with existing billboards.');
        }
        if ($area->image_path) Storage::disk('public')->delete($area->image_path);
        $area->delete();
        return back()->with('success', 'Area deleted.');
    }
}
