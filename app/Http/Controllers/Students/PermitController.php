<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\Students\ExitPermit;
use App\Models\Students\OvernightPermit;
use App\Models\Students\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PermitController extends Controller
{
    // ─── Exit Permits ────────────────────────────────────────────────────────────

    public function exitIndex(Request $request)
    {
        $query = ExitPermit::with('student');
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('student_id')) $query->where('student_id', $request->student_id);

        $permits  = $query->orderByDesc('exit_date')->paginate(25);
        $students = Student::active()->orderBy('first_name')->get();
        $type     = 'exit';

        return view('students.permits.index', compact('permits', 'students', 'type'));
    }

    public function exitCreate()
    {
        $students = Student::active()->orderBy('first_name')->get();
        $type     = 'exit';
        return view('students.permits.form', compact('students', 'type'));
    }

    public function exitStore(Request $request)
    {
        $request->validate([
            'student_id'  => ['required', 'integer'],
            'exit_date'   => ['required', 'date'],
            'return_time' => ['required', 'string'],
            'reason'      => ['nullable', 'string'],
        ]);
        ExitPermit::create($request->only(['student_id', 'exit_date', 'return_time', 'reason'])
            + ['status' => 'pending', 'created_by' => Session::get('user_id'), 'created_at' => now()]);
        return redirect()->route('students.exit-permits.index')->with('success', 'تم تقديم تصريح الخروج.');
    }

    public function exitApprove(ExitPermit $exitPermit)
    {
        $exitPermit->update(['status' => 'approved', 'approved_by' => Session::get('user_id'), 'updated_at' => now()]);
        return back()->with('success', 'تم الموافقة على تصريح الخروج.');
    }

    public function exitReject(ExitPermit $exitPermit)
    {
        $exitPermit->update(['status' => 'rejected', 'updated_at' => now()]);
        return back()->with('success', 'تم رفض تصريح الخروج.');
    }

    // ─── Overnight Permits ────────────────────────────────────────────────────────

    public function overnightIndex(Request $request)
    {
        $query = OvernightPermit::with('student');
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('student_id')) $query->where('student_id', $request->student_id);

        $permits  = $query->orderByDesc('from_date')->paginate(25);
        $students = Student::active()->orderBy('first_name')->get();
        $type     = 'overnight';

        return view('students.permits.index', compact('permits', 'students', 'type'));
    }

    public function overnightCreate()
    {
        $students = Student::active()->orderBy('first_name')->get();
        $type     = 'overnight';
        return view('students.permits.form', compact('students', 'type'));
    }

    public function overnightStore(Request $request)
    {
        $request->validate([
            'student_id'  => ['required', 'integer'],
            'from_date'   => ['required', 'date'],
            'to_date'     => ['required', 'date', 'after_or_equal:from_date'],
            'destination' => ['required', 'string', 'max:200'],
            'reason'      => ['nullable', 'string'],
        ]);
        OvernightPermit::create($request->only(['student_id', 'from_date', 'to_date', 'destination', 'reason'])
            + ['status' => 'pending', 'created_by' => Session::get('user_id'), 'created_at' => now()]);
        return redirect()->route('students.overnight-permits.index')->with('success', 'تم تقديم تصريح المبيت.');
    }

    public function overnightApprove(OvernightPermit $overnightPermit)
    {
        $overnightPermit->update(['status' => 'approved', 'approved_by' => Session::get('user_id'), 'updated_at' => now()]);
        return back()->with('success', 'تم الموافقة على تصريح المبيت.');
    }

    public function overnightReject(OvernightPermit $overnightPermit)
    {
        $overnightPermit->update(['status' => 'rejected', 'updated_at' => now()]);
        return back()->with('success', 'تم رفض تصريح المبيت.');
    }
}
