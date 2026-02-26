@extends('layouts.app')
@section('title', 'أوامر الصرف')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-minus-square ml-2 text-danger"></i>أوامر الصرف من المخزون</h4>
    <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">أوامر الصرف</li></ol>
  </div>
  <a href="{{ route('warehouse.withdrawal-orders.create') }}" class="btn btn-danger">
    <i class="fa fa-plus ml-1"></i>أمر صرف جديد
  </a>
</div>

<div class="px-4">
  <form method="GET" class="filters-bar">
    <div class="row align-items-end">
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
        <thead><tr><th>الصنف</th><th>الإدارة</th><th>الكمية المصروفة</th><th>الغرض</th><th>التاريخ</th></tr></thead>
        <tbody>
          @forelse($orders as $order)
          <tr>
            <td>{{ $order->product?->name ?? '—' }}</td>
            <td>{{ $order->department?->name ?? '—' }}</td>
            <td><strong class="text-danger">{{ $order->quantity }}</strong> {{ $order->product?->unit ?? '' }}</td>
            <td>{{ $order->purpose ?? '—' }}</td>
            <td>{{ $order->withdrawal_date?->format('Y/m/d') ?? '—' }}</td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center text-muted py-4">لا توجد أوامر صرف</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($orders->hasPages())
    <div class="card-footer">{{ $orders->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection
