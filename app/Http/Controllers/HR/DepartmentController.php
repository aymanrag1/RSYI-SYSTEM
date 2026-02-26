<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('employees')->orderBy('name')->get();
        return view('hr.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('hr.departments.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:150', 'unique:wp_rsyi_hr_departments,name'],
            'description' => ['nullable', 'string'],
            'manager_id'  => ['nullable', 'integer'],
        ]);

        Department::create($request->only(['name', 'description', 'manager_id']) + ['created_at' => now()]);

        return redirect()->route('hr.departments.index')->with('success', 'تم إضافة الإدارة بنجاح.');
    }

    public function edit(Department $department)
    {
        return view('hr.departments.form', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'manager_id'  => ['nullable', 'integer'],
        ]);

        $department->update($request->only(['name', 'description', 'manager_id']) + ['updated_at' => now()]);

        return redirect()->route('hr.departments.index')->with('success', 'تم تحديث الإدارة.');
    }

    public function destroy(Department $department)
    {
        if ($department->employees()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف إدارة بها موظفون.');
        }
        $department->delete();
        return redirect()->route('hr.departments.index')->with('success', 'تم حذف الإدارة.');
    }
}
