<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\AddOrder;
use App\Models\Warehouse\Product;
use App\Models\Warehouse\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = AddOrder::with(['product', 'supplier']);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        $orders    = $query->orderByDesc('order_date')->paginate(25);
        $suppliers = Supplier::orderBy('name')->get();

        return view('warehouse.add-orders.index', compact('orders', 'suppliers'));
    }

    public function create()
    {
        $products  = Product::where('status', 'active')->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        return view('warehouse.add-orders.form', compact('products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'  => ['required', 'integer'],
            'supplier_id' => ['nullable', 'integer'],
            'quantity'    => ['required', 'numeric', 'min:0.01'],
            'unit_price'  => ['nullable', 'numeric', 'min:0'],
            'order_date'  => ['required', 'date'],
            'notes'       => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($request) {
            AddOrder::create($request->only([
                'product_id', 'supplier_id', 'quantity', 'unit_price', 'order_date', 'notes'
            ]) + ['created_at' => now()]);

            // Update product stock
            Product::where('id', $request->product_id)
                ->increment('current_qty', $request->quantity);
        });

        return redirect()->route('warehouse.add-orders.index')->with('success', 'تم تسجيل أمر الإضافة وتحديث المخزون.');
    }
}
