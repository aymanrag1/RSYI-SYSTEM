<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\WithdrawalOrder;
use App\Models\Warehouse\Product;
use App\Models\HR\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = WithdrawalOrder::with(['product', 'department']);

        if ($request->filled('dept_id')) {
            $query->where('dept_id', $request->dept_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('withdrawal_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('withdrawal_date', '<=', $request->date_to);
        }

        $orders      = $query->orderByDesc('withdrawal_date')->paginate(25);
        $departments = Department::orderBy('name')->get();

        return view('warehouse.withdrawal-orders.index', compact('orders', 'departments'));
    }

    public function create()
    {
        $products    = Product::where('status', 'active')->where('current_qty', '>', 0)->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        return view('warehouse.withdrawal-orders.form', compact('products', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'      => ['required', 'integer'],
            'dept_id'         => ['required', 'integer'],
            'quantity'        => ['required', 'numeric', 'min:0.01'],
            'withdrawal_date' => ['required', 'date'],
            'purpose'         => ['nullable', 'string', 'max:255'],
            'notes'           => ['nullable', 'string'],
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->current_qty < $request->quantity) {
            return back()->withErrors(['quantity' => 'الكمية المطلوبة (' . $request->quantity . ') أكبر من المتاح (' . $product->current_qty . ')'])->withInput();
        }

        DB::transaction(function () use ($request, $product) {
            WithdrawalOrder::create($request->only([
                'product_id', 'dept_id', 'quantity', 'withdrawal_date', 'purpose', 'notes'
            ]) + ['created_at' => now()]);

            $product->decrement('current_qty', $request->quantity);
        });

        return redirect()->route('warehouse.withdrawal-orders.index')->with('success', 'تم تسجيل أمر الصرف وتحديث المخزون.');
    }
}
