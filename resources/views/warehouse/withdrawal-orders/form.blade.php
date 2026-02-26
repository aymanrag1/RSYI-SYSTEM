@extends('layouts.app')
@section('title', 'أمر صرف جديد')

@section('content')
<div class="page-header">
  <h4><i class="fa fa-minus-square ml-2 text-danger"></i>تسجيل أمر صرف من المخزون</h4>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('warehouse.withdrawal-orders.index') }}">أوامر الصرف</a></li>
    <li class="breadcrumb-item active">جديد</li>
  </ol>
</div>
<div class="px-4">
  <div class="card rsyi-card" style="max-width:700px;">
    <div class="card-body">
      <form method="POST" action="{{ route('warehouse.withdrawal-orders.store') }}">
        @csrf
        <div class="row">
          <div class="col-md-6 mb-3">
            <label>الصنف <span class="text-danger">*</span></label>
            <select name="product_id" id="product_select" class="form-control rsyi-select2 @error('product_id') is-invalid @enderror" required>
              <option value="">— اختر الصنف —</option>
              @foreach($products as $prod)
                <option value="{{ $prod->id }}" data-qty="{{ $prod->current_qty }}" data-unit="{{ $prod->unit }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                  {{ $prod->name }} — متاح: {{ $prod->current_qty }} {{ $prod->unit }}
                </option>
              @endforeach
            </select>
            @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label>الإدارة الطالبة <span class="text-danger">*</span></label>
            <select name="dept_id" class="form-control rsyi-select2 @error('dept_id') is-invalid @enderror" required>
              <option value="">— اختر الإدارة —</option>
              @foreach($departments as $dep)
                <option value="{{ $dep->id }}" {{ old('dept_id') == $dep->id ? 'selected' : '' }}>{{ $dep->name }}</option>
              @endforeach
            </select>
            @error('dept_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label>الكمية <span class="text-danger">*</span></label>
            <input type="number" name="quantity" id="qty_input" class="form-control @error('quantity') is-invalid @enderror"
                   value="{{ old('quantity') }}" min="0.01" step="0.01" required>
            <small class="text-muted" id="max_qty_hint"></small>
            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label>تاريخ الصرف <span class="text-danger">*</span></label>
            <input type="date" name="withdrawal_date" class="form-control @error('withdrawal_date') is-invalid @enderror"
                   value="{{ old('withdrawal_date', date('Y-m-d')) }}" required>
          </div>
          <div class="col-md-4 mb-3">
            <label>الغرض</label>
            <input type="text" name="purpose" class="form-control" value="{{ old('purpose') }}" placeholder="وصف الاستخدام">
          </div>
          <div class="col-md-12 mb-3">
            <label>ملاحظات</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
          </div>
        </div>
        <hr>
        <div class="alert alert-warning py-2"><i class="fa fa-exclamation-triangle ml-1"></i>سيتم خصم الكمية من المخزون تلقائياً.</div>
        <div class="d-flex justify-content-end" style="gap:8px;">
          <a href="{{ route('warehouse.withdrawal-orders.index') }}" class="btn btn-secondary">إلغاء</a>
          <button type="submit" class="btn btn-danger"><i class="fa fa-save ml-1"></i>حفظ وخصم المخزون</button>
        </div>
      </form>
    </div>
  </div>
</div>
@push('scripts')
<script>
$('#product_select').on('change', function() {
  var opt = $(this).find(':selected');
  var qty = opt.data('qty');
  var unit = opt.data('unit');
  if (qty !== undefined) {
    $('#max_qty_hint').text('الكمية المتاحة: ' + qty + ' ' + unit);
    $('#qty_input').attr('max', qty);
  } else {
    $('#max_qty_hint').text('');
  }
});
</script>
@endpush
@endsection
