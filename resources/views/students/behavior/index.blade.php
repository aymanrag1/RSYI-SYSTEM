@extends('layouts.app')
@section('title', 'المخالفات السلوكية')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-exclamation-triangle ml-2 text-danger"></i>المخالفات السلوكية</h4>
    <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">المخالفات السلوكية</li></ol>
  </div>
  <a href="{{ route('students.behavior.create') }}" class="btn btn-danger">
    <i class="fa fa-plus ml-1"></i>تسجيل مخالفة
  </a>
</div>

<div class="px-4">
  <form method="GET" class="filters-bar">
    <div class="row align-items-end">
      <div class="col-md-4 mb-2">
        <label>الطالب</label>
        <select name="student_id" class="form-control rsyi-select2">
          <option value="">الكل</option>
          @foreach($students as $std)
            <option value="{{ $std->id }}" {{ request('student_id') == $std->id ? 'selected' : '' }}>{{ $std->full_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3 mb-2">
        <label>نوع المخالفة</label>
        <input type="text" name="violation_type" class="form-control" value="{{ request('violation_type') }}" placeholder="مثال: تأخر، غياب...">
      </div>
      <div class="col-md-2 mb-2">
        <button type="submit" class="btn btn-primary btn-block">بحث</button>
      </div>
    </div>
  </form>

  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead><tr><th>الطالب</th><th>نوع المخالفة</th><th>الوصف</th><th>النقاط</th><th>الإجراء المتخذ</th><th>التاريخ</th></tr></thead>
        <tbody>
          @forelse($violations as $viol)
          <tr>
            <td>
              <a href="{{ route('students.show', $viol->student) }}">{{ $viol->student?->full_name ?? '—' }}</a>
            </td>
            <td><span class="badge badge-danger">{{ $viol->violation_type }}</span></td>
            <td>{{ Str::limit($viol->description ?? '—', 50) }}</td>
            <td><strong>{{ $viol->points ?? 0 }}</strong></td>
            <td>{{ Str::limit($viol->action_taken ?? '—', 40) }}</td>
            <td>{{ $viol->violation_date?->format('Y/m/d') ?? '—' }}</td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center text-muted py-4">لا توجد مخالفات</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($violations->hasPages())
    <div class="card-footer">{{ $violations->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection
