@extends('layouts.app')
@section('title', 'طلب شراء جديد')

@section('content')
<div class="page-header">
  <h4><i class="fa fa-shopping-cart ml-2 text-primary"></i>طلب شراء جديد</h4>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('warehouse.purchase-requests.index') }}">طلبات الشراء</a></li>
    <li class="breadcrumb-item active">جديد</li>
  </ol>
</div>
<div class="px-4">
  <div class="card rsyi-card" style="max-width:650px;">
    <div class="card-body">
      <form method="POST" action="{{ route('warehouse.purchase-requests.store') }}">
        @csrf
        <div class="row">
          <div class="col-md-6 mb-3">
            <label>الصنف <span class="text-danger">*</span></label>
            <select name="product_id" class="form-control rsyi-select2 @error('product_id') is-invalid @enderror" required>
              <option value="">— اختر —</option>
              @foreach($products as $prod)
                <option value="{{ $prod->id }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
              @endforeach
            </select>
            @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label>الإدارة <span class="text-danger">*</span></label>
            <select name="dept_id" class="form-control rsyi-select2 @error('dept_id') is-invalid @enderror" required>
              <option value="">— اختر —</option>
              @foreach($departments as $dep)
                <option value="{{ $dep->id }}" {{ old('dept_id') == $dep->id ? 'selected' : '' }}>{{ $dep->name }}</option>
              @endforeach
            </select>
            @error('dept_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label>الكمية المطلوبة <span class="text-danger">*</span></label>
            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                   value="{{ old('quantity') }}" min="1" required>
            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-12 mb-3">
            <label>سبب الطلب</label>
            <textarea name="reason" class="form-control" rows="3">{{ old('reason') }}</textarea>
          </div>
        </div>
        <hr>
        <div class="d-flex justify-content-end" style="gap:8px;">
          <a href="{{ route('warehouse.purchase-requests.index') }}" class="btn btn-secondary">إلغاء</a>
          <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane ml-1"></i>تقديم الطلب</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
