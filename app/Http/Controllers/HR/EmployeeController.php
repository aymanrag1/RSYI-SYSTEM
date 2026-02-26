<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use App\Models\HR\Department;
use App\Models\HR\JobTitle;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['department', 'jobTitle']);

        if ($request->filled('dept_id')) {
            $query->where('dept_id', $request->dept_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('s')) {
            $s = $request->s;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('emp_number', 'like', "%{$s}%");
            });
        }

        $employees   = $query->orderBy('first_name')->paginate(25);
        $departments = Department::orderBy('name')->get();

        return view('hr.employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $jobTitles   = JobTitle::orderBy('name')->get();
        return view('hr.employees.form', compact('departments', 'jobTitles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'emp_number'   => ['required', 'string', 'max:50', 'unique:wp_rsyi_hr_employees,emp_number'],
            'first_name'   => ['required', 'string', 'max:100'],
            'last_name'    => ['required', 'string', 'max:100'],
            'dept_id'      => ['required', 'integer'],
            'job_title_id' => ['required', 'integer'],
            'hire_date'    => ['required', 'date'],
            'national_id'  => ['nullable', 'string', 'max:20'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'email'        => ['nullable', 'email', 'max:150'],
            'gender'       => ['required', 'in:male,female'],
            'status'       => ['required', 'in:active,inactive'],
        ]);

        Employee::create($validated + ['created_at' => now()]);

        return redirect()->route('hr.employees.index')
            ->with('success', 'تم إضافة الموظف بنجاح.');
    }

    public function edit(Employee $employee)
    {
        $departments = Department::orderBy('name')->get();
        $jobTitles   = JobTitle::orderBy('name')->get();
        return view('hr.employees.form', compact('employee', 'departments', 'jobTitles'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name'   => ['required', 'string', 'max:100'],
            'last_name'    => ['required', 'string', 'max:100'],
            'dept_id'      => ['required', 'integer'],
            'job_title_id' => ['required', 'integer'],
            'hire_date'    => ['required', 'date'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'email'        => ['nullable', 'email', 'max:150'],
            'status'       => ['required', 'in:active,inactive,resigned'],
        ]);

        $employee->update($validated + ['updated_at' => now()]);

        return redirect()->route('hr.employees.index')
            ->with('success', 'تم تحديث بيانات الموظف.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['department', 'jobTitle', 'leaves', 'attendances', 'violations', 'overtimes']);
        return view('hr.employees.show', compact('employee'));
    }
}
