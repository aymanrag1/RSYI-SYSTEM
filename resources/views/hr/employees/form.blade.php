@extends('layouts.app')
@section('title', isset($employee) ? 'تعديل موظف' : 'إضافة موظف')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-user ml-2 text-primary"></i>{{ isset($employee) ? 'تعديل بيانات موظف' : 'إضافة موظف جديد' }}</h4>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
      <li class="breadcrumb-item"><a href="{{ route('hr.employees.index') }}">الموظفون</a></li>
      <li class="breadcrumb-item active">{{ isset($employee) ? 'تعديل' : 'إضافة' }}</li>
    </ol>
  </div>
</div>

<div class="px-4">
  <div class="card rsyi-card">
    <div class="card-header">
      <i class="fa fa-edit ml-2"></i>{{ isset($employee) ? 'تعديل بيانات: ' . $employee->full_name : 'بيانات الموظف الجديد' }}
    </div>
    <div class="card-body">
      <form method="POST"
            action="{{ isset($employee) ? route('hr.employees.update', $employee) : route('hr.employees.store') }}">
        @csrf
        @if(isset($employee)) @method('PUT') @endif

        <div class="row">

          {{-- رقم الموظف (create only) --}}
          @unless(isset($employee))
          <div class="col-md-4 mb-3">
            <label>رقم الموظف <span class="text-danger">*</span></label>
            <input type="text" name="emp_number" class="form-control @error('emp_number') is-invalid @enderror"
                   value="{{ old('emp_number') }}" placeholder="مثال: EMP-001" required>
            @error('emp_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          @endunless

          {{-- الاسم الأول --}}
          <div class="col-md-4 mb-3">
            <label>الاسم الأول <span class="text-danger">*</span></label>
            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                   value="{{ old('first_name', $employee->first_name ?? '') }}" required>
            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- الاسم الأخير --}}
          <div class="col-md-4 mb-3">
            <label>الاسم الأخير <span class="text-danger">*</span></label>
            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                   value="{{ old('last_name', $employee->last_name ?? '') }}" required>
            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- الإدارة --}}
          <div class="col-md-4 mb-3">
            <label>الإدارة <span class="text-danger">*</span></label>
            <select name="dept_id" class="form-control rsyi-select2 @error('dept_id') is-invalid @enderror" required>
              <option value="">— اختر الإدارة —</option>
              @foreach($departments as $dep)
                <option value="{{ $dep->id }}" {{ old('dept_id', $employee->dept_id ?? '') == $dep->id ? 'selected' : '' }}>
                  {{ $dep->name }}
                </option>
              @endforeach
            </select>
            @error('dept_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- المسمى الوظيفي --}}
          <div class="col-md-4 mb-3">
            <label>المسمى الوظيفي <span class="text-danger">*</span></label>
            <select name="job_title_id" class="form-control rsyi-select2 @error('job_title_id') is-invalid @enderror" required>
              <option value="">— اختر المسمى —</option>
              @foreach($jobTitles as $jt)
                <option value="{{ $jt->id }}" {{ old('job_title_id', $employee->job_title_id ?? '') == $jt->id ? 'selected' : '' }}>
                  {{ $jt->name }}
                </option>
              @endforeach
            </select>
            @error('job_title_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- الجنس (create only) --}}
          @unless(isset($employee))
          <div class="col-md-4 mb-3">
            <label>الجنس <span class="text-danger">*</span></label>
            <select name="gender" class="form-control @error('gender') is-invalid @enderror" required>
              <option value="">— اختر —</option>
              <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>ذكر</option>
              <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>أنثى</option>
            </select>
            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          @endunless

          {{-- تاريخ التعيين --}}
          <div class="col-md-4 mb-3">
            <label>تاريخ التعيين <span class="text-danger">*</span></label>
            <input type="date" name="hire_date" class="form-control @error('hire_date') is-invalid @enderror"
                   value="{{ old('hire_date', isset($employee) ? $employee->hire_date?->format('Y-m-d') : '') }}" required>
            @error('hire_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- الرقم الوطني (create only) --}}
          @unless(isset($employee))
          <div class="col-md-4 mb-3">
            <label>الرقم الوطني</label>
            <input type="text" name="national_id" class="form-control @error('national_id') is-invalid @enderror"
                   value="{{ old('national_id') }}">
            @error('national_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          @endunless

          {{-- الهاتف --}}
          <div class="col-md-4 mb-3">
            <label>رقم الهاتف</label>
            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone', $employee->phone ?? '') }}">
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- البريد الإلكتروني --}}
          <div class="col-md-4 mb-3">
            <label>البريد الإلكتروني</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $employee->email ?? '') }}">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- الحالة --}}
          <div class="col-md-4 mb-3">
            <label>الحالة <span class="text-danger">*</span></label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
              <option value="active"   {{ old('status', $employee->status ?? 'active') === 'active'   ? 'selected' : '' }}>نشط</option>
              <option value="inactive" {{ old('status', $employee->status ?? '')       === 'inactive' ? 'selected' : '' }}>غير نشط</option>
              @if(isset($employee))
              <option value="resigned" {{ old('status', $employee->status ?? '')       === 'resigned' ? 'selected' : '' }}>استقال</option>
              @endif
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

        </div>

        <hr>
        <div class="d-flex justify-content-end" style="gap:8px;">
          <a href="{{ route('hr.employees.index') }}" class="btn btn-secondary">إلغاء</a>
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-save ml-1"></i>{{ isset($employee) ? 'حفظ التعديلات' : 'إضافة الموظف' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
