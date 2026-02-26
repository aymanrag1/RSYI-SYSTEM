@extends('layouts.app')
@section('title', $employee->full_name)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-user ml-2 text-primary"></i>ملف الموظف</h4>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
      <li class="breadcrumb-item"><a href="{{ route('hr.employees.index') }}">الموظفون</a></li>
      <li class="breadcrumb-item active">{{ $employee->full_name }}</li>
    </ol>
  </div>
  <a href="{{ route('hr.employees.edit', $employee) }}" class="btn btn-warning text-white">
    <i class="fa fa-edit ml-1"></i>تعديل
  </a>
</div>

<div class="px-4">
  {{-- Profile Card --}}
  <div class="card rsyi-card mb-4">
    <div class="card-body">
      <div class="d-flex align-items-center">
        <div class="rounded-circle text-white d-flex align-items-center justify-content-center ml-3"
             style="width:64px;height:64px;font-size:24px;flex-shrink:0;background:#2980b9;">
          {{ mb_substr($employee->first_name, 0, 1) }}
        </div>
        <div class="flex-grow-1">
          <h5 class="mb-1 font-weight-600">{{ $employee->full_name }}</h5>
          <div class="text-muted">{{ $employee->jobTitle?->name ?? '—' }} &bull; {{ $employee->department?->name ?? '—' }}</div>
        </div>
        <span class="status-pill {{ $employee->status_class }}">{{ $employee->status_label }}</span>
      </div>
      <hr>
      <div class="row">
        <div class="col-md-3 mb-2"><small class="text-muted d-block">رقم الموظف</small><code>{{ $employee->emp_number }}</code></div>
        <div class="col-md-3 mb-2"><small class="text-muted d-block">تاريخ التعيين</small>{{ $employee->hire_date?->format('Y/m/d') ?? '—' }}</div>
        <div class="col-md-3 mb-2"><small class="text-muted d-block">الهاتف</small>{{ $employee->phone ?? '—' }}</div>
        <div class="col-md-3 mb-2"><small class="text-muted d-block">البريد الإلكتروني</small>{{ $employee->email ?? '—' }}</div>
      </div>
    </div>
  </div>

  {{-- Tabs --}}
  <ul class="nav nav-tabs mb-3" id="empTabs">
    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#leaves-tab">الإجازات ({{ $employee->leaves->count() }})</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#violations-tab">المخالفات ({{ $employee->violations->count() }})</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#overtime-tab">الأوفرتايم ({{ $employee->overtimes->count() }})</a></li>
  </ul>

  <div class="tab-content">

    {{-- Leaves Tab --}}
    <div class="tab-pane fade show active" id="leaves-tab">
      <div class="card rsyi-card">
        <div class="card-body p-0">
          <table class="table rsyi-table mb-0">
            <thead><tr><th>النوع</th><th>من</th><th>إلى</th><th>الأيام</th><th>الحالة</th></tr></thead>
            <tbody>
              @forelse($employee->leaves as $leave)
              <tr>
                <td>{{ $leave->type_label }}</td>
                <td>{{ $leave->start_date?->format('Y/m/d') }}</td>
                <td>{{ $leave->end_date?->format('Y/m/d') }}</td>
                <td>{{ $leave->days }}</td>
                <td><span class="status-pill {{ $leave->status_class }}">{{ $leave->status_label }}</span></td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center text-muted py-4">لا توجد إجازات</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Violations Tab --}}
    <div class="tab-pane fade" id="violations-tab">
      <div class="card rsyi-card">
        <div class="card-body p-0">
          <table class="table rsyi-table mb-0">
            <thead><tr><th>التاريخ</th><th>نوع المخالفة</th><th>الوصف</th><th>العقوبة</th></tr></thead>
            <tbody>
              @forelse($employee->violations as $viol)
              <tr>
                <td>{{ $viol->violation_date?->format('Y/m/d') ?? '—' }}</td>
                <td>{{ $viol->violation_type ?? '—' }}</td>
                <td>{{ $viol->description ?? '—' }}</td>
                <td>{{ $viol->penalty ?? '—' }}</td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted py-4">لا توجد مخالفات</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Overtime Tab --}}
    <div class="tab-pane fade" id="overtime-tab">
      <div class="card rsyi-card">
        <div class="card-body p-0">
          <table class="table rsyi-table mb-0">
            <thead><tr><th>التاريخ</th><th>الساعات</th><th>السبب</th><th>الحالة</th></tr></thead>
            <tbody>
              @forelse($employee->overtimes as $ot)
              <tr>
                <td>{{ $ot->overtime_date?->format('Y/m/d') ?? '—' }}</td>
                <td>{{ $ot->hours ?? '—' }}</td>
                <td>{{ $ot->reason ?? '—' }}</td>
                <td>{{ $ot->status ?? '—' }}</td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted py-4">لا توجد ساعات إضافية</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
