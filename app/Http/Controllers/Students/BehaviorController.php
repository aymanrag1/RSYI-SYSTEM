<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\Students\BehaviorViolation;
use App\Models\Students\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BehaviorController extends Controller
{
    public function index(Request $request)
    {
        $query = BehaviorViolation::with('student');

        if ($request->filled('student_id')) $query->where('student_id', $request->student_id);
        if ($request->filled('violation_type')) $query->where('violation_type', $request->violation_type);

        $violations = $query->orderByDesc('violation_date')->paginate(25);
        $students   = Student::active()->orderBy('first_name')->get();

        return view('students.behavior.index', compact('violations', 'students'));
    }

    public function create()
    {
        $students = Student::active()->orderBy('first_name')->get();
        return view('students.behavior.form', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'     => ['required', 'integer'],
            'violation_date' => ['required', 'date'],
            'violation_type' => ['required', 'string', 'max:100'],
            'description'    => ['required', 'string'],
            'points'         => ['nullable', 'integer', 'min:0'],
            'action_taken'   => ['nullable', 'string'],
        ]);

        BehaviorViolation::create($request->only([
            'student_id', 'violation_date', 'violation_type', 'description', 'points', 'action_taken'
        ]) + ['created_by' => Session::get('user_id'), 'created_at' => now()]);

        return redirect()->route('students.behavior.index')->with('success', 'تم تسجيل المخالفة السلوكية.');
    }
}
