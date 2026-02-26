@extends('layouts.app')
@section('title', 'أوامر الإضافة')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-plus-square ml-2 text-success"></i>أوامر الإضافة للمخزون</h4>
    <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">أوامر الإضافة</li></ol>
  </div>
  <a href="{{ route('warehouse.add-orders.create') }}" class="btn btn-success">
    <i class="fa fa-plus ml-1"></i>إضافة أمر جديد
  </a>
</div>

<div class="px-4">
  <form method="GET" class="filters-bar">
    <div class="row align-items-end">
      <div class="col-md-3 mb-2">
        <label>المورد</label>
        <select name="supplier_id" class="form-control rsyi-select2">
          <option value="">الكل</option>
          @foreach($suppliers as $sup)
            <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
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
        <thead><tr><th>الصنف</th><th>المورد</th><th>الكمية</th><th>سعر الوحدة</th><th>الإجمالي</th><th>التاريخ</th></tr></thead>
        <tbody>
          @forelse($orders as $order)
          <tr>
            <td>{{ $order->product?->name ?? '—' }}</td>
            <td>{{ $order->supplier?->name ?? '—' }}</td>
            <td><strong>{{ $order->quantity }}</strong> {{ $order->product?->unit ?? '' }}</td>
            <td>{{ $order->unit_price ? number_format($order->unit_price, 2) . ' ج.م' : '—' }}</td>
            <td>{{ $order->unit_price ? number_format($order->quantity * $order->unit_price, 2) . ' ج.م' : '—' }}</td>
            <td>{{ $order->order_date?->format('Y/m/d') ?? '—' }}</td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center text-muted py-4">لا توجد أوامر إضافة</td></tr>
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
