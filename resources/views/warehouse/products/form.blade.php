@extends('layouts.app')
@section('title', isset($product) ? 'تعديل صنف' : 'إضافة صنف')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-cube ml-2 text-success"></i>{{ isset($product) ? 'تعديل صنف' : 'إضافة صنف جديد' }}</h4>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
      <li class="breadcrumb-item"><a href="{{ route('warehouse.products.index') }}">المخزن</a></li>
      <li class="breadcrumb-item active">{{ isset($product) ? 'تعديل' : 'إضافة' }}</li>
    </ol>
  </div>
</div>

<div class="px-4">
  <div class="card rsyi-card">
    <div class="card-header"><i class="fa fa-edit ml-2"></i>{{ isset($product) ? 'تعديل: ' . $product->name : 'بيانات الصنف الجديد' }}</div>
    <div class="card-body">
      <form method="POST"
            action="{{ isset($product) ? route('warehouse.products.update', $product) : route('warehouse.products.store') }}">
        @csrf
        @if(isset($product)) @method('PUT') @endif

        <div class="row">

          {{-- الكود (create only) --}}
          @unless(isset($product))
          <div class="col-md-4 mb-3">
            <label>كود الصنف <span class="text-danger">*</span></label>
            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                   value="{{ old('code') }}" placeholder="مثال: WH-001" required>
            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          @endunless

          {{-- اسم الصنف --}}
          <div class="col-md-{{ isset($product) ? '6' : '4' }} mb-3">
            <label>اسم الصنف <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $product->name ?? '') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- الفئة --}}
          <div class="col-md-4 mb-3">
            <label>الفئة <span class="text-danger">*</span></label>
            <select name="category_id" class="form-control rsyi-select2 @error('category_id') is-invalid @enderror" required>
              <option value="">— اختر الفئة —</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                  {{ $cat->name }}
                </option>
              @endforeach
            </select>
            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- وحدة القياس --}}
          <div class="col-md-3 mb-3">
            <label>وحدة القياس <span class="text-danger">*</span></label>
            <select name="unit" class="form-control @error('unit') is-invalid @enderror" required>
              <option value="">— اختر —</option>
              @foreach(['قطعة','كيلو','لتر','متر','علبة','كرتون','دستة','طقم'] as $u)
                <option value="{{ $u }}" {{ old('unit', $product->unit ?? '') === $u ? 'selected' : '' }}>{{ $u }}</option>
              @endforeach
            </select>
            @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- الحد الأدنى للمخزون --}}
          <div class="col-md-3 mb-3">
            <label>الحد الأدنى للمخزون <span class="text-danger">*</span></label>
            <input type="number" name="min_qty" class="form-control @error('min_qty') is-invalid @enderror"
                   value="{{ old('min_qty', $product->min_qty ?? 0) }}" min="0" step="0.01" required>
            @error('min_qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- الحالة (edit only) --}}
          @if(isset($product))
          <div class="col-md-3 mb-3">
            <label>الحالة <span class="text-danger">*</span></label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
              <option value="active"   {{ old('status', $product->status) === 'active'   ? 'selected' : '' }}>نشط</option>
              <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>غير نشط</option>
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          @endif

          {{-- الوصف --}}
          <div class="col-md-12 mb-3">
            <label>الوصف</label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                      rows="3">{{ old('description', $product->description ?? '') }}</textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

        </div>

        <hr>
        <div class="d-flex justify-content-end" style="gap:8px;">
          <a href="{{ route('warehouse.products.index') }}" class="btn btn-secondary">إلغاء</a>
          <button type="submit" class="btn btn-success">
            <i class="fa fa-save ml-1"></i>{{ isset($product) ? 'حفظ التعديلات' : 'إضافة الصنف' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
