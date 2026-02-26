<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\Students\Cohort;
use Illuminate\Http\Request;

class CohortController extends Controller
{
    public function index()
    {
        $cohorts = Cohort::withCount('students')->orderByDesc('start_date')->get();
        return view('students.cohorts.index', compact('cohorts'));
    }

    public function create()
    {
        return view('students.cohorts.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'start_date' => ['required', 'date'],
            'end_date'   => ['nullable', 'date', 'after:start_date'],
            'status'     => ['required', 'in:active,completed,cancelled'],
        ]);
        Cohort::create($request->only(['name', 'start_date', 'end_date', 'status']) + ['created_at' => now()]);
        return redirect()->route('students.cohorts.index')->with('success', 'تم إضافة الدفعة بنجاح.');
    }

    public function edit(Cohort $cohort)
    {
        return view('students.cohorts.form', compact('cohort'));
    }

    public function update(Request $request, Cohort $cohort)
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'start_date' => ['required', 'date'],
            'end_date'   => ['nullable', 'date'],
            'status'     => ['required', 'in:active,completed,cancelled'],
        ]);
        $cohort->update($request->only(['name', 'start_date', 'end_date', 'status']) + ['updated_at' => now()]);
        return redirect()->route('students.cohorts.index')->with('success', 'تم تحديث الدفعة.');
    }
}
