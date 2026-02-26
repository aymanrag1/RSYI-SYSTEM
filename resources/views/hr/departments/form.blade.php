@extends('layouts.app')
@section('title', isset($department) ? 'تعديل إدارة' : 'إضافة إدارة')

@section('content')
<div class="page-header">
  <h4><i class="fa fa-sitemap ml-2 text-primary"></i>{{ isset($department) ? 'تعديل إدارة' : 'إضافة إدارة جديدة' }}</h4>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('hr.departments.index') }}">الإدارات</a></li>
    <li class="breadcrumb-item active">{{ isset($department) ? 'تعديل' : 'إضافة' }}</li>
  </ol>
</div>
<div class="px-4">
  <div class="card rsyi-card" style="max-width:600px;">
    <div class="card-body">
      <form method="POST" action="{{ isset($department) ? route('hr.departments.update', $department) : route('hr.departments.store') }}">
        @csrf
        @if(isset($department)) @method('PUT') @endif
        <div class="mb-3">
          <label>اسم الإدارة <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                 value="{{ old('name', $department->name ?? '') }}" required>
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
          <label>الوصف</label>
          <textarea name="description" class="form-control" rows="3">{{ old('description', $department->description ?? '') }}</textarea>
        </div>
        <div class="d-flex justify-content-end" style="gap:8px;">
          <a href="{{ route('hr.departments.index') }}" class="btn btn-secondary">إلغاء</a>
          <button type="submit" class="btn btn-primary"><i class="fa fa-save ml-1"></i>حفظ</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
