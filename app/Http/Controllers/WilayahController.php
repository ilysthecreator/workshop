<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function index()
    {
        $provinces = Province::orderBy('name')->get();
        return view('wilayah.index', compact('provinces'));
    }

    public function getRegencies($province_id)
    {
        $regencies = Regency::where('province_id', $province_id)
            ->orderBy('name')
            ->get();
        return response()->json($regencies);
    }

    public function getDistricts($regency_id)
    {
        $districts = District::where('regency_id', $regency_id)
            ->orderBy('name')
            ->get();
        return response()->json($districts);
    }

    public function getVillages($district_id)
    {
        $villages = Village::where('district_id', $district_id)
            ->orderBy('name')
            ->get();
        return response()->json($villages);
    }
}
