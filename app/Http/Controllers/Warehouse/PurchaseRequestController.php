<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\PurchaseRequest;
use App\Models\Warehouse\Product;
use App\Models\HR\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequest::with(['product', 'department']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('dept_id')) {
            $query->where('dept_id', $request->dept_id);
        }

        $requests    = $query->orderByDesc('created_at')->paginate(25);
        $departments = Department::orderBy('name')->get();

        $stats = [
            'pending'  => PurchaseRequest::where('status', 'pending')->count(),
            'approved' => PurchaseRequest::where('status', 'approved')->count(),
            'rejected' => PurchaseRequest::where('status', 'rejected')->count(),
            'total'    => PurchaseRequest::count(),
        ];

        return view('warehouse.purchase-requests.index', compact('requests', 'departments', 'stats'));
    }

    public function create()
    {
        $products    = Product::where('status', 'active')->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        return view('warehouse.purchase-requests.form', compact('products', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'integer'],
            'dept_id'    => ['required', 'integer'],
            'quantity'   => ['required', 'numeric', 'min:1'],
            'reason'     => ['nullable', 'string'],
        ]);

        PurchaseRequest::create($request->only(['product_id', 'dept_id', 'quantity', 'reason'])
            + ['status' => 'pending', 'created_by' => Session::get('user_id'), 'created_at' => now()]);

        return redirect()->route('warehouse.purchase-requests.index')->with('success', 'تم تقديم طلب الشراء بنجاح.');
    }

    public function approve(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->update([
            'status'      => 'approved',
            'approved_by' => Session::get('user_id'),
            'approved_at' => now(),
            'updated_at'  => now(),
        ]);
        return back()->with('success', 'تم الموافقة على طلب الشراء.');
    }

    public function reject(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate(['rejection_reason' => ['required', 'string']]);
        $purchaseRequest->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'updated_at'       => now(),
        ]);
        return back()->with('success', 'تم رفض طلب الشراء.');
    }
}
