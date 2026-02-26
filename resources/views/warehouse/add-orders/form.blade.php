@extends('layouts.app')
@section('title', 'أمر إضافة جديد')

@section('content')
<div class="page-header">
  <h4><i class="fa fa-plus-square ml-2 text-success"></i>تسجيل أمر إضافة مخزون</h4>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('warehouse.add-orders.index') }}">أوامر الإضافة</a></li>
    <li class="breadcrumb-item active">جديد</li>
  </ol>
</div>
<div class="px-4">
  <div class="card rsyi-card" style="max-width:700px;">
    <div class="card-body">
      <form method="POST" action="{{ route('warehouse.add-orders.store') }}">
        @csrf
        <div class="row">
          <div class="col-md-6 mb-3">
            <label>الصنف <span class="text-danger">*</span></label>
            <select name="product_id" class="form-control rsyi-select2 @error('product_id') is-invalid @enderror" required>
              <option value="">— اختر الصنف —</option>
              @foreach($products as $prod)
                <option value="{{ $prod->id }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>{{ $prod->name }} ({{ $prod->code }})</option>
              @endforeach
            </select>
            @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label>المورد</label>
            <select name="supplier_id" class="form-control rsyi-select2 @error('supplier_id') is-invalid @enderror">
              <option value="">— بدون مورد —</option>
              @foreach($suppliers as $sup)
                <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label>الكمية <span class="text-danger">*</span></label>
            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                   value="{{ old('quantity') }}" min="0.01" step="0.01" required>
            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label>سعر الوحدة</label>
            <input type="number" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror"
                   value="{{ old('unit_price') }}" min="0" step="0.01" placeholder="اختياري">
          </div>
          <div class="col-md-4 mb-3">
            <label>تاريخ الأمر <span class="text-danger">*</span></label>
            <input type="date" name="order_date" class="form-control @error('order_date') is-invalid @enderror"
                   value="{{ old('order_date', date('Y-m-d')) }}" required>
            @error('order_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-12 mb-3">
            <label>ملاحظات</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
          </div>
        </div>
        <hr>
        <div class="alert alert-info py-2"><i class="fa fa-info-circle ml-1"></i>سيتم تحديث المخزون تلقائياً بعد حفظ الأمر.</div>
        <div class="d-flex justify-content-end" style="gap:8px;">
          <a href="{{ route('warehouse.add-orders.index') }}" class="btn btn-secondary">إلغاء</a>
          <button type="submit" class="btn btn-success"><i class="fa fa-save ml-1"></i>حفظ وتحديث المخزون</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
