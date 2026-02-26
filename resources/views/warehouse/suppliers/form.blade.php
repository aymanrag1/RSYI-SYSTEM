@extends('layouts.app')
@section('title', isset($supplier) ? 'تعديل مورد' : 'إضافة مورد')

@section('content')
<div class="page-header">
  <h4><i class="fa fa-truck ml-2 text-secondary"></i>{{ isset($supplier) ? 'تعديل مورد' : 'إضافة مورد جديد' }}</h4>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('warehouse.suppliers.index') }}">الموردون</a></li>
    <li class="breadcrumb-item active">{{ isset($supplier) ? 'تعديل' : 'إضافة' }}</li>
  </ol>
</div>
<div class="px-4">
  <div class="card rsyi-card" style="max-width:600px;">
    <div class="card-body">
      <form method="POST" action="{{ isset($supplier) ? route('warehouse.suppliers.update', $supplier) : route('warehouse.suppliers.store') }}">
        @csrf
        @if(isset($supplier)) @method('PUT') @endif
        <div class="row">
          <div class="col-md-12 mb-3">
            <label>اسم المورد <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $supplier->name ?? '') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label>رقم الهاتف</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $supplier->phone ?? '') }}">
          </div>
          <div class="col-md-6 mb-3">
            <label>البريد الإلكتروني</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email ?? '') }}">
          </div>
          <div class="col-md-12 mb-3">
            <label>العنوان</label>
            <textarea name="address" class="form-control" rows="2">{{ old('address', $supplier->address ?? '') }}</textarea>
          </div>
          <div class="col-md-12 mb-3">
            <label>ملاحظات</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $supplier->notes ?? '') }}</textarea>
          </div>
        </div>
        <div class="d-flex justify-content-end" style="gap:8px;">
          <a href="{{ route('warehouse.suppliers.index') }}" class="btn btn-secondary">إلغاء</a>
          <button type="submit" class="btn btn-primary"><i class="fa fa-save ml-1"></i>حفظ</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
