@extends('layouts.app')
@section('title', 'طلبات الشراء')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-shopping-cart ml-2 text-primary"></i>طلبات الشراء</h4>
    <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">طلبات الشراء</li></ol>
  </div>
  <a href="{{ route('warehouse.purchase-requests.create') }}" class="btn btn-primary">
    <i class="fa fa-plus ml-1"></i>طلب شراء جديد
  </a>
</div>

<div class="px-4">
  {{-- Stats --}}
  <div class="row mb-3">
    <div class="col-md-3"><div class="card stat-card text-white bg-warning"><div class="card-body py-3"><div class="stat-value">{{ $stats['pending'] }}</div><div class="stat-label">في الانتظار</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card text-white bg-success"><div class="card-body py-3"><div class="stat-value">{{ $stats['approved'] }}</div><div class="stat-label">تمت الموافقة</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card text-white bg-danger"><div class="card-body py-3"><div class="stat-value">{{ $stats['rejected'] }}</div><div class="stat-label">مرفوض</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card text-white bg-secondary"><div class="card-body py-3"><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">الإجمالي</div></div></div></div>
  </div>

  <form method="GET" class="filters-bar">
    <div class="row align-items-end">
      <div class="col-md-3 mb-2">
        <label>الحالة</label>
        <select name="status" class="form-control">
          <option value="">الكل</option>
          <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>في الانتظار</option>
          <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>موافق عليه</option>
          <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
        </select>
      </div>
      <div class="col-md-3 mb-2">
        <label>الإدارة</label>
        <select name="dept_id" class="form-control rsyi-select2">
          <option value="">الكل</option>
          @foreach($departments as $dep)
            <option value="{{ $dep->id }}" {{ request('dept_id') == $dep->id ? 'selected' : '' }}>{{ $dep->name }}</option>
          @endforeach
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
        <thead><tr><th>الصنف</th><th>الإدارة</th><th>الكمية</th><th>السبب</th><th>الحالة</th><th>إجراءات</th></tr></thead>
        <tbody>
          @forelse($requests as $req)
          <tr>
            <td>{{ $req->product?->name ?? '—' }}</td>
            <td>{{ $req->department?->name ?? '—' }}</td>
            <td>{{ $req->quantity }}</td>
            <td>{{ Str::limit($req->reason ?? '—', 40) }}</td>
            <td>
              @if($req->status === 'pending') <span class="badge badge-warning">في الانتظار</span>
              @elseif($req->status === 'approved') <span class="badge badge-success">موافق عليه</span>
              @else <span class="badge badge-danger">مرفوض</span>
              @endif
            </td>
            <td>
              @if($req->status === 'pending')
                <form method="POST" action="{{ route('warehouse.purchase-requests.approve', $req) }}" class="d-inline">
                  @csrf
                  <button class="btn btn-sm btn-success" title="موافقة"><i class="fa fa-check"></i></button>
                </form>
                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal{{ $req->id }}" title="رفض">
                  <i class="fa fa-times"></i>
                </button>
                {{-- Reject Modal --}}
                <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1">
                  <div class="modal-dialog"><div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">رفض الطلب</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                    <form method="POST" action="{{ route('warehouse.purchase-requests.reject', $req) }}">
                      @csrf
                      <div class="modal-body">
                        <label>سبب الرفض <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                      </div>
                    </form>
                  </div></div>
                </div>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center text-muted py-4">لا توجد طلبات شراء</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($requests->hasPages())
    <div class="card-footer">{{ $requests->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection
