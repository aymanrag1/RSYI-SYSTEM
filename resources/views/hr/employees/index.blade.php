@extends('layouts.app')
@section('title', 'الموظفون')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-users ml-2 text-primary"></i>الموظفون</h4>
    <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">الموظفون</li></ol>
  </div>
  <a href="{{ route('hr.employees.create') }}" class="btn btn-success">
    <i class="fa fa-plus ml-1"></i>إضافة موظف
  </a>
</div>

<div class="px-4">
  {{-- Filters --}}
  <div class="filters-bar d-flex flex-wrap align-items-end" style="gap:12px;">
    <form method="GET" class="d-flex flex-wrap align-items-end" style="gap:10px;width:100%;">
      <div>
        <label>القسم</label>
        <select name="dept_id" class="form-control rsyi-select2" style="min-width:160px;">
          <option value="">— كل الأقسام —</option>
          @foreach($departments as $dept)
            <option value="{{ $dept->id }}" {{ request('dept_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label>الحالة</label>
        <select name="status" class="form-control" style="min-width:130px;">
          <option value="">— الكل —</option>
          <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>فعّال</option>
          <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير فعّال</option>
        </select>
      </div>
      <div>
        <label>بحث</label>
        <input type="text" name="s" class="form-control" style="min-width:200px;" placeholder="الاسم أو الرقم الوظيفي" value="{{ request('s') }}">
      </div>
      <button type="submit" class="btn btn-primary">بحث</button>
      <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-secondary">إعادة تعيين</a>
    </form>
  </div>

  <div class="card rsyi-card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span><i class="fa fa-list ml-2"></i>قائمة الموظفين</span>
      <small class="text-muted">{{ $employees->total() }} موظف</small>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table rsyi-table mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>الموظف</th>
              <th>الرقم الوظيفي</th>
              <th>القسم</th>
              <th>المسمى الوظيفي</th>
              <th>تاريخ التعيين</th>
              <th>الحالة</th>
              <th>إجراءات</th>
            </tr>
          </thead>
          <tbody>
            @forelse($employees as $i => $emp)
            <tr>
              <td>{{ $employees->firstItem() + $i }}</td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center ml-2"
                       style="width:34px;height:34px;font-size:13px;flex-shrink:0;">
                    {{ mb_substr($emp->first_name, 0, 1) }}
                  </div>
                  <div>
                    <div class="font-weight-600">{{ $emp->full_name }}</div>
                    <small class="text-muted">{{ $emp->email }}</small>
                  </div>
                </div>
              </td>
              <td><code>{{ $emp->emp_number }}</code></td>
              <td>{{ $emp->department?->name ?? '—' }}</td>
              <td>{{ $emp->jobTitle?->name ?? '—' }}</td>
              <td>{{ $emp->hire_date?->format('Y/m/d') ?? '—' }}</td>
              <td><span class="status-pill {{ $emp->status_class }}">{{ $emp->status_label }}</span></td>
              <td>
                <a href="{{ route('hr.employees.show', $emp) }}" class="btn btn-sm btn-outline-info" title="عرض"><i class="fa fa-eye"></i></a>
                <a href="{{ route('hr.employees.edit', $emp) }}" class="btn btn-sm btn-outline-primary" title="تعديل"><i class="fa fa-edit"></i></a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center text-muted py-5">
                <i class="fa fa-inbox fa-2x d-block mb-2"></i>لا توجد بيانات
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($employees->hasPages())
    <div class="card-footer">
      {{ $employees->withQueryString()->links() }}
    </div>
    @endif
  </div>
</div>
@endsection
