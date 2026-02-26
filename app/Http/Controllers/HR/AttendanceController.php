<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Attendance;
use App\Models\HR\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('employee');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('attendance_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('attendance_date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records   = $query->orderByDesc('attendance_date')->paginate(30);
        $employees = Employee::active()->orderBy('first_name')->get();

        $stats = [
            'today_present' => Attendance::whereDate('attendance_date', today())->where('status', 'present')->count(),
            'today_absent'  => Attendance::whereDate('attendance_date', today())->where('status', 'absent')->count(),
            'today_late'    => Attendance::whereDate('attendance_date', today())->where('status', 'late')->count(),
            'this_month'    => Attendance::whereMonth('attendance_date', now()->month)->count(),
        ];

        return view('hr.attendance.index', compact('records', 'employees', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id'     => ['required', 'integer'],
            'attendance_date' => ['required', 'date'],
            'status'          => ['required', 'in:present,absent,late,excused'],
            'check_in'        => ['nullable', 'date_format:H:i'],
            'check_out'       => ['nullable', 'date_format:H:i'],
            'notes'           => ['nullable', 'string', 'max:255'],
        ]);

        Attendance::updateOrCreate(
            ['employee_id' => $request->employee_id, 'attendance_date' => $request->attendance_date],
            $request->only(['status', 'check_in', 'check_out', 'notes']) + ['created_at' => now()]
        );

        return back()->with('success', 'تم تسجيل الحضور بنجاح.');
    }
}
