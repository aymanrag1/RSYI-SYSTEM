@extends('layouts.app')
@section('title', 'الأصناف والمنتجات')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-cubes ml-2 text-success"></i>الأصناف والمنتجات</h4>
    <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">المخازن</li></ol>
  </div>
  <a href="{{ route('warehouse.products.create') }}" class="btn btn-success">
    <i class="fa fa-plus ml-1"></i>إضافة صنف
  </a>
</div>
<div class="px-4">

  {{-- Stats --}}
  <div class="row mb-3">
    @foreach([
      ['bg-success',  'fa-cubes',   'إجمالي الأصناف',         $stats['total']],
      ['bg-primary',  'fa-check',   'الأصناف النشطة',          $stats['active']],
      ['bg-warning',  'fa-warning', 'منخفضة المخزون',         $stats['low_stock']],
      ['bg-danger',   'fa-times',   'نفد المخزون',            $stats['out']],
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
        <label>الفئة</label>
        <select name="category_id" class="form-control rsyi-select2" style="min-width:150px;">
          <option value="">— الكل —</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label>حالة المخزون</label>
        <select name="stock_status" class="form-control">
          <option value="">— الكل —</option>
          <option value="ok"  {{ request('stock_status')==='ok'  ? 'selected':'' }}>متوفر</option>
          <option value="low" {{ request('stock_status')==='low' ? 'selected':'' }}>منخفض</option>
          <option value="out" {{ request('stock_status')==='out' ? 'selected':'' }}>نفد</option>
        </select>
      </div>
      <div>
        <label>بحث</label>
        <input type="text" name="s" class="form-control" placeholder="الاسم أو الكود" value="{{ request('s') }}">
      </div>
      <button type="submit" class="btn btn-primary">بحث</button>
      <a href="{{ route('warehouse.products.index') }}" class="btn btn-outline-secondary">إعادة تعيين</a>
    </form>
  </div>

  <div class="card rsyi-card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span><i class="fa fa-list ml-2"></i>قائمة الأصناف</span>
      <small class="text-muted">{{ $products->total() }} صنف</small>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table rsyi-table mb-0">
          <thead>
            <tr>
              <th>الكود</th><th>اسم الصنف</th><th>الفئة</th><th>الوحدة</th>
              <th>الكمية الحالية</th><th>الحد الأدنى</th><th>حالة المخزون</th><th>إجراءات</th>
            </tr>
          </thead>
          <tbody>
            @forelse($products as $product)
            <tr>
              <td><code>{{ $product->code }}</code></td>
              <td><strong>{{ $product->name }}</strong></td>
              <td>{{ $product->category?->name ?? '—' }}</td>
              <td>{{ $product->unit }}</td>
              <td class="{{ $product->stock_status === 'out' ? 'text-danger font-weight-bold' : ($product->stock_status === 'low' ? 'text-warning font-weight-bold' : '') }}">
                {{ number_format($product->current_qty, 2) }}
              </td>
              <td>{{ number_format($product->min_qty, 2) }}</td>
              <td><span class="badge badge-sys {{ $product->stock_status_class }}">{{ $product->stock_status_label }}</span></td>
              <td>
                <a href="{{ route('warehouse.products.edit', $product) }}" class="btn btn-sm btn-outline-primary"><i class="fa fa-edit"></i></a>
              </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-5"><i class="fa fa-inbox fa-2x d-block mb-2"></i>لا توجد أصناف</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($products->hasPages())
    <div class="card-footer">{{ $products->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection
