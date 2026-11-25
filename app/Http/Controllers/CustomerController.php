<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $data = Customer::withCount('penjualan')->orderBy('id', 'desc')->get();
        return view('admin.customer.index', compact('data'));
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_customer' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'required|string'
        ]);

        Customer::create($request->all());

        return response()->json(['message' => 'Customer berhasil disimpan!']);
    }

    public function show(string $id)
    {
        $customer = Customer::with('penjualan')->findOrFail($id);
        return view('admin.customer.show', compact('customer'));
    }

    public function edit(string $id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    public function update(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'required|string'
        ]);

        $customer->update([
            'no_telepon' => $request->no_telepon,
            'alamat' => $request->alamat,
        ]);

        return response()->json(['message' => 'Data customer berhasil diperbarui!']);
    }

    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);

        if ($customer->penjualan()->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => "Pelanggan '{$customer->nama_customer}' tidak dapat dihapus karena sudah memiliki riwayat transaksi penjualan!"
            ], 422);
        }

        $customer->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Customer berhasil dihapus!'
        ]);
    }
}
