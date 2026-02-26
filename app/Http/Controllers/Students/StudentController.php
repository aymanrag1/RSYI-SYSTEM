<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\Students\Student;
use App\Models\Students\Cohort;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('cohort');

        if ($request->filled('cohort_id')) {
            $query->where('cohort_id', $request->cohort_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('s')) {
            $s = $request->s;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('file_number', 'like', "%{$s}%");
            });
        }

        $students = $query->orderBy('first_name')->paginate(25);
        $cohorts  = Cohort::orderByDesc('start_date')->get();

        $stats = [
            'total'    => Student::count(),
            'active'   => Student::where('status', 'active')->count(),
            'expelled' => Student::where('status', 'expelled')->count(),
            'cohort'   => $cohorts->first()?->name ?? '—',
        ];

        return view('students.students.index', compact('students', 'cohorts', 'stats'));
    }

    public function create()
    {
        $cohorts = Cohort::where('status', 'active')->orderByDesc('start_date')->get();
        return view('students.students.form', compact('cohorts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'file_number'     => ['required', 'string', 'max:50', 'unique:wp_rsyi_sa_students,file_number'],
            'first_name'      => ['required', 'string', 'max:100'],
            'last_name'       => ['required', 'string', 'max:100'],
            'national_id'     => ['required', 'string', 'max:20'],
            'birth_date'      => ['required', 'date'],
            'gender'          => ['required', 'in:male,female'],
            'phone'           => ['nullable', 'string', 'max:20'],
            'email'           => ['nullable', 'email', 'max:150'],
            'cohort_id'       => ['required', 'integer'],
            'enrollment_date' => ['required', 'date'],
            'address'         => ['nullable', 'string'],
        ]);

        Student::create($validated + ['status' => 'active', 'created_at' => now()]);

        return redirect()->route('students.index')
            ->with('success', 'تم تسجيل الطالب بنجاح.');
    }

    public function show(Student $student)
    {
        $student->load(['cohort', 'documents', 'exitPermits', 'overnightPermits', 'violations']);
        return view('students.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $cohorts = Cohort::orderByDesc('start_date')->get();
        return view('students.students.form', compact('student', 'cohorts'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'email'      => ['nullable', 'email', 'max:150'],
            'cohort_id'  => ['required', 'integer'],
            'status'     => ['required', 'in:active,suspended,expelled,graduated,withdrawn'],
            'address'    => ['nullable', 'string'],
        ]);

        $student->update($validated + ['updated_at' => now()]);

        return redirect()->route('students.index')
            ->with('success', 'تم تحديث بيانات الطالب.');
    }
}
