<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Ward;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Get all provinces.
     */
    public function getProvinces()
    {
        $provinces = Province::orderBy('name')->get(['id', 'name', 'type', 'code']);
        return response()->json($provinces);
    }

    /**
     * Get wards by province ID.
     */
    public function getWardsByProvince($provinceId)
    {
        $province = Province::findOrFail($provinceId);
        
        $wards = Ward::where('province_code', $province->code)
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'code']);
            
        return response()->json($wards);
    }

    /**
     * Search locations by query.
     */
    public function searchLocations(Request $request)
    {
        $query = $request->get('q', '');
        
        $provinces = Province::where('name', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'type']);
            
        return response()->json([
            'provinces' => $provinces,
        ]);
    }
}
