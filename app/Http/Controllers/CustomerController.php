<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $data = Customer::orderBy('id', 'desc')->get();
        return view('admin.customer.index', compact('data'));
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string'
        ]);

        Customer::create($request->all());

        return response()->json(['message' => 'Customer berhasil disimpan!']);
    }

    public function show(string $id)
    {
        // Ambil data customer beserta riwayat penjualannya (jika sudah ada)
        $customer = Customer::with('penjualan')->findOrFail($id);
        return view('admin.customer.show', compact('customer'));
    }

    public function edit(string $id)
    {
        $customer = Customer::find($id);
        return response()->json($customer);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string'
        ]);

        $customer = Customer::find($id);
        $customer->update($request->all());

        return response()->json(['message' => 'Customer berhasil diubah!']);
    }

    public function destroy(string $id)
    {
        Customer::find($id)->delete();
        return response()->json(['message' => 'Customer berhasil dihapus!']);
    }
}