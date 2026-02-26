@extends('layouts.app')
@section('title', isset($cohort) ? 'تعديل دفعة' : 'إضافة دفعة')

@section('content')
<div class="page-header">
  <h4><i class="fa fa-layer-group ml-2 text-warning"></i>{{ isset($cohort) ? 'تعديل دفعة' : 'إضافة دفعة جديدة' }}</h4>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.cohorts.index') }}">الدفعات</a></li>
    <li class="breadcrumb-item active">{{ isset($cohort) ? 'تعديل' : 'إضافة' }}</li>
  </ol>
</div>
<div class="px-4">
  <div class="card rsyi-card" style="max-width:600px;">
    <div class="card-body">
      <form method="POST" action="{{ isset($cohort) ? route('students.cohorts.update', $cohort) : route('students.cohorts.store') }}">
        @csrf
        @if(isset($cohort)) @method('PUT') @endif
        <div class="row">
          <div class="col-md-12 mb-3">
            <label>اسم الدفعة <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $cohort->name ?? '') }}" placeholder="مثال: الدفعة الأولى 2024" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label>تاريخ البدء <span class="text-danger">*</span></label>
            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                   value="{{ old('start_date', $cohort->start_date?->format('Y-m-d') ?? '') }}" required>
            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label>تاريخ الانتهاء</label>
            <input type="date" name="end_date" class="form-control"
                   value="{{ old('end_date', $cohort->end_date?->format('Y-m-d') ?? '') }}">
          </div>
          <div class="col-md-4 mb-3">
            <label>الحالة <span class="text-danger">*</span></label>
            <select name="status" class="form-control" required>
              <option value="active"    {{ old('status', $cohort->status ?? 'active') === 'active'    ? 'selected' : '' }}>نشطة</option>
              <option value="completed" {{ old('status', $cohort->status ?? '')        === 'completed' ? 'selected' : '' }}>منتهية</option>
              <option value="cancelled" {{ old('status', $cohort->status ?? '')        === 'cancelled' ? 'selected' : '' }}>ملغاة</option>
            </select>
          </div>
        </div>
        <div class="d-flex justify-content-end" style="gap:8px;">
          <a href="{{ route('students.cohorts.index') }}" class="btn btn-secondary">إلغاء</a>
          <button type="submit" class="btn btn-warning text-white"><i class="fa fa-save ml-1"></i>حفظ</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
