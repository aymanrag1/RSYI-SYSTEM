@extends('layouts.app')
@section('title', 'الموردون')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-truck ml-2 text-secondary"></i>الموردون</h4>
    <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">الموردون</li></ol>
  </div>
  <a href="{{ route('warehouse.suppliers.create') }}" class="btn btn-primary">
    <i class="fa fa-plus ml-1"></i>إضافة مورد
  </a>
</div>

<div class="px-4">
  <form method="GET" class="filters-bar">
    <div class="row align-items-end">
      <div class="col-md-5 mb-2">
        <label>بحث</label>
        <input type="text" name="s" class="form-control" placeholder="اسم المورد أو رقم الهاتف..." value="{{ request('s') }}">
      </div>
      <div class="col-md-2 mb-2">
        <button type="submit" class="btn btn-primary btn-block">بحث</button>
      </div>
    </div>
  </form>

  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead><tr><th>اسم المورد</th><th>الهاتف</th><th>البريد</th><th>العنوان</th><th>إجراءات</th></tr></thead>
        <tbody>
          @forelse($suppliers as $sup)
          <tr>
            <td class="font-weight-bold">{{ $sup->name }}</td>
            <td>{{ $sup->phone ?? '—' }}</td>
            <td>{{ $sup->email ?? '—' }}</td>
            <td>{{ Str::limit($sup->address ?? '—', 40) }}</td>
            <td>
              <a href="{{ route('warehouse.suppliers.edit', $sup) }}" class="btn btn-sm btn-outline-warning">
                <i class="fa fa-edit"></i>
              </a>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center text-muted py-4">لا يوجد موردون</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($suppliers->hasPages())
    <div class="card-footer">{{ $suppliers->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection
