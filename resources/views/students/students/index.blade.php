@extends('layouts.app')
@section('title', 'قيد الطلاب')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-graduation-cap ml-2 text-warning"></i>قيد الطلاب</h4>
    <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">الطلاب</li></ol>
  </div>
  <a href="{{ route('students.create') }}" class="btn btn-warning text-white">
    <i class="fa fa-user-plus ml-1"></i>تسجيل طالب
  </a>
</div>
<div class="px-4">

  {{-- Stats --}}
  <div class="row mb-3">
    @foreach([
      ['#e67e22', 'fa-users',       'إجمالي الطلاب',   $stats['total']],
      ['#27ae60', 'fa-check',       'طلاب نشطون',      $stats['active']],
      ['#c0392b', 'fa-times',       'طلاب مفصولون',    $stats['expelled']],
      ['#2980b9', 'fa-object-group','الدفعة الحالية',  $stats['cohort']],
    ] as [$color, $icon, $label, $cnt])
    <div class="col-md-3 mb-2">
      <div class="card text-white stat-card" style="background:{{ $color }}">
        <div class="card-body py-3">
          <div class="d-flex justify-content-between">
            <div><div class="stat-label">{{ $label }}</div><div class="stat-value" style="font-size:22px;">{{ $cnt }}</div></div>
            <i class="fa {{ $icon }} stat-icon" style="font-size:28px;"></i>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Filters --}}
  <div class="filters-bar">
    <form method="GET" class="d-flex flex-wrap align-items-end" style="gap:10px;">
      <div>
        <label>الدفعة</label>
        <select name="cohort_id" class="form-control rsyi-select2" style="min-width:150px;">
          <option value="">— كل الدفعات —</option>
          @foreach($cohorts as $cohort)
            <option value="{{ $cohort->id }}" {{ request('cohort_id') == $cohort->id ? 'selected' : '' }}>{{ $cohort->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label>الحالة</label>
        <select name="status" class="form-control">
          <option value="">— الكل —</option>
          <option value="active"    {{ request('status')==='active'    ? 'selected':'' }}>نشط</option>
          <option value="suspended" {{ request('status')==='suspended' ? 'selected':'' }}>موقوف</option>
          <option value="expelled"  {{ request('status')==='expelled'  ? 'selected':'' }}>مفصول</option>
          <option value="graduated" {{ request('status')==='graduated' ? 'selected':'' }}>خريج</option>
        </select>
      </div>
      <div>
        <label>بحث</label>
        <input type="text" name="s" class="form-control" placeholder="الاسم أو رقم الملف" value="{{ request('s') }}">
      </div>
      <button type="submit" class="btn btn-primary">بحث</button>
      <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">إعادة تعيين</a>
    </form>
  </div>

  <div class="card rsyi-card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span><i class="fa fa-list ml-2"></i>قائمة الطلاب</span>
      <small class="text-muted">{{ $students->total() }} طالب</small>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table rsyi-table mb-0">
          <thead>
            <tr>
              <th>#</th><th>الطالب</th><th>رقم الملف</th><th>الدفعة</th>
              <th>تاريخ القيد</th><th>المستندات</th><th>الحالة</th><th>إجراءات</th>
            </tr>
          </thead>
          <tbody>
            @forelse($students as $i => $student)
            <tr>
              <td>{{ $students->firstItem() + $i }}</td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="rounded-circle text-white d-flex align-items-center justify-content-center ml-2"
                       style="width:34px;height:34px;font-size:13px;flex-shrink:0;background:#fd7e14;">
                    {{ mb_substr($student->first_name, 0, 1) }}
                  </div>
                  <div>
                    <div class="font-weight-600">{{ $student->full_name }}</div>
                    <small class="text-muted">{{ $student->national_id }}</small>
                  </div>
                </div>
              </td>
              <td><code>{{ $student->file_number }}</code></td>
              <td>{{ $student->cohort?->name ?? '—' }}</td>
              <td>{{ $student->enrollment_date?->format('Y/m/d') ?? '—' }}</td>
              <td>
                @if($student->pending_documents_count > 0)
                  <span class="badge badge-warning">{{ $student->pending_documents_count }} معلق</span>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td><span class="status-pill {{ $student->status_class }}">{{ $student->status_label }}</span></td>
              <td>
                <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-outline-info" title="عرض"><i class="fa fa-eye"></i></a>
                <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-outline-primary" title="تعديل"><i class="fa fa-edit"></i></a>
              </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-5"><i class="fa fa-inbox fa-2x d-block mb-2"></i>لا توجد بيانات</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($students->hasPages())
    <div class="card-footer">{{ $students->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection
