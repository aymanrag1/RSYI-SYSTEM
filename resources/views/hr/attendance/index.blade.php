@extends('layouts.app')
@section('title', 'الحضور والغياب')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-clock-o ml-2 text-info"></i>الحضور والغياب</h4>
    <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">الحضور والغياب</li></ol>
  </div>
</div>

<div class="px-4">
  {{-- Stats --}}
  <div class="row mb-3">
    <div class="col-md-3">
      <div class="card stat-card text-white bg-success">
        <div class="card-body py-3">
          <div class="d-flex justify-content-between">
            <div><div class="stat-value">{{ $stats['today_present'] }}</div><div class="stat-label">حاضر اليوم</div></div>
            <i class="fa fa-check-circle stat-icon"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card text-white bg-danger">
        <div class="card-body py-3">
          <div class="d-flex justify-content-between">
            <div><div class="stat-value">{{ $stats['today_absent'] }}</div><div class="stat-label">غائب اليوم</div></div>
            <i class="fa fa-times-circle stat-icon"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card text-white bg-warning">
        <div class="card-body py-3">
          <div class="d-flex justify-content-between">
            <div><div class="stat-value">{{ $stats['today_late'] }}</div><div class="stat-label">متأخر اليوم</div></div>
            <i class="fa fa-clock-o stat-icon"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card text-white bg-info">
        <div class="card-body py-3">
          <div class="d-flex justify-content-between">
            <div><div class="stat-value">{{ $stats['this_month'] }}</div><div class="stat-label">سجلات هذا الشهر</div></div>
            <i class="fa fa-calendar stat-icon"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Quick Register --}}
  <div class="card rsyi-card mb-3">
    <div class="card-header"><i class="fa fa-plus ml-2 text-success"></i>تسجيل حضور سريع</div>
    <div class="card-body">
      <form method="POST" action="{{ route('hr.attendance.store') }}">
        @csrf
        <div class="row align-items-end">
          <div class="col-md-3 mb-2">
            <label class="small font-weight-bold">الموظف</label>
            <select name="employee_id" class="form-control rsyi-select2" required>
              <option value="">— اختر —</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->emp_number }})</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2 mb-2">
            <label class="small font-weight-bold">التاريخ</label>
            <input type="date" name="attendance_date" class="form-control" value="{{ date('Y-m-d') }}" required>
          </div>
          <div class="col-md-2 mb-2">
            <label class="small font-weight-bold">الحالة</label>
            <select name="status" class="form-control" required>
              <option value="present">حاضر</option>
              <option value="absent">غائب</option>
              <option value="late">متأخر</option>
              <option value="excused">بعذر</option>
            </select>
          </div>
          <div class="col-md-2 mb-2">
            <label class="small font-weight-bold">دخول</label>
            <input type="time" name="check_in" class="form-control">
          </div>
          <div class="col-md-2 mb-2">
            <label class="small font-weight-bold">خروج</label>
            <input type="time" name="check_out" class="form-control">
          </div>
          <div class="col-md-1 mb-2">
            <button type="submit" class="btn btn-success btn-block"><i class="fa fa-save"></i></button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Filters --}}
  <form method="GET" class="filters-bar">
    <div class="row align-items-end">
      <div class="col-md-3 mb-2">
        <label>الموظف</label>
        <select name="employee_id" class="form-control rsyi-select2">
          <option value="">الكل</option>
          @foreach($employees as $emp)
            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 mb-2">
        <label>الحالة</label>
        <select name="status" class="form-control">
          <option value="">الكل</option>
          <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>حاضر</option>
          <option value="absent"  {{ request('status') === 'absent'  ? 'selected' : '' }}>غائب</option>
          <option value="late"    {{ request('status') === 'late'    ? 'selected' : '' }}>متأخر</option>
          <option value="excused" {{ request('status') === 'excused' ? 'selected' : '' }}>بعذر</option>
        </select>
      </div>
      <div class="col-md-2 mb-2">
        <label>من تاريخ</label>
        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
      </div>
      <div class="col-md-2 mb-2">
        <label>إلى تاريخ</label>
        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
      </div>
      <div class="col-md-2 mb-2">
        <button type="submit" class="btn btn-primary btn-block">بحث</button>
      </div>
    </div>
  </form>

  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead><tr><th>الموظف</th><th>التاريخ</th><th>الحالة</th><th>دخول</th><th>خروج</th></tr></thead>
        <tbody>
          @forelse($records as $rec)
          <tr>
            <td>{{ $rec->employee?->full_name ?? '—' }}</td>
            <td>{{ $rec->attendance_date?->format('Y/m/d') ?? '—' }}</td>
            <td>
              @php
                $statusMap = ['present'=>['success','حاضر'],'absent'=>['danger','غائب'],'late'=>['warning','متأخر'],'excused'=>['info','بعذر']];
                [$cls,$lbl] = $statusMap[$rec->status] ?? ['secondary',$rec->status];
              @endphp
              <span class="badge badge-{{ $cls }}">{{ $lbl }}</span>
            </td>
            <td>{{ $rec->check_in ?? '—' }}</td>
            <td>{{ $rec->check_out ?? '—' }}</td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center text-muted py-4">لا توجد سجلات حضور</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($records->hasPages())
    <div class="card-footer">{{ $records->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection
