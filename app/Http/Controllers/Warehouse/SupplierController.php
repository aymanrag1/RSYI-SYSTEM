<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();
        if ($request->filled('s')) {
            $s = $request->s;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"));
        }
        $suppliers = $query->orderBy('name')->paginate(25);
        return view('warehouse.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('warehouse.suppliers.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:200'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'email'   => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string'],
            'notes'   => ['nullable', 'string'],
        ]);
        Supplier::create($request->only(['name', 'phone', 'email', 'address', 'notes']) + ['created_at' => now()]);
        return redirect()->route('warehouse.suppliers.index')->with('success', 'تم إضافة المورد بنجاح.');
    }

    public function edit(Supplier $supplier)
    {
        return view('warehouse.suppliers.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:200'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'email'   => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string'],
            'notes'   => ['nullable', 'string'],
        ]);
        $supplier->update($request->only(['name', 'phone', 'email', 'address', 'notes']) + ['updated_at' => now()]);
        return redirect()->route('warehouse.suppliers.index')->with('success', 'تم تحديث بيانات المورد.');
    }
}
