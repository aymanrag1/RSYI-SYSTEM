@extends('layouts.app')
@section('title', 'الإجازات')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-calendar-check-o ml-2 text-success"></i>طلبات الإجازات</h4>
    <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">الإجازات</li></ol>
  </div>
  <a href="{{ route('hr.leaves.create') }}" class="btn btn-success">
    <i class="fa fa-plus ml-1"></i>طلب إجازة جديد
  </a>
</div>
<div class="px-4">

  {{-- Stats --}}
  <div class="row mb-3">
    @foreach([
      ['bg-warning','fa-clock-o',     'معلق',       $stats['pending']],
      ['bg-success','fa-check-circle','موافق عليه', $stats['approved']],
      ['bg-danger', 'fa-times-circle','مرفوض',      $stats['rejected']],
      ['bg-primary','fa-list',        'الإجمالي',   $stats['total']],
    ] as [$bg, $icon, $label, $cnt])
    <div class="col-md-3 mb-2">
      <div class="card text-white {{ $bg }} stat-card">
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
        <label>الحالة</label>
        <select name="status" class="form-control">
          <option value="">— الكل —</option>
          <option value="pending"  {{ request('status')==='pending'  ? 'selected':'' }}>معلق</option>
          <option value="approved" {{ request('status')==='approved' ? 'selected':'' }}>موافق عليه</option>
          <option value="rejected" {{ request('status')==='rejected' ? 'selected':'' }}>مرفوض</option>
        </select>
      </div>
      <div>
        <label>من تاريخ</label>
        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
      </div>
      <div>
        <label>إلى تاريخ</label>
        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
      </div>
      <button type="submit" class="btn btn-primary">بحث</button>
      <a href="{{ route('hr.leaves.index') }}" class="btn btn-outline-secondary">إعادة تعيين</a>
    </form>
  </div>

  <div class="card rsyi-card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table rsyi-table mb-0">
          <thead>
            <tr>
              <th>#</th><th>الموظف</th><th>نوع الإجازة</th><th>من</th><th>إلى</th><th>الأيام</th><th>الحالة</th><th>إجراءات</th>
            </tr>
          </thead>
          <tbody>
            @forelse($leaves as $i => $leave)
            <tr>
              <td>{{ $leaves->firstItem() + $i }}</td>
              <td>{{ $leave->employee?->full_name ?? '—' }}</td>
              <td>{{ $leave->getTypeLabel() }}</td>
              <td>{{ $leave->start_date?->format('Y/m/d') }}</td>
              <td>{{ $leave->end_date?->format('Y/m/d') }}</td>
              <td>{{ $leave->days }} يوم</td>
              <td><span class="badge badge-sys {{ $leave->status_class }}">{{ $leave->status_label }}</span></td>
              <td>
                @if($leave->status === 'pending')
                  <form method="POST" action="{{ route('hr.leaves.approve', $leave) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-success" title="موافقة"><i class="fa fa-check"></i></button>
                  </form>
                  <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal{{ $leave->id }}" title="رفض">
                    <i class="fa fa-times"></i>
                  </button>
                  {{-- Reject Modal --}}
                  <div class="modal fade" id="rejectModal{{ $leave->id }}">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <form method="POST" action="{{ route('hr.leaves.reject', $leave) }}">
                          @csrf
                          <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">رفض طلب الإجازة</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                          </div>
                          <div class="modal-body">
                            <div class="form-group">
                              <label>سبب الرفض *</label>
                              <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-danger">رفض</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                @endif
              </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-5"><i class="fa fa-inbox fa-2x d-block mb-2"></i>لا توجد طلبات</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($leaves->hasPages())
    <div class="card-footer">{{ $leaves->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection
