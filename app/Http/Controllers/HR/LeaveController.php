<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Leave;
use App\Models\HR\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with('employee.department');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $leaves = $query->orderByDesc('created_at')->paginate(25);

        $stats = [
            'pending'  => Leave::where('status', 'pending')->count(),
            'approved' => Leave::where('status', 'approved')->count(),
            'rejected' => Leave::where('status', 'rejected')->count(),
            'total'    => Leave::count(),
        ];

        return view('hr.leaves.index', compact('leaves', 'stats'));
    }

    public function approve(Leave $leave)
    {
        $leave->update([
            'status'      => 'approved',
            'approved_by' => Session::get('user_id'),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'تم الموافقة على طلب الإجازة.');
    }

    public function reject(Request $request, Leave $leave)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $leave->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by'      => Session::get('user_id'),
            'approved_at'      => now(),
        ]);

        return back()->with('success', 'تم رفض طلب الإجازة.');
    }

    public function create()
    {
        $employees = Employee::active()->orderBy('first_name')->get();
        return view('hr.leaves.form', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer'],
            'leave_type'  => ['required', 'in:annual,sick,emergency,unpaid'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after_or_equal:start_date'],
            'days'        => ['required', 'integer', 'min:1'],
            'reason'      => ['nullable', 'string', 'max:500'],
        ]);

        Leave::create($validated + [
            'status'     => 'pending',
            'created_at' => now(),
        ]);

        return redirect()->route('hr.leaves.index')
            ->with('success', 'تم تقديم طلب الإجازة بنجاح.');
    }
}
