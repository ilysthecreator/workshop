<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with(['province', 'regency', 'district', 'village'])->get();
        return view('customer.index', compact('customers'));
    }

    public function create1()
    {
        $provinces = Province::orderBy('name')->get();
        return view('customer.create1', compact('provinces'));
    }

    public function store1(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'provinsi_id' => 'required',
            'kota_id' => 'required',
            'kecamatan_id' => 'required',
            'kelurahan_id' => 'required',
            'webcam_image' => 'required|string',
        ]);

        $image_parts = explode(";base64,", $request->webcam_image);
        $image_base64 = base64_decode($image_parts[1]);

        Customer::create([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'provinsi_id' => $request->provinsi_id,
            'kota_id' => $request->kota_id,
            'kecamatan_id' => $request->kecamatan_id,
            'kelurahan_id' => $request->kelurahan_id,
            'foto_blob' => $image_base64,
        ]);

        return redirect()->route('customer.index')->with('success', 'Customer berhasil ditambahkan (via BLOB).');
    }

    public function create2()
    {
        $provinces = Province::orderBy('name')->get();
        return view('customer.create2', compact('provinces'));
    }

    public function store2(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'provinsi_id' => 'required',
            'kota_id' => 'required',
            'kecamatan_id' => 'required',
            'kelurahan_id' => 'required',
            'webcam_image' => 'required|string',
        ]);

        $image_parts = explode(";base64,", $request->webcam_image);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        
        $fileName = 'customer_' . uniqid() . '.' . $image_type;
        $filePath = 'customers/' . $fileName;

        Storage::disk('public')->put($filePath, $image_base64);

        Customer::create([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'provinsi_id' => $request->provinsi_id,
            'kota_id' => $request->kota_id,
            'kecamatan_id' => $request->kecamatan_id,
            'kelurahan_id' => $request->kelurahan_id,
            'foto_path' => $filePath,
        ]);

        return redirect()->route('customer.index')->with('success', 'Customer berhasil ditambahkan (via File).');
    }
}
