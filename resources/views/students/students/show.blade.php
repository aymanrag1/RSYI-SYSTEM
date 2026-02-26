@extends('layouts.app')
@section('title', $student->full_name)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-graduation-cap ml-2 text-warning"></i>ملف الطالب</h4>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
      <li class="breadcrumb-item"><a href="{{ route('students.index') }}">الطلاب</a></li>
      <li class="breadcrumb-item active">{{ $student->full_name }}</li>
    </ol>
  </div>
  <a href="{{ route('students.edit', $student) }}" class="btn btn-warning text-white">
    <i class="fa fa-edit ml-1"></i>تعديل
  </a>
</div>

<div class="px-4">
  {{-- Profile Card --}}
  <div class="card rsyi-card mb-4">
    <div class="card-body">
      <div class="d-flex align-items-center">
        <div class="rounded-circle text-white d-flex align-items-center justify-content-center ml-3"
             style="width:64px;height:64px;font-size:24px;flex-shrink:0;background:#fd7e14;">
          {{ mb_substr($student->first_name, 0, 1) }}
        </div>
        <div class="flex-grow-1">
          <h5 class="mb-1 font-weight-600">{{ $student->full_name }}</h5>
          <div class="text-muted">{{ $student->cohort?->name ?? '—' }} &bull; رقم الملف: <code>{{ $student->file_number }}</code></div>
        </div>
        <span class="status-pill {{ $student->status_class }}">{{ $student->status_label }}</span>
      </div>
      <hr>
      <div class="row">
        <div class="col-md-3 mb-2"><small class="text-muted d-block">الرقم الوطني</small>{{ $student->national_id ?? '—' }}</div>
        <div class="col-md-3 mb-2"><small class="text-muted d-block">تاريخ القيد</small>{{ $student->enrollment_date?->format('Y/m/d') ?? '—' }}</div>
        <div class="col-md-3 mb-2"><small class="text-muted d-block">الهاتف</small>{{ $student->phone ?? '—' }}</div>
        <div class="col-md-3 mb-2"><small class="text-muted d-block">البريد الإلكتروني</small>{{ $student->email ?? '—' }}</div>
        @if($student->address)
        <div class="col-md-12 mb-2"><small class="text-muted d-block">العنوان</small>{{ $student->address }}</div>
        @endif
      </div>
    </div>
  </div>

  {{-- Tabs --}}
  <ul class="nav nav-tabs mb-3" id="studentTabs">
    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#docs-tab">المستندات ({{ $student->documents->count() }})</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#exit-tab">تصاريح الخروج ({{ $student->exitPermits->count() }})</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#overnight-tab">تصاريح المبيت ({{ $student->overnightPermits->count() }})</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#behavior-tab">المخالفات ({{ $student->violations->count() }})</a></li>
  </ul>

  <div class="tab-content">

    {{-- Documents Tab --}}
    <div class="tab-pane fade show active" id="docs-tab">
      <div class="card rsyi-card">
        <div class="card-body p-0">
          <table class="table rsyi-table mb-0">
            <thead><tr><th>اسم المستند</th><th>النوع</th><th>الحالة</th><th>تاريخ الرفع</th></tr></thead>
            <tbody>
              @forelse($student->documents as $doc)
              <tr>
                <td>{{ $doc->name ?? '—' }}</td>
                <td>{{ $doc->type ?? '—' }}</td>
                <td>
                  @if(($doc->status ?? '') === 'pending')
                    <span class="badge badge-warning">معلق</span>
                  @elseif(($doc->status ?? '') === 'approved')
                    <span class="badge badge-success">مقبول</span>
                  @else
                    <span class="badge badge-secondary">{{ $doc->status ?? '—' }}</span>
                  @endif
                </td>
                <td>{{ $doc->created_at?->format('Y/m/d') ?? '—' }}</td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted py-4"><i class="fa fa-inbox fa-2x d-block mb-2"></i>لا توجد مستندات</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Exit Permits Tab --}}
    <div class="tab-pane fade" id="exit-tab">
      <div class="card rsyi-card">
        <div class="card-body p-0">
          <table class="table rsyi-table mb-0">
            <thead><tr><th>تاريخ الخروج</th><th>وقت العودة</th><th>السبب</th><th>الحالة</th></tr></thead>
            <tbody>
              @forelse($student->exitPermits as $permit)
              <tr>
                <td>{{ $permit->exit_date?->format('Y/m/d H:i') ?? '—' }}</td>
                <td>{{ $permit->return_time ?? '—' }}</td>
                <td>{{ $permit->reason ?? '—' }}</td>
                <td>{{ $permit->status ?? '—' }}</td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted py-4">لا توجد تصاريح خروج</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Overnight Permits Tab --}}
    <div class="tab-pane fade" id="overnight-tab">
      <div class="card rsyi-card">
        <div class="card-body p-0">
          <table class="table rsyi-table mb-0">
            <thead><tr><th>من</th><th>إلى</th><th>الوجهة</th><th>الحالة</th></tr></thead>
            <tbody>
              @forelse($student->overnightPermits as $permit)
              <tr>
                <td>{{ $permit->from_date?->format('Y/m/d') ?? '—' }}</td>
                <td>{{ $permit->to_date?->format('Y/m/d') ?? '—' }}</td>
                <td>{{ $permit->destination ?? '—' }}</td>
                <td>{{ $permit->status ?? '—' }}</td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted py-4">لا توجد تصاريح مبيت</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Behavior Tab --}}
    <div class="tab-pane fade" id="behavior-tab">
      <div class="card rsyi-card">
        <div class="card-body p-0">
          <table class="table rsyi-table mb-0">
            <thead><tr><th>التاريخ</th><th>نوع المخالفة</th><th>الوصف</th><th>النقاط</th></tr></thead>
            <tbody>
              @forelse($student->violations as $viol)
              <tr>
                <td>{{ $viol->violation_date?->format('Y/m/d') ?? '—' }}</td>
                <td>{{ $viol->violation_type ?? '—' }}</td>
                <td>{{ $viol->description ?? '—' }}</td>
                <td><span class="badge badge-danger">{{ $viol->points ?? 0 }}</span></td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted py-4">لا توجد مخالفات سلوكية</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
