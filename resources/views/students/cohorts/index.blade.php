@extends('layouts.app')
@section('title', 'الدفعات')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-layer-group ml-2 text-warning"></i>الدفعات الدراسية</h4>
    <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">الدفعات</li></ol>
  </div>
  <a href="{{ route('students.cohorts.create') }}" class="btn btn-warning text-white">
    <i class="fa fa-plus ml-1"></i>إضافة دفعة
  </a>
</div>

<div class="px-4">
  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead><tr><th>اسم الدفعة</th><th>تاريخ البدء</th><th>تاريخ الانتهاء</th><th>الحالة</th><th>عدد الطلاب</th><th>إجراءات</th></tr></thead>
        <tbody>
          @forelse($cohorts as $cohort)
          <tr>
            <td class="font-weight-bold">{{ $cohort->name }}</td>
            <td>{{ $cohort->start_date?->format('Y/m/d') ?? '—' }}</td>
            <td>{{ $cohort->end_date?->format('Y/m/d') ?? '—' }}</td>
            <td>
              @if($cohort->status === 'active') <span class="badge badge-success">نشطة</span>
              @elseif($cohort->status === 'completed') <span class="badge badge-secondary">منتهية</span>
              @else <span class="badge badge-danger">ملغاة</span>
              @endif
            </td>
            <td><span class="badge badge-info">{{ $cohort->students_count }}</span></td>
            <td>
              <a href="{{ route('students.cohorts.edit', $cohort) }}" class="btn btn-sm btn-outline-warning">
                <i class="fa fa-edit"></i>
              </a>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center text-muted py-4">لا توجد دفعات</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
