@extends('layouts.app')
@section('title', $type === 'exit' ? 'تصاريح الخروج' : 'تصاريح المبيت')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    @if($type === 'exit')
      <h4><i class="fa fa-sign-out ml-2 text-info"></i>تصاريح الخروج</h4>
      <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">تصاريح الخروج</li></ol>
    @else
      <h4><i class="fa fa-moon-o ml-2 text-info"></i>تصاريح المبيت</h4>
      <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">تصاريح المبيت</li></ol>
    @endif
  </div>
  @if($type === 'exit')
    <a href="{{ route('students.exit-permits.create') }}" class="btn btn-info text-white"><i class="fa fa-plus ml-1"></i>تصريح خروج جديد</a>
  @else
    <a href="{{ route('students.overnight-permits.create') }}" class="btn btn-info text-white"><i class="fa fa-plus ml-1"></i>تصريح مبيت جديد</a>
  @endif
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
      <div class="col-md-2 mb-2">
        <label>الحالة</label>
        <select name="status" class="form-control">
          <option value="">الكل</option>
          <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>في الانتظار</option>
          <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>موافق عليه</option>
          <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
        </select>
      </div>
      <div class="col-md-2 mb-2">
        <button type="submit" class="btn btn-primary btn-block">بحث</button>
      </div>
    </div>
  </form>

  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead>
          <tr>
            <th>الطالب</th>
            @if($type === 'exit')
              <th>تاريخ الخروج</th><th>وقت العودة</th><th>السبب</th>
            @else
              <th>من</th><th>إلى</th><th>الوجهة</th>
            @endif
            <th>الحالة</th><th>إجراءات</th>
          </tr>
        </thead>
        <tbody>
          @forelse($permits as $permit)
          <tr>
            <td>{{ $permit->student?->full_name ?? '—' }}</td>
            @if($type === 'exit')
              <td>{{ $permit->exit_date?->format('Y/m/d H:i') ?? '—' }}</td>
              <td>{{ $permit->return_time ?? '—' }}</td>
              <td>{{ Str::limit($permit->reason ?? '—', 30) }}</td>
            @else
              <td>{{ $permit->from_date?->format('Y/m/d') ?? '—' }}</td>
              <td>{{ $permit->to_date?->format('Y/m/d') ?? '—' }}</td>
              <td>{{ $permit->destination ?? '—' }}</td>
            @endif
            <td>
              @if($permit->status === 'pending') <span class="badge badge-warning">في الانتظار</span>
              @elseif($permit->status === 'approved') <span class="badge badge-success">موافق</span>
              @else <span class="badge badge-danger">مرفوض</span>
              @endif
            </td>
            <td>
              @if($permit->status === 'pending')
                @if($type === 'exit')
                  <form method="POST" action="{{ route('students.exit-permits.approve', $permit) }}" class="d-inline">@csrf<button class="btn btn-sm btn-success" title="موافقة"><i class="fa fa-check"></i></button></form>
                  <form method="POST" action="{{ route('students.exit-permits.reject', $permit) }}" class="d-inline" onsubmit="return confirm('رفض التصريح؟')">@csrf<button class="btn btn-sm btn-danger" title="رفض"><i class="fa fa-times"></i></button></form>
                @else
                  <form method="POST" action="{{ route('students.overnight-permits.approve', $permit) }}" class="d-inline">@csrf<button class="btn btn-sm btn-success" title="موافقة"><i class="fa fa-check"></i></button></form>
                  <form method="POST" action="{{ route('students.overnight-permits.reject', $permit) }}" class="d-inline" onsubmit="return confirm('رفض التصريح؟')">@csrf<button class="btn btn-sm btn-danger" title="رفض"><i class="fa fa-times"></i></button></form>
                @endif
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center text-muted py-4">لا توجد تصاريح</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($permits->hasPages())
    <div class="card-footer">{{ $permits->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection
